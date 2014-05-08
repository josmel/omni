#-----------------------------------------------------------------------------------------------
 # @Module: Fuxion quiz
 # @Description: Modulo para mostrar respuestas
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "fuxionQuiz", ((Sb) ->
	st=
		btn: ".btn-quiz"
		radio: ".quiz-lst input[name='radquiz']"
		ctnQtyuiz: ".quiz-well"
		lblquiz: ".quiz-well .ioption"
	dom= {}
	catchDom= ()->
		dom.btn= $(st.btn)
		dom.radio= $(st.radio)
		dom.ctnQtyuiz= $(st.ctnQtyuiz)
		dom.lblquiz= $(st.lblquiz)
	bindEvents= ()->
		valRadio= ""
		dom.btn.on "click",(e)->
			e.preventDefault()
			if dom.radio.is(':checked')
				valRadio= $(st.radio+":checked").val()
				utils.loader dom.ctnQtyuiz,true
				$.ajax
					url: "/fuxion/list/"
					data:
						"idalternativa": valRadio
					method:"POST"
					success: (json)->
						utils.loader dom.ctnQtyuiz,false
						if json.state is 1
							animQuiz(json.DatosEncuesta)
						else
							echo json.msg
					error:() ->
						utils.loader dom.ctnQtyuiz,false
						echo "Ocurrió un error en el sistema, intente nuevamente"
			else
				echo "Debe seleccionar una respuesta de la encuesta para su envío"
	prepareLblQuiz= ()->
		dom.lblquiz.each (index,value)->
			$(value).data("data-order",index)
	animQuiz= (arr)->
		if arr.length > 0
			dom.radio.fadeOut 400,()->
				$(this).remove()
			dom.btn.fadeOut 400,()->
				$(this).remove()
			dom.lblquiz.animate(
				"paddingLeft":"50px"
			,400,()->
				$this= $(this)
				order= $this.data "data-order"
				$this.addClass "rquiz"
				json= arr[order]
				cantidad= if json.cantidad isnt null then json.cantidad else 0
				votes= $ "<span />",
					"html": " &nbsp;("+cantidad+" votos)"
					"style": "display:none"
				percent= $ "<strong />",
					"html": json.porcentaje
					"style": "display:none"
				$this.append(votes).append(percent)
				votes.fadeIn 400
				percent.fadeIn 400
			)
	init: (oParams) ->
		catchDom()
		prepareLblQuiz()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
# @Module: Mostrar Carrito POP UP
# @Description: Modulo de Prueba
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "showCartPopUp", ((Sb) ->
	st=
		btn: ".cart-cont"
		cartCnt: ".cart-popUp"
		cartCntL: ".cart-popUp ul"
		total : ".header .info-cart h3 "
		scroll: ".cart-popUp ul"
	Sb.events ["mostrarCart"],()->
		showCart()
	,this
	dom= {}
	catchDom= ()->
		dom.btn= $(st.btn)
		dom.cartCnt= $(st.cartCnt)
		dom.cartCntL= $(st.cartCntL)
		dom.total= $(st.total)
		dom.scroll= $(st.scroll)
	showCart=()->
		if($(st.total).attr("data-total")!="0")
			dom.cartCnt.css
				height: "auto"
				opacity: 1
				transform: "scale(1)"
	hideCart=()->
		dom.cartCnt.css
			opacity: 0
			transform: "scale(0)"
	bindEvents= ()->
		dom.btn.on "mouseenter",()->
			showCart()
		dom.cartCnt.on "mouseleave",()->
			hideCart()
		dom.scroll.mCustomScrollbar()
	init: (oParams) ->
		if yOSON.controller isnt "cart" and yOSON.controller isnt "quick-cart"
			catchDom()
			bindEvents()
), ["libs/plugins/jqCustomScrollbar.js"]

#-----------------------------------------------------------------------------------------------
# @Module: Add Producto
# @Description: Agregar Producto al Carrito POP UP
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addProducto", ((Sb) ->
	st=
		btnAdd: ".btnAddProd"#
		prodCtn: ".prodContent"#
		prodImg: ".prodImg"#
		prodCant: ".prodCant"#
		tmpl: "#tplProduct"#
		cartCnt: ".cart-popUp"#
		scroller: ".cart-popUp ul"#
		listCnt: ".list-scar"#
		listCntDiv: ".list-scar .mCSB_container"#
		h3TotalProductos: "#menuQProd"#
		liCarrito: ".list-scar li"#
		btnDel: "#btnBorrarCarrito"
		totalPrecio : "#listProdTotal"#
	dom= {}
	GBody= $('body')
	currentItem= null
	catchDom= ()->
		dom.btnAdd= $(st.btnAdd)#
		dom.cartCnt= $(st.cartCnt)
		dom.listCnt= $(st.listCnt)#
		dom.listCntDiv= $(st.listCntDiv)#
		dom.scroller= $(st.scroller)#
		dom.h3TotalProductos = $(st.h3TotalProductos)#
		dom.liCarrito= $(st.liCarrito)#
		dom.totalPrecio= $(st.totalPrecio)#
		dom.tmpl= _.template $(st.tmpl).html()#
	updateScroll= ()->
		if(yOSON.totalCartItems==4)
			dom.listCnt.css('height','240px')
			dom.scroller.mCustomScrollbar("update")
	bindEvents= ()->
		$(st.cartCnt).on "click",".removeProduct",removeProduct
		dom.btnAdd.on "click",addProduct
	addProduct= ()->
		$this= $(this)
		prodCtn= $this.parents st.prodCtn
		animateImg(prodCtn)
		qty= calcCantProd(prodCtn)
		idProd = $this.attr "data-id"
		$.ajax "/cart/ajax-add-product",
			type: "POST"
			data:
				"idproduct": idProd
				"quantity": qty
			async: true
			success: (json) ->
				if json.state is 1
					cond= verifyProduct(idProd)
					dataProduct= json.productData
					if cond
						currentItem.find('h4.cantidad span').html(dataProduct.quantity)
						currentItem.find('input').val(dataProduct.quantity)
						currentItem.find('h4.precio span').html(yOSON.monSimbol+" "+dataProduct.price.toFixed(2))
					else
						dataProduct["id"]= idProd
						dataProduct["price"]= dataProduct["price"].toFixed(2)
						$(st.listCntDiv).append dom.tmpl(dataProduct)
						dom.scroller.mCustomScrollbar("update")
						dom.h3TotalProductos.html(json.totalItems)
					setTotalPrice(json.totalOrder)
					Sb.trigger "mostrarCart"
				else
					echo json.msg
			error: (json) ->
				echo "Ocurrió un error en el sistema, intente nuevamente."
	removeProduct= ()->
		idRemove= $(this).parent().attr('data-id')
		$.ajax "/cart/ajax-remove-product",
			type: "post"
			data:
				"idproduct": idRemove
			async: true
			success: (json) ->
				if json.state is 1
					location.reload()
				else
					echo json.msg
			error: () ->
				echo "Ocurrió un error en el sistema, intente nuevamente."
	animateImg= (ctnProd)->
		imgOrig= ctnProd.find(st.prodImg)
		nImg = imgOrig.clone()
		posImg= imgOrig.offset()
		posList= dom.listCnt.offset()
		nImg.addClass('imgCart')
		nImg.css
			'left':posImg.left
			'top':posImg.top
		GBody.append(nImg)
		nImg.animate
			'top':posList.top
			'left':posList.left
			'width':'50px'
			'height':'46px'
			'opacity':'0'
		,1000
	verifyProduct= (idProd)->
		cond= false
		$(st.liCarrito).each ()->
			$this= $(this)
			if $this.attr("data-id") is idProd
				cond= true
				currentItem=$this
		return cond
	calcCantProd= (ctnProd)->
		inptCant= ctnProd.find(st.prodCant)
		cant= inptCant.val()
		if cant is "" or cant is "0"
			inptCant.val "1"
			return 1
		else
			return parseFloat(cant)
	setTotalPrice= (total)->
		totalPrice= parseFloat total
		dom.totalPrecio.html yOSON.monSimbol+" "+totalPrice.toFixed(2)
	init: (oParams) ->
		catchDom()
		bindEvents()
		updateScroll()
), ["libs/plugins/jqUnderscore.js"]

#-----------------------------------------------------------------------------------------------
 # @Module: Order List Product
 # @Description: Modulo ordenar lista de productos
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "orderListProd", ((Sb) ->
	st=
		slct: ".head-lstore select"
	dom= {}
	catchDom= ()->
		dom.slct= $(st.slct)
	bindEvents= ()->
		$this= null
		urlSlct= ""
		valSlct= ""
		dom.slct.on "change",()->
			$this= $(this)
			valSlct= $this.find("option:selected").val()
			urlSlct= yOSON.urlOrderProducts.replace "__ORDER__",valSlct
			location.href= urlSlct
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Changue Product
 # @Description: Cambiar Tipo de vista de listado de productos
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "viewProd", ((Sb) ->
	st=
		prods: ".ctn-lstore .box"
		slct: ".typ-lstore a"
	dom= {}
	catchDom= ()->
		dom.slct= $(st.slct)
		dom.prods= $(st.prods)
	bindEvents= ()->
		$this= null
		dataSel= ""
		dom.slct.on "click",(e)->
			e.preventDefault()
			dom.slct.removeClass "active"
			$this= $(this)
			dataSel= $this.attr("data")
			if dataSel is "detail"
				dom.prods.addClass "lista"
			else
				dom.prods.removeClass "lista"
			$this.addClass "active"
			Cookie.create "viewProd",dataSel
	readCook= ()->
		valCook= Cookie.read "viewProd"
		if valCook isnt null
			$(st.slct+"[data='"+valCook+"']").trigger "click"
	init: (oParams) ->
		catchDom()
		bindEvents()
		readCook()
)
#-----------------------------------------------------------------------------------------------
 # @Module: sliderJs
 # @Description: Modulo para implementar sliders
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "sliderJs", ((Sb) ->
	st=
		slider: "#slider-prom"
	dom= {}
	catchDom= ()->
		dom.slider= $(st.slider)
	bindEvents= ()->
		dom.slider.tinycarousel
			interval: true
			intervaltime: 3500
			duration: 1600
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqTinycarousel.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Cycle Js
 # @Description: Modulo para implementar cyclejs
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "cycleJs", ((Sb) ->
	init: (oParams) ->
		$.each oParams,(index,json)->
			$(json.el).cycle json.opt
), ["libs/plugins/jqCycle.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Calculate Step 1
 # @Description: Modulo de calculo de totales - Paso 1
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calcStep1", ((Sb) ->
	st=
		inpt: ".inptcant"
		delProd: ".btn-close-red"
		totalPoints: "#totalPts"
		subTotal: "#subTotal"
		total: "#totalCart"
		discount: "#totalDis"
		ctnDiscount: "#percentDis"
		tblDiscount: "#tblDiscount"
		#pointsNext: "#pointsNext"
		#percentNext: "#percentNext"
		ctnNote: ".cart-note"
		ctnFullCart: ".fullCar"
		ctnEmptyCart: ".emptyCar"
		countProd: "#descQProd,#menuQProd"
	dom= {}
	timeOut= null
	catchDom= ()->
		dom.inpt= $(st.inpt)
		dom.delProd= $(st.delProd)
		dom.totalPoints= $(st.totalPoints)
		dom.subTotal= $(st.subTotal)
		dom.total= $(st.total)
		dom.discount= $(st.discount)
		dom.ctnDiscount= $(st.ctnDiscount)
		dom.tblDiscount= $(st.tblDiscount)
		#dom.pointsNext= $(st.pointsNext)
		#dom.percentNext= $(st.percentNext)
		dom.ctnNote= $(st.ctnNote)
		dom.ctnFullCart= $(st.ctnFullCart)
		dom.ctnEmptyCart= $(st.ctnEmptyCart)
		dom.countProd= $(st.countProd)
	bindEvents= ()->
		dom.inpt.numeric
			decimal: false
			negative: false
		dom.inpt.jqCount
			changued: (cant)->
				priceInpt= parseFloat this.attr("data-price")
				pointsInpt= parseFloat this.attr("data-points")
				parentInpt= this.parents("tr")
				calcStep(priceInpt,pointsInpt,cant,parentInpt)
		valInpt= ""														#Valor del input de items
		cantInpt= 0														#Cantidad de items
		parentInpt= null												#Padre del input del item
		targetSTotal= null												#Target Subtotal por item
		targetSPoints= null												#Target Subtotal de puntos
		priceInpt= 0													#Precio de cada item
		pointsInpt= 0													#Puntos de cada item

		utils.vLength dom.inpt,4
		dom.inpt.on "keypress", (e)->
			return e.which isnt 13
		dom.inpt.on "keyup",()->
			$this= $(this)
			if timeOut isnt null then clearTimeout timeOut
			valInpt= $this.val()
			parentInpt= $this.parents("tr")
			priceInpt= parseFloat $this.attr("data-price")
			pointsInpt= parseFloat $this.attr("data-points")
			if valInpt isnt "" and valInpt isnt "0"
				cantInpt= parseFloat valInpt
			else
				if valInpt is "0"
					$this.val "1"
				else
					timeOut= setTimeout ()->
						$this.val "1"
						#$this.trigger "keyup"
					, 500
				cantInpt= 1
			calcStep(priceInpt,pointsInpt,cantInpt,parentInpt)
		dom.delProd.on "click",delProduct
	delProduct= (e)->
		e.preventDefault()
		$this= $(this)
		parent= $this.parents "tr"
		idProduct= $this.attr "data-id"
		hash= utils.loader parent,true,true
		insHash= $("#"+hash)
		$.ajax "/cart/ajax-remove-product",
			type: "POST"
			data:
				"idproduct": idProduct
			success: (json)->
				insHash.remove()
				dom.countProd.html json.totalItems
				if json.state is 1
					parent.fadeOut 600,()->
						parent.remove()
						dispatchCalc()
				else
					echo json.msg
			error: ()->
				echo "Ocurrió un error al eliminar el producto. Inténtelo nuevamente."
				insHash.remove()
	calcStep= (price,points,cant,parent)->
		targetSTotal= parent.find ".tdSubTotal"
		targetSPoints= parent.find ".tdPoints"
		calcprePoints(points,cant,targetSPoints)
		calcpreTotal(price,cant,targetSTotal)
		dispatchCalc()
	calcprePoints= (points,cant,target)->
		totalPoints= points*cant
		target.html totalPoints
	calcpreTotal= (price,cant,target)->
		total= (price*100*cant)/100
		target.html yOSON.currency+" "+total.toFixed(2)
	dispatchCalc= ()->
		totalPrice= 0													#Precio Total
		totalPoints= 0													#Puntos Totales
		cantItem= 0														#Cantidad de cada Producto
		pointItem= 0													#Puntos de cada Producto
		priceItem= 0													#Precio de cada Producto
		pricePoint= 0 													#Precio de Total de los productos puntuables
		flagPoint= 0													#Flag para saber si un producto se considera en el descuento o no
		instInpt= $(st.inpt)

		if instInpt.length>0
			instInpt.each (index,inst)->
				$this= $(this)
				cantItem= if $this.val() is "" then 1 else parseFloat $this.val()
				pointItem= parseFloat $this.attr("data-points")
				priceItem= parseFloat $this.attr("data-price")
				flagPoint= $this.attr("data-discount")
				if flagPoint is "1"
					pricePoint= pricePoint+((priceItem*100*cantItem)/100)
				else
					pointItem= 0
				totalPoints= totalPoints+(cantItem*pointItem)
				totalPrice= totalPrice+((priceItem*100*cantItem)/100)
			dom.totalPoints.html totalPoints
			dom.subTotal.html totalPrice.toFixed(2)
			totalDiscount= calcDiscounts totalPoints,pricePoint
			dom.total.html (totalPrice-totalDiscount).toFixed(2)
		else
			dom.ctnFullCart.remove()
			dom.ctnEmptyCart.show()
	calcDiscounts= (totalPoints,price)->
		data= yOSON.discounts
		minPoints= parseFloat yOSON.minPoints
		historyPoints= parseFloat yOSON.historyPoints
		condDiscount= if (totalPoints+historyPoints) >= minPoints then true else false
		result= 0
		nextDiscount= null
		if totalPoints isnt 0
			if condDiscount
				dom.tblDiscount.fadeIn 600
				for json,index in data
					if totalPoints >= json.pStart and json.pEnd >= totalPoints
						result= json
						nextDiscount= data[index+1]
						break
				if typeof nextDiscount isnt "undefined" and nextDiscount isnt null
					dom.ctnNote.fadeIn 600
					dom.ctnNote.find("p").html "Te falta "+(nextDiscount.pStart-totalPoints)+" puntos más para alcanzar un descuento de "+(parseFloat(nextDiscount.discount)*100).toFixed(2)+"%."
					#dom.pointsNext.html nextDiscount.pStart-totalPoints
					#dom.percentNext.html (parseFloat(nextDiscount.discount)*100).toFixed(2)
					nextDiscount= null
				else
					dom.ctnNote.fadeOut 600
			else
				dom.tblDiscount.fadeOut 600
				dom.ctnNote.find("p").html "Te faltan "+(totalPoints-historyPoints)+" puntos más para aplicar a los descuentos."
		discount= if result isnt 0 then parseFloat(result.discount) else 0
		totalDiscount= (price*100*discount)/100

		dom.ctnDiscount.html (discount*100).toFixed(2)
		dom.discount.html totalDiscount.toFixed(2)
		return totalDiscount
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqCount.js","libs/plugins/jqNumeric.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Faq Js
# @Description: Modulo para Faq
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "faq", ((Sb) ->
	st=
		liQuestion: ".questions li a"
	dom= {}
	catchDom= ()->
		dom.liQuestion= $(st.liQuestion)
	bindEvents= ()->
		dom.liQuestion.on "click", ()->
			answer = $(this).next()
			if $(answer).is(":hidden")
				$(answer).slideDown()
			else
				$(answer).slideUp()
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
                condDefaultVal= typeof oParams["defaultVal"] isnt "undefined" then oParams["defaultVal"] else true
                if(typeof oParams.dispatch isnt "undefined")
                    THIS.dispatch= oParams.dispatch
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
                                        if condDefaultVal
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
), ["data/office/selects.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Change Address
# @Description: Modulo cambiar direcciones
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "changeAddress", ((Sb) ->
	st=
		inptRad: "input[name='radAddress']"								#Input Radio para seleccionar direcciones
		hidAddress: "#hidAddress"										#Input Hidden para guardar valores del radio de direcciones
		ctnAddress: ".new-address"										#Contenedor de Formulario para crear una nueva dirección
		lnkAddress: ".lnk-address"										#Link para realizar efecto flipcar
		ctnflip: ".ctn-flipcar"											#Contenedor Principal del FlipCar
		frmDetail: ".frmChangeAddress"									#Formularios para cambio de direcciones								
		targetDetail: ".ctnChangeAddress"								#Target del Formulario para cambiar direcciones
		targetAddress: ".ctnAddress"									#Target de datos de direcciones					
		tmplFront: "#tplAddressFront"									#Template para la parte frontal - datos de direcciones
		tmplBack: "#tplAddressBack"										#Template para la parte posterior - formulario de direcciones
	dom= {}
	catchDom= ()->
		dom.inptRad= $(st.inptRad)
		dom.ctnAddress= $(st.ctnAddress)
		dom.lnkAddress= $(st.lnkAddress)
		dom.hidAddress= $(st.hidAddress)
		dom.inptcant= $(st.inptcant)
		dom.tmplFront= _.template $(st.tmplFront).html()
		dom.tmplBack= _.template $(st.tmplBack).html()
	bindEvents= ()->
		dom.inptRad.on "change", ()->
			$this= $(this)
			dom.hidAddress.val $this.val()
			if $this.val() is "new"
				dom.ctnAddress.slideDown(600)
			else
				dom.ctnAddress.slideUp(600)
		$(st.inptRad+":checked").trigger "change"
		dom.lnkAddress.on "click",()->
			$this= $(this)
			ctnFlip= $this.parents(st.ctnflip)
			if ctnFlip.hasClass "flip"
				hideFrmAddress ctnFlip
			else
				showFrmAddress ctnFlip
	showFrmAddress= (inst)->																#Mostrando Formulario de Direcciones
		idAddress= inst.parents(".address").find("input[type='radio']").val()
		targetDetail= inst.find st.targetDetail
		targetDetail.html ""
		targetDetail.append dom.tmplBack(yOSON.addresses[idAddress])
		frmDetail= inst.find st.frmDetail
		frmDetail.validate yOSON.require["changeAddress"]
		inst.addClass "flip"
	hideFrmAddress= (inst)->
		idAddress= inst.parents(".address").find("input[type='radio']").val()
		targetAddress= inst.find st.targetAddress
		frmDetail= inst.find st.frmDetail
		#validateAddress frmDetail,false
		targetAddress.html ""
		targetAddress.append dom.tmplFront(yOSON.addresses[idAddress])
		inst.removeClass "flip"
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqUnderscore.js","data/office/require.js","libs/plugins/jqValidate.js"]
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
 # @Module: selVoucher
 # @Description: Modulo seleccionar tipo de comprobante
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "selVoucher", ((Sb) ->
	st=
		rad: "input[name='voucher']"									#Radio para seleccione tipo de voucher
		targetChange: ".selChangue"										#Objetivos al cambiar valores del radio
		percentPersep: ".vouchPercep"									#Porcentaje de persepcion
		persep: ".vouchTotalPercep"										#Monto de persepcion
		totalPersep: ".vouchTotal"										#Monto total
	dom= {}
	catchDom= ()->
		dom.rad= $(st.rad)
		dom.targetChange= $(st.targetChange)
		dom.percentPersep= $(st.percentPersep)
		dom.persep= $(st.persep)
		dom.totalPersep= $(st.totalPersep)
	bindEvents= ()->
		valSel= ""
		target= null
		dom.rad.on "change",()->
			valSel= $(this).val()
			changeRad valSel
			calcTotal valSel
		$(st.rad+":checked").trigger "change"
	changeRad= (valSel)->
		target= $ "."+valSel
		if $(st.targetChange+":visible").length>0
			$(st.targetChange+":visible").fadeOut 600, ()->
				target.fadeIn 600
		else
			target.fadeIn 600
	calcTotal= (valSel)->
		dataPersep= yOSON.perception[valSel]
		dom.percentPersep.html dataPersep.perception
		dom.persep.html dataPersep.totalPerception
		dom.totalPersep.html dataPersep.totalOrder
	init: (oParams) ->
		catchDom()
		bindEvents()
)
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
), ["data/office/require.js","libs/plugins/jqValidate.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Upload
 # @Description: Modulo para subir imagenes en el perfil
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "upload", ((Sb) ->
	st=
		btn: ".lnk-upload"
		ctnImg: ".prfl-img"
		imgFile: ".prfl-img img"
	dom= {}
	catchDom= ()->
		dom.ctnImg= $(st.ctnImg)
		dom.imgFile= $(st.imgFile)
	bindEvents= ()->
		$.jqFile
			"nameFile": "picture"
			"routeFile": "/web/businessman-picture"
			"btnFile": st.btn
			"beforeCharge": ()->
				utils.loader dom.ctnImg,true
			"success": successFile
			"error": (state,msg)->
				utils.loader dom.ctnImg,false
				echo msg
	successFile= (json)->
		if json.state is 1
			dom.imgFile.attr "src",json.urlImagen
		else
			echo json.msg
		utils.loader dom.ctnImg,false
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqFile.js"]
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
#-----------------------------------------------------------------------------------------------
 # @Module: Fuxion
 # @Description: Modulo mostrar Categorias
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "fuxion", ((Sb) ->
	st=
		slider: ".fx-ctnslide"
		lnkDetail: ".cat-fx"
		lnkBack: ".fx-back"
		target: ".bx-catfx .row"
		title: ".fx-ncat"
		tmpl: "#tplFuxion"
	dom= {}
	fuxion=
		"Manuales": "Manuales"
		"afiliacion": "Afiliación"
		"banners": "Banners"
		"logotipo": "Logotipo"
		"productos": "Productos"
	catchDom= ()->
		dom.slider= $(st.slider)
		dom.lnkDetail= $(st.lnkDetail)
		dom.lnkBack= $(st.lnkBack)
		dom.target= $(st.target)
		dom.title= $(st.title)
		dom.tmpl= _.template $(st.tmpl).html()
	bindEvents= ()->
		stateDetail= true
		value= null
		dom.lnkDetail.on "click",(e)->
			e.preventDefault()
			if stateDetail
				stateDetail= false
				value= $(this).attr "data-id"
				renderFuxion(value)
				dom.slider.animate
					left: "-948px"
				,800
		dom.lnkBack.on "click",(e)->
			e.preventDefault()
			if stateDetail is false
				stateDetail= true
				dom.slider.animate
					left: "0"
				,800
	renderFuxion= (value)->
		dom.title.html fuxion[value]
		json=
			"data": yOSON.discounts[value]
		dom.target.html dom.tmpl(json)
		yOSON.AppCore.runModule('scrollCustom',{'scroll':"#scrollFx",'height':366});
	init: (oParams) ->
		catchDom()
		bindEvents()
),["libs/plugins/jqUnderscore.js","libs/plugins/jqTinyscrollbar.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Calendar
# @Description: Modulo calendario
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calendar", ((Sb) ->
	st=
		calendar: "#calendar"
		tmpl: "#tplEvent"
	dom= {}
	data=
		"es":
			"dayName": ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sab']
			"monthNames": ['enero', 'febrero', 'marzo', 'abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre']
	catchDom= ()->
		dom.calendar= $(st.calendar)
		dom.tmpl= _.template $(st.tmpl).html()
	bindEvents= ()->
		$('#calendar').fullCalendar
			events:
				url: yOSON.urlCalendar
			eventClick: (calEvent, jsEvent, view)->
				calEvent["fullDate"]= getDate calEvent.start,calEvent.end
				calEvent.description= calEvent.description.replace(/[\n]/g,"<br>")
				$.fancybox dom.tmpl(calEvent)
				return false
			viewRender: ()->
				utils.loader dom.calendar,true
			eventAfterAllRender: ()->
				utils.loader dom.calendar,false
			header:
				left: 'today prev,next'
				center: ''
				right: 'title'
	getDate= (dateStart,dateEnd)->
		dateTrad= data.es
		dayStart= dateTrad.dayName[dateStart.getDay()]
		numStart= dateStart.getDate()
		monthStart= dateTrad.monthNames[dateStart.getMonth()]
		hourStart= dateStart.getHours()
		minStart= if dateStart.getMinutes() is 0 then "00" else dateStart.getMinutes()
		yearStart= dateStart.getFullYear()
		if dateEnd isnt null
			dayEnd= dateTrad.dayName[dateEnd.getDay()]
			numEnd= dateEnd.getDate()
			monthEnd= dateTrad.monthNames[dateEnd.getMonth()]
			hourEnd= dateEnd.getHours()
			minEnd= if dateEnd.getMinutes() is 0 then "00" else dateEnd.getMinutes()
			yearEnd= dateEnd.getFullYear()
			if equalDates(dateStart,dateEnd)
				return dayStart+", "+numStart+" "+monthStart+" "+yearStart+", "+hourStart+":"+minStart+" - "+hourEnd+":"+minEnd
			else
				return dayStart+", "+numStart+" "+monthStart+" "+yearStart+", "+hourStart+":"+minStart+" - "+dayEnd+", "+numEnd+" "+monthEnd+" "+yearEnd+", "+hourEnd+":"+minEnd
		else
			return dayStart+", "+numStart+" "+monthStart+" "+yearStart
	equalDates= (date1,date2)->
		date1.getDate() is date2.getDate() and  date1.getMonth() is date2.getMonth() and date1.getFullYear() is date2.getFullYear()
	init: (oParams) ->
		catchDom()
		bindEvents()
),["libs/plugins/jqFullCalendar.js","libs/plugins/jqGCal.js","libs/plugins/jqUnderscore.js","libs/plugins/jqFancybox.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Iframe
# @Description: Modulo para realizar un submit por iframe
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "subIframe", ((Sb) ->
	st=
		ifrm: "#ifrmFuxion"
	dom= {}
	catchDom= ()->
		dom.ifrm= $(st.ifrm)
	bindEvents= ()->
		dom.ifrm.submit()
		dom.ifrm.remove()
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
# @Module: Tipsy Web
# @Description: Modulo para Mostrar un tipsy
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "tipsyWeb", ((Sb) ->
	st=
		tooltip: ".icon-help"
	dom= {}
	catchDom= ()->
		dom.tooltip= $(st.tooltip)
	bindEvents= ()->
		dom.tooltip.tipsy
			html: true
			gravity: 'se'
	init: (oParams) ->
		catchDom()
		bindEvents()
)
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
				url: "/index/update-captcha"
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
# @Module: searchProduct
# @Description: Modulo buscar productos en el listado
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "searchProduct", ((Sb) ->
	st=
		frm: "#searchProd"
		inpt: "#searchProd input"
	dom= {}
	catchDom= ()->
		dom.frm= $(st.frm)
		dom.inpt= $(st.inpt)
	bindEvents= ()->
		dom.inpt.on "keypress", (e)->
			if e.which is 13
				dom.frm.submit()
				return false
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: validWeb
 # @Description: Modulo para validaciones mi web
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "validWeb", ((Sb) ->
	st=
		history: "#testimony"
		noBlanks: "#urlblog,#urlyoutube,#urltwitter,#urlfacebook,#subdomain"
	dom= {}
	catchDom= ()->
		dom.history= $(st.history)
		dom.noBlanks= $(st.noBlanks)
	bindEvents= ()->
		utils.validBlanks dom.history
		utils.noBlanks dom.noBlanks
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: Scroll Custom
 # @Description: Modulo para personalizar scrolls
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "scrollCustom", ((Sb) ->
	_this= @
	_this.inst= null
	Sb.events ["instTinyScroll"],(fn)->
		fn and fn(_this.inst)
	,this
	bindEvents= (scroll)->
		_this.inst= $(scroll).tinyscrollbar()
	checkScroll= (scroll,hMax,force)->
		if $(scroll).find(".overview").height()<=hMax
			$(scroll).addClass "nscroll"
			if typeof force isnt "undefined" and force
				bindEvents(scroll)
		else
			bindEvents(scroll)
	init: (oParams) ->
		if typeof oParams.height isnt "undefined"
			checkScroll(oParams.scroll,oParams.height,oParams.force)
		else
			bindEvents(oParams.scroll)

), ["libs/plugins/jqTinyscrollbar.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Add Favorite
 # @Description: Modulo para agregar favorito
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addFavorite", ((Sb) ->
	st=
		btnFavorite: "#btnfav"
		ctnFavorite: "#ctn-btnfav"
		descFavorite: "#ctn-btnfav h5"
	dom= {}
	textFavorite=
		"active": "¿Desea Agregar el producto a favoritos?<span>Presionar en la Estrella</span>"
		"disable": "¿Desea Quitar el producto de favoritos?<span>Presionar en la Estrella</span>"
	catchDom= ()->
		dom.btnFavorite= $(st.btnFavorite)
		dom.ctnFavorite= $(st.ctnFavorite)
		dom.descFavorite= $(st.descFavorite)
	bindEvents= ()->
		$this= null
		idProduct= ""
		urlProduct= ""
		dom.btnFavorite.on "click",()->
			$this= $(this)
			idProduct= $this.attr "data-id"
			urlProduct= if $this.hasClass("active") then "/product/ajax-remove-favorite" else "/product/ajax-add-favorite"
			utils.loader dom.ctnFavorite,true
			$.ajax
				"url": urlProduct
				"data":
					"idproduct": idProduct
				success: (json)->
					utils.loader dom.ctnFavorite,false
					if json.state is 1
						if $this.hasClass("active")
							$this.removeClass "active"
							dom.descFavorite.html textFavorite["active"]
							echo "Se eliminó el producto del listado de favoritos"
						else
							$this.addClass "active"
							dom.descFavorite.html textFavorite["disable"]
							echo "Se agregó el producto al listado de favoritos"
					else
						echo json.msg
	init: (oParams) ->
		catchDom()
		bindEvents()
)
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
# @Module: quickCart
# @Description: Modulo para compra rapida
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "quickCart", ((Sb) ->
	st=
		inptCant: ".inptcant"										#Input Text que muestra la cantidad de productos,id,price,points,discount
		inptSearch: "#search"										#Selector del campo de texto de la búsqueda
		inptCat: "#category"										#Selector del filtro de categorías de la búsqueda
		btnSearch: "#btnSearch"										#Selector del botón buscar
		scrollSearch: "#scrollQkResult"								#Scroll de la tabla resultado de búsqueda
		ctnSearch: ".ctn-resultqk"									#Selector del contenedor de la tabla resultado de búsqueda
		tplSProduct: "#tplProdSearch"								#Template de Resutados de Productos
		tplProduct: "#tplProd"										#Template de producto agregado al carrito
		btnFavorite: ".ic-fvt"										#Boton Favoritos
		targetSearch: ".tbl-qkresult tbody"							#Tbody de la tabla que muestra los resultados de búsqueda
		targetCart: ".tbl-carrito tbody"							#Tbody de la tabla que muestra los productos agregados al carrito
		targetChange: ".slrChangue"									#Selector de la funcionalidad de mostrar u ocultar secciones al cambio de input voucher
		singleSubTotal: ".tdSubTotal"								#Columna donde se muestra el subtotal
		singlePoints: ".tdPoints"									#Columna donde se muestra los puntos
		numItems: "#menuQProd"										#Muestra el número de items agregados al carrito
		radChange: "input[name='voucher']"							#Selector del input voucher
		btnAddProd: ".btnAddProd"									#Selector del botón agregar producto al carrito
		btnDelProd: ".btn-close-red"								#Selector del botón eliminar producto de carrito
		tblDiscount: "#tblDiscount"									#Selector de la Tabla de descuentos
		ctnNote: ".cart-note"										#Selector de indicador de proximos descuentos
		ctnDiscount: "#percentDis"									#Selector de contenedor de descuentos
		totalPts: "#totalPts"										#Selector indicador de puntos totales
		totalDisc: "#totalDis"										#Selector indicador de descuento total
		subTotal: "#subTotal"										#Selector indicador del subtotal a pagar
		total: "#totalCart"											#Selector indicador del total a pagar
		subTotalP: "#subTotalP"										#Selector indicador de subtotal sin percepcion
		totalPercep: "#totalPercep"									#Selector indicador de total de percepción
		perceptionPercep: "#perceptionPercep"						#Selector de porcentaje
	dom= {}
	idsCarts= []													#Almacena los ids del carrito
	lastSearch= null												#Almacena el último resultado de búsqueda
	catchDom= ()->
		dom.inptSearch= $(st.inptSearch)
		dom.inptCat= $(st.inptCat)
		dom.btnSearch= $(st.btnSearch)
		dom.ctnSearch= $(st.ctnSearch)
		dom.scrollSearch= $(st.scrollSearch)
		dom.targetSearch= $(st.targetSearch)
		dom.targetCart= $(st.targetCart)
		dom.tplSProduct= _.template $(st.tplSProduct).html()
		dom.tplProduct= _.template $(st.tplProduct).html()
		dom.targetChange= $(st.targetChange)
		dom.radChange= $(st.radChange)
		dom.btnDelProd= $(st.btnDelProd)
		dom.numItems= $(st.numItems)
		dom.tblDiscount= $(st.tblDiscount)
		dom.ctnNote= $(st.ctnNote)
		dom.ctnDiscount= $(st.ctnDiscount)
		dom.totalPts= $(st.totalPts)
		dom.totalDisc= $(st.totalDisc)
		dom.subTotal= $(st.subTotal)
		dom.total= $(st.total)
		dom.subTotalP= $(st.subTotalP)
		dom.totalPercep= $(st.totalPercep)
		dom.perceptionPercep= $(st.perceptionPercep)
	bindEvents= ()->
		entityCart.getIds()
		setCount()
		evtFavorite()
		searchProd()
		selChangue()
		evtAddProduct()
		evtRemoveProduct()
	entityCart=
		getIds: ()->
			dom.targetCart.find(st.inptCant).each ()->
				idsCarts.push $(this).attr("data-id")
		delId: (idProd)->
			idsCarts= _.filter idsCarts,(id)->
				return id isnt idProd
		addId: (idProd)->
			idsCarts.push idProd
	setCount= (inst)->
		inptCant= if typeof inst isnt "undefined" then inst.find(st.inptCant) else $(st.inptCant)
		cantInpt= 0																		#Cantidad de items
		inptCant.numeric
			decimal: false
			negative: false
		inptCant.jqCount
			changued: (cant)->
				dispatchCalc this,this.parents("tr"),cant
				calcTotal()
		inptCant.on "keypress", (e)->
			return e.which isnt 13
		inptCant.on "keyup",()->
			$this= $(this)
			timeOut= if typeof timeOut isnt "undefined" then timeOut else null
			if timeOut isnt null then clearTimeout timeOut
			valInpt= $this.val()
			if valInpt isnt "" and valInpt isnt "0"
				cantInpt= parseFloat valInpt
			else
				if valInpt is "0"
					$this.val "1"
				else
					timeOut= setTimeout ()->
						$this.val "1"
					, 500
				cantInpt= 1
			dispatchCalc $this,$this.parents("tr"),cantInpt
			calcTotal()
	dispatchCalc= (inst,parent,cant)->
		targetSubTotal= parent.find st.singleSubTotal
		targetPoints= parent.find st.singlePoints
		point= parseFloat inst.attr("data-points")
		price= parseFloat inst.attr("data-price")
		total= (1+parseFloat(yOSON.iva))*price*cant
		targetPoints.html cant*point
		if targetSubTotal.length > 0
			targetSubTotal.html yOSON.currency+" "+total.toFixed(2)
	searchProd= ()->
		dom.inptSearch.on "keypress", (e)->
			if e.which is 13
				dispatchSearch()
			return e.which isnt 13
		dom.btnSearch.on "click",()->
			dispatchSearch()
	dispatchSearch= ()->
		search= dom.inptSearch.val()
		category= dom.inptCat.val()
		lenProd= 0
		utils.loader dom.ctnSearch,true
		$.ajax
			"url": "/quick-cart/ajax-get-products"
			"data":
				"search": search
				"category": category
			"success": (json)->
				utils.loader dom.ctnSearch,false
				if json.state is 1
					lastSearch= json
					dom.targetSearch.html dom.tplSProduct(json)
					lenProd= dom.targetSearch.find("tr").length
					if lenProd <=4
						dom.scrollSearch.addClass "nscroll"
					else
						dom.scrollSearch.removeClass "nscroll"
					setCount dom.targetSearch
					evtFavorite()
					evtAddProduct()
					refreshScroll()
				else
					echo json.msg
	refreshScroll= ()->
		if dom.scrollSearch.find(".overview").height()<=352
			dom.scrollSearch.addClass "nscroll"
		else
			dom.scrollSearch.removeClass "nscroll"
		Sb.trigger "instTinyScroll",[(instTiny)->
			instTiny.tinyscrollbar_update()
		]
	evtFavorite= ()->
		$(st.btnFavorite).on "click",()->
			$this= $(this)
			idProduct= $this.attr "data-id"
			urlProduct= if $this.hasClass("active") then "/product/ajax-remove-favorite" else "/product/ajax-add-favorite"
			trgtParent= $this.parents "tr"
			hash= utils.loader trgtParent,true,1
			$.ajax
				"url": urlProduct
				"data":
					"idproduct": idProduct
				success: (json)->
					if json.state is 1
						if $this.hasClass("active")
							$this.removeClass "active"
							echo "Se eliminó el producto del listado de favoritos"
						else
							$this.addClass "active"
							echo "Se agregó el producto al listado de favoritos"
					else
						echo json.msg
					$("#"+hash).remove()
	evtAddProduct= ()->
		$(st.btnAddProd).on "click",()->
			$this= $(this)
			idProduct= $this.attr "data-id"
			parentTR= $this.parents "tr"
			cant= parentTR.find(".inptcant").val()
			hash= utils.loader parentTR,true,true
			$.ajax
				"url": "/cart/ajax-add-product"
				"method": "POST"
				"data":
					"idproduct": idProduct
					"quantity": cant
				"success":(json)->
					if json.state is 1
						entityCart.addId(idProduct)
						el= $(dom.tplProduct(json.productData).replace(/[\n\r]/g, ""))
						el.hide()
						parentTR.fadeOut 600,()->
							parentTR.remove()
							refreshScroll()
						dom.targetCart.append el
						setCount el
						evtRemoveProduct el
						dom.numItems.html json.totalItems
						el.fadeIn 600,()->
							calcTotal()
					else
						echo json.msg
					$("#"+hash).remove()
				"error": ()->
					echo "Ocurrió un error en el sistema, intente nuevamente"
	evtRemoveProduct= (inst)->
		el= if typeof inst isnt "undefined" then inst.find(st.btnDelProd) else dom.btnDelProd
		el.on "click",()->
			$this= $(this)
			parent= $this.parents "tr"
			idProduct= parent.find(st.inptCant).attr "data-id"
			hash= utils.loader parent,true,true
			insHash= $("#"+hash)
			$.ajax "/cart/ajax-remove-product",
				type: "POST"
				data:
					"idproduct": idProduct
				success: (json)->
					insHash.remove()
					if json.state is 1
						entityCart.delId(idProduct)
						parent.fadeOut 600,()->
							parent.remove()
							dom.numItems.html json.totalItems
							calcTotal()
						restartResultSearch()
					else
						echo json.msg
				error: ()->
					echo "Ocurrió un error al eliminar el producto. Inténtelo nuevamente."
					insHash.remove()
	refreshProd= (idProduct,increment)->
		target= dom.targetCart.find(st.inptCant+"[data-id='"+idProduct+"']")
		currentCant= parseFloat target.val()
		target.val(currentCant+parseFloat(increment)).trigger "keyup"
	restartResultSearch= ()->
		products= if !lastSearch then yOSON.products else lastSearch.products
		idsDefault= _.pluck(products, 'codprod')
		arrResult= _.difference(idsDefault,idsCarts)
		jsonResult= _.filter products,(value)->
			return _.contains arrResult,value.codprod
		if jsonResult.length > 0
			showResultSearch(jsonResult)
	showResultSearch= (products)->
		utils.loader dom.ctnSearch,true
		dom.targetSearch.html dom.tplSProduct({"products":products})
		setCount dom.targetSearch
		evtFavorite()
		evtAddProduct()
		refreshScroll()
		utils.loader dom.ctnSearch,false
	calcTotal= ()->
		cantItem= 0														#Cantidad de cada Producto
		pointItem= 0													#Cantidad de puntos de cada Producto
		priceItem= 0													#Precio de cada Producto
		flagDiscount= 0													#Descuento de cada Producto
		totalPoints= 0													#Total de productos puntuables
		totalPrice= 0													#Precio Total de los productos del carrito
		pricePoints= 0													#Precio Total de los productos puntuables
		instProd= if typeof dom.targetCart is "undefined" then [] else dom.targetCart.find(st.inptCant)
		if instProd.length>0
			instProd.each ()->
				$this= $(this)
				cantItem= if $this.val() is "" then 1 else parseFloat $this.val()
				pointItem= parseFloat $this.attr("data-points")
				priceItem= parseFloat $this.attr("data-price")
				flagDiscount= $this.attr "data-discount"
				if flagDiscount is "1"
					pricePoints= pricePoints+((1+parseFloat(yOSON.iva))*priceItem*cantItem)
				else
					pointItem= 0
				totalPoints= totalPoints+(cantItem*pointItem)
				totalPrice= totalPrice+((1+parseFloat(yOSON.iva))*priceItem*cantItem)
			totalDiscount= calcDiscounts totalPoints,pricePoints
			subTotalP= totalPrice-totalDiscount+yOSON.shipPrice
			perception= 0
			if subTotalP>=parseFloat(yOSON.cPercep.start)
				perception= parseFloat(yOSON.cPercep.perception)
				dom.perceptionPercep.html (perception*100).toFixed(2)
				dom.totalPercep.html (subTotalP*perception).toFixed(2)
			else
				dom.perceptionPercep.html "0.00"
				dom.totalPercep.html "0.00"
			totalPay= subTotalP*(1+perception)
			dom.totalPts.html totalPoints
			dom.subTotal.html totalPrice.toFixed(2)
			dom.subTotalP.html subTotalP.toFixed(2)
			dom.total.html totalPay.toFixed(2)
	calcDiscounts= (totalPoints,pricePoints)->
		data= yOSON.discounts
		minPoints= parseFloat yOSON.minPoints
		historyPoints= parseFloat yOSON.historyPoints
		condDiscount= if (totalPoints+historyPoints) >= minPoints then true else false
		result= 0
		nextDiscount= null
		if totalPoints isnt 0
			if condDiscount
				dom.tblDiscount.fadeIn 600
				for json,index in data
					if totalPoints >= json.pStart and json.pEnd >=totalPoints
						result= json
						nextDiscount= data[index+1]
						break
				if typeof nextDiscount isnt "undefined" and typeof nextDiscount isnt null
					dom.ctnNote.fadeIn 600
					dom.ctnNote.find("p").html "Te falta "+(nextDiscount.pStart-totalPoints)+" puntos más para alcanzar un descuento de "+(parseFloat(nextDiscount.discount)*100).toFixed(2)+"%."
				else
					dom.ctnNote.fadeOut 600
			else
				dom.tblDiscount.fadeOut 600
				dom.ctnNote.find("p").html "Te faltan "+(totalPoints-historyPoints)+" puntos más para aplicar a los descuentos."
		discount= if result isnt 0 then parseFloat(result.discount) else 0
		totalDiscount= (pricePoints*100*discount)/100
		dom.ctnDiscount.html (discount*100).toFixed(2)
		dom.totalDisc.html totalDiscount.toFixed(2)
		return totalDiscount
	selChangue= ()->
		valSel= ""
		changeRad= (valSel)->
			target= $ "."+valSel
			if $(st.targetChange+":visible").length>0
				$(st.targetChange+":visible").fadeOut 600, ()->
					target.fadeIn 600
			else
				target.fadeIn 600
		dom.radChange.on "change",()->
			valSel= $(this).val()
			yOSON.cPercep= yOSON.perception[valSel]
			changeRad valSel
			calcTotal()
		$(st.radChange+":checked").trigger "change"
	Sb.events ["quickCartCalc"],()->
		calcTotal()
	,this
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqUnderscore.js","libs/plugins/jqCount.js","libs/plugins/jqNumeric.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: popAddress
 # @Description: Modulo para agregar dirección
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "popAddress", ((Sb) ->
	st=
		popup: "#popAddress"
		tmpl: "#tplAddress"
		frmAddress: "#frmNewAddress"
		radAddress: "input[name='radAddress']"
		valShip: "#totalShip"
	dom= {}
	catchDom= ()->
		dom.popup= $(st.popup)
		dom.tmpl= $(st.tmpl).html()
		dom.valShip= $(st.valShip)
	bindEvents= ()->
		changueAddress()
		dom.popup.fancybox
			"content": dom.tmpl
			"title": null
			"afterShow": ()->
				$(st.frmAddress).validate yOSON.require[st.frmAddress]
				yOSON.AppCore.runModule('select-depends',{'select':'#ubigeo'+yOSON.ubigeoLevels})
			"beforeClose": ()->
				changueAddress()
	changueAddress= ()->
		el= $(st.radAddress)
		$this= null
		idAddress= null
		el.off().on "change",()->
			$this= $(this)
			idAddress= $this.val()
			yOSON.iva= yOSON.addressesData[idAddress]["iva"]
			yOSON.shipPrice= parseFloat yOSON.addressesData[idAddress]["shipPrice"]
			dom.valShip.html yOSON.shipPrice.toFixed(2)
			Sb.trigger "quickCartCalc"
		$(st.radAddress+":checked").trigger "change"
	init: (oParams) ->
		catchDom()
		bindEvents()
),["libs/plugins/jqFancybox.js","data/office/require.js","libs/plugins/jqValidate.js"]