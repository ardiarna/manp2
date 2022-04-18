<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>MR SPK</title>
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
				{id: "a", text: "MR SPK", header: true,height:80},
				{id: "b", text: "",header: false},
			]  
		});
			
		rootLayout.cells("a").fixSize(true);
			
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
				{type: 'separator'},
				{
					type: 'button',
					id: 'print',
					text: 'Print',
					img: 'fa fa-print',
					imgdis: 'fa fa-print',
				},
				// {
				// 	type: 'button',
				// 	id: 'pdf',
				// 	text: 'PDF',
				// 	img: 'fa fa-file-pdf-o',
				// 	imgdis: 'fa fa-file-pdf-o',
				// },
				{type: 'spacer'},
				{type: 'text', id: 'timestamp', text: ''}
			]
		};
		const toolbar = rootLayout.cells("b").attachToolbar(toolbarConfig);	

		toolbar.attachEvent('onClick', itemId => {
			if(itemId === 'baru') {
				openRequestDetails(windows, {mode: 'create' });
			} else if(itemId === 'ubah') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					return;
			  	}
			  	var no_mr = selectedOrderId;
			  	dhx.ajax.post(FRM+"?mode=cekeditable", "no_mr="+no_mr, function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						openRequestDetails(windows, {
							mode: 'ubah', 
							no_mr
						});
					} else {
						dhtmlx.alert({
							title: "Edit "+no_mr,
							type:"alert-warning",
							text:resp.xmlDoc.responseText
						});
					}	
				});
			} else if(itemId === 'del') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		return;
			  	}
			  	var no_mr = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('no_mr')).getValue();
			  	dhx.ajax.post(FRM+"?mode=cekhapus", "no_mr="+no_mr, function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						dhtmlx.confirm({
							title: 'Delete MR SPK',
							ok:"Yes", cancel:"No",
							type: 'confirm-error',
							text: 'Apakah Anda yakin ingin menghapus mr '+no_mr+' ?',
							callback: confirmed => {
								if (confirmed) {
									dhx.ajax.post(FRM+"?mode=delete", "no_mr="+no_mr, function(resp){
			    						if(resp.xmlDoc.responseText == "OK"){
											dhtmlx.alert({
												title: "Info Delete MR SPK",
												text: "mr "+no_mr+" telah dihapus"
											});
											grid_reff.clearAll();
	    									grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
										} else {
											dhtmlx.alert({
												title: "Info Delete MR SPK",
												type:"alert-warning",
												text:"MR SPK gagal dihapus ... "+resp.xmlDoc.responseText
											});
										}	
									});		
						  		}
							}
					  	});	
					} else {
						dhtmlx.alert({
							title: "Delete "+no_mr,
							type:"alert-warning",
							text:resp.xmlDoc.responseText
						});
					}	
				});
			} else if(itemId === 'print') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih MR SPK yang ingin dicetak');
					return;
			  	}
			  	var no_mr = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('no_mr')).getValue();
				cetakMemo(windows, no_mr);
			} else if(itemId === 'pdf') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih MR SPK yang ingin didownload PDF');
					return;
			  	}
			  	var no_mr = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('no_mr')).getValue();
				pdfMemo(windows, no_mr);
			}
		});

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

		myFormSearch = rootLayout.cells("a").attachForm(formSearch);
		myFormSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	grid_reff.clearAll();
	    		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

		grid_reff = rootLayout.cells("b").attachGrid();
       	grid_reff.setHeader("No.,Tanggal,WO#,Nomor MR,Request By,Departemen,Jenis Pekerjaan,Keterangan,Kode Produksi,SPK,Approve1 By,Status,Tgl Approve,Jam Approval,Approve By,Ket. Approval");
        grid_reff.setColumnIds('no,tgl,wo_code,no_mr,usefor,departemen_nama,keterangan,keterangan1,kode_produksi,spk,approve_by1,status,tgl_approval_spk,approval_time,approve_by,keterangan_spk');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,80,100,115,100,100,70,250,60,110,75,85,80,65,75,65");
        grid_reff.setColAlign("center,center,center,center,left,left,left,left,center,center,center,center,center,center,center,left");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        // grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
		grid_reff.attachEvent("onRowDblClicked", function(rId,cInd) {
			var no_mr = grid_reff.cells(rId, grid_reff.getColIndexById('no_mr')).getValue();
		  	openRequestDetails(windows, {
				mode: 'view', 
				no_mr
			});
		});
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 550, 280);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `MR SPK : ${options.no_mr}` : 'New MR SPK';  
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
			{type: "settings", position: "label-left", labelWidth: 110, inputWidth: 150},
			{type:"combo", name: "keterangan",label: "Jenis Pekerjaan :", inputWidth: 150, required:true, options: [
				{text: "MSPK",value:"MSPK"},
				{text: "PROJECT",value:"PROJECT"},
				{text: "MSPO",value:"MSPO"}
			]},
			{type:"input", name: "no_mr", label:"Nomor MR : ", inputWidth: 150, readonly:true, hidden:hideItem},
			{type:"calendar", name: "tgl", button: "calendar_icon",readonly:true, label:"Tanggal :",calendarPosition: "right",inputWidth: 150, required:true},
			{type: "block",width: 500,blockOffset: 0,list:[
				{
					type: 'template',
					label:"WO : ",
					name: 'wobr',
					format: () => '<a href="javascript:void(0);" onclick="showWOwindow(windows);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input", name: "wo_code", inputWidth: 120,readonly:true, required:true},
			]},
			{type:"input", name: "wo_date", label:"WO Date : ", inputWidth: 150,readonly:true},
			{type:"input", name: "wo_scheduled", label:"Scheduled Date : ", inputWidth: 150,readonly:true, hidden:true},
			{type: "block",width: 500,blockOffset: 0,list:[
				{type:"input", name: "asset_code", label:"Asset : ", inputWidth: 90, readonly:true, required:true},
				{type:"newcolumn"},
				{type:"input", name: "asset_name", inputWidth: 290, readonly:true}
			]},
			{type:"input", name: "usefor", label:"Request By : ", inputWidth: 300, required:true},
			{type:"input", name: "keterangan1", label:"Keterangan : ", inputWidth: 400, required:true, rows: 2},
			{type:"input", name: "wo_desc", label:"WO Desc : ", inputWidth: 400,readonly:true, hidden:true},
			{type: "block",width: 500,blockOffset: 0,list:[
				{
					type: 'template',
					label:"Departemen : ",
					name: 'depbr',
					format: () => '<a href="javascript:void(0);" onclick="showDepWindow(windows);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input", name: "departemen_kode",readonly:true, required:true, hidden: true},
				{type:"input", name: "departemen_nama", inputWidth: 250,readonly:true, required:true},
			]},
			{type:"combo", name: "kode_produksi",label: "Kode Produksi :", inputWidth: 150, required:true, options: [
				{value: "", text: ""},
				{value: "AB", text: "AB - Alat Berat"},
				{value: "AT", text: "AT - ATK & IT"},
				{value: "BP", text: "BP - Body Prep"},
				{value: "GL", text: "GL - Glazing Line"},
				{value: "GP", text: "GP - Glaze Prep"},
				{value: "HD", text: "HD - Horizontal Dryer"},
				{value: "HO", text: "HO - Head Office"},
				{value: "KL", text: "KL - Kiln"},
				{value: "PR", text: "PR - Press"},
				{value: "QC", text: "QC - Quality Control"},
				{value: "SP", text: "SP - Sorting Packing"},
				{value: "SQ", text: "SQ - Squaring"},
				{value: "UK", text: "UK - Unloading Kiln"},
				{value: "UL", text: "UL - Utility"},
				{value: "UM", text: "UM - Umum"},
				{value: "WE", text: "WE - Workshop Elektrik"},
				{value: "WM", text: "WM - Workshop Mekanik"}	
			]},
			{type: "block", width: 500, offsetTop:20,blockOffset: 0,list:[
				{type: "button", name: "addpart",value: "Tambah Baris",inputWidth: 50},
				{type:"newcolumn"},
				{type: "button", name: "delpart",value: "Hapus Baris",inputWidth: 50},
			]},
			{type:"container", name: "gridSparepartCont", offsetTop: 5,inputWidth: 1000, inputHeight: 250},
			{type:"block", inputWidth: "auto", id: "form_cell_c", list: buttons},
			{type:"input", name: "sparepartlist", hidden:true},
		];
			
		const dhxLayout = new dhtmlXLayoutObject({
			parent: win,
			pattern: "2E",
			cells: [
				{id: "a", text: "",header: false},
				{id: "b", text: "", header: false},
			]
		});	
		dhxLayout.cells("a").setHeight(150);
		dhxLayout.cells("a").fixSize(true, true);	
		dhxLayout.cells("b").setHeight(50);
		dhxLayout.cells("b").fixSize(true, true);	

		entryForm = dhxLayout.cells("a").attachForm(forminput);
		entryForm.setFontSize("12px");
		
		dhxLayout.cells("b").attachObject("form_cell_c");

		entryForm.attachEvent('onButtonClick', btnName => {
			switch (btnName) {
				case 'close': {
					win.close();
					break;
				}	
				case 'save': {
					entryForm.disableItem('save');
					if(!entryForm.validate()) {
						dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
						entryForm.enableItem('save');
					} else {
						const sparePartsToAdd = [];
						const listBarangNull = [];
						const listQtyNull = [];
						const listSatuanNull = [];
						gridSparepart.forEachRow(rId => {
							sparePartsToAdd.push(gridSparepart.getRowData(rId));
			            });
						sparePartsToAdd.forEach(item => {
							if(item.keterangan == '') {
			            		listBarangNull.push(item);	
			            	}
			            	if(item.qty == '' || item.qty == '0') {
			            		listQtyNull.push(item);	
			            	}
			            	if(item.jenis == '') {
			            		listSatuanNull.push(item);	
			            	}	 
						});

			            let text = '<ol>';
			            var adaNull = false;
						if(listBarangNull.length > 0) {
							listBarangNull.forEach(item => {
								text += `<li>Nama Barang/Jenis Pekerjaan tidak boleh kosong</li>`;
							});
							adaNull = true;
						}
						if(listQtyNull.length > 0) {
							listQtyNull.forEach(item => {
								text += `<li>${item.keterangan} - quantity tidak boleh kosong</li>`;
							});
							adaNull = true;
						}
						if(listSatuanNull.length > 0) {
							listSatuanNull.forEach(item => {
								text += `<li>${item.keterangan} - satuan tidak boleh kosong</li>`;
							});
							adaNull = true;
						}
						if(adaNull) {
							text += '</ol><br/>';
							dhtmlx.alert({title: compname,type:"alert-error",text:text});
							entryForm.enableItem('save');
							break;
						}

						entryForm.setItemValue('sparepartlist', JSON.stringify(sparePartsToAdd));
						entryForm.send(FRM+"?mode=save&stat="+MODE, "post", function(loader, response){
							if(response.trim()=="OK"){
								dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"
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
								entryForm.enableItem('save');
							}
						});					
					}
					break;
				}
				case 'addpart': {
					const newId = gridSparepart.uid();
					gridSparepart.addRow(newId, "");
					gridSparepart.setRowColor(newId, "yellow");
					break;
				}
				case 'delpart': {
					const Id = gridSparepart.getSelectedRowId();
				  	if (!Id) {
				  		alert('Anda belum memilih baris yang ingin dihapus');
						return;
				  	}
					gridSparepart.deleteRow(Id);
					break;
				}	
			}
		});

		gridSparepart = new dhtmlXGridObject(entryForm.getContainer("gridSparepartCont"));
		gridSparepart.setImagePath("../../assets/libs/dhtmlx/imgs/");
		gridSparepart.setHeader("Barang/Jenis Pekerjaan,Quantity,Satuan");
		gridSparepart.setColumnIds('keterangan,qty,jenis');
		gridSparepart.setColTypes("ed,ed,combo");
		gridSparepart.setInitWidths("*,70,100");
		gridSparepart.setColumnMinWidth("200,70,100");
		gridSparepart.setColAlign("left,right,center");
		gridSparepart.enableKeyboardSupport(true);
		gridSparepart.setDateFormat("%Y-%m-%d", "%Y-%m-%d");
		gridSparepart.init();

		comboJenis = gridSparepart.getColumnCombo(gridSparepart.getColIndexById('jenis'));
		comboJenis.enableFilteringMode(true);
		comboJenis.load({options:[
			{value: 'Bak', text: 'Bak'},
			{value: 'Bks', text: 'Bks'},
			{value: 'Box', text: 'Box'},
			{value: 'Btg', text: 'Btg'},
			{value: 'Btl', text: 'Btl'},
			{value: 'Buku', text: 'Buku'},
			{value: 'Container', text: 'Container'},
			{value: 'Drum', text: 'Drum'},
			{value: 'Dus', text: 'Dus'},
			{value: 'Ekl', text: 'Ekl'},
			{value: 'Gln', text: 'Gln'},
			{value: 'Ikat', text: 'Ikat'},
			{value: 'Jam', text: 'Jam'},
			{value: 'Kg', text: 'Kg'},
			{value: 'Klg', text: 'Klg'},
			{value: 'KRG', text: 'KRG'},
			{value: 'Lbr', text: 'Lbr'},
			{value: 'LBS', text: 'LBS'},
			{value: 'Lot', text: 'Lot'},
			{value: 'Lsn', text: 'Lsn'},
			{value: 'Ltr', text: 'Ltr'},
			{value: 'M2', text: 'M2'},
			{value: 'M3', text: 'M3'},
			{value: 'Mmbtu', text: 'Mmbtu'},
			{value: 'Mtr', text: 'Mtr'},
			{value: 'Pack', text: 'Pack'},
			{value: 'Pail', text: 'Pail'},
			{value: 'Pallet', text: 'Pallet'},
			{value: 'Pcs', text: 'Pcs'},
			{value: 'Psg', text: 'Psg'},
			{value: 'Ptg', text: 'Ptg'},
			{value: 'Riem', text: 'Riem'},
			{value: 'Roll', text: 'Roll'},
			{value: 'Set', text: 'Set'},
			{value: 'Tbg', text: 'Tbg'},
			{value: 'Ton', text: 'Ton'},
			{value: 'Truk', text: 'Truk'},
			{value: 'Tube', text: 'Tube'},
			{value: 'Unit', text: 'Unit'},
			{value: 'Zak', text: 'Zak'}
		]});

		if(['create'].includes(MODE)) {
			for (i = 0; i < 2; i++) {
				
			}
		};

		if(['ubah', 'view'].includes(MODE)) {
			entryForm.load(FRM+"?mode=load&no_mr="+options.no_mr);
			gridSparepart.load(FRM+"?mode=loadetail&no_mr="+options.no_mr, "xml");
		};
	}

	function showWOwindow(windows) {
        const window_cabang = windows.createWindow("w1", 0, 0, 840, 400);
        window_cabang.centerOnScreen();
        window_cabang.setText('WO List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
		pGrid.setHeader("No,WO#,Date,Asset#,Asset Name,PIC,Desc,Scheduled");
		pGrid.setColumnIds('no,wo_code,wo_date,asset_code,asset_name,wo_pic1,wo_desc,wo_scheduled'); 
		pGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("25,105,75,80,140,105,280,0");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load(FRM+"?mode=listwo", "xml");
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd) {
			entryForm.setItemValue('wo_code', rId);
			entryForm.setItemValue('wo_date', pGrid.cells(rId, pGrid.getColIndexById('wo_date')).getValue());
			entryForm.setItemValue('wo_scheduled', pGrid.cells(rId, pGrid.getColIndexById('wo_scheduled')).getValue());
			entryForm.setItemValue('asset_code', pGrid.cells(rId, pGrid.getColIndexById('asset_code')).getValue());
			entryForm.setItemValue('asset_name', pGrid.cells(rId, pGrid.getColIndexById('asset_name')).getValue());
			// entryForm.setItemValue('usefor', pGrid.cells(rId, pGrid.getColIndexById('wo_pic1')).getValue());
			entryForm.setItemValue('wo_desc', pGrid.cells(rId, pGrid.getColIndexById('wo_desc')).getValue());
			entryForm.setItemValue('keterangan1', pGrid.cells(rId, pGrid.getColIndexById('wo_desc')).getValue());
			const newId = gridSparepart.uid();
			gridSparepart.addRow(newId, [pGrid.cells(rId, pGrid.getColIndexById('wo_desc')).getValue()]);
			gridSparepart.setRowColor(newId, "yellow");
			window_cabang.close();
		});  
	};

	function showDepWindow(windows) {
        const window_cabang = windows.createWindow("w1", 0, 0, 520, 400);
        window_cabang.centerOnScreen();
        window_cabang.setText('Departemen List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
		pGrid.setHeader("No,Nama Departemen");
		pGrid.setColumnIds('no,departemen_nama'); 
		pGrid.setColTypes("ro,ro"); 
		pGrid.setInitWidths("40,430");
		pGrid.attachHeader(",#text_filter");
		pGrid.init();
		pGrid.load(FRM+"?mode=listdep", "xml");
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd) {
			entryForm.setItemValue('departemen_kode', rId);
			entryForm.setItemValue('departemen_nama', pGrid.cells(rId, pGrid.getColIndexById('departemen_nama')).getValue());
			window_cabang.close();
		});  
	};

	function cetakMemo(windows, id) {
		const window_cabang = windows.createWindow("w2", 0, 0, 1000, 600);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('Print MR SPK - '+id);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    const pGrid = window_cabang.attachURL(FRM+"?mode=cetakmemo&no_mr="+id);
	}

	function pdfMemo(windows, id) {
		const window_cabang = windows.createWindow("w3", 0, 0, 900, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('[PDF] MR SPK - '+id);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.maximize();
	    const pGrid = window_cabang.attachURL("../../libs/mpdf-6.1.4/mrarmasi.pdf.php?no_mr="+id);
	}

  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
