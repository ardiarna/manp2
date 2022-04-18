<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>MR SPK List</title>
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
				{id: "a", text: "MR SPK List", header: true,height:80},
				{id: "b", text: "",header: false},
			]  
		});
			
		rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();
		// const toolbarConfig = {
		// 	iconset: 'awesome',
		// 	items: [
		// 		{
		// 			type: 'button',
		// 			id: 'baru',
		// 			text: 'Add',
		// 			img: 'fa fa-plus',
		// 			imgdis: 'fa fa-plus'
		// 		},
		// 		{
		// 			type: 'button',
		// 			id: 'ubah',
		// 			text: 'Edit',
		// 			img: 'fa fa-edit',
		// 			imgdis: 'fa fa-edit',
		// 		},
		// 		{type: 'separator'},
		// 		{
		// 			type: 'button',
		// 			id: 'del',
		// 			text: 'Delete',
		// 			img: 'fa fa-times',
		// 			imgdis: 'fa fa-times',
		// 		},
		// 		{type: 'spacer'},
		// 		{type: 'text', id: 'timestamp', text: ''}
		// 	]
		// };
		// const toolbar = rootLayout.cells("b").attachToolbar(toolbarConfig);	

		// toolbar.attachEvent('onClick', itemId => {
		// 	if(itemId === 'baru') {
		// 		openRequestDetails(windows, {mode: 'create' });
		// 	} else if(itemId === 'ubah') {
		// 		const selectedOrderId = grid_reff.getSelectedRowId();
		// 	 	if (!selectedOrderId) {
		// 			return;
		// 	  	}
		// 		openRequestDetails(windows, {
		// 			mode: 'ubah', 
		// 			id: selectedOrderId
		// 		});
		// 	} else if(itemId === 'del') {
		// 		const selectedOrderId = grid_reff.getSelectedRowId();
		// 	  	if (!selectedOrderId) {
		// 	  		alert('Anda belum memilih yang ingin dihapus');
		// 			return;
		// 	  	}
		// 		dhtmlx.confirm({
		// 			title: 'Delete',
		// 			type: 'confirm-error',
		// 			text: 'Apakah Anda yakin ingin menghapus ?',
		// 			callback: confirmed => {
		// 				if (confirmed) {
		// 					dhx.ajax.post(FRM+"?mode=delete", "kd="+selectedOrderId, function(resp){
	 //    						if(resp.xmlDoc.responseText == "OK"){
		// 							dhtmlx.alert({
		// 								title: "Info Hapus",
		// 								text: "Data telah dihapus"
		// 							});
		// 							grid_reff.clearAll();
		// 							grid_reff.load(FRM+"?mode=view", "xml");
		// 						} else {
		// 							dhtmlx.alert({
		// 								title: "Info Hapus",
		// 								type:"alert-warning",
		// 								text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
		// 							});
		// 						}	
		// 					});		
		// 		  		}
		// 			}
		// 	  	});
		// 	} 
		// });

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
       	grid_reff.setHeader("No.,MR SPK#,MR SPK Date,WO#,WO Date,Asset#,Asset Name,Asset Location,Description,WO Scheduled");
        grid_reff.setColumnIds('no,mr_code,mr_date,wo_code,wo_date,asset_code,asset_name,asset_location,wo_desc,wo_scheduled');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,110,130,110,130,80,200,150,*,130");
        grid_reff.setColumnMinWidth("40,110,130,110,130,80,200,150,200,130");
        grid_reff.setColAlign("center,center,center,center,center,center,left,left,left,center");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		// grid_reff.attachEvent('onRowDblClicked', rowId => {
  //       	openRequestDetails(windows, {mode: 'view', id: rowId});
  //   	});
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 670, 500);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `MR SPK : ${options.id}` : 'MR SPK Baru';  
	    win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		
		const dhxLayout = new dhtmlXLayoutObject({
			parent: win,
			pattern: "1C",
			cells: [
				{id: "a", text: "",header: false}
			]
		});

		const pGrid = dhxLayout.cells('a').attachGrid();
		pGrid.setHeader("Sparepart Code,Sparepart Name,Unit,Quantity");
		pGrid.attachHeader("#text_filter,#text_filter,#select_filter,#text_filter");
		pGrid.setColumnIds('item_code,item_name,unit,qty'); 
		pGrid.setColTypes("ro,ro,ro,ron"); 
		pGrid.setInitWidths("120,360,50,70");
		pGrid.setColAlign("center,left,left,right");
		pGrid.init();
		pGrid.load(FRM+"?mode=viewdetail&kd="+options.id, "xml");
	}

	function removeAsset() {
		entryForm.setItemValue('assetcode', '');
		entryForm.setItemValue('assetname', '');
	}
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
