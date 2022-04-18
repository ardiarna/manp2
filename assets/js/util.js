const compname="Arwana Citramulia";

function getFormattedDate(date) {
  var year = date.getFullYear();
  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;
  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  return year + "-" + month + "-" + day;
}

function getFormattedDate1(date) {
  var year = date.getFullYear();
  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;
  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  return year + "-" + month + "-" + "01";
}

function showLocationWindow(windows,instform,instfield) {

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
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};


function showManufactureWindow(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Manufacture List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Manufacture Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtmanufacture", "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showOperatorWindow(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Personnel List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Code,Name,Depart,Position",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,90,140,100,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#select_filter,#select_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtoperator", "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showGroupWindow(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Group List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Category Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtgroup", "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};


function showCategoryWindow(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Category List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Category Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtcategory", "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showSubLocationWindow(windows,instform,kdlocation,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Sub Location List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtsublocation&kd="+kdlocation, "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showParentWindow(windows,instform,kdgroup,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 500, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Parent List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Code ,Description",null,["text-align:center;","text-align:center;"]); 
		pGrid.setColTypes("ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,150,*");
		pGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtparent&kd="+kdgroup, "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showAssetWindow(windows,instform,instfield) {

        const window_cabang = windows.createWindow("w1", 0, 0, 650, 300);
        window_cabang.centerOnScreen();
        window_cabang.setText('Asset List');
        window_cabang.button("park").hide();
        window_cabang.setModal(true);
        window_cabang.button("minmax1").hide();
		
		const pGrid = window_cabang.attachGrid();
			
		pGrid.setHeader("No,Asset,Description,,Location,Category,Status"); 
		pGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro"); 
		pGrid.setInitWidths("40,80,200,0,100,100,70");
		pGrid.attachHeader(",#text_filter,#text_filter,,#select_filter,#select_filter,#select_filter");
		pGrid.init();
		pGrid.load("../../libs/utils.php?mode=dtasset", "xml");
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showWorkTypeWindow(windows,instform,instfield) {

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
		
		pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		
		var osize = 0, key;
		for (key in instfield) {
				instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize+1).getValue());
				osize++;
		}
		
		window_cabang.close();
		});  
};

function showSparePartWindow(windows, gridSparepart) {
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
	pGrid.setHeader("Code,Name,Unit",null,["text-align:center;","text-align:center;"]);
	pGrid.setColumnIds('item_kode,item_nama,satuan');
	pGrid.attachHeader("#text_filter,#text_filter,#select_filter"); 
	pGrid.setColTypes("ro,ro,ro"); 
	pGrid.setInitWidths("130,400,50");
	pGrid.init();
	pGrid.attachEvent('onRowDblClicked', rowId => {
		const item_nama = pGrid.cells(rowId, pGrid.getColIndexById('item_nama')).getValue();
		const satuan = pGrid.cells(rowId, pGrid.getColIndexById('satuan')).getValue();
		const newId = (new Date()).getTime()+Math.ceil(Math.random()*1000);
		gridSparepart.addRow(newId, "");
		gridSparepart.setRowColor(newId, "greenyellow");
		gridSparepart.cells(newId, gridSparepart.getColIndexById('amsp_sparepart_code')).setValue(rowId);
		gridSparepart.cells(newId, gridSparepart.getColIndexById('amsp_sparepart_desc')).setValue(item_nama);
		gridSparepart.cells(newId, gridSparepart.getColIndexById('amsp_unit')).setValue(satuan);
		window_cabang.close();
    });  
};

function showSparePartWindowList(windows, gridSparepart) {
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
	pGrid.attachEvent('onRowDblClicked', rowId => {
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

function showSparePartWindowList2(windows, gridSparepart, assetCode) {
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
	pGrid.attachEvent('onRowDblClicked', rowId => {
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

function showMaintenanceWindow(windows,instform,instfield,assetcode) {
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
	
	pGrid.attachEvent("onRowDblClicked", function(rId,cInd){
	
	var osize = 0, key;
	for (key in instfield) {
			instform.setItemValue(instfield[osize],pGrid.cells(pGrid.getSelectedId(),osize).getValue());
			osize++;
	}
	
	window_cabang.close();
	});  
};