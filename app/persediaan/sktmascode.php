<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Master Barang</title>
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
	    const TEXT_RIGHT_ALIGN = gridUtils.styles.TEXT_RIGHT_ALIGN;
		const TEXT_BOLD = gridUtils.styles.TEXT_BOLD;
		const HEADER_TEXT_FILTER = gridUtils.headerFilters.TEXT;
		const HEADER_NUMERIC_FILTER = gridUtils.headerFilters.NUMERIC;
		const COLUMN_SPAN = gridUtils.spans.COLUMN;
		const STYLES = gridUtils.styles;
		
			  
function doOnLoad() {
  
		const rootLayout = new dhtmlXLayoutObject({
				parent: document.body,
			  pattern: '1C',
			  cells: [{id: "a",header: false,text: 'Kategori Barang'}],
			  //cells: [{id: "b", text: "", header: false}]			  
			});
			
			windows = new dhtmlXWindows();
	  
		const cell = rootLayout.cells('a');
		const toolbar = cell.attachToolbar(
		 {
			 iconset: 'awesome',
			 xml:"../../libs/toolbar.php?id=stk145"
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

		grid_reff = rootLayout.cells("a").attachGrid();
        grid_reff.setHeader("No.,Type,Kategori,Kelompok,Kode Barang,Nama,Keterangan,Barcode,Sat Kecil,Sat Sedang,Konv,Sat Besar,konv,User Buat,Tanggal Buat,User Edit,Tanggal Edit", null,
        [STYLES.TEXT_CENTER_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN
		,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN]);		
		grid_reff.attachHeader(",#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,150,150,150,100,200,200,120,100,80,80,80,80,80,80,80,120,120,120,120");
        grid_reff.setColAlign("center,left,left,left,left,left,left,left,left,left,left,left,left,left,left");
        grid_reff.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachEvent("onXLS", function () {
          rootLayout.cells('a').progressOn();
        });
        grid_reff.attachEvent("onXLE", function () {
          rootLayout.cells('a').progressOff()
        });
        grid_reff.enableSmartRendering(true, 100);	
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view", "xml");
}
	
function openRequestDetails(windows, options) {
		  const MODE = options.mode;
		  const win = windows.createWindow('dwg_request_details', 0, 0, 600, 450);
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `Master Barang - ${options.id}` : 'Master Barang Baru';

		  win.centerOnScreen();
		  win.setText(winTitle);
		  win.button("park").hide();
		  win.setModal(true);
		  //win.maximize();
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

		const infoFormConfig = [
        { type: 'settings', position: 'label-left', labelWidth: 150},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
		
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input', name: 'typebrg', label: 'Jenis : ', inputWidth: 100,readonly:true,required: true},
				{type:"newcolumn"},
				{
					type: 'template',
					name: 'show_type',
					format: () => '<a href="javascript:void(0);" onclick="showTypeBarangWindow(windows,entryForm,[\'typebrg\',\'namatypebarang\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				  },
				  {type:"newcolumn"},
				{type: 'input', name: 'namatypebarang', inputWidth: 200,readonly:true},
			]},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input', name: 'kategoribrg', label: 'Kategori : ', inputWidth: 100,readonly:true,required: true},
				{type:"newcolumn"},
				{
					type: 'template',
					name: 'show_kategori',
					format: () => '<a href="javascript:void(0);" onclick="showKategoriBarang();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				  },
				  {type:"newcolumn"},
				{type: 'input', name: 'namakategoribarang', inputWidth: 200,readonly:true},
			]},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input', name: 'groupbarang', label: 'Kelompok : ', inputWidth: 100,readonly:true,required: true},
				{type:"newcolumn"},
				{
					type: 'template',
					name: 'show_group',
					format: () => '<a href="javascript:void(0);" onclick="showGroupBarang();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				  },
				  {type:"newcolumn"},
				{type: 'input', name: 'namagroupbarang', inputWidth: 200,readonly:true},
			]},
			
			{type: 'input', name: 'kodebarang', maxLength: 10,label: 'Kode Barang : ', inputWidth: 100,required: true},
			{type: 'input', name: 'nama', label: 'Nama : ', inputWidth: 330},
			{type: 'input', name: 'Keterangan', label: 'Keterangan : ', inputWidth: 330},
			{type: 'input', name: 'barcode', label: 'Kode Barcode : ', inputWidth: 200},
			
			{type: "block", offsetTop:0,offsetLeft:0,width: 500,blockOffset: 0,list:[
						{type:"combo", name: "satbrg", label:"Satuan Terkecil: ",inputWidth: 120,required: true,readonly:true, connector: "../../libs/utils.php?mode=cbosatuan"},	
						{type:"newcolumn"},
						{type: "label",offsetLeft:10,labelWidth: 100, label: "&nbspKonversi&nbsp"},		
			]},
			{type: "block", offsetTop:0,offsetLeft:0,width: 500,blockOffset: 0,list:[
						{type:"combo", name: "satbrgs", label:"Satuan Sedang : ",inputWidth: 120,readonly:true, connector: "../../libs/utils.php?mode=cbosatuan"},
						{type:"newcolumn"},
						{type:"input", name: "satbrgskon",offsetLeft:10,numberFormat: "0,000.00",inputWidth: 50,value:0,style:"text-align: right;"},
			]},
			{type: "block", offsetTop:0,offsetLeft:0,width: 500,blockOffset: 0,list:[
						{type:"combo", name: "satbrgb", label:"Satuan Besar : ",inputWidth: 120,readonly:true, connector: "../../libs/utils.php?mode=cbosatuan"},
						{type:"newcolumn"},
						{type:"input", name: "satbrgbkon",offsetLeft:10,numberFormat: "0,000.00",inputWidth: 50,value:0,style:"text-align: right;"},
			]},	
			{type: "container", label: 'Cabang : ',name: "ContGridCabang", inputWidth: 330, inputHeight: 150},

		]},
			{type: "block", offsetTop:20,offsetLeft:170,blockOffset: 0,list:[
				{type: "button", name: "save",value: "Simpan", className: 'btn-success'},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Batal", className: 'btn-danger'}

		]}
		
      ];

		entryForm = win.attachForm(infoFormConfig);
		entryForm.enableLiveValidation(true);  
		
		gridCabang = new dhtmlXGridObject(entryForm.getContainer("ContGridCabang"));
		gridCabang.setImagePath("../../assets/libs/dhtmlx/imgs/");
		//myGrid2.setImagePath("skins/web/imgs/");
		gridCabang.setHeader(",Kode Cabang,Nama",null,["text-align:center;","text-align:center;","text-align:left;"]);
		gridCabang.setColumnIds('no,kodecabang,namacabang');
		gridCabang.setColTypes("ch,ro,ro");
		gridCabang.setInitWidths("50,100,*");
		gridCabang.setColAlign("center,center,left");
		gridCabang.enableKeyboardSupport(true);
		gridCabang.init();
		gridCabang.load("../../libs/utils.php?mode=dtcabwithcheck", "xml");
		
		
		if(MODE=="ubah"){
			/*
			entryForm.disableItem("typereff");
			entryForm.disableItem("kodereff");
			entryForm.disableItem("penomoran");
			entryForm.disableItem("show_cabang");
			entryForm.hideItem("show_cabang");
			*/
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
						//entryForm.disableItem("save");
						entryForm.send(FRM+"?mode=save&stat="+MODE, "post", function(loader, response){
							if(response.trim()=="OK"){
									dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"});
									win.skipWindowCloseEvent = true;
									win.close();
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
									//entryForm.enableItem("save");
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

function showKategoriBarang(){
	var kdjenis=entryForm.getItemValue("typebrg");
	if(!kdjenis){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Jenis Barang terlebih dahulu..",
			});
	} else {
		showKategoriBarangWindow(windows,entryForm,kdjenis,['kategoribrg','namakategoribarang']);
	}	
}

function showGroupBarang(){
	var kdjenis=entryForm.getItemValue("typebrg");
	var kdkat=entryForm.getItemValue("kategoribrg");
	
	if(!kdjenis){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Jenis Barang terlebih dahulu..",
			});
	} else if(!kdkat){
			dhtmlx.alert({
				title: compname,
				type:"alert-warning",
				text:"Pilih Kategori Barang terlebih dahulu..",
			});
	} else {
		showGroupBarangWindow(windows,entryForm,kdjenis,kdkat,['groupbarang','namagroupbarang']);
	}	
}
	
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
