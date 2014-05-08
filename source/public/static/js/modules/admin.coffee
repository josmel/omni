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
), ["libs/plugins/jqDataTable.js","data/datatable.js"]
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
 # @Module: AddBanner
 # @Description: Modulo para agregar banners
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "addBanner", ((Sb) ->
	st=
		sort: "#sortBanner"
		btnClose: ".ic-close"
		notBanner: ".nd-banner"
		ctnBanner: ".row-well"
	dom= {}
	tmpl= null
	catchDom= ()->
		dom.sort= $(st.sort)
		dom.notBanner= $(st.notBanner)
		dom.ctnBanner= $(st.ctnBanner)
	bindEvents= ()->
		$.jqFile
			"nameFile": "imagen"
			"routeFile": "/banner/banner-image"
			"btnFile": ".ctn-file .btn-file"
			"beforeCharge": ()->
				utils.loader dom.ctnBanner,true
			"success": successFile
			"error": (state,msg)->
				utils.loader dom.ctnBanner,false
				echo msg
		dom.sort.sortable
			"axis": "y"
			"containment": "parent"
			"tolerance": "pointer"
			"delay": 200
		dom.sort.find("li").each((index,value)->
			$(value).find(st.btnClose).on "click",evtClose
		)
	successFile= (json)->
		if json.state
			if dom.notBanner.is(":visible")
				dom.notBanner.slideUp 600
			element= tmpl(json).replace(/[\n\r]/g, "")
			element= $(element)
			element.css "display","none"
			dom.sort.append element
			element.find(st.btnClose).on "click",evtClose
			element.slideDown 600,()->
				utils.loader dom.ctnBanner,false
		else
			utils.loader dom.ctnBanner,false
			echo json.msg
	evtClose= (e)->
		e.preventDefault()
		parent= $(this).parent()
		parent.css "border","none"
		if dom.sort.find("li").length is 1
			dom.notBanner.slideDown 600
		parent.slideUp 600,()->
			$(this).remove()
	init: (oParams) ->
		tmpl= _.template $("#tplBanner").html()	
		catchDom()
		bindEvents()
), ["libs/plugins/jqFile.js","libs/plugins/jqUI.js","libs/plugins/jqSortable.js","libs/plugins/jqUnderscore.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: selBanner
 # @Description: Modulo para seleccionar un banner determinado
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "selBanner", ((Sb) ->
	st=
		slct: "#type"
	dom= {}
	catchDom= ()->
		dom.slct= $(st.slct)
	bindEvents= ()->
		$this= null
		dom.slct.on "change",(e)->
			$this= $(this)
			location.href= yOSON.bannerType+$this.find("option:selected").val()
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: colorPick
 # @Description: Modulo para mostrar un color picker
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "colorPick", ((Sb) ->
	init: (oParams) ->
), ["libs/plugins/jqColor.js"]
#-----------------------------------------------------------------------------------------------
 # @Module: typeArchive
 # @Description: Modulo tipo de archivo
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "typeArchive", ((Sb) ->
	st=
		slct: "#codtfile"
		check: "#vchestado"
		ctnAct: "#ctnActive"
		content: "#frm-article"
	dom= {}
	memoizeCheck= null
	catchDom= ()->
		dom.slct= $(st.slct)
		dom.check= $(st.check)
		dom.ctnAct= $(st.ctnAct)
		dom.content= $(st.content)
	bindEvents= ()->
		condEdit= yOSON.action is "/file/new"
		dom.slct.on "change",()->
			$this= $(this)
			valSlct= $this.find("option:selected").val()
			utils.loader dom.content,true
			$.ajax
				"url": "/file/validar-file"
				"data":
					"codtfile": valSlct
				"success": (json)->
					utils.loader dom.content,false
					if json.state is 1
						if json.flag is "inactivo"
							if condEdit and yOSON.stFile is 1 and yOSON.codFile is valSlct
								utils.block dom.ctnAct,false
								dom.check[0].checked= memoizeCheck
							else
								memoizeCheck= dom.check.is ":checked"
								dom.check[0].checked= false
								utils.block dom.ctnAct,true
								echo "El tipo seleccionado, solo permite tener como máximo 3 archivos activos asociados.",10000
						else
							utils.block dom.ctnAct,false
							dom.check[0].checked= memoizeCheck
					else
						echo json.msg
		memoizeCheck= dom.check.is ":checked"
		dom.slct.trigger "change"
	init: (oParams) ->
		catchDom()
		bindEvents()
)
#-----------------------------------------------------------------------------------------------
 # @Module: tinymce
 # @Description: Modulo para implementar tinymce
#-----------------------------------------------------------------------------------------------
yOSON.AppCore.addModule "tinymce", ((Sb) ->
	st=
		txtarea: "#text"
	dom= {}
	catchDom= ()->
		dom.txtarea= $(st.txtarea)
	bindEvents= ()->
		tinyMCE.init
			selector: st.txtarea
			language: "es"
			menubar: false
			plugins: [
				"code",
				"paste"
			]
			toolbar: "undo redo | bold italic underline | pastetext | code"
			height: 214
			element_format: "html"
	init: (oParams) ->
		bindEvents()
), ["libs/plugins/tinymce/tinymce.min.js"]
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
), ["data/admin/require.js","libs/plugins/jqValidate.js"]
