(($) ->
	htm= $("html")[0]
	win= $(window)
	class jqNavigate
		constructor: (that,options)->
			opt=
				pages: ".slid-page"				#Slides de paginas
				nav: ".slid-nav"				#Menu de navegación
				targetNav: "href"				#Target donde se extraera el id para ubicar la pagina a navegar
				widthNav: "auto"				#Ancho que va a tener el slider, si se deja en auto se calculará automaticamente
				heightNav: 200					#Alto del slider contenedor
				wpage: 996					    #Ancho de la página
				easing: "easeInOutCubic"		#Tipo de animación
				duration: 2000					#Tiempo de duración de la animación
				loadSlide: null					#Callback luego de finalizar el armado del slide
				resizeSlide: null				#Callback cada vez que se realiza el resize del slide
				beforeSlide: null				#Antes del Slide
				afterSlide: null				#Despues del Slide
				beforeHash: null				#Antes del Evento Hash
				afterHash: null					#Despues del Evento Hash	
			@settings= $.extend opt, options
			@mainWidth= if @settings.widthNav is "auto" then htm.clientWidth else @settings.widthNav
			@el= that
			@arquitect= {}
			@resize= htm.clientWidth
			@animate= true
			@currentPage= window.location.hash
			@_init()
		_init: ()->
			@_construct()
			@_chargue()
			@_resize()
			@_bindEvents()
			@settings.loadSlide and @settings.loadSlide(@mainWidth,@arquitect.countPages)
		_construct: ()->						#Construye y Storiza los elementos a animar
			settings= @settings
			_this= @
			ctnSlide= null
			idPage= null
			page= null
			@arquitect.pages= $(settings.pages,@el)
			@arquitect.nav= $(settings.nav)
			@arquitect.countPages= @arquitect.pages.length
			@arquitect.container= $("<div />",
				"class": "cont-slideNav"
				"css":
					"width": @mainWidth*@arquitect.countPages
					"height": settings.heightNav
					"position": "relative"
			)
			@arquitect.slides= {}
			$.each @arquitect.pages, (index,value)->
				page= $(value)
				idPage= page.attr("data-id")
				page.removeAttr("data-id")
				ctnSlide= $("<div />",
					"class":"slide-Nav"
					"id": idPage
					"css":
						"height": settings.heightNav
						"float": "left"
						"width": _this.mainWidth
						"position": "relative"
				)
				ctnSlide.appendTo _this.arquitect.container
				page.css(
					position: "absolute"
					left: ((_this.mainWidth-settings.wpage)/2)+"px"
				).appendTo ctnSlide
				_this.arquitect.slides["#"+idPage]= 
					"el": ctnSlide
					"order": index
			@el.prepend @arquitect.container
		_resize: ()->
			_this= @
			settings= @settings
			arquitect= @arquitect
			winWidth= 0
			leftSlide= 0
			leftContent= 0
			win.on "resize", ()->
				winWidth= htm.clientWidth
				winCurrent= if winWidth >= settings.wpage then winWidth else settings.wpage
				if winCurrent isnt _this.resize
					leftSlide= if winCurrent isnt settings.wpage then ((winCurrent-settings.wpage)/2) else 0
					currentPage= _this.currentPage
					leftContent= arquitect.slides[currentPage]["order"]*winCurrent
					_this.resize= winCurrent
					arquitect.container.css
						"width": winCurrent*arquitect.countPages
						"left": "-"+leftContent+"px"
					arquitect.container.find(".slide-Nav").css "width",winCurrent
					settings.resizeSlide and settings.resizeSlide(winCurrent,arquitect.countPages)
					arquitect.pages.stop().animate
						left: leftSlide
					, 400, "easeInCubic"
		_bindEvents: ()->
			_this= @
			settings= @settings
			arquitect= @arquitect
			targetPage= null
			nav= null
			posPage= null
			idTarget= null
			arquitect.nav.on "click",(e)->
				e.preventDefault()
				idTarget= $(this).attr(settings.targetNav)
				targetPage= arquitect.slides[idTarget]["el"]
				if typeof targetPage isnt "undefined" and _this.animate and not $(this).hasClass "active"
					nav= $(this)
					_this.animate= false
					posPage= targetPage.position()
					_this.currentPage= idTarget
					_this._animate nav,posPage.left,idTarget
		_chargue: ()->
			_this= @
			settings= @settings
			arquitect= @arquitect
			hash= @currentPage
			targetPage= null
			posPage= null
			firstNav= null
			if hash isnt ""
				@currentPage= hash
				targetPage= arquitect.slides[hash]["el"]
				if typeof targetPage isnt "undefined" and _this.animate
					_this.animate= false
					posPage= targetPage.position()
					_this._animate $(settings.nav+"[href='"+hash+"']"),posPage.left,hash,false
			else
				firstNav= arquitect.nav.eq(0)
				@currentPage= firstNav.attr settings.targetNav
				window.location.hash= @currentPage
				firstNav.addClass "active"
		_animate: (lnk,slideTo,hash,cond)->
			_this= @
			settings= @settings
			arquitect= @arquitect
			arquitect.nav.removeClass "active"
			if typeof cond is "undefined"
				settings.beforeSlide and settings.beforeSlide(slideTo)
				arquitect.container.animate
					left: "-"+slideTo
				, settings.duration, settings.easing, ()->
					window.location.hash= hash
					_this.animate = true
					lnk.addClass "active"
					settings.afterSlide and settings.afterSlide()
			else
				settings.beforeHash and settings.beforeHash(slideTo)
				arquitect.container.css("left","-"+slideTo+"px")
				window.location.hash= hash
				_this.animate = true
				lnk.addClass "active"
				settings.afterHash and settings.afterHash()
	$.fn.jqNavigate = (methods) ->
		if typeof methods is "undefined" or methods.constructor is Object
			new jqNavigate(this, methods)
		else if typeof methods isnt "undefined" and methods.constructor is String
			jqNavigate[methods].apply this, Array::slice.call(arguments_, 1)
			return
		else
			$.error "El parametro proporcionado " + method + " esta mal declarado o no es un objeto"
			return
	return
) jQuery