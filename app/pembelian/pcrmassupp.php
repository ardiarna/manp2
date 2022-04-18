<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Referensi Pembelian</title>
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
			  pattern: '1C',
			  cells: [{id: "a",header: false,text: 'Master Supplier'}],
			  //cells: [{id: "b", text: "", header: false}]			  
			});
			
			windows = new dhtmlXWindows();
	  
		const cell = rootLayout.cells('a');
		const toolbar = cell.attachToolbar(
		 {
			 iconset: 'awesome',
			 xml:"../../libs/toolbar.php?id=pcr13"
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
//        grid_reff.setHeader("No.,Kode,Nama Pemasok,Kontak 1,#cspan,Kontak 2,#cspan,#cspan,Kontak 3,#cspan,#cspan,Alamat", null,
//							[TC,TC,TL,TC,,TC,,,TC,,,TL,TL,TL]);		
	
		grid_reff.setHeader("No.,Kode,Nama Pemasok,Cabang,Kontak 1,#cspan,#cspan,Kontak 2,#cspan,#cspan,Kontak 3,#cspan,#cspan,Alamat", null,
							[TC,TC,TL,TL,TC,,,TC,,,TC,,,TL]);		
	
		grid_reff.attachHeader('#rspan,#rspan,#rspan,#rspan,Nama,Jabatan,No.HP,Nama,Jabatan,No.HP,Nama,Jabatan,No.HP');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,80,200,150,150,150,150,150,150,150,150,150,150,300");
        grid_reff.setColAlign("center,center,left,left,left,left,left,left,left,left,left,left,left,left,left");
        grid_reff.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str");
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
		  const win = windows.createWindow('dwg_request_details', 0, 0, 850, 450);
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `Data Pemasok - ${options.id}` : 'Data Pemasok Baru';

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

		const infoFormConfig = [
        { type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: 'input', name: 'kodesupp', label: 'Kode Pemasok : ', inputWidth: 100,required: true},
			{type: 'input', name: 'nama', label: 'Nama : ', inputWidth: 400},
			{type: 'input', name: 'kodepers', label: 'Kode Perusahaan: ', inputWidth: 100,required: true},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input', name: 'kontak1', label: 'Kontak 1 : ', inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'jabatan1', labelWidth: 80,label: 'Jabatan : ',inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'nohp1', labelWidth: 80,label: 'No HP : ',inputWidth: 150},
			]},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input', name: 'kontak2', label: 'Kontak 2 : ', inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'jabatan2', labelWidth: 80,label: 'Jabatan : ',inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'nohp2', labelWidth: 80,label: 'No HP : ',inputWidth: 150},
			]},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input',name: 'kontak3', label: 'Kontak 3 : ', inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'jabatan3',labelWidth: 80, label: 'Jabatan : ',inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'nohp3', labelWidth: 80,label: 'No HP : ',inputWidth: 150},
			]},
			{type: "block", offsetTop:0,blockOffset: 0,list:[
				{type: 'input',name: 'notelp', label: 'No.Telp : ', inputWidth: 150},
				{type:"newcolumn"},
				{type: 'input', offsetLeft:20,name: 'faks',labelWidth: 80, label: 'Facs : ',inputWidth: 150},
			]},
			{type: 'input', name: 'website', label: 'Website : ', inputWidth: 405},
			{type: 'input', name: 'email', label: 'Email : ', inputWidth: 405},
			{type:"combo", name: "top", label:"Pembayaran : ",inputWidth: 150,required: true,options: [
					{text: "Cash",value:"0"},
					{text: "15 Hari",value:"15"},					
					{text: "30 Hari",value:"30"},					
					{text: "45 Hari",value:"45"},
					{text: "60 Hari",value:"60"},
					{text: "90 Hari",value:"90"},
					{text: "120 Hari",value:"120"},
			]},
			{type: 'input', name: 'alamat', label: 'Alamat : ', inputWidth: 405},
			{type: 'input', name: 'kota', label: 'Kota : ', inputWidth: 405},
			{type: 'input', name: 'propinsi', label: 'Provinsi : ', inputWidth: 405},
			{type: 'input', name: 'negara', label: 'Negara : ', inputWidth: 405},
		]},
			{type: "block", offsetTop:20,offsetLeft:20,blockOffset: 0,list:[
				{type: "button", name: "save",value: "Simpan", className: 'btn-success'},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Batal", className: 'btn-danger'}

		]}
		
      ];

		entryForm = win.attachForm(infoFormConfig);
		entryForm.enableLiveValidation(true);  
		
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
