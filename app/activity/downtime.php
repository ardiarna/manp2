<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Downtime Asset</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

  <link rel="stylesheet" type="text/css" href="../../assets/libs/dhtmlx/dhtmlx.css"/>
  <link rel="stylesheet" type="text/css" href="../../assets/fonts/font_roboto/roboto.css"/>
  <link rel="stylesheet" type="text/css" href="../../assets/fonts/font_awesome/css/font-awesome.min.css"/>
  <style>
    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
      margin: 0px;
      background-color: #EBEBEB;
    }

    div.dhxform_item_label_left.button_width div.dhxform_btn_txt {
      padding-left: 0px;
      padding-right: 0px;
      margin: 0px 0px 0px 0px;
    }
	
	.btn-success > .dhxform_btn {
      background-color: #4cae4c !important;
    }
    .btn-danger > .dhxform_btn {
      background-color: #761c19 !important;;
    }

  </style>

  <script src="../../assets/libs/dhtmlx/dhtmlx.js"></script>
  <!--<script src="../../assets/libs/axios/axios.min.js"></script>-->
  <script src="../../assets/libs/moment/moment-with-locales.min.js"></script>
  <script src="../../assets/libs/pdfmake/pdfmake.min.js"></script>
  <script src="../../assets/libs/pdfmake/vfs_fonts.js"></script>
  <!--<script src="../../assets/libs/js-cookie/js.cookie.min.js"></script>-->
  <script src="../../assets/libs/lodash/lodash.min.js"></script>
  <script src="../../assets/js/date-utils.js"></script>
  <script src="../../assets/js/grid-utils.js"></script>
  <script src="../../assets/js/util.js"></script>
  <script>
	const FRM='<?= $pageName ?>';
	const DEFAULT_MESSAGE_BOX_EXPIRE_MS = 5000;
    const TR = gridUtils.styles.TEXT_RIGHT_ALIGN;
	const TL = gridUtils.styles.TEXT_LEFT_ALIGN;
	const TC = gridUtils.styles.TEXT_CENTER_ALIGN;
	const TEXT_BOLD = gridUtils.styles.TEXT_BOLD;
	const HEADER_TEXT_FILTER = gridUtils.headerFilters.TEXT;
	const HEADER_NUMERIC_FILTER = gridUtils.headerFilters.NUMERIC;
	const COLUMN_SPAN = gridUtils.spans.COLUMN;
	const STYLES = gridUtils.styles;
			  
	function doOnLoad() {
		const rootLayout = new dhtmlXLayoutObject({
			parent: document.body,
			pattern: '2E',
			cells: [
				{id: "a", text: "Downtime Asset", header: true,height:80},
				{id: "b", text: "",header: false},
			]  
		});
			
		rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();
		const toolbarConfig = {
			iconset: 'awesome',
			items: [
				{
					type: 'button',
					id: 'baru',
					text: 'Add',
					img: 'fa fa-plus',
					imgdis: 'fa fa-plus'
				},
				{
					type: 'button',
					id: 'ubah',
					text: 'Edit',
					img: 'fa fa-edit',
					imgdis: 'fa fa-edit',
				},
				{type: 'separator'},
				{
					type: 'button',
					id: 'del',
					text: 'Delete',
					img: 'fa fa-times',
					imgdis: 'fa fa-times',
				},
				{type: 'spacer'},
				{type: 'text', id: 'timestamp', text: ''}
			]
		};
		const toolbar = rootLayout.cells("b").attachToolbar(toolbarConfig);	

		toolbar.attachEvent('onClick', itemId => {
			if(itemId === 'baru') {
				openRequestDetails(windows, {mode: 'create' });
			} else if(itemId === 'ubah') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					return;
			  	}
				openRequestDetails(windows, {
					mode: 'ubah', 
					id: selectedOrderId
				});
			} else if(itemId === 'del') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih yang ingin dihapus');
					return;
			  	}
				dhtmlx.confirm({
					title: 'Delete',
					type: 'confirm-error',
					text: 'Apakah Anda yakin ingin menghapus ?',
					callback: confirmed => {
						if (confirmed) {
							dhx.ajax.post(FRM+"?mode=delete", "kd="+selectedOrderId, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Hapus",
										text: "Data telah dihapus"
									});
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
								} else {
									dhtmlx.alert({
										title: "Info Hapus",
										type:"alert-warning",
										text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
									});
								}	
							});		
				  		}
					}
			  	});
			}
		});

		const formSearch = [
			{type: "settings", position: "label-left",labelWidth: 70,inputWidth: 160},
			{
				type: 'calendar',
				offsetLeft: 20,
				name: 'from_date',
				label: 'From',
				enableTodayButton: true,
				required: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-01') ?>'
			},
			{ type: 'newcolumn' },
			{
				type: 'calendar',
				offsetLeft: 20,
				name: 'to_date',
				label: 'To',
				enableTodayButton: true,
				required: true,
				readonly: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-d') ?>'
			},
			{type:"newcolumn"},
			{type: "button",offsetLeft:30, name: "search",value: "Get Data"},
		];	

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	grid_reff.clearAll();
	    		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

		grid_reff = rootLayout.cells("b").attachGrid();
       	grid_reff.setHeader("No.,Downtime#,Date,Asset#,Asset Name,Minutes Downtime,Description,Personnel");
        grid_reff.setColumnIds('no,dt_code,tanggal,amm_code,amm_desc,dt_value,dt_desc,dt_personil');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,ron,rotxt,rotxt');
        grid_reff.setInitWidths("40,120,100,100,300,90,*,150");
        grid_reff.setColumnMinWidth("40,120,100,100,300,90,250,150");
        grid_reff.setColAlign("center,center,center,center,left,right,left,left");
        grid_reff.setColSorting("na,str,str,str,str,int,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 600, 340);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `Downtime Asset : ${options.id}` : 'New Downtime Asset';  
	    win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		win.attachEvent('onClose', window => {
			if (!['view','del'].includes(MODE)) {
				if (window.skipWindowCloseEvent) {
					return true;
			  	}
			  	dhtmlx.confirm({
					title: MODE === 'ubah' ? 'Batalkan Perubahan' : 'Batalkan Pembuatan',
					type: 'confirm-warning',
					text: 'Apakah Anda yakin ingin membatalkan? Semua perubahan akan hilang.',
					callback: confirmed => {
						if (confirmed) {
							window.skipWindowCloseEvent = true;
							window.close();
				  		}
					}
			  	});
				return false;
			} else {
			  return true;
			}
		});
		hideItem = false;
		if (['create'].includes(MODE)) {
			hideItem = true;
		}
		const buttons = [
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	if (['create', 'ubah'].includes(MODE)) {
			buttons.unshift({ type: 'newcolumn' });
			buttons.unshift({type: "button", name: "save",value: "Save", className: 'btn-success'});
		}
		var forminput = [
			{type: "settings", position: "label-left", labelWidth: 150, inputWidth: 150},
			{type:"input", name: "dtcode", label:"Downtime# : ", inputWidth: 150, readonly:true, hidden:hideItem},
			{type:"calendar", name: "tanggal", button: "calendar_icon",readonly:true, label:"Date :",calendarPosition: "right",inputWidth: 150, required:true},
			{type: "block",width: 550,blockOffset: 0,list:[
				{
					type: 'template',
					label:"Asset : ",
					name: 'reqbr',
					format: () => '<a href="javascript:void(0);" onclick="showAssetWindow(windows,entryForm,[\'assetcode\',\'assetname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input", name: "assetname", inputWidth: 200,readonly:true},
				{type:"newcolumn"},
				{type:"input",name: "assetcode",offsetLeft:0,inputWidth: 100,readonly:true, required:true},
				{type:"newcolumn"},
				{
					type: 'template',
					label: '',
					name: 'delbr',
					format: () => '<a href="javascript:void(0);" onclick="removeAsset();"><i class="fa fa-times fa-2x"></i></a>', 
					inputWidth: 25
				}
			]},
			{type:"input", label: "Minutes of Downtime :", name: "dtvalue", inputWidth: 100, required:true},
			{type:"input", label: "Description :", name: "dtdesc", inputWidth: 330, rows:1, required:true},
			{type: "block",width: 550,blockOffset: 0,list:[
				{
					type: 'template',
					label:"Personnel : ",
					name: 'personilbr',
					format: () => '<a href="javascript:void(0);" onclick="showOperatorWindow(windows,entryForm,[\'dtpersonil\',\'personilname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input", name: "personilname", inputWidth: 200,readonly:true},
				{type:"newcolumn"},
				{type:"input",name: "dtpersonil",offsetLeft:0,inputWidth: 100,readonly:true, required:true},
				{type:"newcolumn"},
				{
					type: 'template',
					label: '',
					name: 'delbr',
					format: () => '<a href="javascript:void(0);" onclick="removePersonil();"><i class="fa fa-times fa-2x"></i></a>', 
					inputWidth: 25
				}
			]},
			{type: "block", inputWidth: "auto", id: "form_cell_c", list: buttons}
		];
			
		const dhxLayout = new dhtmlXLayoutObject({
			parent: win,
			pattern: "2E",
			cells: [
				{id: "a", text: "",header: false},
				{id: "b", text: "", header: false},
			]
		});	
		dhxLayout.cells("b").setHeight(50);
		dhxLayout.cells("b").fixSize(true, true);	

		entryForm = dhxLayout.cells("a").attachForm(forminput);
		entryForm.setFontSize("12px");
		
		dhxLayout.cells("b").attachObject("form_cell_c");

		entryForm.attachEvent("onKeyUp",function(inp, ev, id){
			if(id == "dtvalue"){
				value = entryForm.getItemValue(id);
				if (/([^0123456789]|)/g.test(value)) { 
			        entryForm.setItemValue(id, value.replace(/([^0123456789])/g, ''));
			    }
			}
		});

		entryForm.attachEvent('onButtonClick', btnName => {
			switch (btnName) {
				case 'close': {
					win.close();
					break;
				}	
				case 'save': {
					if(!entryForm.validate()) {
						dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
					} else {
						entryForm.send(FRM+"?mode=save&stat="+MODE, "post", function(loader, response){
							if(response.trim()=="OK"){
								dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"
								});
								win.skipWindowCloseEvent = true;
								win.close();
								grid_reff.clearAll();
								grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
							} else {
								dhtmlx.alert({
									title: compname,
									type:"alert-warning",
									text:response
								});
							}
						});					
					}
					break;
				}
				case 'delete': {
					entryForm.send(FRM+"?mode=delete", "post", function(loader, response){
						if(response.trim()=="OK"){
							dhtmlx.alert({
								title: compname,
								text:" Data telah dihapus"
							});
							win.skipWindowCloseEvent = true;
							win.close();
							grid_reff.clearAll();
							grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
						} else {
							dhtmlx.alert({
								title: compname,
								type:"alert-warning",
								text:response
							});
						}
					});
					break;
				}	
			}
		});

		if(MODE=="ubah"){
			entryForm.load(FRM+"?mode=load&kd="+options.id);
		};
	}

	function removeAsset() {
		entryForm.setItemValue('assetcode', '');
		entryForm.setItemValue('assetname', '');
	}
	function removePersonil() {
		entryForm.setItemValue('dtpersonil', '');
		entryForm.setItemValue('personilname', '');
	}
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
