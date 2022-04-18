<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Downtime Report</title>
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
				{id: "a", text: "Downtime Report", header: true, height:80},
				{id: "b", text: "", header: false},
			]  
		});
			
		rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();

		var myGrid = "";
		
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
				name: 'typerpt',
				label: 'Type',
				required: true,
				offsetLeft: 20,
				inputWidth: 150,
				options: [
        			{text: "Detail", value: "D"},
        			{text: "Rekap By Asset", value: "A"},
        			{text: "Rekap By Main Type", value: "M"},
    			]
			},
			{type:"newcolumn"},
			{type: "button",offsetLeft:30, name: "search",value: "Get Data"},
		];	

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
			if(isObject(myGrid)) {
				myGrid.destructor();
				myGrid = '';
			}
	        if (id === 'search') {
	        	const typerpt = myFormSearch.getItemValue('typerpt');
	        	const from_date = myFormSearch.getItemValue('from_date', true);
	        	const to_date = myFormSearch.getItemValue('to_date', true);
	        	if(typerpt != '') {
	        		myGrid = setupGrid(rootLayout.cells("b"), typerpt, from_date, to_date);	
	        	}			
	        }
	    });
	}

	function setupGrid(cell, typerpt, from_date, to_date) {
		switch (typerpt) {
			case 'D': {
				vHeader = "No.,Date,Asset#,Asset Name,Downtime,WO#,WO Date,Maintenance Type,Description";
				vHeaderB = ",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter";
				vColId = "no,date,assetcode,assetname,downtime,wocode,wodate,mtctype,note";
				vColType = "ron,rotxt,rotxt,rotxt,ron,rotxt,rotxt,rotxt,rotxt";
				vWidth = "50,90,70,250,80,110,90,200,*";
				vMinWidth = "50,90,70,250,80,110,90,200,250";
				vColAlign = "center,center,center,left,right,center,center,left,left";
				vColSort = "na,str,str,str,int,str,str,str,str";
				vLoad = FRM+"?mode=detail&from_date="+from_date+"&to_date="+to_date;
				break;
			}
			case 'A': {
				vHeader = "No.,Date,Asset#,Asset Name,Downtime";
				vHeaderB = ",#text_filter,#text_filter,#text_filter,#text_filter";
				vColId = "no,date,assetcode,assetname,downtime";
				vColType = "ron,rotxt,rotxt,rotxt,ron";
				vWidth = "50,90,70,*,110";
				vMinWidth = "50,90,70,250,110";
				vColAlign = "center,center,center,left,right";
				vColSort = "na,str,str,str,int"; 
				vLoad = FRM+"?mode=rekapasset&from_date="+from_date+"&to_date="+to_date;
				break;
			}
			case 'M': {
				vHeader = "No.,Date,Asset#,Asset Name,Maintenance Type,Downtime";
				vHeaderB = ",#text_filter,#text_filter,#text_filter,#select_filter,#text_filter";
				vColId = "no,date,assetcode,assetname,mtctype,downtime";
				vColType = "ron,rotxt,rotxt,rotxt,rotxt,ron";
				vWidth = "50,90,70,*,200,110";
				vMinWidth = "50,90,70,250,200,110";
				vColAlign = "center,center,left,left,left,right";
				vColSort = "na,str,str,str,str,int"; 
				vLoad = FRM+"?mode=rekapmtc&from_date="+from_date+"&to_date="+to_date;
				break;
			}
		}
		const grid_reff = cell.attachGrid();
        grid_reff.setHeader(vHeader);
        grid_reff.setColumnIds(vColId);
        grid_reff.setColTypes(vColType);
        grid_reff.setInitWidths(vWidth);
        grid_reff.setColumnMinWidth(vMinWidth);
        grid_reff.setColAlign(vColAlign);
        grid_reff.setColSorting(vColSort);
		grid_reff.attachHeader(vHeaderB);
		grid_reff.attachEvent("onXLS", function () {cell.progressOn();});
        grid_reff.attachEvent("onXLE", function () {cell.progressOff()});
        grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		grid_reff.load(vLoad, "xml");
		return grid_reff;
	}

	function isObject (value) {
		return value && typeof value === 'object' && value.constructor === Object;
	}


  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
