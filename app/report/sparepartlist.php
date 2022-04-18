<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Sparepart List Req.</title>
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
		windows = new dhtmlXWindows();
		
		const mainLayout = new dhtmlXLayoutObject({
			parent: document.body,
			pattern: '1C',
			cells: [
				{id: "a", text: "", header: false},
			]  
		});

		const tabbar = mainLayout.cells("a").attachTabbar({
			tabs: [
				{ id: "open", text: "Not Completed", active: true },
				{ id: "completed", text: "Completed"}
			]
		});

  		const openLayout = new dhtmlXLayoutObject({
			parent: tabbar.tabs("open"),
			pattern: '1C',
			cells: [
				{id: "a", text: "", header: false},
			]  
		});

		openGrid = setupGrid(openLayout.cells("a"));
		openGrid.load(FRM+"?mode=view&status=O", "xml");
		
		openToolbar = setupToolbar(openLayout.cells("a"), 'O', openGrid);

		const completedLayout = new dhtmlXLayoutObject({
			parent: tabbar.tabs("completed"),
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
	    		completedGrid.load(FRM+"?mode=view&status=C&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

		completedGrid.load(FRM+"?mode=view&status=C&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
	}
	
	function setupGrid(cell) {
		const grid_reff = cell.attachGrid();
        grid_reff.setHeader("No.,Sparepart Code,Sparepart Name,Unit,Qty,WO#,WO Date,Scheduled Date");
        grid_reff.setColumnIds('no,item_code,item_name,unit,qty,wo_code,wo_date,wo_scheduled');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,ron,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,130,*,80,80,120,150,150");
        grid_reff.setColumnMinWidth("50,130,350,80,80,120,150,150");
        grid_reff.setColAlign("center,center,left,left,right,center,center,center");
        grid_reff.setColSorting("na,str,str,str,int,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {cell.progressOn();});
        grid_reff.attachEvent("onXLE", function () {cell.progressOff()});
        grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		return grid_reff;
	}

	function setupToolbar(cell, status, grid_reff) {
		const toolbarConfig = {
			iconset: 'awesome',
			items: [
				{type: 'spacer'},
				{
					type: 'button',
					id: 'refresh',
					text: 'Refresh',
					img: 'fa fa-refresh',
					imgdis: 'fa fa-refresh',
				},
				{type: 'text', id: 'timestamp', text: ''}
			]
		};
		const toolbar = cell.attachToolbar(toolbarConfig);
		toolbar.attachEvent('onClick', itemId => {
			if(itemId === 'refresh') {
				grid_reff.clearAll();
				grid_reff.load(FRM+"?mode=view&status="+status, "xml");
			}
		});

		return toolbar;	
	}
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
