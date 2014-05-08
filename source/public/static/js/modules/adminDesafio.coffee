#-----------------------------------------------------------------------------------------------
 # @Module: DataTable
 # @Description: Modulo DataTable
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "dataTable", ((Sb) ->
	init: (oParams) ->
		dataUrl= oParams.url
		opts=
			"bJQueryUI": false
			"bAutoWidth": false
			"sPaginationType": "full_numbers"
			"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>'
			"oLanguage":
				"sSearch": "Busqueda"
				"sLengthMenu": "<span>Mostrar registros por pagina</span> _MENU_"
				"sZeroRecords": "No hay resultados"
				"sInfo": "Mostrar _START_ a _END_ de _TOTAL_ registros"
				"sInfoEmpty": "Mostrar 0 a 0 de 0 records"
				"sInfoFiltered": "( _MAX_ registros en total)"
				"oPaginate":
					"sLast": "Última"
					"sFirst": "Primera"
					"sNext": ">"
					"sPrevious": "<"
			"bServerSide": true
			"sAjaxSource": dataUrl
		json= $.extend opts,yOSON.datable[oParams.table]
		window.instDataTable= $('#datatable').dataTable json
), ["libs/plugins/jqDataTable.js","data/desafio/datatable.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: ActionDel
 # @Description: Modulo para eliminar registros
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "actionDel", ((Sb) ->
	st=
		del: ".ico-delete"
		table: "#datatable"
	dom= {}
	catchDom= ()->
		dom.table= $(st.table)
	bindEvents= (qus)->
		$this= null
		url= ""
		id= ""
		answer= if typeof qus isnt "undefined" then qus else "¿Esta seguro que desea eliminar el item seleccionado?"
		dom.table.on "click",st.del,(e)->
			e.preventDefault()
			$this= $(this)
			if confirm(answer)
				url= $this.attr "href"
				id= $this.attr "data-id"
				parent= $this.parents "tr"
				hash= utils.loader parent,true,1
				$.ajax
					"url": url
					"data":
						"id": id
					"dataType": "JSON"
					"success": (json)->
						utils.loader $("#"+hash),false,1
						if json.msj is "ok"
							instDataTable.fnDraw()
	init: (oParams) ->
		catchDom()
		bindEvents(oParams.title)
), ["libs/plugins/jqDataTable.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: selectRow
 # @Description: Modulo para seleccionar filas
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "selectRow", ((Sb) ->
	st=
		rows: "#datatable tbody"
		btnMessage: ".sendMessage"
		btnAlert: ".sendAlert"
		btnActive: ".sendActive"
		check: ".chkAdmin"
		frmSendMessage: "#frmSendMessage"
		slctSem: "#week"
		slctCycle: "#cycle"
		tplMessage: "#tplMessage"
		ctnGeneral: "#wrapper"
	dom= {}
	window.arrSelect= []
	catchDom= ()->
		dom.rows= $(st.rows)
		dom.ctnGeneral= $(st.ctnGeneral)
		dom.btnMessage= $(st.btnMessage)
		dom.btnAlert= $(st.btnAlert)
		dom.btnActive= $(st.btnActive)
		dom.slctSem= $(st.slctSem)
		dom.slctCycle= $(st.slctCycle)
		dom.tplMessage= _.template $(st.tplMessage).html()
	bindEvents= ()->
		selectRow()
		chngSelect()
		sendMessage()
		sendActive()
		sendAlert()
	selectRow= ()->
		trTarget= null
		$this= null
		valSel= ""
		dom.rows.on "change",st.check, ()->
			$this= $(this)
			valSel= $this.val()
			trTarget= $this.parents "tr"
			window.arrSelect= _.filter window.arrSelect,(num)->
				return num isnt valSel
			if $(this).is(":checked")
				window.arrSelect.push valSel
			trTarget.toggleClass('row_selected')
	chngSelect= ()->
		dom.slctSem.on "change",()->
			location.href= "/challenge/?cycle="+$(st.slctCycle+" option:selected").val()+"&week="+$(this).val()
		dom.slctCycle.on "change",()->
			location.href= "/challenge/?cycle="+$(this).val()+"&week="+$(st.slctSem+" option:selected").val()
	sendMessage= ()->
		dom.btnMessage.on "click",()->
			if window.arrSelect.length
				frmMessage= dom.tplMessage({"ids":window.arrSelect.join(",")}).replace(/[\n\r]/g, "")
				$.fancybox(frmMessage,
					afterShow: ()->
						$(st.frmSendMessage).validate yOSON.require[st.frmSendMessage]
				)
			else
				echo "Debe seleccionar al menos un participante"
	sendAlert= ()->
		dom.btnAlert.on "click",()->
			if window.arrSelect.length
				if confirm("¿Desea enviar alertas a los usuarios seleccionados?")
					utils.loader dom.ctnGeneral,true
					$.ajax
						url: "/challenge/ajax-send-alert"
						data:
							"ids": window.arrSelect.join(",")
						success: (json)->
							if json.state is 1
								echo "Se envío las alertas a los usuarios seleccionados"
								clearChecks()
							else
								echo json.msg
							utils.loader dom.ctnGeneral,false
						error: (json)->
							echo "Ocurrió un error en el sistema inténtelo nuevamente"
							utils.loader dom.ctnGeneral,false
			else
				echo "Debe seleccionar al menos un participante"
	sendActive= ()->
		dom.btnActive.on "click",()->
			if window.arrSelect.length
				if confirm("¿Desea activar a los usuarios seleccionados?")
					utils.loader dom.ctnGeneral,true
					$.ajax
						url: "/challenge/reactive"
						data:
							"ids": window.arrSelect.join(",")
						success: (json)->
							if json.state is 1
								echo json.msg
								instDataTable.fnDraw()
								clearChecks()
							else
								echo json.msg
							utils.loader dom.ctnGeneral,false
						error: (json)->
							echo "Ocurrió un error en el sistema inténtelo nuevamente"
							utils.loader dom.ctnGeneral,false
			else
				echo "Debe seleccionar al menos un participante"
	clearChecks= ()->
		dom.rows.find("tr").removeClass "row_selected"
		window.arrSelect= []
		$(st.check).each (index,value)->
			value.checked= false
			$(value).removeAttr "checked"
	init: (oParams) ->
		catchDom()
		bindEvents()
), ["libs/plugins/jqDataTable.js","libs/plugins/jqUnderscore.js","libs/plugins/jqFancybox.js","data/desafio/require.js","libs/plugins/jqValidate.js"]
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
 # @Module: SliderJs
 # @Description: Modulo para Slider
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