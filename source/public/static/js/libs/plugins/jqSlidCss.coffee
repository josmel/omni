(($,_) ->
	Cookie=
		create: (name,value,days)->
			if(days)
				date= new Date()
				date.setTime date.getTime()+(days*24*60*60*1000)
				expires= "; expires="+date.toGMTString()
			else
				expires= ""
			document.cookie= name+"="+value+expires+"; path=/"
			this
		read: (name) ->
			nameEQ = name + "="
			ca = document.cookie.split(";")
			i = 0
			while i < ca.length
				c = ca[i]
				c = c.substring(1, c.length)  while c.charAt(0) is " "
				return c.substring(nameEQ.length, c.length)  if c.indexOf(nameEQ) is 0
				i++
			null
		del: (name)->
			return this.create name,"",-1
	getJsonCategory= (data,indexes)->
		result= {}
		for index of indexes
			result[index]= []
			data= _.reject data,(json)->
				if json.category is index
					result[index].push json
					return true
				else
					return false
		result
	caldivExc = (dividendo, divisor) ->
		residuo = dividendo % divisor
		(if (residuo isnt 0) then ((dividendo - residuo) / divisor) + 1 else dividendo / divisor)
	class SlidCss
		constructor: (that,options)->
			opt=
				next: ".slidcss-next"					#Selector del Nav Siguiente
				prev: ".slidcss-prev"					#Selector del Nav Anterior
				classContent: "content-slid"			#Clase del contenedor de los elementos
				addContent: true						#Activa o desactiva la creaccion del contenedor de los elementos
				defaultImg: ""							#Imagen por default 
				contentTag: "div"						#Tag de los contenedores de los elementos
				readCookie: true						#Activar leer cookie
				nameCookie: "cookie"					#Nombre de la cookie
				paginate: 6								#Cantidad de paginas a mostrar
				source: null							#Data en Formato Json, donde el indice es la categoria
				dataSource: ""							#Data Source
				category: ""							#Category View
				dataCategory: ""						#Parametro del cual se extraeran las categorias
				categoryAll: "all"							#Categoria que representa todos los productos
				template: "#template"					#Id de Template
				addedElements: null						#Callback luego que se añaden los elementos
			@settings= $.extend opt,options
			@slider= that								#Seteando el contenedor
			@elements= []								#Instancia de los elementos
			@paginate= @settings.paginate				#Elementos a paginar
			@nav=
				next: $(@settings.next)
				prev: $(@settings.prev)
			@template= if $(@settings.template).length then _.template $(@settings.template).html() else null
			@data= {}
			@oldElements= []							#Array que almacena los elementos anteriores a borrar
			@newElements= []							#Array que almacena los elementos nuevos
			@effectNext= true							#Efecto para hacer un next
			@currentCategory= @settings.category		#Categoria actual
			@page= 1									#Pagina actual
			@totalPage= {}
			@_init()
			return @
		_init: ()->
			@_construct()
			if @settings.readCookie
				@_cookie()
			@_preDispatch()
			@_bindEvents()
		_construct: ()->
			_this= @
			settings= @settings
			if settings.source is null
				@data[settings.categoryAll]= settings.dataSource
				indexes= _.indexBy(settings.dataSource, settings.dataCategory)						#Indices de todas las categorías
				jsonCategory= getJsonCategory settings.dataSource, indexes
				@data= $.extend @data, jsonCategory
			else
				@data= settings.source
			element= null
			i= 0
			for index,value of @data
				_this.totalPage[index]= caldivExc value.length,_this.paginate
			if settings.addContent
				while i < @paginate
					element= $ "<"+settings.contentTag+" />",
						class: settings.classContent
					_this.elements.push element
					_this.slider.append element
					i++
			else
				$("."+settings.classContent).each (index,value)->
					_this.elements.push $(value)
		_cookie: ()->
			_this= @
			settings= @settings
			cookie= Cookie.read settings.nameCookie
			category= ""
			if cookie isnt null
				jsonCookie= (new Function('return '+cookie))();
				if jsonCookie.category
					category= ""+jsonCookie.category
					_this.currentCategory= if category.length is 1 then "0"+category else category
				if jsonCookie.page then _this.page= jsonCookie.page
		_preDispatch: ()->
			category= @currentCategory
			page= @page
			data= @data[category]
			pages= @totalPage[category]
			if pages>=page
				@_stateNavs(pages,page)
				@_dispatch()
			else if pages is 0
				@_stateNavs(pages,page)
		_dispatch: ()->
			_this= @
			settings= @settings
			category= @currentCategory
			data= @data[category]
			frecuency= (@page-1)*@paginate
			cond= if @newElements.length>0 then true else false
			element= null
			if cond
				@oldElements= @newElements
				@newElements= []
			$.each @elements, (index,value)->
				if typeof data[index+frecuency] isnt "undefined"
					element= _this.template(data[index+frecuency]).replace(/[\n\r]/g, "")
					element= $(element)
					if cond
						element= _this._setStyles(element)
					element.find("img").on "error", ()->
						$(this).attr("src",settings.defaultImg)
						data[index+frecuency]["img"]= settings.defaultImg
					_this.newElements.push element
					$(value).append element
			settings.addedElements and settings.addedElements(_this.newElements,_this.page)
			if cond
				lengthOld= @oldElements.length
				lengthNew= @newElements.length
				maxLength= if lengthOld>=lengthNew then lengthOld else lengthNew
				@_animation(maxLength)
		_setStyles: (element)->
			leftEl= if @effectNext then "100%" else "-100%"
			element.css(
				"left": leftEl
				"transform": "scale(0.8)"
				"-ms-transform": "scale(0.8)"
				"-webkit-transform": "scale(0.8)"
				"text-indent": "0.8px"
			)
			return element
		_animation: (max)->
			_this= @
			oldElements= @oldElements
			newElements= @newElements
			lengthOld= oldElements.length
			leftOld= ""
			i= 0
			while i < max
				if typeof oldElements[i] isnt "undefined"
					oldElements[i].css "text-indent", "1px"
					if typeof newElements[i] is "undefined"
						oldElements[i].fadeOut 500,()->
							$(this).off().remove()
					else
						leftOld= if _this.effectNext then "-60%" else "60%"
						oldElements[i].animate(
							"left": leftOld
							"text-indent": "0.8px"
						,
							"step": (now,fx)->
								if fx.prop is "textIndent"
									$(this).css
										"transform": "scale("+now+")"
										"-ms-transform": "scale("+now+")"
										"-webkit-transform": "scale("+now+")"
							"duration": 800
							"complete": ()->
								$(this).off().remove()
						)
				if typeof newElements[i] isnt "undefined"
					newElements[i].animate(
						"left": 0
						"text-indent": "1px"
					,
						"step": (now,fx)->
							if fx.prop is "textIndent"
								$(this).css
									"transform": "scale("+now+")"
									"-ms-transform": "scale("+now+")"
									"-webkit-transform": "scale("+now+")"
						"duration": 800
					)
				i++
		_stateNavs: (totalPages,currentPage)->
			nav= @nav
			if totalPages is 0 or currentPage is 1
				nav.prev.addClass "disabled"
			else
				nav.prev.removeClass "disabled"
			if totalPages is 0 or totalPages is currentPage
				nav.next.addClass "disabled"
			else
				nav.next.removeClass "disabled"
		_bindEvents: ()->
			_this= @ 
			nav= @nav
			nav.next.on "click",(e)->
				e.preventDefault()
				$this= $(this)
				if !$this.hasClass "disabled"
					_this.page= _this.page+1
					_this.effectNext= true
					_this._preDispatch()
			nav.prev.on "click",(e)->
				e.preventDefault()
				$this= $(this)
				if !$this.hasClass "disabled"
					_this.page= _this.page-1
					_this.effectNext= false
					_this._preDispatch()
		#Public Methods
		changueCategory: (category)->
			@currentCategory= category
			data= @data[category]
			@page= 1
			if data.length>0
				@_preDispatch()
		searchTitle: (search,category,callback,noResult)->
			settings= @settings
			cat= if typeof category isnt "undefined" then category else settings.categoryAll
			data= @data[cat]
			eReg= new RegExp(search, "i")
			rspta= _.filter data,(json)->
				eReg.test(json.title)
			if rspta.length>0
				@data["search"]= rspta
				@currentCategory= "search"
				@page= 1
				@totalPage["search"]= caldivExc rspta.length,@paginate
				callback and callback.call(this)
			else
				noResult and noResult(search)
	$.fn.SlidCss = (methods) ->
		if typeof methods is "undefined" or methods.constructor is Object
			new SlidCss(this, methods)
		else if typeof methods isnt "undefined" and methods.constructor is String
			SlidCss[methods].apply this, Array::slice.call(arguments_, 1)
			return
		else
			$.error "El parametro proporcionado " + method + " esta mal declarado o no es un objeto"
			return
	return
) jQuery,_