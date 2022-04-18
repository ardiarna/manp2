<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Transaksi Barang Keluar</title>
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
					{id: "a", text: "Transaksi Barang Keluar", header: true,height:80},
					{id: "b", text: "Detail PO",header: false},
				
				]  
			});
			
			rootLayout.cells("a").fixSize(true);
			
		windows = new dhtmlXWindows();
	  
		//const cell = rootLayout.cells('a');
		const toolbar = rootLayout.cells("b").attachToolbar(
		 {
			 iconset: 'awesome',
			 xml:"../../libs/toolbar.php?id=stk208"
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
        grid_reff.setHeader("No.,Tahun,Refferensi,No.Transaksi,Tanggal,Gudang,Lokasi,Total Qty,Keterangan,User Buat,Tanggal Buat,User Edit,Tanggal Edit", null,
							[TC,TC,TC,TC,TC,TL,TL,TR,TL,TL,TL,TL,TL]);		
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,80,80,150,100,150,200,100,200,100,120,100,120");
        grid_reff.setColAlign("center,center,center,center,center,center,left,left,left,right,right,right,left,left,left");
        grid_reff.setColSorting("na,int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#select_filter,#select_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        grid_reff.enableSmartRendering(true, 100);	
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view", "xml");
}
	
function openRequestDetails(windows, options) {
		  const MODE = options.mode;
		  const win = windows.createWindow('dwg_request_details', 0, 0, 530, 280);
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `Keluar Barang - ${options.id}` : 'Keluar Barang Baru';

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
							format: () => '<a href="javascript:void(0);" onclick="showReffWindow(windows,entryForm,\'INVTK\',[\'reff\',\'reffdesc\',\'kodecabang\',\'namacabang\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
						{type:"newcolumn"},
						{type:"input", name: "nopo1",inputWidth: 100,readonly:true},
						{type:"newcolumn"},
						{type:"input",name: "reffdesc",offsetLeft:0,inputWidth: 390,readonly:true,style:"text-transform: uppercase;",readonly:true},
				]},
				{type: "block", inputWidth: "auto", id: "form_cell_a", list:[	
					{type:"calendar", name: "tgltransaksi", button: "calendar_icon",readonly:true, label:"Tanggal : ",calendarPosition: "right",inputWidth: 150,required: true},
					{type:"input",label:"Keterangan : ",name: "note",inputWidth: 150,rows:2},	
					{type:"newcolumn"},	
					{type: "block",width: 500,blockOffset: 50,list:[
					/*
						{
							type: 'template',
							label:"Cabang : ",
							name: 'cabangbr',
							format: () => '<a href="javascript:void(0);" onclick="showPurchaseOrder();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
					*/
						{type:"newcolumn"},
						{type:"input",label:"Cabang : ", name: "namacabang", inputWidth: 225,readonly:true},
						{type:"newcolumn"},
						{type:"input",name: "kodecabang",offsetLeft:0,inputWidth: 100,readonly:true,style:"text-transform: uppercase;",readonly:true},
					]},

					{type: "block",width: 500,blockOffset: 50,list:[
						{
							type: 'template',
							label:"Gudang : ",
							name: 'gudangbr',
							format: () => '<a href="javascript:void(0);" onclick="showGudang();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
						{type:"newcolumn"},
						{type:"input", name: "namagudang", inputWidth: 200,required: true,readonly:true},
						{type:"newcolumn"},
						{type:"input",name: "kodegudang",offsetLeft:0,inputWidth: 100,readonly:true,style:"text-transform: uppercase;",readonly:true},
					]},
					{type: "block",width: 500,blockOffset: 50,list:[
						{
							type: 'template',
							label:"Lokasi : ",
							name: 'gudangbr',
							format: () => '<a href="javascript:void(0);" onclick="showLokasi();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
							inputWidth: 25
						},
						{type:"newcolumn"},
						{type:"input", name: "namalokasi", inputWidth: 200,required: true,readonly:true},
						{type:"newcolumn"},
						{type:"input",name: "kodelokasi",offsetLeft:0,inputWidth: 100,readonly:true,style:"text-transform: uppercase;",readonly:true},
					]},
					
					
				]},
				{type: "block", inputWidth: "auto", id: "form_cell_c", list:[
					{type: "button", name: "save",value: "Simpan", className: 'btn-success'},
						{type:"newcolumn"},
					{type: "button", name: "close",value: "Batal", className: 'btn-danger'}
				]}
			];
			
		const toolbarDetailConfig = {
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
			dhxLayout.cells("a").setHeight(150);
			dhxLayout.cells("a").fixSize(true, true);	
			dhxLayout.cells("c").setHeight(50);
			dhxLayout.cells("c").fixSize(true, true);	

	
			
			entryForm = dhxLayout.cells("a").attachForm(forminput);
			entryForm.setFontSize("12px");
			
			myCalendar = new dhtmlXCalendarObject(entryForm.getInput("tgltransaksi"));
				

			dhxLayout.cells("c").attachObject("form_cell_c");
			
	const toolbarDetail = dhxLayout.cells("b").attachToolbar(toolbarDetailConfig);		
			
	//const myGrid2 = new dhtmlXGridObject(entryForm.getContainer("myGrid"));
	const myGrid2 = dhxLayout.cells("b").attachGrid();
	
	myGrid2.setImagePath("codebase/imgs/");
	myGrid2.setHeader("Divisi,Kode Barang,Nama,Satuan,Qty Stock,Qty Masuk",null,
	["text-align:center;","text-align:center;","text-align:left;","text-align:left;","text-align:right;","text-align:right;"
	,"text-align:center;","text-align:center;"]);
	myGrid2.setColTypes("rotxt,rotxt,rotxt,ron,edn,ed,ed");
	myGrid2.setInitWidths("120,120,*,60,100,100");
	myGrid2.setColAlign("center,left,left,right,right");
	myGrid2.enableKeyboardSupport(true);
	myGrid2.setNumberFormat("0,000",4,".",",");
	myGrid2.setNumberFormat("0,000",5,".",",");
	//myGrid2.attachEvent("onCellChanged",doOnCellEdit);
	//myGrid2.attachEvent("onEnter",doOnEnter);  
	myGrid2.enableExcelKeyMap();
	myGrid2.init();
//	myGrid2.submitOnlyChanged(false);
	
	
		if(MODE=="ubah"){
	//		entryForm.disableItem("typereff");
	//		entryForm.disableItem("kodereff");
	//		entryForm.disableItem("penomoran");
	//		entryForm.disableItem("show_cabang");
	//		entryForm.hideItem("show_cabang");
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

function showGudang(){
	var kdcabang=entryForm.getItemValue("kodecabang");
	if(!kdcabang){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Cabang terlebih dahulu..",
			});
	} else {
		showGudangWindow(windows,entryForm,kdcabang,['kodegudang','namagudang']);
	}	
}

function showLokasi(){
	var kdcabang=entryForm.getItemValue("kodecabang");
	var kdgudang=entryForm.getItemValue("kodegudang");
	if(!kdcabang){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Cabang terlebih dahulu..",
			});
	} if(!kdgudang){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Gudang terlebih dahulu..",
			});
	}else {
		showLokasiWindow(windows,entryForm,kdcabang,kdgudang,['kodelokasi','namalokasi']);
	}	
}


  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
