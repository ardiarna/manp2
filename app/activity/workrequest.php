<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Work Request</title>
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
				{id: "a", text: "Request", header: true,height:80},
				{id: "b", text: "Detail PO",header: false},
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
					text: 'Cancel',
					img: 'fa fa-times',
					imgdis: 'fa fa-times',
				},
				{type: 'spacer'},
				{type: 'text', id: 'timestamp', text: ''}
			]
		};
		const toolbar = rootLayout.cells("b").attachToolbar(toolbarConfig);
		toolbar.disableItem('ubah');
        toolbar.disableItem('del');		

		toolbar.attachEvent('onClick', itemId => {
			if(itemId === 'baru') {
				openRequestDetails(windows, {mode: 'create' });
			} else if(itemId === 'ubah' || itemId === 'del') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					return;
			  	}
			  	const reqdate = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_date')).getValue();
			  	const reqbycode = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_request_by')).getValue();
			  	const reqbyname = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_request_byname')).getValue();
				const departemen = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_to_department')).getValue();
				const urgency = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_urgency')).getValue();
				const duerequest = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_due')).getValue();
				const note = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_desc')).getValue();
				const assetcode = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_asset')).getValue();
			  	const assetname = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_assetname')).getValue();
			  	
				openRequestDetails(windows, {
					mode: itemId, 
					id: selectedOrderId,
					reqdate, reqbycode, reqbyname, departemen, urgency, duerequest, note, assetcode, assetname
				});
			} else if(itemId === 'slip') {
			} else if(itemId === 'export_csv') {
			} else if(itemId === 'export_pdf') {
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
       	grid_reff.setHeader("No.,Request#,Date,Urgency,Description,Due Date,,,,Request By,To Department,Approve Status,Reason Reject,Approve By,App. Date", null,[TC,TL,TC,TL,TL,TC,TL,TL,TL,TL,TL,TL,TL,TL,TC]);
        grid_reff.setColumnIds('no,wr_code,wr_date,wr_urgency,wr_desc,wr_due,wr_asset,wr_assetname,wr_request_by,wr_request_byname,wr_to_department,wr_approve_status,wr_reason_reject,wr_approve_by,wr_approve_date');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,130,130,80,220,80,0,0,0,100,100,100,150,100,80");
        grid_reff.setColAlign("center,left,center,left,left,center,left,left,left,left,left,left,left,left,center");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        grid_reff.enableSmartRendering(true, 100);
        grid_reff.attachEvent('onCellChanged', (rowId, colIdx, newValue) => {
	        if (colIdx === grid_reff.getColIndexById('wr_approve_status')) {
				switch (newValue) {
				case 'Waiting Approval':
					grid_reff.setRowColor(rowId, 'yellow');
					break;
				case 'Approve':
					grid_reff.setRowColor(rowId, 'palegreen');
					break;
				case 'Reject':
					grid_reff.setRowColor(rowId, 'lightpink');
					break;
				default:
					grid_reff.setRowColor(rowId, 'white');
					break;
				}
			}
      	});	
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
		grid_reff.attachEvent('onRowSelect', rowId => {
	        const app_status = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_approve_status')).getValue();
	        if (app_status === 'Waiting Approval') {
	          toolbar.enableItem('ubah');
	          toolbar.enableItem('del');
	        } else {
	          toolbar.disableItem('ubah');
	          toolbar.disableItem('del');
	        }
	    });
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 530, 280);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `Work Request : ${options.id}` : 'Work Request Baru';
		const URGENCY_OPTIONS = {'Normal': 'Normal', 'Emergency': 'Emergency', 'Urgent': 'Urgent'};
		const urgencyOptions = Object.keys(URGENCY_OPTIONS).map((urgency, idx) => ({
	    	text: URGENCY_OPTIONS[urgency],
	        value: urgency,
	        selected: options.urgency ? urgency === options.urgency : idx === 0
	    }));
	    const DEPARTEMEN_OPTIONS = {'Maintenance': 'Maintenance', 'Utility': 'Utility', 'Umum': 'Umum'};
		const departemenOptions = Object.keys(DEPARTEMEN_OPTIONS).map((departemen, idx) => ({
	    	text: DEPARTEMEN_OPTIONS[departemen],
	        value: departemen,
	        selected: options.departemen ? departemen === options.departemen : idx === 0
	    }));    
	    win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		win.maximize();
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
		} else if (MODE === 'del') {
			buttons.unshift({ type: 'newcolumn' });
			buttons.unshift({type: "button", name: "delete",value: "Delete", className: 'btn-danger'});
		}
		var forminput = [
			{type: "settings", position: "label-left", labelWidth: 110, inputWidth: 150},
			{type: "block",width: 800,blockOffset: 20,list:[
				{type:"input", name: "requestno", label:"Request# : ", inputWidth: 120, readonly:true, hidden:hideItem, value: options.id},
				{type:"input", name: "reqdate", label:"Date : ", inputWidth: 150, readonly:true, hidden:hideItem, value: options.reqdate},
			]},
			{type: "block", inputWidth: "auto", id: "form_cell_a", list:[	
				{type:"label",blockOffset: 0,label: "Detail :"},
				{type: "block",width: 500,blockOffset: 0,list:[
					{
						type: 'template',
						label:"Request By : ",
						name: 'reqbr',
						format: () => '<a href="javascript:void(0);" onclick="showOperatorWindow(windows,entryForm,[\'reqbycode\',\'reqbyname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input", name: "reqbyname", inputWidth: 200, required: true, readonly:true, value: options.reqbyname},
					{type:"newcolumn"},
					{type:"input", name: "reqbycode", offsetLeft:0, inputWidth: 100, readonly:true, style:"text-transform: uppercase;", value: options.reqbycode},
				]},
				{type:"combo", name: "departemen",label: "To Department :", inputWidth: 330, value: options.departemen, options: departemenOptions},
				{type:"combo", name: "urgency",label: "Urgency :", inputWidth: 330, value: options.urgency, options: urgencyOptions},
				{type:"calendar", name: "duerequest", button: "calendar_icon",readonly:true, label:"Due Date :",calendarPosition: "right",inputWidth: 150, value: options.duerequest},
				{type:"input", label: "Description : ", name: "note", inputWidth: 330, rows: 2, value: options.note},	
				{type: "block",width: 500,blockOffset: 0,list:[
					{
						type: 'template',
						label:"Asset : ",
						name: 'reqbr',
						format: () => '<a href="javascript:void(0);" onclick="showAssetWindow(windows,entryForm,[\'assetcode\',\'assetname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input", name: "assetname", inputWidth: 200,readonly:true, value: options.assetname},
					{type:"newcolumn"},
					{type:"input",name: "assetcode",offsetLeft:0,inputWidth: 100,readonly:true,style:"text-transform: uppercase;", value: options.assetcode},
					{type:"newcolumn"},
					{
						type: 'template',
						label: '',
						name: 'delbr',
						format: () => '<a href="javascript:void(0);" onclick="removeAsset();"><i class="fa fa-times fa-2x"></i></a>', 
						inputWidth: 25
					}
				]},
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
		dhxLayout.cells("a").setHeight(150);
		dhxLayout.cells("a").fixSize(true, true);	
		dhxLayout.cells("b").setHeight(50);
		dhxLayout.cells("b").fixSize(true, true);	

		entryForm = dhxLayout.cells("a").attachForm(forminput);
		entryForm.setFontSize("12px");
		
		dhxLayout.cells("b").attachObject("form_cell_c");

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
