(($) ->
	class jqCount
		constructor: (that,options)->
			opt=
				class: "inpt-count"								#Clases de contenedor del input
				classBtn: "btn-counts"							#Clases de btn
				btnUp: "btn-up"									#Clase del boton aumentar
				btnDown: "btn-down"								#Clases del boton disminuir
				changued: null									#Evento al realizar un cambio
			@settings= $.extend opt, options
			@el= that
			@_init()
		_init: ()->
			@_construct()
		_construct: ()->
			_this= @
			settings= @settings
			content= null
			element= null
			valElement= ""
			buttonUp= null
			buttonDown= null
			@el.each (index,value)->
				element= $(value)
				valElement= element.val()
				content= $ "<div />",
					"class": settings.class
				buttonUp= $ "<button />",
					"class": settings.classBtn+" "+settings.btnUp
					"type": "button"
				buttonDown= $ "<button />",
					"class": settings.classBtn+" "+settings.btnDown
					"type": "button"
				element.parent().append content
				element.appendTo(content)
				if valElement is "1"
					buttonDown.addClass "disabled"
				content.append(buttonUp).append(buttonDown)
				_this._animate.call element,buttonUp, buttonDown,settings.changued
		_animate: (btnUp,btnDown,callback)->
			THIS= this
			btnUp.on "click",(e)->
				e.preventDefault()
				$this= $(this)
				valInpt= parseFloat THIS.val()
				THIS.val ++valInpt
				btnDown.removeClass "disabled"
				callback and callback.call THIS,valInpt
			btnDown.on "click",(e)->
				e.preventDefault()
				$this= $(this)
				if !$this.hasClass "disabled"
					valInpt= parseFloat THIS.val()
					if --valInpt <= 1
						$this.addClass "disabled"
					THIS.val valInpt
					callback and callback.call THIS,valInpt
			THIS.on "keyup",()->
				valInpt= THIS.val()
				if valInpt is "" or valInpt is "0" or valInpt is "1"
					btnDown.addClass "disabled"
				else
					btnDown.removeClass "disabled"
	$.fn.jqCount = (methods) ->
		if typeof methods is "undefined" or methods.constructor is Object
			new jqCount(this, methods)
		else if typeof methods isnt "undefined" and methods.constructor is String
			jqCount[methods].apply this, Array::slice.call(arguments_, 1)
			return
		else
			$.error "El parametro proporcionado " + method + " esta mal declarado o no es un objeto"
			return
	return
) jQuery