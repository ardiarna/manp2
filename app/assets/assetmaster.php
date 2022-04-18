<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Asset Master</title>
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
		
			  
function doOnLoad() {
	const rootLayout = new dhtmlXLayoutObject({
		parent: document.body,
		pattern: "1C",
		cells: [
			{id: "a", text: " ", header: false}
		]
	});
	windows = new dhtmlXWindows();
	  
	// const cell = rootLayout.cells('a');
	// const myTree = rootLayout.cells("a").attachTree();
	// myTree.setImagePath("../../assets/libs/dhtmlx/imgs/dhxtree_web/");
	// myTree.load("../../libs/treemenu.xml");
	// myTree.openAllItems(0);
	// myTree.setOnClickHandler(tonclick);
		
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
		
	const toolbar = rootLayout.cells("a").attachToolbar(toolbarConfig);		
	toolbar.attachEvent('onClick', itemId => {
		if(itemId === 'baru') {
			openRequestDetails(windows, {mode: 'create' });
		} else if(itemId === 'ubah') {
			const selectedOrderId = grid_reff.getSelectedRowId();
		  	if (!selectedOrderId) {
		  		alert('Anda belum memilih asset yang ingin diedit');
				return;
		  	}
			openRequestDetails(windows, {mode: itemId, id: selectedOrderId});
		} else if(itemId === 'del') {
			const selectedOrderId = grid_reff.getSelectedRowId();
		  	if (!selectedOrderId) {
		  		alert('Anda belum memilih asset yang ingin dihapus');
				return;
		  	}
		  	const amm_number = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('amm_number')).getValue();
			const assetdesc = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('amm_desc')).getValue();
			dhtmlx.confirm({
				title: 'Delete Asset',
				type: 'confirm-error',
				text: 'Apakah Anda yakin ingin menghapus asset dengan kode '+selectedOrderId+' [Desc: '+amm_number+'-'+assetdesc+'] ?',
				callback: confirmed => {
					if (confirmed) {
						dhx.ajax.post(FRM+"?mode=delete", "assetcode="+selectedOrderId, function(resp){
    						if(resp.xmlDoc.responseText == "OK"){
								dhtmlx.alert({
									title: "Info Hapus Asset",
									text: "Data telah dihapus"
								});
								grid_reff.clearAll();
								grid_reff.load(FRM+"?mode=view", "xml");
							} else {
								dhtmlx.alert({
									title: "Info Hapus Asset",
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

	grid_reff = rootLayout.cells("a").attachGrid();
    grid_reff.setHeader("No.,Asset#,Asset Number,Description,Location,Sub Location,Group,Category,Status", null, [TC,TC,TL,TL,TL,TL,TL,TL]);
    grid_reff.setColumnIds('no,amm_code,amm_number,amm_desc,sl_desc,ssl_desc,sag_desc,sac_desc,amm_status');
    grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
    grid_reff.setInitWidths("50,100,100,*,120,120,120,120,120");
    grid_reff.setColAlign("center,center,center,left,left,left,left,left,left");
    grid_reff.setColSorting("int,str,str,str,str,str,str,str,str");
	grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter");
	grid_reff.attachEvent("onXLS", function () {
      rootLayout.cells('a').progressOn();
    });
    grid_reff.attachEvent("onXLE", function () {
      rootLayout.cells('a').progressOff()
    });
    // grid_reff.enableSmartRendering(true, 100);	
	grid_reff.init();
	grid_reff.load(FRM+"?mode=view", "xml");
}
	
function openRequestDetails(windows, options) {
	const MODE = options.mode;
	const win = windows.createWindow('dwg_request_details', 0, 0, 530, 200);
	const winTitle = ['ubah', 'view'].includes(MODE) ? `Asset Master - ${options.id}` : 'New Asset Master';
	win.centerOnScreen();
	win.setText(winTitle);
	win.button("park").hide();
	win.setModal(true);
	win.maximize();
	win.attachEvent('onClose', window => {
		if (!['view'].includes(MODE)) {
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

	const tabbarinput = win.attachTabbar({
		tabs: [
			{ id: "main", text: "Main", active: true },
			{ id: "spec", text: "Spesification"},
			{ id: "part", text: "Part"},
			{ id: "sparepart", text: "Sparepart"},
			{ id: "pm", text: "Maintenance Schedule"},
			{ id: "image", text: "Image"},
		]
	});

	const mainLayout = new dhtmlXLayoutObject({
		parent: tabbarinput.tabs("main"),
		pattern: "2E",
		cells: [
			{id: "a", text: "Input", header: false, fix_size: [true, null]},
			{id: "b", text: "save", height: 50, header: false, fix_size: [true, null]},
		]
	});	

	const specLayout = new dhtmlXLayoutObject({
		parent: tabbarinput.tabs("spec"),
		pattern: "2E",
		cells: [
			{id: "a", text: "Input", header: false, fix_size: [true, null]},
			{id: "b", text: "save", height: 50, header: false, fix_size: [true, null]},
		]
	});	

	const partLayout = new dhtmlXLayoutObject({
		parent: tabbarinput.tabs("part"),
		pattern: "2E",
		cells: [
			{id: "a", text: "Input", header: false, fix_size: [true, null]},
			{id: "b", text: "save", height: 50, header: false, fix_size: [true, null]},
		]
	});	
	
	const sparepartLayout = new dhtmlXLayoutObject({
		parent: tabbarinput.tabs("sparepart"),
		pattern: "2E",
		cells: [
			{id: "a", text: "Input", header: false, fix_size: [true, null]},
			{id: "b", text: "save", height: 50, header: false, fix_size: [true, null]},
		]
	});		
		
	const pmLayout = new dhtmlXLayoutObject({
		parent: tabbarinput.tabs("pm"),
		pattern: "2E",
		cells: [
			{id: "a", text: "Input", header: false, fix_size: [true, null]},
			{id: "b", text: "Input", header: false, fix_size: [true, null]}
		]
	});
			
		const imageLayout = new dhtmlXLayoutObject({
			parent: tabbarinput.tabs("image"),
			pattern: "2E",
			cells: [
				{id: "a", text: "Input", header: false, fix_size: [true, null]},
				{id: "b", text: "save", height: 50, header: false, fix_size: [true, null]},
			]
		});	
			
			

	hideItem = false;
	if (['create'].includes(MODE)) {
		hideItem = true;
	}
	disabItem = true;
	if (['ubah'].includes(MODE)) {
		disabItem = false;
	}
	const mainConfig = [
		{type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type:"label",blockOffset: 0,label: 'Identification '},
			{type: 'input', name: 'assetcode', label: 'Code : ', inputWidth: 100, readonly:true, hidden:hideItem},
			{type: 'input', name: 'amm_number', label: 'Asset Number : ', required: true,inputWidth: 330},
			{type: 'input', name: 'assetdesc', label: 'Description : ', required: true,inputWidth: 330},
			{type: "block",blockOffset: 0, list:[
				{
					label:"Category : ",
					type: 'template',
					name: 'supbr',
					format: () => '<a href="javascript:void(0);" onclick="showCategoryWindow(windows,mainForm,[\'kodecate\',\'namecate\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "namecate",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},
				{type:"input", name: "kodecate", inputWidth: 75,required: true,readonly:true},
			]},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Manufacture : ",
					type: 'template',
					name: 'manubr',
					format: () => '<a href="javascript:void(0);" onclick="showManufactureWindow(windows,mainForm,[\'assetmanucode\',\'assetmanuname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "assetmanuname",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},
				{type:"input", name: "assetmanucode", inputWidth: 75,required: true,readonly:true},
			]},
			{type: 'input', name: 'assetmodel', label: 'Model : ', inputWidth: 330},
			{type: 'input', name: 'assetserialno', label: 'Serial No. : ', inputWidth: 330},
			{type: 'input', name: 'assetyear', label: 'Year : ', inputWidth: 330},
			{type: 'input', name: 'assettype', label: 'Type : ', inputWidth: 330},
		]},
		{type:"newcolumn"},	
		{type: "block", offsetTop:0,offsetLeft:30,blockOffset: 0,list:[
			{type:"label",blockOffset: 0,label: 'Assigment '},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Location : ",
					type: 'template',
					name: 'locationbr',
					format: () => '<a href="javascript:void(0);" onclick="showLocationWindow(windows,mainForm,[\'kodelocation\',\'namelocation\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "namelocation",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},
				{type:"input", name: "kodelocation", inputWidth: 75,required: true,readonly:true},
			]},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Sub Location : ",
					type: 'template',
					name: 'sublocationbr',
					format: () => '<a href="javascript:void(0);" onclick="showSubLocation();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "namesublocation",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},
				{type:"input", name: "kodesublocation", inputWidth: 75,required: true,readonly:true},
			]},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Grop : ",
					type: 'template',
					name: 'groupbr',
					format: () => '<a href="javascript:void(0);" onclick="showGroupWindow(windows,mainForm,[\'kodegroup\',\'namegroup\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "namegroup",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},	
				{type:"input", name: "kodegroup", inputWidth: 75,required: true,readonly:true},
			]},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Parent : ",
					type: 'template',
					name: 'parentbr',
					format: () => '<a href="javascript:void(0);" onclick="showParent();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "nameparent",offsetLeft:0,inputWidth: 225,style:"text-transform: uppercase;",readonly:true},
				{type:"newcolumn"},
				{type:"input", name: "kodeparent", inputWidth: 75,readonly:true},
			]},
			{type:"combo", label: "Status :",name: "assetstatus", inputWidth: 130,options: [
				{text: "Active",value:"Active"},
				{text: "In Shop",value:"In Shop"},
				{text: "Inactive",value:"Inactive"},
				{text: "Out Of Service",value:"Out Of Service"},
				{text: "Transferred",value:"Transferred"}
			]},
			{type: "block",blockOffset: 0,list:[
				{
					label:"Operator : ",
					type: 'template',
					name: 'operatorbr',
					format: () => '<a href="javascript:void(0);" onclick="showOperatorWindow(windows,mainForm,[\'kodeoperator\',\'nameoperator\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input",name: "nameoperator",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
				{type:"newcolumn"},
				{type:"input", name: "kodeoperator", inputWidth: 75,required: true,readonly:true},
			]},	
		]},
		{type: "block", id: "savemain",offsetTop:20,offsetLeft:120,list:[
			{type: "button", name: "save",value: "Save", className: 'btn-success'},
			{type:"newcolumn"},
			{type: "button", name: "close",value: "Cancel", className: 'btn-danger'},
			{type:"newcolumn"},
			{type: "button", name: "copy",value: "Copy from other assets", offsetLeft:50, hidden:true},
		]}		
	];
		
	const specConfig = [
		{ type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type:"label",blockOffset: 0,label: 'Detail '},
			{type: 'input', name: 'assetcolor', label: 'Color : ', maxLength: 20,inputWidth: 150,required: true},
			{type: 'input', name: 'assetlength', label: 'Length : ', inputWidth: 150},
			{type: 'input', name: 'assetwidth', label: 'Width : ', inputWidth: 150},
			{type: 'input', name: 'assetheight', label: 'Height : ', inputWidth: 150},
			{type: 'input', name: 'assetweight', label: 'Gross Weight : ', inputWidth: 150},
			{type:"newcolumn"},
			{type:"label",blockOffset: 0,offsetLeft:50,label: 'Custom Field '},
			{type: "block", offsetTop:0,offsetLeft:50,blockOffset: 0,list:[
				{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
					{type: 'input', name: 'assetcustom1',inputWidth: 120},
					{type: 'input', name: 'assetcustom2',inputWidth: 120},
					{type: 'input', name: 'assetcustom3',inputWidth: 120},
					{type: 'input', name: 'assetcustom4',inputWidth: 120},
					{type: 'input', name: 'assetcustom5',inputWidth: 120},
				]},	
				{type:"newcolumn"},
				{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
					{type: 'input', name: 'assetcustom11',inputWidth: 300},
					{type: 'input', name: 'assetcustom21',inputWidth: 300},
					{type: 'input', name: 'assetcustom31',inputWidth: 300},
					{type: 'input', name: 'assetcustom41',inputWidth: 300},
					{type: 'input', name: 'assetcustom51',inputWidth: 300},
				]},	
			]},	
		]},
		{type: "block", id: "savespec",offsetTop:20,offsetLeft:120,list:[
			{type: "button", name: "save",value: "Save Spesification", className: 'btn-success', disabled:disabItem},
			{type:"newcolumn"},
			{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
		]}		
	];

	const partConfig = [
		{ type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
				{type: "button", name: "addpart",value: "Add Part",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "delpart",value: "Delete Part",inputWidth: 50},
			]},
			{type: "container", name: "gridPartCont", offsetTop: 5,inputWidth: 700, inputHeight: 250},
		]},
		{type: "block", id: "savepart",offsetTop:20,offsetLeft:120,list:[
			{type: "button", name: "save",value: "Save Part", className: 'btn-success', disabled:disabItem},
			{type:"newcolumn"},
			{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
		]}		
	];

	const sparepartConfig = [
		{ type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
				{type: "button", name: "addsparepart",value: "Add Sparepart",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "delsparepart",value: "Delete Sparepart",inputWidth: 50},
			]},
				{type: "container", name: "gridSparepartCont", offsetTop: 5,inputWidth: 700, inputHeight: 250},
		]},
			{type: "block", id: "savesparepart",offsetTop:20,offsetLeft:120,list:[
				{type: "button", name: "save",value: "Save Sparepart", className: 'btn-success', disabled:disabItem},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
		]}		
	];

	const pmLayoutConfig = [
		{type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
				{type: "button", name: "addpm",value: "Add Schedule",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "edtpm",value: "Edit Schedule",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "delpm",value: "Delete Schedule",inputWidth: 50},
			]},
			{type: "container", name: "gridPmCont", offsetTop: 5,inputWidth: 700, inputHeight: 200},
		]}		
	];

		const imageLayoutConfig = [
			{ type: 'settings', position: 'label-left', labelWidth: 120},
			{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
				{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
					{type: "button", name: "addimage",value: "Add Image",inputWidth: 50},
					{type:"newcolumn"},
					{type: "button", name: "delimage",value: "Delete Image",inputWidth: 50},
				]},
					{type: "container", name: "gridImageCont", offsetTop: 5,inputWidth: 700, inputHeight: 250},
			]},
				{type: "block", id: "saveimage",offsetTop:20,offsetLeft:120,list:[
					{type: "button", name: "save",value: "Save", className: 'btn-success'},
					{type:"newcolumn"},
					{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
			]}		
		];


	mainForm = mainLayout.cells("a").attachForm(mainConfig);
	mainLayout.cells("b").attachObject("savemain");
	if (['create'].includes(MODE)) {
		mainForm.showItem('copy');
	}

	specForm = specLayout.cells("a").attachForm(specConfig);
	specLayout.cells("b").attachObject("savespec");
		
	partForm = partLayout.cells("a").attachForm(partConfig);
	partLayout.cells("b").attachObject("savepart");
		
	sparepartForm = sparepartLayout.cells("a").attachForm(sparepartConfig);
	sparepartLayout.cells("b").attachObject("savesparepart");
		
	pmForm = pmLayout.cells("a").attachForm(pmLayoutConfig);
		
		imageForm = imageLayout.cells("a").attachForm(imageLayoutConfig);
		imageLayout.cells("b").attachObject("saveimage");
		
	//--- Grid Part
	gridPart = new dhtmlXGridObject(partForm.getContainer("gridPartCont"));
	gridPart.setHeader("Part Name,Description,Qty,Unit",null,["text-align:center;","text-align:center;","text-align:center;","text-align:center;"]);
	gridPart.setColumnIds('amp_part,amp_description,amm_qty,amm_unit');
	gridPart.setColTypes("ed,ed,ed,ed");
	gridPart.setInitWidths("150,*,80,80");
	gridPart.setColAlign("left,left,right,center");
	gridPart.enableKeyboardSupport(true);
	gridPart.init();
	//-- End Grid Part		

	//--- Grid Sparepart
	gridSparepart = new dhtmlXGridObject(sparepartForm.getContainer("gridSparepartCont"));
	gridSparepart.setHeader("Sparepart Code,Name,Unit",null,["text-align:center;","text-align:center;","text-align:center;"]);
	gridSparepart.setColumnIds('amsp_sparepart_code,amsp_sparepart_desc,amsp_unit');
	gridSparepart.setColTypes("ro,ro,ro");
	gridSparepart.setInitWidths("200,*,80");
	gridSparepart.setColAlign("left,left,left");
	gridSparepart.enableKeyboardSupport(true);
	gridSparepart.init();
	//-- End Grid Sparepart

	//--- Grid Maintenance Schedule
	gridPM = new dhtmlXGridObject(pmForm.getContainer("gridPmCont"));
	gridPM.setHeader("Service Code,Instructions / Notes,Part Name,Cycle Interval,Cycle Type",null,["text-align:center;","text-align:center;","text-align:center;","text-align:center;","text-align:center;"]);
	gridPM.setColumnIds('amms_code,amms_description,amms_part,amms_intv_cycle,amms_type_cycle');
	gridPM.setColTypes("ro,ro,ro,ron,ro");
	gridPM.setInitWidths("70,*,200,60,70");
	gridPM.setColAlign("left,left,left,right,left");
	gridPM.enableKeyboardSupport(true);
	gridPM.attachEvent('onRowSelect', rowId => {
		const assetcode = mainForm.getItemValue('assetcode');
		const amms_code = gridPM.cells(rowId, gridPM.getColIndexById('amms_code')).getValue();
		formSparPM = showSparepartPM(assetcode, amms_code, pmLayout.cells("b"));		
	});
	gridPM.init();
	//-- End Grid Maintenance Schedule					

//--- Grid Image
	gridImage = new dhtmlXGridObject(imageForm.getContainer("gridImageCont"));
	gridImage.setHeader("Image,Description",null,
	["text-align:center;","text-align:center;","text-align:left;","text-align:left;","text-align:right;","text-align:center;","text-align:left;"]);
	gridImage.setColTypes("ro,ro");
	gridImage.setInitWidths("*,200");
	gridImage.setColAlign("left,left,center,center");
	gridImage.enableKeyboardSupport(true);
	gridImage.init();
//-- End Grid Image
	
	if (['ubah'].includes(MODE)) {
		mainForm.load(FRM+"?mode=loadmain&kd="+options.id);
		specForm.load(FRM+"?mode=loadspec&kd="+options.id);
		gridPart.load(FRM+"?mode=loadpart&kd="+options.id, "xml");
		gridSparepart.load(FRM+"?mode=loadsparepart&kd="+options.id, "xml");
		gridPM.load(FRM+"?mode=loadmaintenance&kd="+options.id, "xml");
	}

	mainForm.enableLiveValidation(true);  
	mainForm.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'close': {
				win.close();
				break;
			}	
			case 'save': {
				if(!mainForm.validate()) {
					dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
				} else {
					mainForm.send(FRM+"?mode=savemain&stat="+MODE, "post", function(loader, response){
						if(response.trim()=="OK"){
							dhtmlx.alert({
								title: compname,
								text:" Data telah tersimpan"
							});
							win.skipWindowCloseEvent = true;
							win.close();
							grid_reff.clearAll();
							grid_reff.load(FRM+"?mode=view", "xml");
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
			case 'copy': {
				showAssetForCopy(windows);
				break;
			}	
		}
	});

	specForm.enableLiveValidation(true);
	specForm.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'close': {
				win.close();
				break;
			}	
			case 'save': {
				if(!specForm.validate()) {
					dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
				} else {
					specForm.send(FRM+"?mode=savespec&assetcode="+mainForm.getItemValue('assetcode'), "post", function(loader, response){
						if(response.trim()=="OK"){
							dhtmlx.alert({
								title: compname,
								text:" Data spesification telah tersimpan"
							});
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

	partForm.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'close': {
				win.close();
				break;
			}	
			case 'save': {
				const partsToAdd = [];
	            gridPart.forEachRow(rowId => {
	            	partsToAdd.push(gridPart.getRowData(rowId));
	            });
	            dhx.ajax.post(FRM+"?mode=savepart&assetcode="+mainForm.getItemValue('assetcode'), "prm="+JSON.stringify(partsToAdd), function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						dhtmlx.alert({
							title: compname,
							text:" Data Part telah tersimpan"
						});
					} else {
						dhtmlx.alert({
							title: compname,
							type:"alert-warning",
							text:resp.xmlDoc.responseText
						});
					}	
				});
				break;
			}
			case 'addpart': {
				const newId = (new Date()).getTime()+Math.ceil(Math.random()*1000);
				gridPart.addRow(newId, "");
				gridPart.setRowColor(newId, "yellow");
				break;
			}
			case 'delpart': {
				const Id = gridPart.getSelectedRowId();
			  	if (!Id) {
			  		alert('Anda belum memilih part yang ingin dihapus');
					return;
			  	}
				gridPart.deleteRow(Id);
				break;
			}	
		}
	});

	sparepartForm.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'close': {
				win.close();
				break;
			}	
			case 'save': {
				const sparePartsToAdd = [];
	            gridSparepart.forEachRow(rowId => {
	            	sparePartsToAdd.push(gridSparepart.getRowData(rowId));
	            });
	            var v_sparePartsToAdd = JSON.stringify(sparePartsToAdd);
	            v_sparePartsToAdd = v_sparePartsToAdd.replace(/&amp;/gi, "");
	            v_sparePartsToAdd = v_sparePartsToAdd.replace(/&lt;/gi, "");
	            v_sparePartsToAdd = v_sparePartsToAdd.replace(/&gt;/gi, "");
	            console.log(v_sparePartsToAdd); 
	            dhx.ajax.post(FRM+"?mode=savesparepart&assetcode="+mainForm.getItemValue('assetcode'), "prm="+v_sparePartsToAdd, function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						dhtmlx.alert({
							title: compname,
							text:" Data Sparepart telah tersimpan"
						});
					} else {
						dhtmlx.alert({
							title: compname,
							type:"alert-warning",
							text:resp.xmlDoc.responseText
						});
					}	
				});
				break;
			}
			case 'addsparepart': {
				showSparePartWindow(windows, gridSparepart);
				break;
			}
			case 'delsparepart': {
				const Id = gridSparepart.getSelectedRowId();
			  	if (!Id) {
			  		alert('Anda belum memilih aparepart yang ingin dihapus');
					return;
			  	}
				gridSparepart.deleteRow(Id);
				break;
			}	
		}
	});

	pmForm.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'addpm': {
				const assetcode = mainForm.getItemValue('assetcode');
				showMaintenanceWindow(windows, gridPM, {mode: 'tambah', assetcode});
				break;
			}
			case 'edtpm': {
				const Id = gridPM.getSelectedRowId();
			  	if (!Id) {
			  		alert('Anda belum memilih maintenance schedule yang ingin diedit');
					return;
			  	}
				const assetcode = mainForm.getItemValue('assetcode');
				const amms_code = gridPM.cells(Id, gridPM.getColIndexById('amms_code')).getValue();
				const amms_description = gridPM.cells(Id, gridPM.getColIndexById('amms_description')).getValue();
				const amms_intv_cycle = gridPM.cells(Id, gridPM.getColIndexById('amms_intv_cycle')).getValue();
				const amms_type_cycle = gridPM.cells(Id, gridPM.getColIndexById('amms_type_cycle')).getValue();
				const amms_part = gridPM.cells(Id, gridPM.getColIndexById('amms_part')).getValue();
				showMaintenanceWindow(windows, gridPM, {mode: 'ubah', assetcode, amms_code, amms_description, amms_intv_cycle, amms_type_cycle, amms_part});  	
				break;
			}	
			case 'delpm': {
				const Id = gridPM.getSelectedRowId();
			  	if (!Id) {
			  		alert('Anda belum memilih maintenance schedule yang ingin dihapus');
					return;
			  	}
			  	dhtmlx.confirm({
				title: 'Delete Maintenance Schedule',
				type: 'confirm-error',
				text: 'Apakah Anda yakin ingin menghapus '+Id+' ?',
				callback: confirmed => {
						if (confirmed) {
							const assetcode = mainForm.getItemValue('assetcode');
							const amms_code = gridPM.cells(Id, gridPM.getColIndexById('amms_code')).getValue();
							dhx.ajax.post(FRM+"?mode=savemaintenance&stat=hapus", "assetcode="+assetcode+"&amms_code="+amms_code, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Hapus Maintenance Schedule",
										text: "Data telah dihapus"
									});
									gridPM.clearAll();
									gridPM.load(FRM+"?mode=loadmaintenance&kd="+assetcode, "xml");
								} else {
									dhtmlx.alert({
										title: "Info Hapus Maintenance Schedule",
										type:"alert-warning",
										text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
									});
								}	
							});		
				  		}
					}
			  	});
				break;
			}	
		}
	});
}

function showSubLocation(){
	var kodelocation=mainForm.getItemValue("kodelocation");
	if(!kodelocation){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Kode Lokasi terlebih dahulu..",
			});
	} else {
		showSubLocationWindow(windows,mainForm,kodelocation,['kodesublocation','namesublocation']);
	}
	
}

function showParent(){
	var kodegroup=mainForm.getItemValue("kodegroup");
	if(!kodegroup){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Group terlebih dahulu..",
			});
	} else {
		showParentWindow(windows,mainForm,kodegroup,['kodeparent','nameparent']);
	}
}

function showMaintenanceWindow(windows, grid, options) {
	const MODE = options.mode;
	const TYPE_CYCLE_OPTIONS = { 'Hours': 'Hours','Days': 'Days', 'Months': 'Months', 'Years': 'Years', 'KM': 'KM'};
	const typeCycleOptions = Object.keys(TYPE_CYCLE_OPTIONS).map((typecycle, idx) => ({
    	text: TYPE_CYCLE_OPTIONS[typecycle],
        value: typecycle,
        selected: options.amms_type_cycle ? typecycle === options.amms_type_cycle : idx === 0
    }));
    const winTitle = ['ubah'].includes(MODE) ? `Maintenance Schedule - ${options.amms_code}` : 'Maintenance Schedule Form';
    const window_cabang = windows.createWindow("w1", 0, 0, 530, 300);
    window_cabang.centerOnScreen();
    window_cabang.setText(winTitle);
    window_cabang.button("park").hide();
    window_cabang.setModal(true);
    window_cabang.button("minmax1").hide(); 

    const formConfig = [
		{ type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: 'input', name: 'assetcode', label: 'Asset Code', hidden: true, value: options.assetcode},
			{type: 'input', name: 'amms_code_lm', label: '', hidden: true, value: options.amms_code},
			{type: 'input', name: 'amms_code', label: 'Service Code : ', required: true, inputWidth: 330, value: options.amms_code},
			{type: 'input', name: 'amms_description', label: 'Instructions / Notes : ', inputWidth: 330, rows: 1, value: options.amms_description},
			{type: 'combo', name: 'amms_part', label: 'Part : ', inputWidth: 330, connector: FRM+"?mode=cmbpart&kd="+options.assetcode+"&id="+options.amms_part},
			{type: 'input', name: 'amms_intv_cycle', label: 'Cycle Interval : ', required: true, value: options.amms_intv_cycle},
			{type: 'combo', name: 'amms_type_cycle', label: 'Cycle TYpe : ', required: true, options: typeCycleOptions}
		]},
		{type: "block", offsetTop:20,offsetLeft:20,blockOffset: 0,list:[
			{type: "button", name: "save",value: "Save", className: 'btn-success'},
			{type:"newcolumn"},
			{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
		]}
	];

	Form = window_cabang.attachForm(formConfig);
	Form.enableLiveValidation(true);
	Form.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'close': {
				window_cabang.close();
				break;
			}	
			case 'save': {
				if(!Form.validate()) {
					dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
				} else {
					Form.send(FRM+"?mode=savemaintenance&stat="+MODE, "post", function(loader, response){
						if(response.trim()=="OK"){
								dhtmlx.alert({
								title: compname,
								text:" Data telah tersimpan"
							});
							window_cabang.skipWindowCloseEvent = true;
							window_cabang.close();
							grid.clearAll();
							grid.load(FRM+"?mode=loadmaintenance&kd="+options.assetcode, "xml");
						} else {
							dhtmlx.alert({
								title: compname,
								type:"alert-warning",
								text:response
							});
						}
					});					
				}
			}	
		}
	});
};

function showSparepartPM(assetcode, amms_code, cell) {
	const formConfig = [
		{type: "block", inputWidth: "auto", list:[
			{type:"input", name: "assetcode", label:"", readonly:true, hidden:true, value: assetcode},	
			{type:"input", name: "amms_code", label:"", readonly:true, hidden:true, value: amms_code},	
			{type:"input", name: "sparepartlist", label:"", readonly:true, hidden:true},	
			{type:"label", offsetTop:0,blockOffset: 0,label: "Sparepart - "+amms_code+" : "},
			{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
				{type: "button", name: "edit",value: "Edit Sparepart", className: 'btn-success'},
				{type:"newcolumn"},
				{type: "button", name: "save",value: "Save Sparepart", className: 'btn-success', hidden: true},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Cancel", className: 'btn-danger', hidden: true},
				{type:"newcolumn"},
				{type: "button", name: "add",value: "Add Sparepart", hidden: true},
				{type:"newcolumn"},
				{type: "button", name: "del",value: "Delete Sparepart", hidden: true}
			]},
			{type: "container", name: "gridCont", offsetTop: 5, inputWidth: 700, inputHeight: 150},
		]},
	];

	form = cell.attachForm(formConfig);
	form.attachEvent('onButtonClick', btnName => {
		switch (btnName) {
			case 'edit': {
				form.hideItem('edit');
				form.showItem('save');
				form.showItem('close');
				form.showItem('add');
				form.showItem('del');
				break;
			}
			case 'close': {
				grid.clearAll();
				grid.load(FRM+"?mode=loadmaintenancepart&kd="+assetcode+"&amms="+amms_code, "xml");
				form.showItem('edit');
				form.hideItem('save');
				form.hideItem('close');
				form.hideItem('add');
				form.hideItem('del');
				break;
			}
			case 'save': {
				const sparePMToAdd = [];
	            grid.forEachRow(rowId => {
	            	sparePMToAdd.push(grid.getRowData(rowId));
	            });
				form.setItemValue('sparepartlist', JSON.stringify(sparePMToAdd));
				form.send(FRM+"?mode=savemaintenancepart", "post", function(loader, response){
					if(response.trim()=="OK"){
						dhtmlx.alert({
							title: compname,
							text:" Data telah tersimpan"
						});
						grid.clearAll();
						grid.load(FRM+"?mode=loadmaintenancepart&kd="+assetcode+"&amms="+amms_code, "xml");
						form.showItem('edit');
						form.hideItem('save');
						form.hideItem('close');
						form.hideItem('add');
						form.hideItem('del');
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
			case 'add': {
				showSparePartWindowList2(windows, grid, assetcode);
				break;
			}
			case 'del': {
				const Id = grid.getSelectedRowId();
			  	if (!Id) {
			  		alert('Anda belum memilih sparepart yang ingin dihapus');
					return;
			  	}
				grid.deleteRow(Id);
				break;
			}	
		}
	});
	grid = new dhtmlXGridObject(form.getContainer("gridCont"));
	grid.setHeader("Sparepart Code,Name,Unit,Stock Qty,Qty",null,["text-align:center;","text-align:center;","text-align:center;","text-align:center;","text-align:center;"]);
	grid.setColumnIds('item_code,item_name,unit,stock_qty,qty');
	grid.setColTypes("ro,ro,ro,ron,ed");
	grid.setInitWidths("120,*,60,50,50");
	grid.setColumnMinWidth("150,160,80,50,50");
	grid.setColAlign("left,left,left,right,right");
	grid.enableKeyboardSupport(true);
	grid.init();
	grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
		if(cInd == grid.getColIndexById('qty') && stage == 2) {
			if (isNaN(nValue)) { 
		        alert('Qty hanya boleh diisi angka');
		        return false;
		    } else {
		    	return true;
		    }	
		}
	});
	grid.load(FRM+"?mode=loadmaintenancepart&kd="+assetcode+"&amms="+amms_code, "xml");

	return form;
}

function showAssetForCopy(windows) {
    const window_cabang = windows.createWindow("w2", 0, 0, 650, 300);
    window_cabang.centerOnScreen();
    window_cabang.setText('Asset List');
    window_cabang.button("park").hide();
    window_cabang.setModal(true);
    window_cabang.button("minmax1").hide();
	
	const pGrid = window_cabang.attachGrid();

	pGrid.setHeader("No,Asset,Description,,Location,Category,Status"); 
	pGrid.setColumnIds('no,amm_code,amm_desc,amm_location,sl_desc,sac_desc,amm_status'); 
	pGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro"); 
	pGrid.setInitWidths("40,80,200,0,100,100,70");
	pGrid.attachHeader(",#text_filter,#text_filter,,#select_filter,#select_filter,#select_filter");
	pGrid.init();
	pGrid.load("../../libs/utils.php?mode=dtasset", "xml");
	pGrid.attachEvent("onRowDblClicked", function(rowId, cInd) {
		const amm_code = pGrid.cells(rowId, pGrid.getColIndexById('amm_code')).getValue();
		mainForm.load(FRM+"?mode=loadmain&kd="+amm_code);
		window_cabang.close();
	});  
};

// function editSparepartPM() {
// 	formSparPM.showItem('add');
// }
	
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
