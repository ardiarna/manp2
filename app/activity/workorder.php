<?php
$pageName = "include/".basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Work Order</title>
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
		windows = new dhtmlXWindows();
		
		const mainLayout = new dhtmlXLayoutObject({
			parent: document.body,
			pattern: '1C',
			cells: [
				{id: "a", text: "", header: false},
			]  
		});

		const tabbar = mainLayout.cells("a").attachTabbar({
			tabs: [
				{ id: "open", text: "Open", active: true },
				{ id: "scheduled", text: "Scheduled"},
				{ id: "completed", text: "Completed"}
			]
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
  		
  		const openLayout = new dhtmlXLayoutObject({
			parent: tabbar.tabs("open"),
			pattern: '2E',
			cells: [
				{id: "a", text: "", header: false, height:60},
				{id: "b", text: "", header: false},
			]  
		});
		openLayout.cells("a").fixSize(true);

		openGrid = setupGrid(openLayout.cells("b"));
		
		openSearch = openLayout.cells("a").attachForm(formSearch);
		openSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	openGrid.clearAll();
	    		openGrid.load(FRM+"?mode=view&status=O&from_date="+openSearch.getItemValue('from_date', true)+"&to_date="+openSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

		openToolbar = setupToolbar(openLayout.cells("b"), 'O', openGrid, openSearch);

		openGrid.attachEvent('onRowSelect', rowId => {
	        const wo_status = openGrid.cells(rowId, openGrid.getColIndexById('wo_status')).getValue();
	        if (wo_status === 'Open' || wo_status === 'Scheduled') {
	          openToolbar.enableItem('ubah');
	        } else {
	          openToolbar.disableItem('ubah');
	        }
	    });

	    openGrid.load(FRM+"?mode=view&status=O&from_date="+openSearch.getItemValue('from_date', true)+"&to_date="+openSearch.getItemValue('to_date', true), "xml");

		const scheduledLayout = new dhtmlXLayoutObject({
			parent: tabbar.tabs("scheduled"),
			pattern: '2E',
			cells: [
				{id: "a", text: "", header: false, height:60},
				{id: "b", text: "", header: false},
			]  
		});
		scheduledLayout.cells("a").fixSize(true);

		scheduledGrid = setupGrid(scheduledLayout.cells("b"));

		scheduledSearch = scheduledLayout.cells("a").attachForm(formSearch);
		scheduledSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	scheduledGrid.clearAll();
	    		scheduledGrid.load(FRM+"?mode=view&status=S&from_date="+scheduledSearch.getItemValue('from_date', true)+"&to_date="+scheduledSearch.getItemValue('to_date', true), "xml");		
	        }
	    });
		
	    scheduledToolbar = setupToolbar(scheduledLayout.cells("b"), 'S', scheduledGrid, scheduledSearch);

	    scheduledGrid.attachEvent('onRowSelect', rowId => {
	        const wo_status = scheduledGrid.cells(rowId, scheduledGrid.getColIndexById('wo_status')).getValue();
	        if (wo_status === 'Open' || wo_status === 'Scheduled') {
	          scheduledToolbar.enableItem('ubah');
	        } else {
	          scheduledToolbar.disableItem('ubah');
	        }
	    });

	    scheduledGrid.load(FRM+"?mode=view&status=S&from_date="+scheduledSearch.getItemValue('from_date', true)+"&to_date="+scheduledSearch.getItemValue('to_date', true), "xml");

		const completedLayout = new dhtmlXLayoutObject({
			parent: tabbar.tabs("completed"),
			pattern: '2E',
			cells: [
				{id: "a", text: "", header: false, height:60},
				{id: "b", text: "", header: false},
			]  
		});
		completedLayout.cells("a").fixSize(true);

		completedGrid = setupGrid(completedLayout.cells("b"));
		
		completedSearch = completedLayout.cells("a").attachForm(formSearch);
		completedSearch.attachEvent('onButtonClick', id => {
	        if (id === 'search') {
	        	completedGrid.clearAll();
	    		completedGrid.load(FRM+"?mode=view&status=C&from_date="+completedSearch.getItemValue('from_date', true)+"&to_date="+completedSearch.getItemValue('to_date', true), "xml");		
	        }
	    });

		completedGrid.load(FRM+"?mode=view&status=C&from_date="+completedSearch.getItemValue('from_date', true)+"&to_date="+completedSearch.getItemValue('to_date', true), "xml");
	}
	
	function openRequestDetails(windows, status, grid_reff, search_reff, options) {
		const MODE = options.mode;
		const win = windows.createWindow('dwg_request_details', 0, 0, 530, 280);
		const winTitle = ['ubah', 'view'].includes(MODE) ? `Work Order : ${options.id}` : 'Work Order Baru';
		const URGENCY_OPTIONS = {'Normal': 'Normal', 'Emergency': 'Emergency', 'Urgent': 'Urgent'};
		const urgencyOptions = Object.keys(URGENCY_OPTIONS).map((urgency, idx) => ({
	    	text: URGENCY_OPTIONS[urgency],
	        value: urgency,
	        selected: options.urgency ? urgency === options.urgency : idx === 0
	    }));
	    const DURATIONSAT_OPTIONS = {'Minutes': 'Minutes', 'Hours': 'Hours', 'Days': 'Days'};
		const durationsatOptions = Object.keys(DURATIONSAT_OPTIONS).map((durationsat, idx) => ({
	    	text: DURATIONSAT_OPTIONS[durationsat],
	        value: durationsat,
	        selected: options.durationsat ? durationsat === options.durationsat : idx === 0
	    }));
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
		hideItem = false;
		if (['tambah'].includes(MODE)) {
			hideItem = true;
		}
		const buttons = [
        	{type: "button", name: "close", value: "Cancel", className: 'btn-warning'}
      	];
      	if (['tambah', 'ubah'].includes(MODE)) {
			buttons.unshift({ type: 'newcolumn' });
			buttons.unshift({type: "button", name: "save",value: "Save", className: 'btn-success'});
		}
		var forminput = [
			// {type: "settings", position: "label-left", labelWidth: 140, inputWidth: 150},
			{type: "block",blockOffset: 20,list:[
				{type:"input", name: "wono", label:"WO# : ", labelWidth: 140, inputWidth: 120, readonly:true, hidden:hideItem},
				{type:"input", name: "wodate", label:"WO Date : ", labelWidth: 140, inputWidth: 150, readonly:true, hidden:hideItem},
				{type:"input", name: "requestno", label:"Request# : ", labelWidth: 140, inputWidth: 120, readonly:true, hidden:hideItem},
				{type:"input", name: "reqdate", label:"Request Date: ", labelWidth: 140, inputWidth: 150, readonly:true, hidden:hideItem},
			]},
			{type: "block", inputWidth: "auto", id: "form_cell_a", list:[	
				{type:"label",blockOffset: 0,label: "Detail :"},
				{type: "block",width: 500,blockOffset: 0, hidden:hideItem, list:[
					{type:"input", name: "reqbyname", label:"Request By : ", labelWidth: 140, inputWidth: 200, readonly:true},
					{type:"newcolumn"},
					{type:"input", name: "reqbycode", offsetLeft:0, inputWidth: 100, readonly:true, style:"text-transform: uppercase;"},
				]},
				{type:"combo", name: "sub_plant",label: "Sub plant :", labelWidth: 140, inputWidth: 75, required: true, options: [
					{text: "",value:""},
					{text: "2A",value:"2A"},
					{text: "2B",value:"2B"},
					{text: "2C",value:"2C"}
				]},
				{type:"combo", name: "urgency",label: "Urgency :", labelWidth: 140, inputWidth: 150, options: [
					{text: "",value:""},
					{text: "Normal",value:"Normal"},
					{text: "Emergency",value:"Emergency"},
					{text: "Urgent",value:"Urgent"}
				]},
				{type: "block",blockOffset: 0,list:[
					{
						type: 'template',
						label:"Maintenance Work Type :",
						name: 'reqbr',
						format: () => '<a href="javascript:void(0);" onclick="showWorkTypeWindow2(windows,entryForm,[\'worktype\',\'worktypecode\',\'worktypename\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input", name: "worktypename", inputWidth: 195, required: true, readonly:true},
					{type:"newcolumn"},
					{type:"input", name: "worktype", required: true, readonly:true, hidden:true},
					{type:"newcolumn"},
					{type:"input", name: "worktypecode", offsetLeft:0, inputWidth: 100, readonly:true, style:"text-transform: uppercase;"},
				]},
				{type:"calendar", name: "duerequest", button: "calendar_icon",readonly:true, label:"Due date : ",calendarPosition: "right", labelWidth: 140,inputWidth: 150},
				{type:"input", label: "Description : ", name: "note", labelWidth: 140, inputWidth: 330, rows: 1},
				{type: "block",width: 500,blockOffset: 0,list:[
					{
						type: 'template',
						label:"Asset : ",
						name: 'reqbr',
						format: () => '<a href="javascript:void(0);" onclick="showAssetWindow2(windows,entryForm);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						labelWidth: 140,
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input", name: "assetname", inputWidth: 180,readonly:true},
					{type:"newcolumn"},
					{type:"input",name: "amm_number",offsetLeft:0,inputWidth: 100,readonly:true},
					{type:"input",name: "assetcode",offsetLeft:0,inputWidth: 100,readonly:true, hidden:true},
					{type:"newcolumn"},
					{
						type: 'template',
						label: '',
						name: 'delbr',
						format: () => '<a href="javascript:void(0);" onclick="removeAsset();"><i class="fa fa-times fa-2x"></i></a>', 
						inputWidth: 25
					}
				]},
				{type: "block",blockOffset: 0,list:[
					{
						label:"Location : ",
						type: 'template',
						name: 'locationbr',
						format: () => '<a href="javascript:void(0);" onclick="showLocationWindow2(windows,entryForm,[\'kodelocation\',\'namelocation\']);"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						labelWidth: 140,
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input",name: "namelocation",offsetLeft:0,inputWidth: 225,readonly:true,style:"text-transform: uppercase;"},
					{type:"newcolumn"},
					{type:"input", name: "kodelocation", inputWidth: 75,required: true,readonly:true},
				]},
				{type: "block",blockOffset: 0,list:[
					{
						label:"Maintenance : ",
						type: 'template',
						name: 'maintenancebr',
						format: () => '<a href="javascript:void(0);" onclick="showMaintenance();"><img border="0" src="../../assets/imgs/ic_search.gif" style="border:1px solid #a4bed4; padding:2px;" ></a>',
						labelWidth: 140,
						inputWidth: 25
					},
					{type:"newcolumn"},
					{type:"input",name: "namemaintenance",offsetLeft:0,inputWidth: 205,readonly:true,style:"text-transform: uppercase;"},
					{type:"newcolumn"},
					{type:"input", name: "kodemaintenance", inputWidth: 75,readonly:true},
					{type:"newcolumn"},
					{
						type: 'template',
						label: '',
						name: 'delbr',
						format: () => '<a href="javascript:void(0);" onclick="removeMaintenance();"><i class="fa fa-times fa-2x"></i></a>', 
						inputWidth: 25
					}
				]},
				{type: "block",blockOffset: 0, offsetTop:20, list:[
					{type:"input", name: "duration", label:"Duration Est. : ", labelWidth: 140, inputWidth: 100, required: true},
					{type:"newcolumn"},
					{type:"combo", name: "durationsat", inputWidth: 100, required: true, options: durationsatOptions}
				]},
				{type: "block",blockOffset: 0,list:[
					{type:"calendar", name: "schdate", button: "calendar_icon",readonly:true, label:"Scheduled Date Est. : ", calendarPosition: "right", labelWidth: 140,inputWidth: 150, dateFormat: "%Y-%m-%d %H:%i:%s", required: true},
					{type:"newcolumn"},
					{
						type: 'template',
						label: '',
						name: 'delsch',
						format: () => '<a href="javascript:void(0);" onclick="removeSchDate();"><i class="fa fa-times fa-2x"></i></a>', 
						inputWidth: 25
					}
				]},
				{type:"input", name: "sparepartlist", label:"", inputWidth: 120, readonly:true, hidden:true},
				{type:"input", label: "", name: "woinstruction", hidden: true},
				{type:"input", label: "", name: "pictype", hidden: true},
				{type:"input", label: "", name: "pic1", hidden: true},
				{type:"input", label: "", name: "pic2", hidden: true},
				{type:"input", label: "", name: "pic3", hidden: true},
			]},
			{type: "block", inputWidth: "auto", id: "form_cell_c", list: buttons},
		];

		var WOIinput = [
			{type: "block", inputWidth: "auto", id: "form_cell_b", list:[	
				{type:"label",blockOffset: 0,label: "Work Instruction :"},
				{type:"input", label: "", name: "woinstruction", inputWidth: 500, rows: 4},
				{type:"label", offsetTop:30,blockOffset: 0,label: "Persom In Charge :"},
				{type: "block",blockOffset: 0,list:[
					{type:"radio", name:"pictype", value:"I", label:"Internal"},
					{type:"newcolumn"},
					{type:"radio", name:"pictype", value:"E", label:"Eksternal"}
				]},
				{type:"input", label: "1.", name: "pic1", labelWidth: 50, inputWidth: 200, hidden: true},
				{type:"input", label: "2.", name: "pic2", labelWidth: 50, inputWidth: 200, hidden: true},
				{type:"input", label: "3.", name: "pic3", labelWidth: 50, inputWidth: 200, hidden: true},
				{type:"label", offsetTop:30,blockOffset: 0,label: "Sparepart :"},
				{type: "block", offsetTop:0,offsetLeft:0,blockOffset: 0,list:[
					{type: "button", name: "addsparepart",value: "Add Sparepart",inputWidth: 50},
					{type:"newcolumn"},
					{type: "button", name: "delsparepart",value: "Delete Sparepart",inputWidth: 50},
				]},
				{type: "container", name: "gridSparepartCont", offsetTop: 5,inputWidth: 500, inputHeight: 150},
			]}
		];
	
		const dhxLayout = new dhtmlXLayoutObject({
			parent: win,
			pattern: "3U",
			cells: [
				{id: "a", text: "", header: false},
				{id: "b", text: "", header: false},
				{id: "c", text: "", header: false},
			]
		});	
		// dhxLayout.cells("a").setHeight(150);
		// dhxLayout.cells("a").setWidth(550);
		// dhxLayout.cells("a").fixSize(true, true);
		dhxLayout.cells("c").setHeight(50);
		// dhxLayout.cells("c").fixSize(true, true);	

		entryForm = dhxLayout.cells("a").attachForm(forminput);
		entryForm.setFontSize("12px");

		// dhxLayout.cells("b").attachObject("form_cell_b");
		dhxLayout.cells("c").attachObject("form_cell_c");

		WOIform = dhxLayout.cells("b").attachForm(WOIinput);

		WOIform.attachEvent('onChange', (name, value, state) => {
			switch (name) {
				case 'pictype': {
					WOIform.setItemValue('pic1', '');
					WOIform.setItemValue('pic2', '');
					WOIform.setItemValue('pic3', '');
					if(value == 'I') {
						WOIform.showItem('pic1');
						WOIform.showItem('pic2');
						WOIform.showItem('pic3');
					} else {
						WOIform.showItem('pic1');
						WOIform.hideItem('pic2');
						WOIform.hideItem('pic3');
					}
					break;
				}
			}
		});

		WOIform.attachEvent('onButtonClick', btnName => {
			switch (btnName) {
				case 'addsparepart': {
					const assetcode = entryForm.getItemValue('assetcode'); 
					if(assetcode != '') {
						showSparePartWindowListKlik2(windows, gridSparepart, assetcode);
					} else {
						showSparePartWindowListKlik(windows, gridSparepart);
					}
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

		gridSparepart = new dhtmlXGridObject(WOIform.getContainer("gridSparepartCont"));
		gridSparepart.setHeader("Sparepart Code,Name,Unit,Stock Qty,Qty",null,["text-align:center;","text-align:center;","text-align:center;","text-align:center;","text-align:center;"]);
		gridSparepart.setColumnIds('item_code,item_name,unit,stock_qty,qty');
		gridSparepart.setColTypes("ro,ro,ro,ron,ed");
		gridSparepart.setInitWidths("120,*,60,50,50");
		gridSparepart.setColumnMinWidth("150,160,80,50,50");
		gridSparepart.setColAlign("left,left,left,right,right");
		gridSparepart.enableKeyboardSupport(true);
		gridSparepart.init();
		gridSparepart.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
    		if(cInd == gridSparepart.getColIndexById('qty') && stage == 2) {
    			if (isNaN(nValue)) { 
			        alert('Qty hanya boleh diisi angka');
			        return false;
			    } else {
			    	return true;
			    }	
    		}
		});
		gridSparepart.load(FRM+"?mode=loadsparepart&kd="+options.id, "xml");
			
		const schDate = entryForm.getCalendar("schdate");
		schDate.showTime();

		entryForm.attachEvent("onKeyUp",function(inp, ev, id){
			if(id == "duration"){
				value = entryForm.getItemValue(id);
				if (/([^0123456789]|)/g.test(value)) { 
			        entryForm.setItemValue(id, value.replace(/([^0123456789])/g, ''));
			    }
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
						const sparePartsToAdd = [];
			            gridSparepart.forEachRow(rowId => {
			            	sparePartsToAdd.push(gridSparepart.getRowData(rowId));
			            });
						entryForm.setItemValue('sparepartlist', JSON.stringify(sparePartsToAdd));
						entryForm.setItemValue('woinstruction', WOIform.getItemValue('woinstruction'));
						entryForm.setItemValue('pictype', WOIform.getItemValue('pictype'));
						entryForm.setItemValue('pic1', WOIform.getItemValue('pic1'));
						entryForm.setItemValue('pic2', WOIform.getItemValue('pic2'));
						entryForm.setItemValue('pic3', WOIform.getItemValue('pic3'));
						entryForm.send(FRM+"?mode=save&stat="+MODE, "post", function(loader, response){
							if(response.trim()=="OK"){
								dhtmlx.alert({
									title: compname,
									text:" Data telah tersimpan"
								});
								win.skipWindowCloseEvent = true;
								win.close();
								grid_reff.clearAll();
								grid_reff.load(FRM+"?mode=view&status="+status+"&from_date="+search_reff.getItemValue('from_date', true)+"&to_date="+search_reff.getItemValue('to_date', true), "xml");
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

		if (['ubah'].includes(MODE)) {
			entryForm.load(FRM+"?mode=load&kd="+options.id);
			WOIform.load(FRM+"?mode=load&kd="+options.id);
			if(options.pictype == 'I') {
				WOIform.showItem('pic1');
				WOIform.showItem('pic2');
				WOIform.showItem('pic3');
			} else if(options.pictype == 'E') {
				WOIform.showItem('pic1');
				WOIform.hideItem('pic2');
				WOIform.hideItem('pic3');
			} else {
				WOIform.hideItem('pic1');
				WOIform.hideItem('pic2');
				WOIform.hideItem('pic3');
			} 
		}
	}

	function setupGrid(cell) {
		const grid_reff = cell.attachGrid();
        grid_reff.setHeader("No.,WO#,Date,Request#,MR#,PSP#,Source,Status,Urgency,Description,Asset,Due Date,Duration,Request By,Approve By,Scheduled,");
        grid_reff.setColumnIds('no,wo_code,wo_date,wr_code,mrequest_kode,psp_code,wo_source,wo_status,wo_urgency,wo_desc,wo_asset_lbl,wo_due,wo_dur_lbl,wr_request_byname,wr_approve_by,wo_scheduled,wo_pic_type');
        grid_reff.setColTypes('ron,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt,rotxt');
        grid_reff.setInitWidths("40,110,130,110,120,120,120,80,80,220,150,80,100,100,100,130,0");
        grid_reff.setColAlign("center,center,center,center,center,center,left,left,left,left,left,center,left,left,left,center,left");
        grid_reff.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
		grid_reff.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		grid_reff.attachEvent("onXLS", function () {cell.progressOn();});
        grid_reff.attachEvent("onXLE", function () {cell.progressOff()});
        // grid_reff.enableSmartRendering(true, 100);
        grid_reff.attachEvent('onCellChanged', (rowId, colIdx, newValue) => {
	        if (colIdx === grid_reff.getColIndexById('wo_status')) {
				switch (newValue) {
				case 'Open':
					grid_reff.setRowColor(rowId, 'yellow');
					break;
				case 'Scheduled':
					grid_reff.setRowColor(rowId, 'gold');
					break;
				case 'Completed':
					grid_reff.setRowColor(rowId, 'palegreen');
					break;
				default:
					grid_reff.setRowColor(rowId, 'white');
					break;
				}
			}
      	});	
		grid_reff.init();
		return grid_reff;
	}

	function setupToolbar(cell, status, grid_reff, search_reff) {
		const toolbarConfig = {
			iconset: 'awesome',
			items: [
				{
					type: 'button',
					id: 'tambah',
					text: 'Add',
					img: 'fa fa-plus',
					imgdis: 'fa fa-plus'
				},
				{
					type: 'button',
					id: 'ubah',
					text: 'To Schedule',
					img: 'fa fa-edit',
					imgdis: 'fa fa-edit'
				},
				{type: 'separator'},
				{
					type: 'button',
					id: 'cancel',
					text: 'Cancel',
					img: 'fa fa-times',
					imgdis: 'fa fa-times'
				},
				{type: 'separator'},
				{
					type: 'button',
					id: 'cetak',
					text: 'Print',
					img: 'fa fa-print',
					imgdis: 'fa fa-print'
				},
				{type: 'text', id: 'timestamp', text: ''}
			]
		};
		const toolbar = cell.attachToolbar(toolbarConfig);
		toolbar.disableItem('ubah');
		if(status != 'S') {
			toolbar.hideItem('cancel');
			toolbar.hideItem('cetak');
		}
		toolbar.attachEvent('onClick', itemId => {
			if(itemId === 'tambah') {	
				openRequestDetails(
					windows, status, grid_reff, search_reff,
					{
						mode: itemId
					}
				);
			} else if(itemId === 'ubah') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					return;
			  	}
			  	const pictype = grid_reff.cells(selectedOrderId, grid_reff.getColIndexById('wo_pic_type')).getValue();
				openRequestDetails(
					windows, status, grid_reff, search_reff,
					{
						mode: itemId, 
						id: selectedOrderId, pictype
					}
				);
			} else if(itemId === 'cetak') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					alert('Anda belum memilih work order yang ingin dicetak');
					return;
			  	}
			  	printWO(windows, selectedOrderId);
			}  else if(itemId === 'cancel') {
				const selectedOrderId = grid_reff.getSelectedRowId();
			 	if (!selectedOrderId) {
					alert('Anda belum memilih work order yang ingin dicancel');
					return;
			  	}
			  	dhtmlx.confirm({
					title: 'Cancel Work Order',
					ok:"Yes", cancel:"No",
					type: 'confirm-error',
					text: 'Apakah Anda yakin ingin mengbatalkan work order dengan kode '+selectedOrderId+' ?',
					callback: confirmed => {
						if (confirmed) {
							dhx.ajax.post(FRM+"?mode=delete", "kd="+selectedOrderId, function(resp){
	    						if(resp.xmlDoc.responseText == "OK"){
									dhtmlx.alert({
										title: "Info Cancel Work Order",
										text: "Work Order "+selectedOrderId+" telah dicancel"
									});
									grid_reff.clearAll();
									grid_reff.load(FRM+"?mode=view&status="+status+"&from_date="+search_reff.getItemValue('from_date', true)+"&to_date="+search_reff.getItemValue('to_date', true), "xml");
								} else {
									dhtmlx.alert({
										title: "Info Cancel Work Order",
										type:"alert-warning",
										text:"Work Order Gagal Dicancel ... "+resp.xmlDoc.responseText
									});
								}	
							});		
				  		}
					}
			  	});
			}
		});

		return toolbar;	
	}

	function removeAsset() {
		entryForm.setItemValue('assetcode', '');
		entryForm.setItemValue('assetname', '');
	}

	function removeMaintenance() {
		entryForm.setItemValue('kodemaintenance', '');
		entryForm.setItemValue('namemaintenance', '');
	}

	function removeSchDate() {
		entryForm.setItemValue('schdate', '');
	}

	function printWO(windows, wono) {
		const window_cabang = windows.createWindow("w2", 0, 0, 750, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('[PDF] Print Work Order - '+wono);
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.maximize();
	    const pGrid = window_cabang.attachURL("../../libs/mpdf-6.1.4/workorder.pdf.php?kd="+wono);;
	}

	function showMaintenance() {
		const assetcode = entryForm.getItemValue('assetcode');
		if(!assetcode) {
			alert('Pilih asset terlebih dahulu');
			return;
		}
		showMaintenanceWindow2(windows,entryForm,['kodemaintenance','namemaintenance'],assetcode)
	}

	function showMaintenanceWindow2(windows,instform,instfield,assetcode) {
	    const window_cabang = windows.createWindow("w1", 0, 0, 600, 300);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('Maintenance List');
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("Service Code,Instructions / Notes,Part Name,Cycle Interval,Cycle Type"); 
		pGrid.setColTypes("ro,ro,ro,ron,ro"); 
		pGrid.setInitWidths("50,*,150,50,70");
		pGrid.setColAlign("left,left,left,right,left");
		pGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtmaintenance&kd="+assetcode, "xml");
		
		pGrid.attachEvent("onRowSelect", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
	};

	function showWorkTypeWindow2(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 550, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Maintenance Work Type');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Work Type,Detail,,Description",null,["text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,50,0,250");
		pGrid.attachHeader(",#select_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtworktype", "xml");
		
		pGrid.attachEvent("onRowSelect", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
	};

	function showAssetWindow2(windows,instform) {

        const window_cabang = windows.createWindow("w1", 0, 0, 650, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Asset List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
	
		pGrid.setHeader("No,Asset#,Asset Number,Description,,Location,Category,Status");
		pGrid.setColumnIds('no,amm_code,amm_number,amm_desc,amm_location,sl_desc,sac_desc,amm_status');  
		pGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,0,80,200,0,100,100,70");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,,#select_filter,#select_filter,#select_filter");
		pGrid.init();
		pGrid.load(FRM+"?mode=dtasset", "xml");
		
		pGrid.attachEvent("onRowSelect", function(rId,cInd){
			var amm_number = pGrid.cells(pGrid.getSelectedId(),pGrid.getColIndexById('amm_number')).getValue();
			var assetname = pGrid.cells(pGrid.getSelectedId(),pGrid.getColIndexById('amm_desc')).getValue();
			instform.setItemValue('assetcode', pGrid.cells(pGrid.getSelectedId(),pGrid.getColIndexById('amm_code')).getValue());
			instform.setItemValue('assetname', assetname);
			instform.setItemValue('amm_number', amm_number);
			instform.setItemValue('kodelocation', pGrid.cells(pGrid.getSelectedId(),pGrid.getColIndexById('amm_location')).getValue());
			instform.setItemValue('namelocation', pGrid.cells(pGrid.getSelectedId(),pGrid.getColIndexById('sl_desc')).getValue());
			window_cabang.close();
			if(amm_number == '') {
				dhtmlx.alert({title: compname,type:"alert-warning",text:"Asset Number "+assetname+" belum diisi, silahkan edit di MENU ASSET terlebih dahulu"});
			}
		});  
	};

	function showLocationWindow2(windows,instform,instfield) {
        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Location List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Location Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtlocation", "xml");
		
		pGrid.attachEvent("onRowSelect", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
	};

	function showSparePartWindowListKlik(windows, gridSparepart) {
	    const window_cabang = windows.createWindow("w1", 0, 0, 650, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('Daftar Sparepart');
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.button("minmax1").hide();

	    const layout = window_cabang.attachLayout({
	        pattern: '2E',
	        cells: [
	          {id: 'a', height: 40, fix_size: true, header: false},
	          {id: 'b', fix_size: true, header: false}
	        ]
	    });

	    const filterFormConfig = [
	        {type: "settings", position: "label-left", labelWidth: 75, inputWidth: 160},
	        {
	        	type: 'input',
				name: 'prm',
				label: 'Code/Name : ',
				inputWidth: 350,
	        },
	        {type: 'newcolumn'},
	        {type: 'button', offsetLeft: 20, name: 'getdata', value: 'Cari!'}
	    ];
	    const filterForm = layout.cells('a').attachForm(filterFormConfig);
		filterForm.attachEvent('onButtonClick', id => {
			if (id === 'getdata') {
				const prm = filterForm.getItemValue('prm');
				pGrid.clearAll();
				pGrid.load("../../libs/initarmasi.php?mode=dtsparepart&prm="+prm, "xml");
			}
	    });
		const pGrid = layout.cells('b').attachGrid();
		pGrid.setHeader("Code,Name,Unit,Stock",null,["text-align:center;","text-align:center;"]);
		pGrid.setColumnIds('item_kode,item_nama,satuan,stok');
		pGrid.attachHeader("#text_filter,#text_filter,#select_filter,#text_filter"); 
		pGrid.setColTypes("ro,ro,ro,ron"); 
		pGrid.setInitWidths("120,360,50,50");
		pGrid.init();
		pGrid.attachEvent('onRowSelect', rowId => {
			const item_kode = pGrid.cells(rowId, pGrid.getColIndexById('item_kode')).getValue();
			const item_nama = pGrid.cells(rowId, pGrid.getColIndexById('item_nama')).getValue();
			const satuan = pGrid.cells(rowId, pGrid.getColIndexById('satuan')).getValue();
			const stok = pGrid.cells(rowId, pGrid.getColIndexById('stok')).getValue();
			const newId = (new Date()).getTime()+Math.ceil(Math.random()*1000);
			gridSparepart.addRow(newId, "");
			gridSparepart.setRowColor(newId, "greenyellow");
			gridSparepart.cells(newId, gridSparepart.getColIndexById('item_code')).setValue(item_kode);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('item_name')).setValue(item_nama);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('unit')).setValue(satuan);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('stock_qty')).setValue(stok);
			window_cabang.close();
	    });  
	};

	function showSparePartWindowListKlik2(windows, gridSparepart, assetCode) {
	    const window_cabang = windows.createWindow("w1", 0, 0, 650, 500);
	    window_cabang.centerOnScreen();
	    window_cabang.setText('Daftar Sparepart');
	    window_cabang.button("park").hide();
	    window_cabang.setModal(true);
	    window_cabang.button("minmax1").hide();

	    const layout = window_cabang.attachLayout({
	        pattern: '1C',
	        cells: [
	          {id: 'a', fix_size: true, header: false}
	        ]
	    });

	    const pGrid = layout.cells('a').attachGrid();
		pGrid.setHeader("Code,Name,Unit,Stock",null,["text-align:center;","text-align:center;"]);
		pGrid.attachHeader("#text_filter,#text_filter,#select_filter,#text_filter");
		pGrid.setColumnIds('item_kode,item_nama,satuan,stok'); 
		pGrid.setColTypes("ro,ro,ro,ron"); 
		pGrid.setInitWidths("120,360,50,50");
		pGrid.init();
		pGrid.load("../../libs/initarmasi.php?mode=dtsparepartlokal&kd="+assetCode, "xml");
		pGrid.attachEvent('onRowSelect', rowId => {
			const item_kode = pGrid.cells(rowId, pGrid.getColIndexById('item_kode')).getValue();
			const item_nama = pGrid.cells(rowId, pGrid.getColIndexById('item_nama')).getValue();
			const satuan = pGrid.cells(rowId, pGrid.getColIndexById('satuan')).getValue();
			const stok = pGrid.cells(rowId, pGrid.getColIndexById('stok')).getValue();
			const newId = (new Date()).getTime()+Math.ceil(Math.random()*1000);
			gridSparepart.addRow(newId, "");
			gridSparepart.setRowColor(newId, "greenyellow");
			gridSparepart.cells(newId, gridSparepart.getColIndexById('item_code')).setValue(item_kode);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('item_name')).setValue(item_nama);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('unit')).setValue(satuan);
			gridSparepart.cells(newId, gridSparepart.getColIndexById('stock_qty')).setValue(stok);
			window_cabang.close();
	    });  
	};

  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>
