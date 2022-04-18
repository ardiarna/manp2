<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Personnel</title>
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
			  cells: [{id: "a",header: false,text: 'Personnel'}],
			  //cells: [{id: "b", text: "", header: false}]			  
			});
			
			windows = new dhtmlXWindows();
	  
		const cell = rootLayout.cells('a');
		
		
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
				  {type: 'separator'},
				  {type: 'button', id: 'print', text: 'Print', img: 'fa fa-print'},
				  {type: 'spacer'},
				  {type: 'text', id: 'timestamp', text: ''}
				]
			};
		
		const toolbar = cell.attachToolbar(toolbarConfig);		

		toolbar.attachEvent('onClick', itemId => {

			if(itemId === 'baru') {
				openRequestDetails(windows, {mode: 'create' });

			} else if(itemId === 'ubah') {
				//alert(itemId);
			  const selectedOrderId = grid_reff.getSelectedRowId();
			  //alert(selectedOrderId);
			  if (!selectedOrderId) {
				return;
			  }
				openRequestDetails(windows, {mode: 'ubah' });
			} else if(itemId === 'del') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih user yang ingin dihapus');
					return;
			  	}
				const desc = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('user_name')).getValue();
				dhtmlx.confirm({
					title: 'Delete User',
					type: 'confirm-error',
					text: 'Apakah Anda yakin ingin menghapus user dengan kode '+selectedOrderId+' [Desc: '+desc+'] ?',
					callback: confirmed => {
						if (confirmed) {
							dhx.ajax.post(FRM+"?mode=delete", "kd="+selectedOrderId, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Hapus User",
										text: "Data telah dihapus"
									});
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
								} else {
									dhtmlx.alert({
										title: "Info Hapus User",
										type:"alert-warning",
										text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
									});
								}	
							});		
				  		}
					}
			  	});
			} else if(itemId === 'slip') {
			} else if(itemId === 'export_csv') {
			} else if(itemId === 'export_pdf') {
			}
		});

		grid_reff = rootLayout.cells("a").attachGrid();
        grid_reff.setHeader("No.,User Name,First Name,Last Name", null,
        [STYLES.TEXT_CENTER_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN]);
        grid_reff.setColumnIds('no,user_name,first_name,last_name');		
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,150,*,200");
        grid_reff.setColAlign("center,left,left,left");
        grid_reff.setColSorting("int,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter");
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
		  const win = windows.createWindow('dwg_request_details', 0, 0, 530, 250);
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `User - ${options.id}` : 'New User';

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
        { type: 'settings', last_name: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
			{type: 'input', name: 'user_id', label: 'User Id : ', hidden: true},
			{type: 'input', name: 'user_name', label: 'User Name : ', inputWidth: 200,required: true},
			{type: 'input', name: 'first_name', label: 'First Name : ', inputWidth: 330, required: true},
			{type: 'input', name: 'last_name', label: 'Last Name : ', inputWidth: 330},
			{type: 'password', name: 'password', label: 'Password : ', inputWidth: 250, required: true}
		]},
			{type: "block", offsetTop:20,offsetLeft:20,blockOffset: 0,list:[
				{type: "button", name: "save",value: "Save", className: 'btn-success'},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Cancel", className: 'btn-danger'}
		]}
      ];

		entryForm = win.attachForm(infoFormConfig);
		entryForm.enableLiveValidation(true);  
		
		if(MODE=="ubah"){
			entryForm.disableItem("user_id");

			entryForm.load(FRM+"?mode=load&kd="+grid_reff.getSelectedId());
			entryForm.setFocusOnFirstActive("user_name");
		};
		
	//	entryForm.attachEvent("onKeyUp",function(inp, ev, id){
	//		if(id=="user_id"){
	//			entryForm.setItemValue(id,entryForm.getItemValue(id).toUpperCase());
	//		}
	//	});
	
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
