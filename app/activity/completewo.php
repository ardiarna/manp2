<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Completed WO</title>
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
			pattern: '3E',
			cells: [
				{id: "a", text: "Completed Work Order", header: true, height:80},
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
				name: 'wostatus',
				label: 'Status',
				required: true,
				offsetLeft: 20,
				inputWidth: 130,
				options: [
        			{text: "All", value: "ALL"},
        			{text: "Open", value: "O"},
        			{text: "Scheduled", value: "S", selected: true},
        			{text: "Completed", value: "C"},
    			]
			},
			{type:"newcolumn"},
			{type: "button",offsetLeft:30, name: "search",value: "Get Data"},
			{type:"newcolumn"},
			{type: "button",offsetLeft:30, name: "export",value: "Export"},
		];	

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	grid_reff.clearAll();
	    		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&wostatus="+myFormSearch.getItemValue('wostatus'), "xml");		
	        } else if (id === 'export') {

	        	opsi = "width=900,height=600,screenX=500,toolbars=1,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable='no'";

	        	window.open(FRM+"?mode=export&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&wostatus="+myFormSearch.getItemValue('wostatus'),"",opsi);    		
	        }
	    });
		const formApprove = [
			{type: "settings", position: "label-left",labelWidth: 70,inputWidth: 160},
			{type:"input", name: "wono", label:"", inputWidth: 120, readonly:true, hidden:true},
			{type: "newcolumn"},
			{type: "button", offsetLeft:30, name: "complete", value: "Complete", className: 'btn-success'},
		];

		myFormApprove = rootLayout.cells("c").attachForm(formApprove);
		myFormApprove.disableItem('complete');
		myFormApprove.attachEvent('onButtonClick', id => {
	        switch (id) {
				case 'complete': {
					const selectedOrderId = grid_reff.getSelectedRowId();
				 	if (!selectedOrderId) {
						return;
				  	}
				  	
				  	openRequestDetails(windows, {
						id: selectedOrderId
					});
					break;
				}	
			}
	    });

		grid_reff = rootLayout.cells("b").attachGrid();
        grid_reff.setHeader("No.,WO#,Date,Status,Description,Due Date,Duration Est.,Duration Real,Scheduled Est.,Start Date Real,End Date Real,Approve By,Complete By,Compl. Date", null,[TC,TL,TC,TL,TL,TC,TL,TL,TC,TC,TC,TL,TL,TC]);
        grid_reff.setColumnIds('no,wo_code,wo_date,wo_status,wo_desc,wo_due,wo_dur_lbl,wo_real_dur_lbl,wo_scheduled,wo_real_scheduled_start,wo_real_scheduled_end,wr_approve_by,wo_complete_by,wo_complete_date');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,110,130,80,250,80,90,90,130,130,130,80,90,80");
        grid_reff.setColAlign("center,left,center,left,left,center,left,leff,center,center,center,left,left,center");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        // grid_reff.enableSmartRendering(true, 100);
        grid_reff.attachEvent('onCellChanged', (rowId, colIdx, newValue) => {
	        if (colIdx === grid_reff.getColIndexById('wo_status')) {
				switch (newValue) {
				case 'Open':
					grid_reff.setRowColor(rowId, 'yellow');
					break;
				case 'Scheduled':
					grid_reff.setRowColor(rowId, 'gold');
					break;
				case 'Completed':
					grid_reff.setRowColor(rowId, 'palegreen');
					break;
				default:
					grid_reff.setRowColor(rowId, 'white');
					break;
				}
			}
      	});
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true)+"&wostatus="+myFormSearch.getItemValue('wostatus'), "xml");
		grid_reff.attachEvent('onRowSelect', rowId => {
	        const wo_status = grid_reff.cells(rowId, grid_reff.getColIndexById('wo_status')).getValue();
	        if (wo_status === 'Scheduled') {
	         	myFormApprove.enableItem('complete');
	        } else {
	        	myFormApprove.disableItem('complete');
	        }
	    });
	}

	function openRequestDetails(windows, options) {
		const win = windows.createWindow('dwg_request_details', 0, 0, 600, 560);
		const winTitle = `Complete Work Order : ${options.id}`;
		const DURATIONSAT_OPTIONS = {'Minutes': 'Minutes', 'Hours': 'Hours', 'Days': 'Days'};
		const durationsatOptions = Object.keys(DURATIONSAT_OPTIONS).map((durationsat, idx) => ({
	    	text: DURATIONSAT_OPTIONS[durationsat],
	        value: durationsat,
	        selected: options.durationsat ? durationsat === options.durationsat : idx === 0
	    }));
		win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		win.maximize();
		const buttons = [
			{type: "button", name: "save",value: "OK", className: 'btn-success'},
			{type: 'newcolumn' },
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	var forminput = [
			{type: "settings", position: "label-left", labelWidth: 170, inputWidth: 150},
			{type: "block",width: 530,blockOffset: 0,list:[
				{type:"input", name: "cycleintv", label:"", readonly:true, hidden: true},
				{type:"input", name: "cycletype", label:"", readonly:true, hidden: true},
				{type:"input", name: "wono", label:"WO# : ", inputWidth: 120, readonly:true},
				{type:"input", name: "wodate", label:"WO Date : ", inputWidth: 150, readonly:true},
				{type:"input", label: "Description : ", name: "note", inputWidth: 300, rows: 1, readonly: true},
				{type: "block",width: 500,blockOffset: 0,list:[
					{type:"input", label: "Asset : ", name: "assetname", inputWidth: 200,readonly:true},
					{type:"newcolumn"},
					{type:"input",name: "assetcode",inputWidth: 100,readonly:true},
				]},
				{type: "block",width: 500,blockOffset: 0,list:[
					{type:"input", label: "Location : ",name: "namelocation",inputWidth: 200,readonly:true},
					{type:"newcolumn"},
					{type:"input", name: "kodelocation", inputWidth: 100,readonly:true},
				]},
				{type: "block",width: 500,blockOffset: 0,list:[
					{type:"input", label: "Maintenance : ",name: "namemaintenance",inputWidth: 200,readonly:true},
					{type:"newcolumn"},
					{type:"input", name: "kodemaintenance", inputWidth: 100,readonly:true},
				]},	
				{type: "block",width: 400,blockOffset: 0, offsetTop:0, list:[
					{type:"input", name: "duration", label:"Duration Est. : ", inputWidth: 100, readonly: true},
					{type:"newcolumn"},
					{type:"combo", name: "durationsat", inputWidth: 100, options: durationsatOptions, disabled: true}
				]},
				{type:"input", name: "schdate", readonly:true, label:"Scheduled Date Est. : ", inputWidth: 150},
				{type: "block",width: 400,blockOffset: 0,list:[
					{type:"label", label: "Downtime : "},
					{type:"newcolumn"},
					{type:"radio", name:"isdowntime", value:"Y", label:"Yes", position:"label-right", labelWidth:50, checked: true},
					{type:"newcolumn"},
					{type:"radio", name:"isdowntime", value:"N", label:"No", position:"label-right", labelWidth:50}
				]},
				{type: "block",width: 550,blockOffset: 0, offsetTop:0, list:[
					{type:"combo", name: "reasonnodowntime", label:"Reason no downtime : ", inputWidth: 200, options: [
						{text: "",value:""},
						{text: "Asset tidak dimatikan",value:"Asset tidak dimatikan"},
						{text: "Bersamaan dengan production",value:"Bersamaan dengan production"},
						{text: "Bersamaan dengan WO lain",value:"Inactive"}
					], hidden: true},
					{type:"newcolumn"},
					{type:"input", name: "reasonnodowntime2", inputWidth: 150, hidden: true}
				]},
				{type: "block",width: 400,blockOffset: 0, offsetTop:0, list:[
					{type:"input", name: "realduration", label:"Duration Real : ", inputWidth: 100, required: true},
					{type:"newcolumn"},
					{type:"combo", name: "realdurationsat", inputWidth: 100, required: true, options: durationsatOptions}
				]},
				{type:"calendar", name: "realschstart", button: "calendar_icon",readonly:true, label:"Start Date Real : ", calendarPosition: "right",inputWidth: 150, dateFormat: "%Y-%m-%d %H:%i:%s", required: true},
				{type:"calendar", name: "realschend", button: "calendar_icon",readonly:true, label:"End Date Real : ", calendarPosition: "right",inputWidth: 150, dateFormat: "%Y-%m-%d %H:%i:%s", required: true},
				{type:"input", label: "Note : ", name: "notewo", inputWidth: 300, rows: 1},
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
			
		const realSchStart = entryForm.getCalendar("realschstart");
		realSchStart.showTime();

		const realSchEnd = entryForm.getCalendar("realschend");
		realSchEnd.showTime();

		entryForm.attachEvent("onKeyUp",function(inp, ev, id){
			if(id == "realduration"){
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
						entryForm.send(FRM+"?mode=complete", "post", function(loader, response){
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

		entryForm.attachEvent('onChange', (name, value, state) => {
			switch (name) {
				case 'isdowntime':
					if(value == 'N') {
						entryForm.showItem('reasonnodowntime');
					} else {
						entryForm.setItemValue('reasonnodowntime', ''),
						entryForm.setItemValue('reasonnodowntime2', ''),
						entryForm.hideItem('reasonnodowntime');
						entryForm.hideItem('reasonnodowntime2');
					}
					break;
				case 'reasonnodowntime' :
					if(value == 'Asset tidak dimatikan') {
						entryForm.setItemValue('reasonnodowntime2', ''),
						entryForm.hideItem('reasonnodowntime2');
					} else {
						entryForm.showItem('reasonnodowntime2');
					}
					break;

			}
		});

		entryForm.load(FRM+"?mode=load&kd="+options.id);
	}

  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
