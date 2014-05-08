#-----------------------------------------------------------------------------------------------
 # @Module: Main Navigate
 # @Description: Modulo de navegación principal del menu
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "mainNavigate", ((Sb) ->
	st=
		slide: "#main-slider"
		decorMain: ".decor-top, .decor-bottom"
		decorSecond: ".ctn-bgfx"
	durationSlide= 1600
	animateSlide= "easeInOutCubic"
	dom= {}
	catchDom= ()->
		dom.slide= $(st.slide)
		dom.decorMain= $(st.decorMain)
		dom.decorSecond= $(st.decorSecond)
	bindEvents= ()->
		dom.slide.jqNavigate
			pages: ".slide-main"
			nav: ".main-snav"
			heightNav: 414
			wpage: 856
			duration: durationSlide
			easing: animateSlide
			loadSlide: (widthSlide,slides)->
				dom.decorMain.css "width", widthSlide*slides
				dom.decorSecond.css "width", widthSlide*slides
			resizeSlide: (widthSlide,slides)->
				dom.decorMain.css "width", widthSlide*slides
				dom.decorSecond.css "width", widthSlide*slides
			beforeSlide: (slideTo)->
				dom.decorMain.animate
					left: "-"+slideTo/8+"px"
				, durationSlide, animateSlide
				dom.decorSecond.animate
					left: "-"+slideTo/4+"px"
				, durationSlide, animateSlide
			beforeHash: (slideTo)->
				dom.decorMain.css "left","-"+slideTo/8+"px"
				dom.decorSecond.css "left","-"+slideTo/4+"px"
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqNavigate.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Navigate Products
 # @Description: Modulo de navegación de productos
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "navigateProducts", ((Sb) ->
	st=
		slide: ".slid-product"
		navsCat: ".scateg-list a"
		listCat: ".scateg-list li"
		titCat: ".scateg-tit"
	dom= {}
	instSlidCSS= null
	catchDom= ()->
		dom.slide= $(st.slide)
		dom.navsCat= $(st.navsCat)
		dom.listCat= $(st.listCat)
		dom.titCat= $(st.titCat)
	bindEvents= ()->
		defaultCat= dom.navsCat.eq(0).attr "id"
		instSlidCSS= dom.slide.SlidCss
			classContent: "ctn-product"
			addContent: false
			next: ".btnav.next"
			prev: ".btnav.prev"
			defaultImg: yOSON.baseHost+'static/img/no-disponible.png'
			source: yOSON.dataProduct
			category: defaultCat
			template: "#tplProduct"
			paginate: 8
			addedElements: setCookie
	readCookie= ()->
		cookie= Cookie.read "cookieProduct"
		category= ""
		if cookie isnt null
			jsonCookie= (new Function('return '+cookie))()
			Cookie.del "cookieProduct"
			if jsonCookie.category
				category= ""+jsonCookie.category
				category= if category.length is 1 then "#0"+category else "#"+category
				if $(category).length>0
					$(category).trigger "click"
				else
					activeCatDef()
		else
			activeCatDef()
	navCategory= ()->
		category= ""
		$this= null
		valSearch= ""
		dom.navsCat.on "click", (e)->
			e.preventDefault()
			$this= $(this)
			category= $this.attr("id")
			if typeof($this.data("state")) is "undefined" or !$this.data("state")
				dom.navsCat.data "state",false
				$this.data("state",true)
				instSlidCSS.changueCategory category
	setCookie= (arrEl,page)->
		$.each arrEl,(index,element)->
			element.find("a").on "click",(e)->
				e.preventDefault()
				category= dom.listCat.find("a.active").attr("id")
				Cookie.create "cookieProduct","{category:"+category+",page:"+page+"}"
				location.href= $(this).attr "href"
	activeCatDef= ()->
		defaultCat= dom.navsCat.eq(0)
		color= defaultCat.attr "data-color"
		defaultCat.data "state",true
		dom.titCat.css "background-color",color
		defaultCat.addClass "active"
		defaultCat.css "color",color
	init: (oParams) ->
		catchDom()
		bindEvents()
		navCategory()
		readCookie()
), ["libs/plugins/jqUnderscore.js","libs/plugins/jqSlidCss.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: fancyCategory
 # @Description: Modulo mejorar visualmente el listado de catogorías
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "fancyCategory", ((Sb) ->
	st=
		listLnk: ".scateg-list li > a"
		titCat: ".scateg-tit"
	dom= {}
	catchDom= ()->
		dom.listLnk= $(st.listLnk)
		dom.titCat= $(st.titCat)
	utils=
		"setIds": ()->
			dom.listLnk.each (index,value)->
				$(value).attr "id",$(value).attr("data-id")
		"hoverEffect": ()->
			dom.listLnk.on "mouseenter",()->
				$this= $(this)
				color= $this.attr "data-color"
				$this.css "color",color
			dom.listLnk.on "mouseleave",()->
				$this= $(this)
				if !$this.hasClass "active"
					$this.css "color","#A0A0A0"
		"activeEffect": ()->
			dom.listLnk.on "click",()->
				$this= $(this)
				color= $this.attr "data-color"
				dom.listLnk.css "color","#A0A0A0"
				dom.listLnk.removeClass "active"
				dom.titCat.css "background-color",color
				$this.addClass "active"
				$this.css "color",color
		"readCookie": ()->
			cookie= Cookie.read "cookieProduct"
			category= ""
			if cookie isnt null
				jsonCookie= (new Function('return '+cookie))()
				if jsonCookie.category
					category= ""+jsonCookie.category
					category= if category.length is 1 then "#0"+category else "#"+category
					$(category).trigger "click"
	init: (oParams) ->
		catchDom()
		for fn in oParams
			utils[fn] and utils[fn]()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Select Category
 # @Description: Modulo para seleccionar categorias en listado de lineas
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "selectCategory", ((Sb) ->
	bindEvents= (json)->
		$(json.nav).on "click",(e)->
			e.preventDefault()
			Cookie.create "cookieProduct","{category:"+$(this).attr("data-id")+"}"
			location.href= if typeof json.href isnt "undefined" then json.href else $(this).attr "data-href"
	init: (oParams) ->
		bindEvents(oParams)
)
#-----------------------------------------------------------------------------------------------
 # @Module: Change Tabs
 # @Description: Modulo para cambiar tabs
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "changeTabs", ((Sb) ->
	st=
		lnks: ".lnk-tab"
		tabs: ".ctn-tab"
	dom= {}
	catchDom= ()->
		dom.lnks= $(st.lnks)
		dom.tabs= $(st.tabs)
	bindEvents= ()->
		targetId= ""
		dom.lnks.on "click",(e)->
			dom.tabs.hide()
			targetId= "#"+$(this).attr "data-id"
			$(targetId).fadeIn(1000)
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Add Cart
 # @Description: Modulo que se utiliza para agregar un producto al carrito
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addCart", ((Sb) ->
	st=
		btn: ".bntAddCar"
		counter: ".count-product"
		targetAnim: ".trgcart"
	dom= {}
	body= $("body")
	catchDom= (json)->
		dom.btn= $(st.btn)
		dom.counter= $(st.counter)
		dom.targetAnim= $(st.targetAnim)
		dom.wrapProd= $(json.wrapProd)
	bindEvents= ()->
		dom.btn.on "click",(e)->
			e.preventDefault()
			$this= $(this)
			utils.loader(dom.wrapProd,true)
			idProduct= if typeof yOSON.idProduct isnt "undefined" then yOSON.idProduct else $this.attr "data-id"
			imgProduct= $this.parent().parent().find "img"
			$.ajax
				url: yOSON.urlCart
				data:
					"idproduct": idProduct
				dataType: "JSON"
				method: "POST"
				success: (json)->
					if json.state is 1
						utils.loader(dom.wrapProd,false)
						animateProduct imgProduct,()->
							dom.counter.html json.totalItems
							dom.counter.fadeIn 600
					else
						utils.loader(dom.wrapProd,false)
						echo json.msg
	animateProduct= (imgProduct,callback)->
		cloneImg= imgProduct.clone()
		posImg= imgProduct.offset()
		cloneImg.css $.extend(
			"position": "absolute"
			"width": "182px"
			"height": "274px"
			"z-index": "9999"
			"opacity": "0.6"
		,
			posImg
		)
		body.append cloneImg
		opts= optAnimate()
		cloneImg.animate opts, 1200, ()->
			cloneImg.fadeOut 400,()->
				cloneImg.remove()
				callback&&callback()
	optAnimate= (posTarget)->
		wTarget= dom.targetAnim.outerWidth()/2
		hTarget= dom.targetAnim.outerHeight()/2
		posTarget= dom.targetAnim.offset()
		topTarget= posTarget.top+hTarget-75
		leftTarget= posTarget.left+wTarget-25
		opt=
			"width": "50px"
			"height": "75px"
			"left": leftTarget
			"top": topTarget
		$.extend posTarget,opt
	init: (oParams) ->
		catchDom(oParams)
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Delete Cart
 # @Description: Modulo para borrar productos del carrito
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "deleteCar", ((Sb) ->
	st=
		btn: ".tdAction .delete"
		counter: ".count-product"
		ctn: ".ctn-step1"
	dom= {}
	catchDom= ()->
		dom.btn= $(st.btn)
		dom.counter= $(st.counter)
		dom.ctn= $(st.ctn)
	bindEvents= ()->
		$this= null
		parentTr= null
		hash= ""
		idProduct= ""
		dom.btn.on "click",(e)->
			e.preventDefault()
			$this= $(this)
			parentTr= $this.parents "tr"
			idProduct= $this.attr "data-id"
			hash= utils.loader parentTr,true,true
			$.ajax
				url: "/cart/ajax-remove-product"
				data:
					idproduct: idProduct
				success: (json)->
					if json.state is 1
						utils.loader $("#"+hash),false,true
						parentTr.fadeOut 600,()->
							$(this).remove()
							Sb.trigger "totalStep1"
							Sb.trigger "instTinyScroll",[(instTiny)->
								instTiny.tinyscrollbar_update()
							]
							if json.totalItems is 0
								dom.ctn.html "<p class='emptyCar'>El carrito esta vacío, <a href='/#product'>haga click aquí</a> para iniciar su compra. Buena Suerte!</p>"
						counterCar(json.totalItems)
					else
						echo json.msg
	counterCar= (items)->
		if items>0
			dom.counter.html(items).fadeIn 600
		else
			dom.counter.fadeOut 600
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Calculate Step 1
 # @Description: Modulo de calculo de totales - Paso 1
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calcStep1", ((Sb) ->
	st=
		inpt: ".inptcant"
		preTotal: ".tbl-detail .tdTotal"
		subTotal: ".tdsubTotal"
		IGV: ".tdIgv"
		iTotal: ".tdITotal"
	dom= {}
	timeOut= null
	catchDom= ()->
		dom.inpt= $(st.inpt)
		dom.subTotal= $(st.subTotal)
		dom.IGV= $(st.IGV)
		dom.iTotal= $(st.iTotal)
	bindEvents= ()->
		dom.inpt.numeric
			decimal: false
			negative: false
		dom.inpt.jqCount
			changued: (cant)->
				priceInpt= parseFloat this.attr("data-price")
				target= this.parents("tr").find ".tdTotal"
				calcpreTotal priceInpt,cant,target
		valInpt= ""
		cantInpt= 0
		target= null
		priceInpt= 0
		utils.vLength dom.inpt,4
		dom.inpt.on "keypress", (e)->
			return e.which isnt 13
		dom.inpt.on "keyup",()->
			$this= $(this)
			if timeOut isnt null then clearTimeout timeOut
			valInpt= $this.val()
			target= $this.parents("tr").find ".tdTotal"
			priceInpt= parseFloat $this.attr("data-price")
			if valInpt isnt "" and valInpt isnt "0"
				cantInpt= parseFloat valInpt
			else
				if valInpt is "0"
					$this.val "1"
				else
					timeOut= setTimeout ()->
						$this.val "1"
						$this.trigger "keyup"						
					, 500
				cantInpt= 1
			calcpreTotal(priceInpt,cantInpt,target)
	calcpreTotal= (price,cant,target)->
		total= (price*100*cant)/100
		target.html yOSON.monSymbol+" "+total.toFixed(2)
		calcTotal()
	calcTotal= ()->
		price= 0
		pSubTotal= 0
		$(st.preTotal).each (index,value)->
			price= parseFloat $(value).text().replace(yOSON.monSymbol+" ","")
			pSubTotal= pSubTotal+price
		igv= (pSubTotal*100*yOSON.iva)/100
		pTotal= pSubTotal+igv
		dom.subTotal.html yOSON.monSymbol+" "+pSubTotal.toFixed(2)
		dom.IGV.html yOSON.monSymbol+" "+igv.toFixed(2)
		dom.iTotal.html yOSON.monSymbol+" "+pTotal.toFixed(2)
	Sb.events ["totalStep1"], ()->
		calcTotal()
	,this
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqCount.js","libs/plugins/jqNumeric.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Add Address
 # @Description: Modulo de agregar direccion
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addAddress", ((Sb) ->
	st=
		btn: ".btnAddress"
		target: ".ctn-address"
		ctrl: ".ctrl-address"
		flagInpt: "#newAddress"
	dom= {}
	catchDom= ()->
		dom.btn= $(st.btn)
		dom.target= $(st.target)
		dom.ctrl= $(st.ctrl)
		dom.flagInpt= $(st.flagInpt)
	bindEvents= ()->
		$this= null
		flag= true
		dom.btn.on "click",(e)->
			e.preventDefault()
			$this= $(this)
			if flag
				flag= false
				if !$this.hasClass "act"
					$this.addClass "act"
					dom.flagInpt.val "1"
					utils.block dom.ctrl,true
					dom.target.fadeIn 600,()->
						flag= true
						$this.html "Cancelar"
				else
					$this.removeClass "act"
					dom.flagInpt.val "0"
					dom.target.fadeOut 600,()->
						utils.block dom.ctrl,false
						flag= true
						$this.html "Nueva dirección"
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Select Depends
 # @Description: Modulo de selects dependientes
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "select-depends", ((Sb) ->
	setDefaultValues= (selector)->
		deps= treeDeps[selector]
		i= 0
		while i<deps.length
			$(deps[i]).html('').append "<option value=''>"+dValues[deps[i]]+"</option>"
			i++
	setUrl= (url,pars)->
		url.replace /\$([0-9]+)/gi, (res,match)->
			return pars[parseInt(match)]
	ajaxReq= (urlAjax,callback)->
		$.ajax
			url: urlAjax
			type: "GET"
			dataType: "JSON"
			success: callback
	blockInputs= (collection)->
		collection.each (index,value)->
			utils.block $(value).parent(),true
	treeDeps= {}										#Arbol de Dependencias
	dValues= {}											#Arbol de Valores por Default
	init: (oParams) ->
		THIS= this
		Params= selectDepends[oParams.select]
		l= Params.length
		i= 0
		j= 0
		instMain= null									#Instancia del selector independiente
		instDep= null									#Instancia del selector dependiente
		selMain= ""										#Selector independiente
		selDep= ""										#Selector dependiente
		selNextMain= ""									#Proximo selector dependiente
		selNextDep= ""									#Proximo selector independiente
		lastSelDep= ""									#Ultimo selector dependiente utilizado
		instObjDeps= null								#Instancia de objetos dependientes
		urlAjax= ""										#Url del ajax
		while i<l
			selMain= Params[i].ids[0]
			selDep= Params[i].ids[1]
			instMain= $ selMain
			instDep= $ selDep
			treeDeps[selMain]=[selDep]
			dValues[selDep]= Params[i].valueDefault
			j= i+1
			while j<l
				selNextMain= Params[j].ids[0]
				selNextDep= Params[j].ids[1]
				lastSelDep= treeDeps[selMain][treeDeps[selMain].length-1]
				if selNextMain is lastSelDep then treeDeps[selMain].push selNextDep
				j++
			instMain.on "change",((i,instDep,selDep)->
				()->
					valSel= ""+$.trim $("option:selected",this).val()
					instObjDeps= $ treeDeps["#"+this.id].join(',')
					blockInputs instObjDeps
					setDefaultValues("#"+this.id)
					if valSel isnt ""
						urlAjax= setUrl Params[i].url, [valSel]
						ajaxReq urlAjax,(json)->
							THIS.dispatch instDep,selDep,json
			)(i++,instDep,selDep)
	dispatch: (inst,selector,json)->
		if json.state is 1
			$.each json.data,(index,obj)->
				inst.append "<option value='"+obj.id+"'>"+obj.value+"</option>"
			utils.block inst.parent(),false
			inst.prop 'disabled',false
		else
			echo json.msg
), ["data/selects.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Validate Form
 # @Description: Validacion de formularios
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "validation", ((Sb) ->
	init: (oParams) ->
		forms= oParams.form.split(",")
		$.each forms,(index,value)->
			settings= {}
			value= $.trim value
			for prop of yOSON.require[value]
				settings[prop]= yOSON.require[value][prop]
			$(value).validate settings
), ["data/require.js","libs/plugins/jqValidate.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Datepicker
 # @Description: Modulo de datepicker
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "datepicker", ((Sb) ->
	st=
		inpt: ".datepicker"
	dom= {}
	catchDom= ()->
		dom.inpt= $(st.inpt)
	bindEvents= ()->
		dom.inpt.attr "readonly",true
		dom.inpt.datepicker
			yearRange: "-80:c"
			maxDate: 0
			changeMonth: true
			changeYear: true
			dateFormat: "dd/mm/yy"
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqUI.js","libs/plugins/jqDatepicker.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: RefreshCaptcha
 # @Description: Modulo de refresh captcha
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "refreshCaptcha", ((Sb) ->
	st=
		btn: ".btn-refresh",
		ctnRefresh: ".ctn-captcha"
		inptCap: "input[name='captcha[input]']"
		inptCaptcha: "input[name='captcha[id]']"
		imgCaptcha: ".ctn-captcha img"
	dom= {}
	catchDom= ()->
		dom.btn= $(st.btn)
		dom.ctnRefresh= $(st.ctnRefresh)
		dom.inptCaptcha= $(st.inptCaptcha)
		dom.imgCaptcha= $(st.imgCaptcha)
	bindEvents= ()->
		dom.btn.on "click",(e)->
			e.preventDefault()
			utils.loader dom.ctnRefresh,true
			$.ajax
				url: "/cart/update-captcha"
				dataType: "JSON"
				method: "POST"
				success: (json)->
					dom.inptCaptcha.val json.id
					dom.imgCaptcha.attr "src",json.src
					utils.loader dom.ctnRefresh,false
		utils.vLength st.inptCap,4
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: selectShow
 # @Description: Modulo para mostrar controles segun la opcion del select
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "selectShow", ((Sb) ->
	st=
		select: "#paymethod"
		targets: ".selChangue"
	dom= {}
	catchDom= ()->
		dom.select= $(st.select)
		dom.targets= $(st.targets)
	bindEvents= ()->
		valSel= ""
		target= null
		dom.select.on "change",()->
			valSel= $(this).val()
			target= $ "#"+valSel
			if $(st.targets+":visible").length>0
				$(st.targets+":visible").slideUp 600, ()->
					target.slideDown 600
			else
				target.slideDown 600
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Slider Js
 # @Description: Modulo para implementar sliders
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "sliderJs", ((Sb) ->
	bindEvents= (slides)->
		for inst,opt of slides
			$(inst).tinycarousel opt
	init: (oParams) ->
		bindEvents(oParams.slides)
), ["libs/plugins/jqTinycarousel.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Numeric
 # @Description: Modulo que valida solo números
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "numeric", ((Sb) ->
	st=
		inpt: ".numeric"
	dom= {}
	catchDom= ()->
		dom.inpt= $(st.inpt)
	bindEvents= ()->
		dom.inpt.numeric
			decimal: false
			negative: false
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqNumeric.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Scroll Custom
 # @Description: Modulo que valida solo números
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "scrollCustom", ((Sb) ->
	_this= @
	_this.inst= null
	Sb.events ["instTinyScroll"],(fn)->
		fn and fn(_this.inst)
	,this
	bindEvents= (scroll)->
		_this.inst= $(scroll).tinyscrollbar()
	checkScroll= (scroll,hMax)->
		if $(scroll).find(".overview").height()<=hMax
			$(scroll).addClass "nscroll"
		else
			bindEvents(scroll)
	init: (oParams) ->
		if typeof oParams.height isnt "undefined"
			checkScroll(oParams.scroll,oParams.height)
		else
			bindEvents(oParams.scroll)

), ["libs/plugins/jqTinyscrollbar.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: nav Oportunity
 # @Description: Modulo de la navegacion de oportunidades
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "navOportunity", ((Sb) ->
	st=
		lnkNavs: ".oporLnk, .map-typOporty area, .oporBack"
		navs: ".oporNav"
	dom= {}
	flagAnim= true
	catchDom= ()->
		dom.lnkNavs= $(st.lnkNavs)
		dom.navs= $(st.navs)
	bindEvents= ()->
		targetNav= null
		currentNav= null
		dom.lnkNavs.on "click",(e)->
			e.preventDefault()
			$this= $(this)
			if flagAnim
				flagAnim= false
				targetNav= $ $this.attr("rel")
				currentNav= $this.parents(".oporNav")
				targetNav.css "z-index","5"
				currentNav.fadeOut 1200,()->
					dom.navs.not(currentNav).css "z-index","1"
					targetNav.css "z-index","10"
					currentNav.css
						"z-index": "1"
						"display": "block"
					flagAnim= true
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Nav Footer
 # @Description: Modulo para posicionar el footer
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "nav-footer", ((Sb) ->
	st=
		ctnNav: ".main-menu"
		nav: ".main-menu ul"
	dom= {}
	wdth= 0													#Width Menu Footer
	cWdth= 0												#Width del Menu Contenedor
	leftNav= 0												#Left del Nav
	catchDom= ()->
		dom.ctnNav= $(st.ctnNav)
		dom.nav= $(st.nav)
	bindEvents= ()->
		$(window).on "resize", ()->
			wdth= dom.nav.width()
			cWdth= dom.ctnNav.width()
			dispatchNav()
	dispatchNav= ()->
		if cWdth >= wdth
			leftNav= (cWdth-wdth)/2
			dom.nav.css "left",leftNav
		else
			dom.nav.css "left",0
	init: (oParams) ->
		catchDom()
		wdth= dom.nav.width()
		cWdth= dom.ctnNav.width()
		dispatchNav()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Alignet
 # @Description: Modulo para integra alignet
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "alignet", ((Sb) ->
	st=
		ctn: ".slide-main"
		frmAlignet: "#Alignet"
		ifrmAlignet: "#iframevpos"
	dom= {}
	catchDom= ()->
		dom.ctn= $(st.ctn)
		dom.frmAlignet= $(st.frmAlignet)
		dom.ifrmAlignet= $(st.ifrmAlignet)
	bindEvents= ()->
		browser= yOSON.browser
		dom.ifrmAlignet.on "load",()->
			response= if browser.msie and parseInt(browser.version.substr(0,1)) <= 8 then window.frames["ifrmAlignet"].document.body.innerHTML else dom.ifrmAlignet[0].contentDocument.body.innerHTML
			if response isnt "false"
				utils.loader dom.ctn,false
				dom.ifrmAlignet.fadeIn(600)
	init: (oParams) ->
		catchDom()
		#utils.loader dom.ctn,true
		dom.frmAlignet.submit()
		#bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Placeholder
 # @Description: Modulo hacer compatible placeholder
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "placeholder", ((Sb) ->
	init: (oParams) ->
		$.jqPlaceholder
			charguePlaceholder: (element,placeholder)->
				placeholder.on "mouseenter",()->
					element.trigger "mouseenter"
				placeholder.on "mouseleave",()->
					element.trigger "mouseleave"
), ["libs/plugins/jqPlaceholder.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Error Images
# @Description: Modulo para colocar imagenes por default para las imagenes
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "error-images", ((Sb) ->
	st=
		img: "img"
	dom= {}
	catchDom= ()->
		dom.img= $(st.img)
	bindEvents= ()->
		dom.img.on "error",()->
			$(this).attr "src",yOSON.baseHost+'static/img/no-disponible.png'
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Alerts
 # @Description: Modulo para remover alerts
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "alerts", ((Sb) ->
	st=
		alert: ".alert"
	dom= {}
	catchDom= ()->
		dom.alert= $(st.alert)
	bindEvents= ()->
		setTimeout ()->
			dom.alert.slideUp 600
		,5000
	init: (oParams) ->
		catchDom()
		bindEvents()
)