<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Asset History</title>
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
		const HEADER_TEXT_FILTER = gridUtils.headerFilters.TEXT;
		const HEADER_NUMERIC_FILTER = gridUtils.headerFilters.NUMERIC;
		const COLUMN_SPAN = gridUtils.spans.COLUMN;
		const STYLES = gridUtils.styles;
		var grid_reff;
		
			  
function doOnLoad() {
	const rootLayout = new dhtmlXLayoutObject({
		parent: document.body,
		pattern: "2E",
		cells: [
			{id: "a", text: "Filter", height: 80, header: true },
			{id: "b", text: " ", header: false},
		]
	});

	windows = new dhtmlXWindows();
	
	const toolbarConfig = {
		iconset: 'awesome',
		items: [
			{
				type: 'button',
				id: 'print_all',
				text: 'Print All',
				img: 'fa fa-print',
				imgdis: 'fa fa-print'
			},
			{type: 'spacer'},
			{
				type: 'button',
				id: 'print',
				text: 'Print',
				img: 'fa fa-print',
				imgdis: 'fa fa-print'
			},
			{type: 'spacer'},
			{
				type: 'button',
				id: 'excel_all',
				text: 'Excel',
				img: 'fa fa-file-excel-o',
				imgdis: 'fa fa-file-excel-o'
			},
			{type: 'spacer'},
			{type: 'text', id: 'timestamp', text: ''}
		]
	};
		
	
	const formSearch = [
		{type: "settings", position: "label-left",labelWidth: 80,inputWidth: 160},
		{type: "combo", offsetLeft:20,label: "Location :",required: true,name:"location", connector: FRM+"?mode=cmblocation",inputWidth: 120},
		{ type: 'newcolumn' },
		{type: "combo", offsetLeft:20,label: "Sub Location :",required: true,name:"sublocation",inputWidth: 120},
		{type:"newcolumn"},
		{type: "combo", offsetLeft:20,label: "Group :",required: true,name:"group", connector: FRM+"?mode=cmbgroup",inputWidth: 120},
		{type:"newcolumn"},
		{type: "button",offsetLeft:30, name: "search",value: "Get Data"},
	];

	myFormSearch = rootLayout.cells("a").attachForm(formSearch);

	var cmbSublocation = myFormSearch.getCombo("sublocation");

	myFormSearch.attachEvent("onChange", function (name, value, state){
		if(name == 'location') {
			myFormSearch.setItemValue('sublocation', '');
			cmbSublocation.load(FRM+"?mode=cmbsublocation&kd="+value);
		}
	});
		
	const toolbar = rootLayout.cells("b").attachToolbar(toolbarConfig);		
	toolbar.attachEvent('onClick', itemId => {
		if(itemId === 'print_all') {
			openWinPrint(windows, 'all');
		} else if(itemId === 'print') {
			const selectedOrderId = grid_reff.getSelectedRowId();
		  	if (!selectedOrderId) {
		  		alert('Anda belum memilih asset');
				return;
		  	}
			openWinPrint(windows, selectedOrderId);
		} else if(itemId === 'excel_all') {
			openWinExcel(windows, 'all');
		} 
	});

	const grid_reff = rootLayout.cells("b").attachGrid();
    grid_reff.setHeader("No.,Asset#,Description,Location,Sub Location,Group,Category,Status", null, [TC,TC,TL,TL,TL,TL,TL,TL]);
    grid_reff.setColumnIds('no,amm_code,amm_desc,sl_desc,ssl_desc,sag_desc,sac_desc,amm_status');
    grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
    grid_reff.setInitWidths("50,100,*,120,120,120,120,120");
    grid_reff.setColAlign("center,center,left,left,left,left,left,left");
    grid_reff.setColSorting("int,str,str,str,str,str,str,str");
	grid_reff.attachHeader(",#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter");
	grid_reff.attachEvent("onXLS", function () {
      rootLayout.cells('a').progressOn();
    });
    grid_reff.attachEvent("onXLE", function () {
      rootLayout.cells('a').progressOff()
    });
    // grid_reff.enableSmartRendering(true, 100);	
	grid_reff.init();
	grid_reff.load(FRM+"?mode=view", "xml");
	grid_reff.attachEvent('onRowDblClicked', rowId => {
        openRequestDetails(windows, {id: rowId});
    });

	myFormSearch.attachEvent('onButtonClick', id => {
        if (id === 'search') {
        	grid_reff.clearAll();
    		grid_reff.load(FRM+"?mode=view&location="+myFormSearch.getItemValue('location')+"&sublocation="+myFormSearch.getItemValue('sublocation')+"&group="+myFormSearch.getItemValue('group'), "xml");		
        }
    });
}

function openRequestDetails(windows, options) {
	const win = windows.createWindow('dwg_request_details', 0, 0, 530, 200);
	const winTitle = `Asset History - ${options.id}`;
	win.centerOnScreen();
	win.setText(winTitle);
	win.button("park").hide();
	win.setModal(true);
	win.maximize();

	const completedLayout = new dhtmlXLayoutObject({
		parent: win,
		pattern: '2E',
		cells: [
			{id: "a", text: "", header: false, height:60},
			{id: "b", text: "", header: false},
		]  
	});

	completedLayout.cells("a").fixSize(true);

	completedGrid = setupGrid(completedLayout.cells("b"));

		
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

		myFormSearch = completedLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	completedGrid.clearAll();
	    		completedGrid.load(FRM+"?mode=viewwo&status=C&assetcode="+options.id+"&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

	completedGrid.load(FRM+"?mode=viewwo&status=C&assetcode="+options.id+"&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");

	completedGrid.attachEvent('onRowDblClicked', rowId => {
        printWO(windows, rowId);

    });

}

function setupGrid(cell) {
	const grid_reff = cell.attachGrid();
    grid_reff.setHeader("No.,WO#,Date,Request#,Source,Status,Urgency,Description,Asset,Due Date,Duration,Request By,Approve By,Scheduled,");
    grid_reff.setColumnIds('no,wo_code,wo_date,wr_code,wo_source,wo_status,wo_urgency,wo_desc,wo_asset_lbl,wo_due,wo_dur_lbl,wr_request_byname,wr_approve_by,wo_scheduled,wo_pic_type');
    grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
    grid_reff.setInitWidths("40,110,130,110,120,80,80,220,150,80,100,100,100,130,0");
    grid_reff.setColAlign("center,center,center,center,left,left,left,left,left,center,left,left,left,center,left");
    grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
	grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
	grid_reff.attachEvent("onXLS", function () {cell.progressOn();});
    grid_reff.attachEvent("onXLE", function () {cell.progressOff()});
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
	return grid_reff;
}

function openWinPrint(windows, id) {
	const win = windows.createWindow('w1', 0, 0, 400, 200);
	const winTitle = `Print - ${id}`;
	win.centerOnScreen();
	win.setText(winTitle);
	win.button("park").hide();
	win.setModal(true);

	const myLayout = new dhtmlXLayoutObject({
		parent: win,
		pattern: '1C',
		cells: [
			{id: "a", text: "", header: false},
		]  
	});
	
	const formSearch = [
		{type: "block",blockOffset: 0,list:[
			{type:"radio", name:"stat", value:"M", label:"Monthly", position:"label-right", labelWidth:50, checked: true},
			{type:"newcolumn"},
			{type:"radio", name:"stat", value:"P", label:"Period", position:"label-right", labelWidth:50}
		]},
		{type: "block",blockOffset: 0,offsetTop:10,name:"dvM", list:[
			{type:"combo", name: "bln", label: "", labelWidth: 140, inputWidth: 150, options: [
				{text: "January",value:"1"},
				{text: "February",value:"2"},
				{text: "March",value:"3"},
				{text: "April",value:"4"},
				{text: "May",value:"5"},
				{text: "June",value:"6"},
				{text: "July",value:"7"},
				{text: "August",value:"8"},
				{text: "September",value:"9"},
				{text: "October",value:"10"},
				{text: "November",value:"11"},
				{text: "December",value:"12"}
			]},
			{ type: 'newcolumn' },
			{type:"combo", name: "thn", label: "", labelWidth: 140, inputWidth: 150, options: [
				{text: "2016",value:"2016"},
				{text: "2017",value:"2017"},
				{text: "2018",value:"2018"},
				{text: "2019",value:"2019"},
				{text: "2020",value:"2020"},
				{text: "2021",value:"2021"},
				{text: "2022",value:"2022"},
				{text: "2023",value:"2023"},
				{text: "2024",value:"2024"},
				{text: "2025",value:"2025"},
				{text: "2026",value:"2026"},
				{text: "2027",value:"2027"},
				{text: "2028",value:"2028"},
				{text: "2029",value:"2029"},
				{text: "2030",value:"2030"}
			]},
		]},
		{type: "block",blockOffset: 0,offsetTop:10,name:"dvP",hidden:true, list:[
			{
				type: 'calendar',
				name: 'from_date',
				label: 'From',
				enableTodayButton: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-01') ?>'
			},
			{ type: 'newcolumn' },
			{
				type: 'calendar',
				name: 'to_date',
				label: '-',
				enableTodayButton: true,
				readonly: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-d') ?>'
			},
		]},
		{type: "button", name: "cetak", value: "Print", offsetTop:20},
	];

	myFormSearch = myLayout.cells("a").attachForm(formSearch);
	myFormSearch.attachEvent('onButtonClick', nama => {
        if (nama === 'cetak') {
        	var stat = myFormSearch.getItemValue("stat");
        	var period = "";
        	if(stat == 'M') {
        		period = myFormSearch.getItemValue("bln")+'@@'+myFormSearch.getItemValue("thn");
        	} else if(stat == 'P') {
        		period = myFormSearch.getItemValue("from_date", true)+'@@'+myFormSearch.getItemValue("to_date", true);
        	}
        	win.skipWindowCloseEvent = true;
			win.close();
        	printWO(windows, id, stat, period);
        }
    });

    myFormSearch.attachEvent('onChange', (name, value, state) => {
		switch (name) {
			case 'stat':
				if(value == 'P') {
					myFormSearch.hideItem('dvM');
					myFormSearch.showItem('dvP');
				} else {
					myFormSearch.hideItem('dvP');
					myFormSearch.showItem('dvM');
				}
				break;
		}
	});

    myFormSearch.setItemValue("bln", '<?= date('n') ?>');
    myFormSearch.setItemValue("thn", '<?= date('Y') ?>');
}

function openWinExcel(windows, id) {
	const win = windows.createWindow('w1', 0, 0, 400, 200);
	const winTitle = `Excel - ${id}`;
	win.centerOnScreen();
	win.setText(winTitle);
	win.button("park").hide();
	win.setModal(true);

	const myLayout = new dhtmlXLayoutObject({
		parent: win,
		pattern: '1C',
		cells: [
			{id: "a", text: "", header: false},
		]  
	});
	
	const formSearch = [
		{type: "block",blockOffset: 0,list:[
			{type:"radio", name:"stat", value:"M", label:"Monthly", position:"label-right", labelWidth:50, checked: true},
			{type:"newcolumn"},
			{type:"radio", name:"stat", value:"P", label:"Period", position:"label-right", labelWidth:50}
		]},
		{type: "block",blockOffset: 0,offsetTop:10,name:"dvM", list:[
			{type:"combo", name: "bln", label: "", labelWidth: 140, inputWidth: 150, options: [
				{text: "January",value:"1"},
				{text: "February",value:"2"},
				{text: "March",value:"3"},
				{text: "April",value:"4"},
				{text: "May",value:"5"},
				{text: "June",value:"6"},
				{text: "July",value:"7"},
				{text: "August",value:"8"},
				{text: "September",value:"9"},
				{text: "October",value:"10"},
				{text: "November",value:"11"},
				{text: "December",value:"12"}
			]},
			{ type: 'newcolumn' },
			{type:"combo", name: "thn", label: "", labelWidth: 140, inputWidth: 150, options: [
				{text: "2016",value:"2016"},
				{text: "2017",value:"2017"},
				{text: "2018",value:"2018"},
				{text: "2019",value:"2019"},
				{text: "2020",value:"2020"},
				{text: "2021",value:"2021"},
				{text: "2022",value:"2022"},
				{text: "2023",value:"2023"},
				{text: "2024",value:"2024"},
				{text: "2025",value:"2025"},
				{text: "2026",value:"2026"},
				{text: "2027",value:"2027"},
				{text: "2028",value:"2028"},
				{text: "2029",value:"2029"},
				{text: "2030",value:"2030"}
			]},
		]},
		{type: "block",blockOffset: 0,offsetTop:10,name:"dvP",hidden:true, list:[
			{
				type: 'calendar',
				name: 'from_date',
				label: 'From',
				enableTodayButton: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-01') ?>'
			},
			{ type: 'newcolumn' },
			{
				type: 'calendar',
				name: 'to_date',
				label: '-',
				enableTodayButton: true,
				readonly: true,
				dateFormat: "%Y-%m-%d",
				calendarPosition: "right",
				inputWidth: 100,
				value: '<?= date('Y-m-d') ?>'
			},
		]},
		{type: "button", name: "cetak", value: "Print", offsetTop:20},
	];

	myFormSearch = myLayout.cells("a").attachForm(formSearch);
	myFormSearch.attachEvent('onButtonClick', nama => {
        if (nama === 'cetak') {
        	var stat = myFormSearch.getItemValue("stat");
        	var period = "";
        	if(stat == 'M') {
        		period = myFormSearch.getItemValue("bln")+'@@'+myFormSearch.getItemValue("thn");
        	} else if(stat == 'P') {
        		period = myFormSearch.getItemValue("from_date", true)+'@@'+myFormSearch.getItemValue("to_date", true);
        	}
        	win.skipWindowCloseEvent = true;
			win.close();
        	window.open(FRM+"?mode=excel&kd="+id+"&stat="+stat+"&period="+period, "", "width=900,height=600,screenX=500,toolbars=1,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable='no'");
        }
    });

    myFormSearch.attachEvent('onChange', (name, value, state) => {
		switch (name) {
			case 'stat':
				if(value == 'P') {
					myFormSearch.hideItem('dvM');
					myFormSearch.showItem('dvP');
				} else {
					myFormSearch.hideItem('dvP');
					myFormSearch.showItem('dvM');
				}
				break;
		}
	});

    myFormSearch.setItemValue("bln", '<?= date('n') ?>');
    myFormSearch.setItemValue("thn", '<?= date('Y') ?>');
}

function printWO(windows, wono, stat, period) {
	const window_cabang = windows.createWindow("w2", 0, 0, 750, 500);
    window_cabang.centerOnScreen();
    window_cabang.setText('[PDF] Print Asset Card - '+wono);
    window_cabang.button("park").hide();
    window_cabang.setModal(true);
    window_cabang.maximize();
    const pGrid = window_cabang.attachURL("../../libs/mpdf-6.1.4/assethistory.pdf.php?kd="+wono+"&stat="+stat+"&period="+period);
}
	
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
