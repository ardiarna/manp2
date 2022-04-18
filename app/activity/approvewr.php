<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Request Approval</title>
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
  <script src="../../assets/js/dhtmlx.prompt.js"></script>
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
			pattern: '3E',
			cells: [
				{id: "a", text: "Request Approval", header: true, height:80},
				{id: "b", text: "", header: false},
				{id: "c", text: "", header: false, height:60},
			]  
		});
			
		rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();
		
		const formSearch = [
			{type: "settings", position: "label-left",labelWidth: 50,inputWidth: 160},
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
			{type: 'newcolumn'},
			{
				type: 'combo',
				name: 'approvestatus',
				label: 'Status',
				required: true,
				offsetLeft: 20,
				inputWidth: 130,
				options: [
        			{text: "All", value: "ALL"},
        			{text: "Approve", value: "A"},
        			{text: "Reject", value: "R"},
        			{text: "Waiting Approval", value: "W", selected: true},
    			]
			},
			{type:"newcolumn"},
			{type: "button",offsetLeft:30, name: "search",value: "Get Data"},
		];	

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	grid_reff.clearAll();
	    		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&approvestatus="+myFormSearch.getItemValue('approvestatus'), "xml");		
	        }
	    });
		const formApprove = [
			{type: "settings", position: "label-left",labelWidth: 70,inputWidth: 160},
			{type:"input", name: "requestno", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type:"input", name: "urgency", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type:"input", name: "duedate", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type:"input", name: "note", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type:"input", name: "assetcode", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type: "button", offsetLeft:30, name: "approve", value: "Approve", className: 'btn-success'},
			{type: "newcolumn"},
			{type: "button", offsetLeft:30, name: "reject", value: "Reject", className: 'btn-danger'},
		];

		myFormApprove = rootLayout.cells("c").attachForm(formApprove);
		myFormApprove.disableItem('approve');
		myFormApprove.disableItem('reject');
		myFormApprove.attachEvent('onButtonClick', id => {
	        switch (id) {
				case 'approve': {
					myFormApprove.send(FRM+"?mode=approve", "post", function(loader, response){
						if(response.trim()=="OK"){
							dhtmlx.alert({
								title: compname,
								text:" Work request dengan kode "+myFormApprove.getItemValue('requestno')+" berhasil di-approve"
							});
							myFormApprove.setItemValue('requestno', '');
							myFormApprove.setItemValue('urgency', '');
							myFormApprove.setItemValue('duedate', '');
							myFormApprove.setItemValue('note', '');
							myFormApprove.setItemValue('assetcode', '');
							myFormApprove.disableItem('approve');
							myFormApprove.disableItem('reject');
							grid_reff.clearAll();
							grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&approvestatus="+myFormSearch.getItemValue('approvestatus'), "xml");
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
				case 'reject': {
					const selectedOrderId = grid_reff.getSelectedRowId();
				 	if (!selectedOrderId) {
						return;
				  	}
				  	const reqdate = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wr_date')).getValue();

					openRequestDetails(windows, {
						id: selectedOrderId,
						reqdate
					});
				}	
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
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&approvestatus="+myFormSearch.getItemValue('approvestatus'), "xml");
		grid_reff.attachEvent('onRowSelect', rowId => {
	        const app_status = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_approve_status')).getValue();
	        if (app_status === 'Waiting Approval') {
	        	const urgency = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_urgency')).getValue();
	        	const duedate = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_due')).getValue();
	        	const note = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_desc')).getValue();
	        	const assetcode = grid_reff.cells(rowId, grid_reff.getColIndexById('wr_asset')).getValue();
	        	myFormApprove.setItemValue('requestno', rowId);
	        	myFormApprove.setItemValue('urgency', urgency);
	    		myFormApprove.setItemValue('duedate', duedate);    	
	        	myFormApprove.setItemValue('note', note);    	
	        	myFormApprove.setItemValue('assetcode', assetcode);    	
	        	myFormApprove.enableItem('approve');
				myFormApprove.enableItem('reject');
	        } else {
	          	myFormApprove.disableItem('approve');
				myFormApprove.disableItem('reject');
	        }
	    });
	}

	function openRequestDetails(windows, options) {
		const win = windows.createWindow('dwg_request_details', 0, 0, 490, 270);
		const winTitle = `Konfirmasi Reject Work Request : ${options.id}`;
		win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		// win.maximize();
		const buttons = [
			{type: "button", name: "save",value: "OK", className: 'btn-success'},
			{type: 'newcolumn' },
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	var forminput = [
			{type: "settings", position: "label-left", labelWidth: 100},
			{type: "block",width: 430,list:[
				{type:"input", name: "requestno", label:"Request# : ", inputWidth: 120, readonly:true, value: options.id},
				{type:"input", name: "reqdate", label:"Date : ", inputWidth: 150, readonly:true, value: options.reqdate},
				{type:"input", name: "reason", label: "Reason Reject : ", inputWidth: 300, rows: 3, value: options.reason}
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
						entryForm.send(FRM+"?mode=reject", "post", function(loader, response){
							if(response.trim()=="OK"){
								dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"
								});
								win.skipWindowCloseEvent = true;
								win.close();
								grid_reff.clearAll();
								grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&wostatus="+myFormSearch.getItemValue('wostatus'), "xml");
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
			}
		});
	}

  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
