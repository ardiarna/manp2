<?php
// $pageName = "include/".basename($_SERVER['PHP_SELF']);
$pageName = "include/ceklist0.php";
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Daftar Check List</title>
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

    table.adaborder {
    	border-collapse:collapse;
    	width:100%;
    }
    table.adaborder th,table.adaborder td {
    	border:1px solid black;
    }

    .button {
		border: none;
		color: white;
		padding: 2px 7px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		/*font-size: 16px;*/
		/*margin: 4px 2px;*/
		cursor: pointer;
	}

	.biru {
		background-color: #3da0e3;
	}

	.hijau {
		background-color: #4cae4c !important;
	}

	.merah {
		background-color: #f17373;
	}

	.ungu {
		background-color: #ca73f1;
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
			cells: [
				{id: "a", text: "",header: false}
			]  
		});
			
		windows = new dhtmlXWindows();
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
					return;
			  	}
				openRequestDetails(windows, {
					mode: 'ubah', 
					id: selectedOrderId
				});
			} else if(itemId === 'del') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih yang ingin dihapus');
					return;
			  	}
				dhtmlx.confirm({
					title: 'Delete',
					type: 'confirm-error',
					text: `Apakah Anda yakin ingin menghapus ${selectedOrderId} ?`,
					callback: confirmed => {
						if (confirmed) {
							dhx.ajax.post(FRM+"?mode=delete", "ceklist_code="+selectedOrderId, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Hapus",
										text: "Data telah dihapus"
									});
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
								} else {
									dhtmlx.alert({
										title: "Info Hapus",
										type:"alert-warning",
										text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
									});
								}	
							});		
				  		}
					}
			  	});
			} else if(itemId === 'export_csv') {
			} else if(itemId === 'export_pdf') {
			}
		});

		grid_reff = rootLayout.cells("a").attachGrid();
       	grid_reff.setHeader("No.,Code#,Description,Asset");
        grid_reff.setColumnIds('no,ceklist_code,ceklist_name,asset_list');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,130,250,*");
        grid_reff.setColumnMinWidth("40,130,250,300");
        grid_reff.setColAlign("center,left,left,left");
        grid_reff.setColSorting("na,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('a').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('a').progressOff()});
        // grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view", "xml");
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 550, 280);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `Check List : ${options.id}` : 'Check List Baru';  
	    win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		win.maximize();
		win.attachEvent('onClose', window => {
			if (!['view','del'].includes(MODE)) {
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
		hideItem = false;
		if (['create'].includes(MODE)) {
			hideItem = true;
		}
		const buttons = [
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	if (['create', 'ubah'].includes(MODE)) {
			buttons.unshift({ type: 'newcolumn' });
			buttons.unshift({type: "button", name: "save",value: "Save", className: 'btn-success'});
		}
		var forminput = [
			{type: "input", label: "Code# : ", name: "ceklist_code", labelWidth: 90, inputWidth: 100, readonly:true, hidden:hideItem},
			{type: "input", label: "Description : ", name: "ceklist_name", labelWidth: 90, inputWidth: 330, rows: 1, required:true},
			{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
				{type: "button", name: "addasset",value: "Add Asset",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "delasset",value: "Delete Asset",inputWidth: 50},
			]},
			{type: "container", name: "gridAssetCont", offsetTop: 5,inputWidth: 500, inputHeight: 150},
			{type: "input", name: "asset_list", hidden: true},
			{type: "block", inputWidth: "auto", id: "form_cell_c", list: buttons}
		];
			
		const dhxLayout = new dhtmlXLayoutObject({
			parent: win,
			pattern: "3J",
			cells: [
				{id: "a", text: "",header: false},
				{id: "b", text: "", header: false},
				{id: "c", text: "", header: false},
			]
		});	
		dhxLayout.cells("c").setHeight(50);
		
		entryForm = dhxLayout.cells("a").attachForm(forminput);
		entryForm.setFontSize("12px");
		
		dhxLayout.cells("c").attachObject("form_cell_c");

		assetGrid = new dhtmlXGridObject(entryForm.getContainer("gridAssetCont"));
		assetGrid.setHeader("Asset Code,Asset Name");
		assetGrid.setColumnIds('asset_code,asset_name'); 
		assetGrid.setColTypes("ro,ro"); 
		assetGrid.setColumnMinWidth("80,240");
		assetGrid.setInitWidths("80,*");
		assetGrid.init();

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
						const assetToAdd = [];
			            assetGrid.forEachRow(rowId => {
			            	assetToAdd.push(assetGrid.getRowData(rowId));
			            });
			            entryForm.setItemValue('asset_list', JSON.stringify(assetToAdd));
						entryForm.send(FRM+"?mode=save&stat="+MODE, "post", function(loader, response){
							if(response.substring(0,2)=="OK") {
								dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"
								});
								win.skipWindowCloseEvent = true;
								win.close();	
								if(MODE=="ubah") {
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view", "xml");
								} else {
									openRequestDetails(windows, {
										mode: 'ubah', 
										id: response.substring(2)
									});
								}
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
				case 'delete': {
					entryForm.send(FRM+"?mode=delete", "post", function(loader, response){
						if(response.trim()=="OK"){
							dhtmlx.alert({
								title: compname,
								text:" Data telah dihapus"
							});
							win.skipWindowCloseEvent = true;
							win.close();
							grid_reff.clearAll();
							grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
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
				case 'addasset': {
					showWindowAsset(windows, assetGrid);
					break;
				}
				case 'delasset': {
					const Id = assetGrid.getSelectedRowId();
				  	if (!Id) {
				  		alert('Anda belum memilih asset yang ingin dihapus');
						return;
				  	}
					assetGrid.deleteRow(Id);
					break;
				}	
			}
		});

		dhxLayout.cells("b").attachHTMLString('<div id="dvDetail" style="display:block;max-height:'+(window.innerHeight-100)+'px;overflow-y:auto;-ms-overflow-style:-ms-autohiding-scrollbar;"></div>');
		if(MODE=="ubah"){
			entryForm.load(FRM+"?mode=load&kd="+options.id);
			assetGrid.load(FRM+"?mode=loadasset&kd="+options.id);
			dhx.ajax.get(FRM+"?mode=loadetail&kd="+options.id, function(resp){
				document.getElementById("dvDetail").innerHTML = resp.xmlDoc.responseText;	
			});
		};
	}

	function showWindowAsset(windows, assetGrid) {
	    const window_cabang = windows.createWindow("w1", 0, 0, 650, 300);
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
		pGrid.attachEvent('onRowDblClicked', rowId => {
			const amm_code = pGrid.cells(rowId, pGrid.getColIndexById('amm_code')).getValue();
			const amm_desc = pGrid.cells(rowId, pGrid.getColIndexById('amm_desc')).getValue();
			assetGrid.addRow(amm_code, "");
			assetGrid.setRowColor(amm_code, "greenyellow");
			assetGrid.cells(amm_code, assetGrid.getColIndexById('asset_code')).setValue(amm_code);
			assetGrid.cells(amm_code, assetGrid.getColIndexById('asset_name')).setValue(amm_desc);
			window_cabang.close();
	    });  
	};

	function editDetail(ceklist_code, cd_parent, mode, cd_name) {
		const window_cabang = windows.createWindow("w2", 0, 0, 430, 150);
	    const winTitle = ['ubah', 'view'].includes(mode) ? `Edit Detail` : 'Input Detail'; 
	    window_cabang.centerOnScreen();
	    window_cabang.setText(winTitle);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.button("minmax1").hide();

	    var form = [
			{type: "input", name: "ceklist_code", readonly:true, hidden:true, value: ceklist_code},
			{type: "input", name: "cd_parent", readonly:true, hidden:true, value: cd_parent},
			{type: "input", label: "Nama Detail : ", name: "cd_name", labelWidth: 90, inputWidth: 300, rows: 1, required:true, value: cd_name},
			{type: "block", offsetTop:10,offsetLeft:90,blockOffset: 0,list:[
				{type: "button", name: "savedetail",value: "Save",inputWidth: 50, className: 'btn-success'},
				{type:"newcolumn"},
				{type: "button", name: "close",value: "Cancel",inputWidth: 50},
			]}
		];

		var myForm = window_cabang.attachForm(form);

		myForm.attachEvent('onButtonClick', btnName => {
			switch (btnName) {
				case 'close': {
					window_cabang.close();
					break;
				}	
				case 'savedetail': {
					if(!myForm.validate()) {
						dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
					} else {
						myForm.send(FRM+"?mode=savedetail&stat="+mode, "post", function(loader, response){
							if(response.trim()=="OK"){
								window_cabang.skipWindowCloseEvent = true;
								window_cabang.close();
								dhx.ajax.get(FRM+"?mode=loadetail&kd="+ceklist_code, function(resp){
									document.getElementById("dvDetail").innerHTML = resp.xmlDoc.responseText;	
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
	}

	function hapusDetail(ceklist_code, cd_code, cd_name) {
		dhtmlx.confirm({
			title: 'Delete Detail',
			type: 'confirm-error',
			text: `Apakah Anda yakin ingin menghapus ${cd_name} ?`,
			callback: confirmed => {
				if (confirmed) {
					dhx.ajax.post(FRM+"?mode=deletedetail", "ceklist_code="+ceklist_code+"&cd_code="+cd_code, function(resp){
						if(resp.xmlDoc.responseText == "OK"){
							dhx.ajax.get(FRM+"?mode=loadetail&kd="+ceklist_code, function(resp){
								document.getElementById("dvDetail").innerHTML = resp.xmlDoc.responseText;	
							});
						} else {
							dhtmlx.alert({
								title: "Info Hapus",
								type:"alert-warning",
								text:"Data Gagal Dihapus ... "+resp.xmlDoc.responseText
							});
						}	
					});		
		  		}
			}
	  	});
	}

	function ubahSort(stat, ceklist_code, cd_code, cd_sort, cd_parent) {
		dhx.ajax.post(FRM+"?mode=ubahsort", "stat="+stat+"&ceklist_code="+ceklist_code+"&cd_code="+cd_code+"&cd_sort="+cd_sort+"&cd_parent="+cd_parent, function(resp){
			if(resp.xmlDoc.responseText == "OK"){
				dhx.ajax.get(FRM+"?mode=loadetail&kd="+ceklist_code, function(resp){
					document.getElementById("dvDetail").innerHTML = resp.xmlDoc.responseText;	
				});
			}	
		});
	}
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
