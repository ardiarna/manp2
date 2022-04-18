<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>PSP</title>
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
				{id: "a", text: "PSP", header: true,height:80},
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
					text: 'Cancel',
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
				{
					type: 'button',
					id: 'pdf',
					text: 'PDF',
					img: 'fa fa-file-pdf-o',
					imgdis: 'fa fa-file-pdf-o',
				},
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
			  	dhx.ajax.post(FRM+"?mode=cekeditable", "kd="+selectedOrderId, function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						openRequestDetails(windows, {
							mode: 'ubah', 
							id: selectedOrderId
						});
					} else {
						dhtmlx.alert({
							title: "Edit "+selectedOrderId,
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
			  	dhx.ajax.post(FRM+"?mode=cekhapus", "kd="+selectedOrderId, function(resp){
					if(resp.xmlDoc.responseText == "OK"){
						const tanggal = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('tanggal')).getValue();
						openCancelWindow(windows, { 
							id: selectedOrderId, tanggal
						});	
					} else {
						dhtmlx.alert({
							title: "Cancel "+selectedOrderId,
							type:"alert-warning",
							text:resp.xmlDoc.responseText
						});
					}	
				});
			}  else if(itemId === 'print') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih psp yang ingin dicetak');
					return;
			  	}
				cetakPSP(windows, selectedOrderId);
			} else if(itemId === 'pdf') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			  	if (!selectedOrderId) {
			  		alert('Anda belum memilih psp yang ingin didownload PDF');
					return;
			  	}
				pdfPSP(windows, selectedOrderId);
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
       	grid_reff.setHeader("No.,PSP#,Date,Status,WO#,KSP No.,Requestor,Create By,Approve Status,Approve By,Approve Date");
        grid_reff.setColumnIds('no,psp_code,tanggal,status,wo_code,ksp_code,requestor,user_create,approve_status,approve_by,approve_date');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,120,100,100,120,120,120,120,100,120,100");
        grid_reff.setColAlign("center,center,center,left,center,center,left,left,left,left,center");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {rootLayout.cells('b').progressOn();});
        grid_reff.attachEvent("onXLE", function () {rootLayout.cells('b').progressOff()});
        // grid_reff.enableSmartRendering(true, 100);
		grid_reff.init();
		grid_reff.load(FRM+"?mode=view&from_date="+myFormSearch.getItemValue('from_date', true)+"&to_date="+myFormSearch.getItemValue('to_date', true), "xml");
		grid_reff.attachEvent("onRowDblClicked", function(rId,cInd) {
			openRequestDetails(windows, {
				mode: 'view', 
				id: rId
			});
		});
	}
	
	function openRequestDetails(windows, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 550, 280);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `PSP : ${options.id}` : 'New PSP';  
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
			{type:"input", name: "psp_code", label:"PSP# : ", inputWidth: 150, readonly:true, hidden:hideItem},
			{type:"calendar", name: "tanggal", button: "calendar_icon",readonly:true, label:"PSP Date :",calendarPosition: "right",inputWidth: 150, required:true},
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
			{type: "block",width: 500,blockOffset: 0,list:[
				{type:"input", name: "asset_code", label:"Asset : ", inputWidth: 90, readonly:true, required:true},
				{type:"newcolumn"},
				{type:"input", name: "asset_name", inputWidth: 290, readonly:true}
			]},
			{type:"input", name: "wo_pic1", label:"Requestor : ", inputWidth: 300},
			{type:"input", name: "wo_desc", label:"Description : ", inputWidth: 400,readonly:true, rows: 1},
			{type: "block",width: 500,blockOffset: 0,list:[
				{
					type: 'template',
					label:"Departemen : ",
					name: 'depbr',
					format: () => '<a href="javascript:void(0);" onclick="showDepWindow(windows);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
					inputWidth: 25
				},
				{type:"newcolumn"},
				{type:"input", name: "departemen_code",readonly:true, required:true, hidden: true},
				{type:"input", name: "departemen_name", inputWidth: 250,readonly:true, required:true},
			]},
			{type:"input", name: "sub_plant", label:"Sub Plant : ", inputWidth: 75,readonly:true, required: true},
			{type:"container", name: "gridSparepartCont", offsetTop: 20,inputWidth: 850, inputHeight: 250},
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
					const sparePartsToAdd = [];	
					const sparePartsOverQty = [];
					entryForm.disableItem('save');
		            gridSparepart.forEachRow(rId => {
		            	sparePartsToAdd.push(gridSparepart.getRowData(rId));
		            	var qty_ava = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty_ava')).getValue());
			    		var qty = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty')).getValue());
			    		if(qty > qty_ava) {
			    			sparePartsOverQty.push(gridSparepart.getRowData(rId));
			    		}  
		            });

					let text = '';
					if (sparePartsOverQty.length > 0) {
						text += `${sparePartsOverQty.length} sparepart berikut Qty Requestnya melebihi Qty Available :<br/><ol>`;
						sparePartsOverQty.forEach(item => {
							text += `<li>${item.item_name} </li>`;
						});
						text += '</ol><br/>';
						dhtmlx.alert({title: compname,type:"alert-error",text:text});
						entryForm.enableItem('save');
						break;
					}

					if(!entryForm.validate()) {
						dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
						entryForm.enableItem('save');
					} else {
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
			}
		});

		gridSparepart = new dhtmlXGridObject(entryForm.getContainer("gridSparepartCont"));
		gridSparepart.setHeader("Sparepart Code,Name,Unit,Keterangan,Ket. Brg Kembali,QTY on Hand,QTY on Booking,QTY Available,Qty Request");
		gridSparepart.setColumnIds('item_code,item_name,unit,keterangan,ket_kembali,qty_oh,qty_ob,qty_ava,qty');
		gridSparepart.setColTypes("ro,ro,ro,ed,ed,ron,ron,ron,ed");
		gridSparepart.setInitWidths("120,*,60,120,120,65,65,65,65");
		gridSparepart.setColumnMinWidth("120,160,60,120,120,65,65,65,65");
		gridSparepart.setColAlign("left,left,left,left,left,right,right,right,right");
		gridSparepart.enableKeyboardSupport(true);
		gridSparepart.init();

		gridSparepart.attachEvent("onRowCreated", function(rId,rObj,rXml){
    		var qty_ava = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty_ava')).getValue());
    		var qty = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty')).getValue());
    		if(qty > qty_ava) {
    			gridSparepart.setRowColor(rId,"yellow");
    		}
		});

		gridSparepart.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
    		if(stage == 2 && cInd == gridSparepart.getColIndexById('qty')) {
    			var qty_ava = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty_ava')).getValue());
    			var qty = parseFloat(gridSparepart.cells(rId, gridSparepart.getColIndexById('qty')).getValue());
	    		if(qty > qty_ava) {
	    			gridSparepart.setRowColor(rId,"yellow");
	    			dhtmlx.alert({title: compname,type:"alert-error",text:"Qty Request tidak boleh melebihi Qyt Available"});
	    		} else {
	    			gridSparepart.setRowColor(rId,"white");
	    		}
    		}
    		return true;
		});

		if(['ubah', 'view'].includes(MODE)) {
			entryForm.load(FRM+"?mode=load&kd="+options.id);
			gridSparepart.load(FRM+"?mode=loadetail&kd="+options.id, "xml");
		};
	}

	function openCancelWindow(windows, options) {
		const win = windows.createWindow('dwg_request_details', 0, 0, 500, 270);
		const winTitle = `Konfirmasi Pembatalan PSP : ${options.id}`;
		win.centerOnScreen();
		win.setText(winTitle);
		win.button("park").hide();
		win.setModal(true);
		// win.maximize();
		const buttons = [
			{type: "button", name: "save",value: "OK", className: 'btn-success'},
			{type: 'newcolumn' },
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	var forminput = [
			{type: "settings", position: "label-left", labelWidth: 110},
			{type: "block",width: 440,list:[
				{type:"input", name: "psp_code", label:"PSP# : ", inputWidth: 120, readonly:true, required:true, value: options.id},
				{type:"input", name: "tanggal", label:"Date : ", inputWidth: 150, readonly:true, value: options.tanggal},
				{type:"input", name: "alasan_cancel", label: "Alasan Pembatalan : ", inputWidth: 300, rows: 3, required:true}
			]},
			{type: "block", inputWidth: "auto", id: "form_cell_c", list: buttons}
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
					if(!entryForm.validate()) {
						dhtmlx.alert({title: compname,type:"alert-warning",text:"Isian masih belum lengkap"});
					} else {
						entryForm.send(FRM+"?mode=delete", "post", function(loader, response){
							if(response.trim()=="OK"){
								dhtmlx.alert({
									title: compname,
									text:" PSP telah dibatalkan"
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
					}
					break;
				}
			}
		});
	}

	function showWOwindow(windows) {
        const window_cabang = windows.createWindow("w1", 0, 0, 880, 400);
        window_cabang.centerOnScreen();
        window_cabang.setText('WO List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
		pGrid.setHeader("No,WO#,Date,Sub Plant,Asset#,Asset Name,PIC,Desc");
		pGrid.setColumnIds('no,wo_code,wo_date,sub_plant,asset_code,asset_name,wo_pic1,wo_desc'); 
		pGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("25,105,75,65,65,140,90,265");
		pGrid.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load(FRM+"?mode=listwo", "xml");
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd) {
			entryForm.setItemValue('wo_code', rId);
			entryForm.setItemValue('wo_date', pGrid.cells(rId, pGrid.getColIndexById('wo_date')).getValue());
			entryForm.setItemValue('asset_code', pGrid.cells(rId, pGrid.getColIndexById('asset_code')).getValue());
			entryForm.setItemValue('asset_name', pGrid.cells(rId, pGrid.getColIndexById('asset_name')).getValue());
			entryForm.setItemValue('wo_pic1', pGrid.cells(rId, pGrid.getColIndexById('wo_pic1')).getValue());
			entryForm.setItemValue('wo_desc', pGrid.cells(rId, pGrid.getColIndexById('wo_desc')).getValue());
			entryForm.setItemValue('sub_plant', pGrid.cells(rId, pGrid.getColIndexById('sub_plant')).getValue());
			gridSparepart.clearAll();
			gridSparepart.load(FRM+"?mode=loadsparepart&kd="+rId, "xml");
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
		pGrid.setColumnIds('no,departemen_name'); 
		pGrid.setColTypes("ro,ro"); 
		pGrid.setInitWidths("40,430");
		pGrid.attachHeader(",#text_filter");
		pGrid.init();
		pGrid.load(FRM+"?mode=listdep", "xml");
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd) {
			entryForm.setItemValue('departemen_code', rId);
			entryForm.setItemValue('departemen_name', pGrid.cells(rId, pGrid.getColIndexById('departemen_name')).getValue());
			window_cabang.close();
		});  
	};

	function cetakPSP(windows, id) {
		const window_cabang = windows.createWindow("w2", 0, 0, 900, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('Print PSP - '+id);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    const pGrid = window_cabang.attachURL(FRM+"?mode=cetakpsp&kd="+id);
	}

	function pdfPSP(windows, id) {
		const window_cabang = windows.createWindow("w3", 0, 0, 900, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('[PDF] PSP - '+id);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.maximize();
	    const pGrid = window_cabang.attachURL("../../libs/mpdf-6.1.4/psp.pdf.php?kd="+id);
	}

  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
