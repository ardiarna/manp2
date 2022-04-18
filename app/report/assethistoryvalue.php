<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Asset History By Cost</title>
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
				id: 'print',
				text: 'Print',
				img: 'fa fa-print',
				imgdis: 'fa fa-print'
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
		if(itemId === 'print') {
			const selectedOrderId = grid_reff.getSelectedRowId();
		  	if (!selectedOrderId) {
		  		alert('Anda belum memilih asset');
				return;
		  	}
		}
	});

	const grid_reff = rootLayout.cells("b").attachGrid();
    grid_reff.setHeader("No.,Asset#,Description,Cost,Location,Sub Location,Group,Category,Status");
    grid_reff.setColumnIds('no,amm_code,amm_desc,netcost,sl_desc,ssl_desc,sag_desc,sac_desc,amm_status');
    grid_reff.setColTypes('ron,rotxt,rotxt,ron,rotxt,rotxt,rotxt,rotxt,rotxt');
    grid_reff.setInitWidths("50,100,*,100,120,120,120,120,120");
    grid_reff.setColumnMinWidth("50,100,150,100,120,120,120,120,120");
    grid_reff.setColAlign("center,center,left,right,left,left,left,left,left");
    grid_reff.setColSorting("int,str,str,int,str,str,str,str,str");
	grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter");
	grid_reff.attachEvent("onXLS", function () {
      rootLayout.cells('a').progressOn();
    });
    grid_reff.attachEvent("onXLE", function () {
      rootLayout.cells('a').progressOff()
    });
    // grid_reff.enableSmartRendering(true, 100);
    grid_reff.setNumberFormat("0,000", grid_reff.getColIndexById('netcost'), ",", ".");	
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
    grid_reff.setHeader("No.,WO#,Date,Request#,Source,Status,Urgency,Description,Asset,Due Date,Duration,Request By,Approve By,Scheduled,Cost");
    grid_reff.setColumnIds('no,wo_code,wo_date,wr_code,wo_source,wo_status,wo_urgency,wo_desc,wo_asset_lbl,wo_due,wo_dur_lbl,wr_request_byname,wr_approve_by,wo_scheduled,netcost');
    grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,ron');
    grid_reff.setInitWidths("40,110,130,110,120,80,80,220,150,80,100,100,100,130,100");
    grid_reff.setColAlign("center,center,center,center,left,left,left,left,left,center,left,left,left,center,right");
    grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,int");
	grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
	grid_reff.attachEvent("onXLS", function () {cell.progressOn();});
    grid_reff.attachEvent("onXLE", function () {cell.progressOff()});
    // grid_reff.enableSmartRendering(true, 100);
    grid_reff.setNumberFormat("0,000", grid_reff.getColIndexById('netcost'), ",", ".");
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

function printWO(windows, wono) {
	const window_cabang = windows.createWindow("w2", 0, 0, 750, 500);
    window_cabang.centerOnScreen();
    window_cabang.setText('[PDF] Print Work Order - '+wono);
    window_cabang.button("park").hide();
    window_cabang.setModal(true);
    window_cabang.maximize();
    const pGrid = window_cabang.attachURL("../../libs/mpdf-6.1.4/workorder.pdf.php?kd="+wono);;
}


	
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
