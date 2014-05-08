(($) ->
	class jqPlaceholder
		constructor: (options)->
			opt=
				inpt: ":input[placeholder]"				#Selector de placeholder
				ignore: "input[type=hidden]"			#Selector para ignorar
				classPlaceholder: "plc-label"			#Clase del placeholder
				charguePlaceholder: null				#Callback al cargar cada placeholder
			@settings= $.extend opt,options
			@test= document.createElement "input"
			@state= @test.placeholder isnt undefined
			@_init()
		_init: ()->
			@_dipatch()
		_dipatch: ()->
			_this= @
			settings= @settings
			state= @state
			@elements= $(settings.inpt).not(settings.ignore)
			cond= ""									#Condicional si el Elemento Placeholder sale visible o no
			element= null								#Instancia del Elemento
			place= null									#Elemento Placeholder
			paddEl= ""									#Padding del Elemento Placeholder
			posEl= null									#Posicion del Elemento Placeholder
			cssEl= null									#Css de Elemento Placeholder
			parentEl= null								#Padre del Elemento Input
			propEl= null								#Ancho y Alto del Elemento
			if !state
				@elements.each( (index,value)->
					element= $(value)
					cond= if element.val() is "" then "block" else "none"
					parentEl= element.parent()
					parentEl.css "position","relative"
					paddEl= _this._getPadding(element)
					posEl= _this._getPosition(element)
					propEl= _this._getProportions(element)
					place= $ "<label />",
						"html": element.attr "placeholder"
						"class": settings.classPlaceholder
						"display": cond
					cssEl=
						"position": "absolute"
						"z-index": "99"
					cssEl= $.extend cssEl,posEl,paddEl,propEl
					place.css cssEl
					_this._bindEvents.call place,element,_this
					parentEl.append place
					settings.charguePlaceholder and settings.charguePlaceholder(element,place)
				)
		_bindEvents: (element,inst)->
			el= @
			settings= inst.settings
			el.on "click",(e)->
				e.preventDefault()
				el.hide()
				element.focus()
			element.on "blur",(e)->
				if element.val() is ""
					el.show()
		_getPosition: (ele)->
			mTop= parseFloat ele.css("margin-top").replace("px","")
			mLeft= parseFloat ele.css("margin-left").replace("px","")
			posEl= ele.position()
			mTop= posEl.top+mTop
			mLeft= posEl.left+mLeft
			"left": mLeft
			"top": mTop
		_getPadding: (ele)->
			pTop= ele.css "padding-top"
			pLeft= ele.css "padding-left"
			pBottom= ele.css "padding-bottom"
			pRight= ele.css "padding-right"
			"padding-top": pTop
			"padding-left": pLeft
			"padding-bottom": pBottom
			"padding-right": pRight
		_getProportions: (ele)->
			wdth= ele.width()
			hght= ele.height()
			"width": wdth
			"height": hght
	$.extend
		jqPlaceholder: (json) ->
			new jqPlaceholder(json)
			return
	return
) jQuery