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
), ["data/desafio/require.js","libs/plugins/jqValidate.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Add File
 # @Description: Agregar files
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addFile", ((Sb) ->
	arrFiles= ["#btnWincha","#btnFrente","#btnPerfil","#btnOtro"]
	init: (oParams) ->
		optFile= oParams.file
		for file in arrFiles
			((file)->
				$file= $(file)
				nameFile= $file.attr "data-file"
				ctnFile= $file.parent()
				json=
					"nameFile": nameFile
					"routeFile": "/operation/upload-img"
					"btnFile": file
					"beforeCharge": ()->
						utils.loader ctnFile,true
					"success": (json)->
						if json.state is 1
							ctnFile.find('img').attr "src",json.domain+json[nameFile]
							$file.attr "data-valid","1"
						else
							echo json.msj
						utils.loader ctnFile,false
					"error": (state,msg)->
						utils.loader ctnFile,false
						echo msg
				if typeof optFile isnt "undefined"
					json= $.extend json,optFile
				$.jqFile json
			) file
), ["libs/plugins/jqFile.js"]

#-----------------------------------------------------------------------------------------------
 # @Module: Slider Js
 # @Description: Modulo slidejs
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "sliderJs", ((Sb) ->
	st=
		slider: "#sliderprom"
	dom= {}
	catchDom= ()->
		dom.slider= $(st.slider)
	bindEvents= ()->
		dom.slider.tinycarousel
			interval: true
			intervaltime: 3500
			duration: 1600
	init: (oParams)->
		catchDom()
		bindEvents()
), ["libs/plugins/jqTinycarousel.js"]

#-----------------------------------------------------------------------------------------------
 # @Module: Search Mentor
 # @Description: Modulo para buscar mentor
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "searchMentor", ((Sb) ->
	st=
		ctnMentor: "#dataMentor"
		inptMentor: "#codmentor"
		btnMentor: "#verifMentor"
		ctnSearchM: "#ctnSearchMentor"
		tmpl: "#tplMentor"
	dom= {}
	catchDom= ()->
		dom.ctnMentor= $(st.ctnMentor)
		dom.inptMentor= $(st.inptMentor)
		dom.btnMentor= $(st.btnMentor)
		dom.ctnSearchM= $(st.ctnSearchM)
		dom.tmpl= _.template $(st.tmpl).html()
	bindEvents= ()->
		dom.btnMentor.on "click",()->
			valSearch= dom.inptMentor.val()
			if valSearch isnt ""
				utils.loader dom.ctnSearchM,true
				dom.ctnMentor.slideUp 500,()->
					$.ajax
						"url": "/operation/get-mentor"
						data:
							"idmentor": valSearch
						success: (json)->
							utils.loader dom.ctnSearchM,false
							if json.state is 1
								dom.ctnMentor.html dom.tmpl(json.dataMentor)
								dom.ctnMentor.slideDown 500
							else
								echo json.msg
		dom.inptMentor.on "keypress", (e)->
			if e.which is 13
				dom.btnMentor.trigger "click"
				return false
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqFile.js","libs/plugins/jqUnderscore.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Numeric
 # @Description: Modulo para buscar mentor
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "numeric", ((Sb) ->
	st=
		inpt: ".numeric"
		dec: ".decimal"
		edad: ".inptEdad"
	dom= {}
	catchDom= ()->
		dom.inpt= $(st.inpt)
		dom.dec= $(st.dec)
		dom.edad= $(st.edad)
	bindEvents= ()->
		dom.inpt.numeric
			decimal: false
			negative: false
		dom.dec.numeric
			negative: false
		if dom.edad.length > 0
			utils.vLength dom.edad,2
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqNumeric.js"]
#-----------------------------------------------------------------------------------------------
# @Module: Calendar
# @Description: Modulo calendario
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calendar", ((Sb) ->
	st=
		calendar: "#calendar"
		tplProd: "#tplProduct"
		tplConsume: "#tplConsume"
	dom= {}
	catchDom= ()->
		dom.calendar= $(st.calendar)
		dom.tplProd= _.template $(st.tplProd).html()
		dom.tplConsume= _.template $(st.tplConsume).html()
	cacheEvent=
		"measure": {}
		"product": {}
	traduction=
		"es":
			"monthNames": ['enero', 'febrero', 'marzo', 'abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre']
	bindEvents= ()->
		$('#calendar').fullCalendar
			header:
				left: 'today prev,next'
				center: 'title'
				right: ''
			eventSources: [
				url: '/operation/ajax-data-calendar'
				color: 'transparent'
			]
			eventRender: (event,element)->
				evt= "<span></span>"+event.title
				return element.html evt
			eventAfterRender: (event,element)->
				if event.className[0] is "evtProduct"
					evtProduct element,event
				else if event.className[0] is "evtMeasure"
					evtMeasure element,event
			loading: (bool)->
				if bool
					utils.loader dom.calendar,true
				else
					utils.loader dom.calendar,false
	evtProduct= (element,event)->
		json= {}
		element.on "click",()->
			json["fDate"]= getDate event._start
			dispatchEvt event.id,dom.tplProd,"/operation/ajax-data-detail-order",cacheEvent["product"],json
	evtMeasure= (element,event)->
		json= {}
		element.on "click",()->
			json["fDate"]= getDate event._start
			dispatchEvt event.id,dom.tplConsume,"/operation/ajax-data-cycle-detail",cacheEvent["measure"],json
	dispatchEvt= (id,tpl,url,cache,json)->
		if typeof cache[id] is "undefined"
			utils.loader dom.calendar,true
			$.ajax
				"url": url
				"data":
					"id": id
				"success": (result)->
					result= $.extend(result,json)
					cache[id]= result
					utils.loader dom.calendar,false
					$.fancybox
						content: tpl(result)
						autoResize: false
						fitToView: false
				"error": ()->
					utils.loader dom.calendar,false
					echo "Ocurrió un error en la petición. Inténtelo nuevamente."
		else
			$.fancybox
				content: tpl(cache[id])
				autoResize: false
				fitToView: false
	getDate= (date)->
		dateTrad= traduction.es
		day= date.getDate()
		month= dateTrad.monthNames[date.getMonth()]
		year= date.getFullYear()
		return day+" de "+month+" del "+year
	init: (oParams) ->
		catchDom()
		bindEvents()
),["libs/plugins/jqFullCalendar.js","libs/plugins/jqUnderscore.js","libs/plugins/jqFancybox.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Chart
 # @Description: Modulo para pintar gráficos estadísticos
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "chart", ((Sb) ->
	st=
		chart: "#chartProgress"
		tplTip: "#tplTip"
	dom= {}
	catchDom= ()->
		dom.chart= $(st.chart)
		dom.tplTip= _.template $(st.tplTip).html()
	bindEvents= ()->
		dom.chart.highcharts
			chart:
				type: 'area'
				spacingBottom: 30
			title:
				text: "Estadística"
				align: "left"
				margin: 35
				style:
					color: "#939393"
					fontSize: "15px"
					fontFamily: "'myriadproBold',Arial,sans-serif"
			legend:
				enabled: false
			xAxis:
				categories: yOSON.chartX
				ineColor: "#bebebe"
				lineWidth: 1
				tickColor: "#bebebe"
				tickWidth: 1
			yAxis:
				title:
					text: null
				labels:
					align: 'left'
					x: 0
					y: -6
					format: '{value} kg.'
					style:
						color: "#d58001"
						fontSize: "13px"
						fontFamily: "'myriadproBold',Arial,sans-serif"
			tooltip:
				borderColor: "#c2c2c2"
				formatter: (json)->
					return dom.tplTip yOSON.chartData[this.x]
				useHTML: true
			plotOptions:
				area:
					fillOpacity: 0.5
			credits:
				enabled: false
			series:[
				data: yOSON.chartY
			]
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqHighcharts.js","libs/plugins/jqUnderscore.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: PopProgress
 # @Description: Modulo para subir avances
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "popProgress", ((Sb) ->
	st=
		btnPopup: ".btnPopup"
		tplProgress: "#tplProgress"
		frmProgress: "#frmProgress"
	dom= {}
	catchDom= ()->
		dom.btnPopup= $(st.btnPopup)
		dom.tplProgress= _.template $(st.tplProgress).html()
	bindEvents= ()->
		dom.btnPopup.fancybox
			content: dom.tplProgress()
			title: null
			autoResize: false
			fitToView: false
			afterShow: ()->
				validateFrm()
				yOSON.AppCore.runModule 'calcGrease',{"sexo":true}
				yOSON.AppCore.runModule 'numeric'
				yOSON.AppCore.runModule 'addFile',
					'file':
						"content": "#popAdvance"
	validateFrm= ()->
		json= yOSON.require[st.frmProgress]
		$(st.frmProgress).validate json
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqFancybox.js","libs/plugins/jqUnderscore.js","data/desafio/require.js","libs/plugins/jqValidate.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: Error Images
 # @Description: Modulo para subir avances
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "error-images", ((Sb) ->
	st=
		img: "img"
	dom= {}
	catchDom= ()->
		dom.img= $(st.img)
	bindEvents= ()->
		dom.img.on "error",()->
			$(this).attr "src",yOSON.baseHost+"static/img/no-disponible.png"
	init: ()->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: tipsyWe
 # @Description: Modulo para tipsyWeb
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "tipsyWeb", ((Sb) ->
	st=
		tooltip: ".help-circle"
	dom= {}
	catchDom= ()->
		dom.tooltip= $(st.tooltip)
	bindEvents= ()->
		dom.tooltip.tipsy
			html: true
			gravity: "se"
	init: (oParams)->
		catchDom()
)
#-----------------------------------------------------------------------------------------------
 # @Module: calcGrease
 # @Description: Modulo para calcular grasa corporal
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "calcGrease", ((Sb) ->
	st=
		cintura: "#cintura"
		cadera: "#cadera"
		cuello: "#cuello"
		inpts: "#cintura,#cadera,#cuello,#talla"
		talla: "#talla"
		grasa: "#indgrasa"
		sexo: "input[name='sexempr']:checked"
	dom= {}
	typeSexo= null
	catchDom= ()->
		dom.cintura= parseFloat $(st.cintura)
		dom.cadera= parseFloat $(st.cadera)
		dom.cuello= parseFloat $(st.cuello)
		dom.inpts= parseFloat $(st.inpts)
		dom.talla= parseFloat $(st.talla)
		dom.grasa= parseFloat $(st.grasa)
	bindEvents= ()->
		dom.inpts.on "keyup",calcGrease
	calcGrease= (e)->
		e.preventDefault()
		sexo= if typeof typeSexo is "undefined" then $(st.sexo).val() else $("#sexempr").val()
		cintura= dom.cintura.val()
		cuello= dom.cuello.val()
		cadera= dom.cadera.val()
		talla= dom.talla.val()
		percentGrease= 0
		if cintura isnt "" and cuello isnt "" and cadera isnt "" and talla isnt "" and talla >= 100
			if sexo is "M"
				percentGrease= Math.round(495/(1.0324 - 0.19077*(log10(cintura - cuello))+0.15456*(log10(talla)))- 450)
			else
				percentGrease= Math.round(495/(1.29579- (0.35004 * (log10(cintura + cadera - cuello )))+0.22100*(log10(talla))) - 450)
			dom.grasa.val percentGrease
	log10= (val)->
		return Math.log(val)/Math.LN10
	init: (oParams)->
		typeSexo= oParams.sexo
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: challenge
 # @Description: Modulo mostrar modales
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "desafioModal", ((Sb)->
	st=
		dieta: "#dieta"
		plan514: "#plan514"
		tips: "#tips"
		launch: "#launch"
		tmpl: "#modalDieta"
		fancy: ".lnlFancy"
		tipsFancy: ".tipsFancy"
		lastGroup: ".lastGroups"
	dom= {}
	catchDom= ()->
		dom.dieta = $(st.dieta)
		dom.plan514 = $(st.plan514)
		dom.tips = $(st.tips)
		dom.launch= $(st.launch)
		dom.tmpl= _.template $(st.tmpl).html()
		dom.fancy= $(st.fancy)
		dom.tipsFancy= $(st.tipsFancy)
		dom.lastGroup= $(st.lastGroup)
	bindEvents= ()->
		dom.fancy.fancybox
			"afterShow": ()->
				$(".fancybox-skin").addClass("fancyArrows")
		dom.tipsFancy.fancybox
			"afterShow": ()->
				$(".fancybox-skin").addClass("fancyArrows")
		dom.lastGroup.fancybox
			"afterShow": ()->
				$(".fancybox-skin").addClass("fancyArrows")
		dom.dieta.on "click", ()->
			dom.fancy.eq(0).trigger("click")
		dom.launch.on "click", ()->
			dom.tipsFancy.eq(0).trigger("click")
		dom.tips.on "click", ()->
			dom.lastGroup.eq(0).trigger("click")
	init: ()->
		catchDom()
		bindEvents()
), ["libs/plugins/jqFancybox.js","libs/plugins/jqUnderscore.js"]
