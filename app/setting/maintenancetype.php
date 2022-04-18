<?php
//require_once '../../libs/init.php'; 
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Maintenance Work Type</title>
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
			  cells: [{id: "a",header: false,text: 'Maintenance Work Type'}],
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
/*		
		const toolbar = cell.attachToolbar(
		 {
			 iconset: 'awesome',
			 xml:"../../libs/toolbar.php?id=stk142"
		});
*/
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
			  		alert('Anda belum memilih maintenance work type yang ingin dihapus');
					return;
			  	}
				const smt_work_type = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('smt_work_type')).getValue();
				const smt_code = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('smt_code')).getValue();
				const desc = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('smt_description')).getValue();
				dhtmlx.confirm({
					title: 'Delete Maintenance Work Type',
					type: 'confirm-error',
					text: 'Apakah Anda yakin ingin menghapus maintenance work type dengan kode '+smt_code+' [Desc: '+smt_work_type+' - '+desc+'] ?',
					callback: confirmed => {
						if (confirmed) {
							dhx.ajax.post(FRM+"?mode=delete", "smt_code="+smt_code+"&smt_work_type="+smt_work_type, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Hapus Maintenance Work Type",
										text: "Data telah dihapus"
									});
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
								} else {
									dhtmlx.alert({
										title: "Info Hapus Maintenance Work Type",
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
        grid_reff.setHeader("No.,Work Type,Detail,Description", null,
        [STYLES.TEXT_CENTER_ALIGN,STYLES.TEXT_CENTER_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN,STYLES.TEXT_LEFT_ALIGN]);	
        grid_reff.setColumnIds('no,smt_work_type,smt_code,smt_description');	
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("50,150,200,*");
        grid_reff.setColAlign("center,center,left,left,left,left,left,left");
        grid_reff.setColSorting("int,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#select_filter,#text_filter,#text_filter,#select_filter");
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
		  const winTitle = ['ubah', 'view'].includes(MODE) ? `Maintenance Work Type - ${options.id}` : 'New Maintenance Work Type';

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
        { type: 'settings', position: 'label-left', labelWidth: 120},
		{type: "block", offsetTop:0,offsetLeft:20,blockOffset: 0,list:[
		
			{type:"combo", name: "worktype", label:"Work Type : ",inputWidth: 150,required: true,options: [
							{text: "",value:""}, 
							{text: "Preventive",value:"Preventive"}, 
							{text: "Breakdown",value:"Breakdown"},
							{text: "On Condition",value:"On Condition"},
							{text: "Improvement",value:"Improvement"},
							{text: "Quality",value:"Quality"},
			]},
			{type: 'input', name: 'kodesat', label: 'Detail Code : ', maxLength: 2,inputWidth: 100,required: true,maxlength:3},
			{type: 'input', name: 'nama', label: 'Description : ', inputWidth: 330},
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
			entryForm.disableItem("kodesat");

			entryForm.load(FRM+"?mode=load&kd="+grid_reff.getSelectedId());
			entryForm.setFocusOnFirstActive("nama");
		};
		
	//	entryForm.attachEvent("onKeyUp",function(inp, ev, id){
	//		if(id=="kodesat"){
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
