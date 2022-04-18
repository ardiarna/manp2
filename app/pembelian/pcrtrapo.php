<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Pesanan Pembelian</title>
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
					{id: "a", text: "Order Pembelian", header: true,height:80},
					{id: "b", text: "Detail PO",header: false},
				
				]  
			});
			
			rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();
	  
		//const cell = rootLayout.cells('a');
		const toolbar = rootLayout.cells("b").attachToolbar(
		 {
			 iconset: 'awesome',
			 xml:"../../libs/toolbar.php?id=pcr21"
		});

		toolbar.attachEvent('onClick', itemId => {

			if(itemId === 'baru') {
				openRequestDetails(windows, {mode: 'create' });

			} else if(itemId === 'ubah' || itemId === 'dele') {
				//alert(itemId);
			  const selectedOrderId = grid_reff.getSelectedRowId();
			  //alert(selectedOrderId);
			  if (!selectedOrderId) {
				return;
			  }
				openRequestDetails(windows, {mode: 'ubah' });

			} else if(itemId === 'slip') {
			} else if(itemId === 'export_csv') {
			} else if(itemId === 'export_pdf') {
			}
		});

		const 	formSearch = [
					{type: "settings", position: "label-left",labelWidth: 70,inputWidth: 160},
				 {
						type: 'calendar',
						offsetLeft: 20,
						name: 'from_date',
						label: 'Dari',
						enableTodayButton: true,
						required: true,
						dateFormat: "%Y-%m-%d",
						calendarPosition: "right",
						inputWidth: 100
					  },
					  { type: 'newcolumn' },
					  {
						type: 'calendar',
						offsetLeft: 20,
						name: 'to_date',
						label: 'Hingga',
						enableTodayButton: true,
						required: true,
						readonly: true,
						dateFormat: "%Y-%m-%d",
						calendarPosition: "right",
						inputWidth: 100
					  },
					{type:"newcolumn"},
					{type: "button",offsetLeft:30, name: "search",value: "Dapatkan Data"},
				];	

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.setItemValue('to_date', moment().endOf('day').toDate());
		myFormSearch.setItemValue('from_date', moment().startOf('month').toDate());

		grid_reff = rootLayout.cells("b").attachGrid();
        grid_reff.setHeader("No.,Tahun,Refferensi,No.PO,Tanggal,Status,Type,Pemasok,Gudang,Tgl Kirim,Mata Uang,Nilai,Pajak,Total,User Buat,Tanggal Buat,User Edit,Tanggal Edit", null,
        [TC,TC,TC,TC,TC,TC,TC,TC,TL,TL,TL,TL,TR,TR,TR,TL,TL,TL,TL]);		
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,80,80,120,100,80,80,200,120,80,80,80,80,80,120,100,120,100");
        grid_reff.setColAlign("center,center,center,center,center,center,center,center,left,left,left,left,left,right,right,right,left,left,left");
        grid_reff.setColSorting("na,int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#select_filter,#select_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        grid_reff.enableSmartRendering(true, 100);	
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view", "xml");
}
	
function openRequestDetails(windows, options) {
		  const MODE = options.mode;
		  const win = windows.createWindow('dwg_request_details', 0, 0, 530, 280);
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `Order Pembelian - ${options.id}` : 'Order Pembelian Baru';

		  win.centerOnScreen();
		  win.setText(winTitle);
		  win.button("park").hide();
		  win.setModal(true);
		  win.maximize();
		  win.attachEvent('onClose', window => {
			if (!['view',].includes(MODE)) {
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

				var forminput = [
				{type: "settings", position: "label-left", labelWidth: 110, inputWidth: 150},
				{type: "block",width: 800,blockOffset: 20,list:[
						{type:"input", name: "reff", label:"Reff : ",inputWidth: 120,required: true,readonly:true},
						{type:"newcolumn"},
						{
							type: 'template',
							name: 'reffbr',
							format: () => '<a href="javascript:void(0);" onclick="showReffWindow(windows,entryForm,\'POPO\',[\'reff\',\'reffdesc\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
						{type:"newcolumn"},
						{type:"input", name: "nopo1",inputWidth: 100,readonly:true},
						{type:"newcolumn"},
						{type:"input",name: "reffdesc",offsetLeft:0,inputWidth: 390,readonly:true,style:"text-transform: uppercase;",readonly:true},
				]},
				{type: "block", inputWidth: "auto", id: "form_cell_a", list:[	
					{type:"calendar", name: "tglpo", button: "calendar_icon",readonly:true, label:"Tgl PO : ",calendarPosition: "right",inputWidth: 150,required: true},
					{type:"combo", name: "typepo", label:"Type : ",inputWidth: 150,required: true,options: [
							{text: "Stock",value:"S"}, 
							{text: "Biaya",value:"B"},
					]},

					{type:"calendar", name: "tgldelv", button: "calendar_icon",readonly:true,label:"Tgl Kirim : ",calendarPosition: "right",inputWidth: 150,required: true},
					{type:"checkbox", name: "ppnyn",label: "Ppn : ",checked: true},
					
					{type:"newcolumn"},	
					{type: "block",width: 500,blockOffset: 50,list:[
						{type:"input", name: "kdvend", label:"Supplier : ",inputWidth: 75,required: true,readonly:true},
						{type:"newcolumn"},
							{
							type: 'template',
							name: 'supbr',
							format: () => '<a href="javascript:void(0);" onclick="showSupplierWindow(windows,entryForm,[\'kdvend\',\'vendname\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
						{type:"newcolumn"},
						{type:"input",name: "vendname",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;",readonly:true},
					]},
					{type: "block",width: 500,blockOffset: 50,list:[					
						{type: "combo", label: "Pembayaran :",required: true,name:"topcode", connector: "../../libs/utils.php?mode=cbotop",value: "",inputWidth: 100},
						{type:"newcolumn"},
						{type:"combo", name: "toptype", inputWidth: 225,options: [
							{text: "",value:""},
							{text: "Dari Tanggal Invoice",value:"1"},
							{text: "Dari Terima Barang",value:"2"}, 
							{text: "Dari Tanggal PO",value:"3"},
						]},
					]},
					{type: "block",width: 500,blockOffset: 50,list:[					
						{type: "combo", label: "Mata uang :",required: true,name:"curr", connector: "../../libs/utils.php?mode=cbocurr",value: "",inputWidth: 100},
						{type:"newcolumn"},
						{type:"combo", name: "kurs", inputWidth: 225,options: [
							{text: "",value:""},
							{text: "Dari Tanggal PO",value:"1"},
							{text: "Dari Terima Barang",value:"2"}, 
							
						]},
					]},
					
					
					
					{type:"input",label:"Keterangan : ",name: "note",offsetLeft:50,inputWidth: 325,style:"text-transform: uppercase;"},	
				]},
				{type: "block", inputWidth: "auto", id: "form_cell_c", list:[
					{type: "button", name: "save",value: "Simpan", className: 'btn-success'},
						{type:"newcolumn"},
					{type: "button", name: "close",value: "Batal", className: 'btn-danger'}
				]}
			];
			
		
		const toolbarDetailPOConfig = {
			iconset: 'awesome',
			items: [
					  {
						type: 'button',
						id: 'add',
						text: 'Tambah',
						img: 'fa fa-plus',
						imgdis: 'fa fa-plus'
					  },
					   {type: 'separator'},
					  {
						type: 'button',
						id: 'cancom',
						text: 'Hapus',
						img: 'fa fa-times',
						imgdis: 'fa fa-times',
						enabled: false
					  },
					]
		};

		const toolbarDetailBiayaConfig = {
			iconset: 'awesome',
			items: [
					  {
						type: 'button',
						id: 'add',
						text: 'Tambah',
						img: 'fa fa-plus',
						imgdis: 'fa fa-plus'
					  },
					   {type: 'separator'},
					  {
						type: 'button',
						id: 'cancom',
						text: 'Hapus',
						img: 'fa fa-times',
						imgdis: 'fa fa-times',
						enabled: false
					  },
					]
		};

		
			const dhxLayout = new dhtmlXLayoutObject({
				parent: win,
				pattern: "3E",
				cells: [
					{id: "a", text: "",header: false},
					{id: "b", text: "", header: false},
					{id: "c", text: "", header: false},
				]
			});	
			dhxLayout.cells("a").setHeight(180);
			dhxLayout.cells("a").fixSize(true, true);	
			dhxLayout.cells("c").setHeight(50);
			dhxLayout.cells("c").fixSize(true, true);	

			const dhxTabbar = dhxLayout.cells("b").attachTabbar({
				tabs: [
					{id: "a1", text: "Detail PO", active: true},
					{id: "a2", text: "Biaya Pembelian"},
				]
			});
			
			entryForm = dhxLayout.cells("a").attachForm(forminput);
			entryForm.setFontSize("12px");
			
			myCalendar = new dhtmlXCalendarObject(entryForm.getInput("tglpo"));
			myCalendar1 = new dhtmlXCalendarObject(entryForm.getInput("tgldelv"));
						
			//dhxTabbar.tabs("a1").attachObject("form_tab1");
			//dhxTabbar.tabs("a2").attachObject("form_tab2");
			
		
			dhxLayout.cells("c").attachObject("form_cell_c");
			
			const toolbarDetailPO = dhxTabbar.tabs("a1").attachToolbar(toolbarDetailPOConfig);
			const toolbarDetailBiaya = dhxTabbar.tabs("a2").attachToolbar(toolbarDetailBiayaConfig);
			
			
	//const myGrid2 = new dhtmlXGridObject(entryForm.getContainer("myGrid"));
	const myGrid2 = dhxTabbar.tabs("a1").attachGrid();
	myGrid2.setSkin("dhx_web");
	myGrid2.setImagePath("codebase/imgs/");
	myGrid2.setHeader("Kode Barang,Nama,Satuan,Qty,Harga,Total Harga,Tgl Kirim",null,
	["text-align:center;","text-align:left;","text-align:left;","text-align:right;","text-align:right;"
	,"text-align:right;","text-align:right;","text-align:left;","text-align:left;","text-align:left;"]);
	myGrid2.setColTypes("ro,ro,ro,ron,ron,ron,dhxCalendar");
	myGrid2.setInitWidths("110,*,60,70,90,100,150,150");
	myGrid2.setColAlign("center,left,center,right,right,right,right,right,right");
	myGrid2.enableKeyboardSupport(true);
	myGrid2.setNumberFormat("0,000",3,".",",");
	myGrid2.setNumberFormat("0,000",4,".",",");
	myGrid2.setNumberFormat("0,000",5,".",",");
	//myGrid2.attachEvent("onCellChanged",doOnCellEdit);
	//myGrid2.attachEvent("onEnter",doOnEnter);  
	myGrid2.init();
//	myGrid2.submitOnlyChanged(false);
	
	//const myGrid3 = new dhtmlXGridObject(entryForm.getContainer("myGridbea"));
	const myGrid3 = dhxTabbar.tabs("a2").attachGrid();
	myGrid3.setSkin("dhx_web");
	myGrid3.setImagePath("skins/web/imgs/");
	myGrid3.setHeader("Kode Biaya,Biaya,Mata Uang,Nilai,Komp.Hrg Brg,Vendor,,",null,
	["text-align:center;","text-align:left;","text-align:center;","text-align:right;","text-align:center;","text-align:left;","text-align:right;","text-align:left;","text-align:left;","text-align:left;"]);
	myGrid3.setColTypes("ro,ro,ro,ron,ch,ro,ro,ro,ro");
	myGrid3.setInitWidths("100,200,80,100,100,250,100,100,100,100");
	myGrid3.setColAlign("center,left,center,right,center,left,right,left,left,left");
	myGrid3.enableKeyboardSupport(true);
	myGrid3.setNumberFormat("0,000",3,".",",");
	//myGrid3.attachEvent("onCellChanged",doOnCellEdit);
	//myGrid3.attachEvent("onEnter",doOnEnter);  
	myGrid3.init();
//	myGrid3.submitOnlyChanged(false);
		
		if(MODE=="ubah"){
			entryForm.disableItem("typereff");
			entryForm.disableItem("kodereff");
			entryForm.disableItem("penomoran");
			entryForm.disableItem("show_cabang");
			entryForm.hideItem("show_cabang");
			entryForm.load(FRM+"?mode=load&kd="+grid_reff.getSelectedId());
			entryForm.setFocusOnFirstActive("nama");
		};
		
		entryForm.attachEvent("onKeyUp",function(inp, ev, id){
			if(id=="kodereff"){
				entryForm.setItemValue(id,entryForm.getItemValue(id).toUpperCase());
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
									text:" Data telah tersimpan"});
									win.skipWindowCloseEvent = true;
									win.close();
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
							} else {
									dhtmlx.alert({
									title: compname,
									type:"alert-warning",
									text:response});
							}
						});					
					}
				}	
			}
		});
}

	
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
