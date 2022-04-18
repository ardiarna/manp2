(function (global, factory) {
  if (typeof define === 'function' && define.amd) {
    define(['WMSApi', 'gridUtils', 'DateUtils', 'moment'], factory);
  } else if (typeof module === 'object' && module.exports) {
    module.exports = factory(require('WMSApi'), require('gridUtils'), require('DateUtils'), require('moment'))
  } else {
    if (!global.hasOwnProperty('WMSApi')) {
      throw Error('WMSApi is not loaded!')
    }
    if (!global.hasOwnProperty('gridUtils')) {
      throw Error('gridUtils is not loaded!')
    }
    if (!global.hasOwnProperty('DateUtils')) {
      throw Error('DateUtils is not loaded!')
    }
    if (!global.hasOwnProperty('moment')) {
      throw Error('moment is not loaded!')
    }
    global.MutationDetails = factory(global.WMSApi, global.gridUtils, global.DateUtils, global.moment)
  }
}(this, function (WMSApi, gridUtils, DateUtils, moment) {
  moment.locale('id');
  moment.defaultFormat = 'D MMM YYYY, HH:mm:ss';

  const STYLES = gridUtils.styles;
  const FILTERS = gridUtils.headerFilters;

  const COLUMN_MAP = Object.freeze({
    initial_quantity: 'BEGIN',

    prod_initial_quantity: 'PROD',
    manual_initial_quantity: 'PLM',
    in_mut_quantity: 'MUT_IN',
    in_adjusted_quantity: 'ADJ_IN',
    in_downgrade_quantity: 'DWG_IN',
    in_quantity_total: 'TTL_IN',

    out_mut_quantity: 'MUT_OUT',
    out_adjusted_quantity: 'ADJ_OUT',
    returned_quantity: 'PROD_RET',
    broken_quantity: 'BROKEN',
    sales_in_progress_quantity: 'SALES_IN_PROGRESS',
    sales_confirmed_quantity: 'SALES_CONFIRMED',
    foc_quantity: 'FOC',
    sample_quantity: 'SMP',
    out_downgrade_quantity: 'DWG_OUT',
    out_quantity_total: 'TTL_OUT',

    final_quantity: 'END'
  });

  function fetchDetailsData(mutationType, subplant, dateFrom, dateTo, motifId) {
    return WMSApi.stock.fetchStockMutationSummaryDetails(mutationType, subplant, dateFrom, dateTo, motifId)
      .then(summaryDetails => ({
        data: summaryDetails.map(detail => {
          let id;
          if (mutationType === COLUMN_MAP.initial_quantity || mutationType === COLUMN_MAP.final_quantity) {
            id = detail.pallet_no;
          } else if (mutationType === COLUMN_MAP.sales_confirmed_quantity || mutationType === COLUMN_MAP.sales_in_progress_quantity) {
            id = `${detail.mutation_id}_${detail.size}_${detail.shading}`;
          } else {
            id = `${detail.mutation_id}_${detail.pallet_no}_${detail.size}_${detail.shading}`;
          }
          return Object.assign(detail, { id: id });
        })
      }))
  }

  function openMutationDetailsWindow(windows, mutationType, subplant, dateFrom, dateTo, motifId, motifName, colName) {
    // setup window
    const win = windows.createWindow("w1", 0, 0, 700, 500);

    win.centerOnScreen();
    win.setText('Detail Mutasi Palet');
    win.button("park").hide();
    win.setModal(true);

    const title = `(${DateUtils.toSqlDate(dateFrom)} - ${DateUtils.toSqlDate(dateTo)}) Detail Mutasi ${subplant} - ${colName} - ${motifName}`;
    win.setText(title);

    // setup toolbar
    const toolbar = win.attachToolbar({
      iconset: 'awesome',
      items: [
        {type: 'button', id: 'refresh', text: 'Segarkan', img: 'fa fa-refresh'},
        {type: 'button', id: 'export_csv', text: 'Ke CSV', img: 'fa fa-file-excel-o'},
        {type: 'spacer'},
        {type: 'text', id: 'timestamp'}
      ]
    });
    toolbar.attachEvent('onClick', itemId => {
      if (itemId === 'refresh') {
        win.progressOn();
        fetchDetailsData(mutationType, subplant, dateFrom, dateTo, motifId)
          .then(summaryDetails => {
            win.progressOff();
            gridUtils.clearAllGridFilters(grid);
            grid.clearAll();
            grid.parse({ data: summaryDetails }, 'js');
            toolbar.setItemText('timestamp', moment().format());

            if (summaryDetails.length === 0) {
              dhtmlx.message(`Tidak ada detail mutasi ${mutationType} untuk ${subplant} - ${motifName} pada periode yang diminta!`)
            }
          })
          .catch(error => {
            win.progressOff();
            console.error(error);
            dhtmlx.alert({
              type: "alert-warning",
              text: error instanceof Object ? error.message : error,
              title: 'Error'
            })
          });
      } else if (itemId === 'export_csv') {
        gridUtils.downloadFilteredCSV(grid, title)
      }
    });

    // setup grid
    const grid = win.attachGrid();
    if (mutationType === COLUMN_MAP.initial_quantity) {
      grid.setHeader(
        'NO. PALET,SIZE,SHADE,QTY.',
        null,
        ['', '', '', STYLES.TEXT_RIGHT_ALIGN]
      );
      grid.setColTypes('ro,ro,ro,ron');
      grid.setColumnIds('pallet_no,size,shading,quantity');
      grid.setInitWidths('*,60,60,60,50');
      grid.attachHeader([FILTERS.TEXT, FILTERS.SELECT, FILTERS.SELECT, FILTERS.NUMERIC]);
      grid.setColAlign('left,left,left,right');
      grid.setColSorting('str,str,str,int');
      grid.attachFooter(['Total', gridUtils.spans.COLUMN, gridUtils.reducers.STATISTICS_COUNT, gridUtils.reducers.STATISTICS_TOTAL],
        [STYLES.TEXT_BOLD, '', STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN, STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN]);

      grid.setNumberFormat('0,000', grid.getColIndexById('quantity'), ',', '.');

    } else if (mutationType === COLUMN_MAP.final_quantity) {
      grid.setHeader(
        'NO. PALET,SIZE,SHADE,LOKASI,QTY.',
        null,
        ['', '', '', '', STYLES.TEXT_RIGHT_ALIGN]
      );
      grid.setColTypes('ro,ro,ro,ro,ron');
      grid.setColumnIds('pallet_no,size,shading,location_id,quantity');
      grid.setInitWidths('*,60,60,90,70');
      grid.attachHeader([FILTERS.TEXT, FILTERS.SELECT, FILTERS.SELECT, FILTERS.TEXT, FILTERS.NUMERIC]);
      grid.setColAlign('left,left,left,left,right');
      grid.setColSorting('str,str,str,str,int');
      grid.attachFooter(['Total', gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.reducers.STATISTICS_COUNT, gridUtils.reducers.STATISTICS_TOTAL],
        [STYLES.TEXT_BOLD, '', '', STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN, STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN]);

      grid.setNumberFormat('0,000', grid.getColIndexById('quantity'), ',', '.');
    } else if (mutationType === COLUMN_MAP.sales_in_progress_quantity) {
      grid.setHeader(
        'TGL.,NO. BA MUAT,SIZE,SHADE,QTY.',
        null,
        ['', '', '', '', STYLES.TEXT_RIGHT_ALIGN]
      );
      grid.setColTypes('ro_date,ro,ro,ro,ron');
      grid.setColumnIds('mutation_date,mutation_id,size,shading,quantity');
      grid.setInitWidths('*,120,60,60,100');
      grid.attachHeader([FILTERS.TEXT, FILTERS.TEXT, FILTERS.SELECT, FILTERS.SELECT, FILTERS.NUMERIC]);
      grid.setColAlign('left,left,left,left,right');
      grid.setColSorting('str,str,str,str,int');
      grid.attachFooter(['Total', gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.reducers.STATISTICS_COUNT, gridUtils.reducers.STATISTICS_TOTAL],
        [STYLES.TEXT_BOLD, '', '', STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN, STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN]);

      grid.setNumberFormat('0,000', grid.getColIndexById('quantity'), ',', '.');

      // show shipping details
      grid.attachEvent('onRowDblClicked', (rowId) => {
        const shippingId = grid.cells(rowId, grid.getColIndexById('mutation_id')).getValue();
      })
    } else if (mutationType === COLUMN_MAP.sales_confirmed_quantity) {
      grid.setHeader(
        'TGL.,NO. BA MUAT,NO. SJ.,SIZE,SHADE,QTY.',
        null,
        ['', '', '', '', '', STYLES.TEXT_RIGHT_ALIGN]
      );
      grid.setColTypes('ro_date,ro,ro,ro,ro,ron');
      grid.setColumnIds('mutation_date,mutation_id,ref_txn_id,size,shading,quantity');
      grid.setInitWidths('*,120,120,60,60,100');
      grid.attachHeader([FILTERS.TEXT, FILTERS.TEXT, FILTERS.TEXT, FILTERS.SELECT, FILTERS.SELECT, FILTERS.NUMERIC]);
      grid.setColAlign('left,left,left,left,left,right');
      grid.setColSorting('str,str,str,str,str,int');
      grid.attachFooter(['Total', gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.reducers.STATISTICS_COUNT, gridUtils.reducers.STATISTICS_TOTAL],
        [STYLES.TEXT_BOLD, '', '', '', STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN, STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN]);

      grid.setNumberFormat('0,000', grid.getColIndexById('quantity'), ',', '.');

      // show shipping details
      grid.attachEvent('onRowDblClicked', (rowId) => {
        const shippingId = grid.cells(rowId, grid.getColIndexById('mutation_id')).getValue();
      })
    } else {
      grid.setHeader(
        'TGL.,NO. TXN.,NO. PALET,SIZE,SHADE,QTY.',
        null,
        ['', '', '', '', STYLES.TEXT_RIGHT_ALIGN]
      );
      grid.setColTypes('ro_ts,ro,ro,ro,ro,ron');
      grid.setColumnIds('mutation_time,mutation_id,pallet_no,size,shading,quantity');
      grid.setInitWidths('160,120,130,60,60,*');
      grid.attachHeader([FILTERS.TEXT, FILTERS.TEXT, FILTERS.TEXT, FILTERS.SELECT, FILTERS.SELECT, FILTERS.NUMERIC]);
      grid.setColAlign('left,left,left,left,left,right');
      grid.setColSorting('str,str,str,str,str,int');
      grid.attachFooter(['Total', gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.spans.COLUMN, gridUtils.reducers.STATISTICS_COUNT, gridUtils.reducers.STATISTICS_TOTAL],
        [STYLES.TEXT_BOLD, '', '', '', STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN, STYLES.TEXT_BOLD + STYLES.TEXT_RIGHT_ALIGN]);

      grid.setNumberFormat('0,000', grid.getColIndexById('quantity'), ',', '.');
    }

    grid.enableSmartRendering(true, 100);
    grid.init();

    win.progressOn();
    fetchDetailsData(mutationType, subplant, dateFrom, dateTo, motifId)
      .then(summaryDetails => {
        win.progressOff();
        gridUtils.clearAllGridFilters(grid);
        grid.clearAll();

        grid.parse(summaryDetails, 'js');
        toolbar.setItemText('timestamp', moment().format());

        if (summaryDetails.length === 0) {
          dhtmlx.message(`Tidak ada detail mutasi ${mutationType} untuk ${subplant} - ${motifName}!`)
        }
      })
      .catch(error => {
        win.progressOff();
        console.error(error);
        dhtmlx.alert({
          type: "alert-warning",
          text: error instanceof Object ? error.message : error,
          title: 'Error'
        })
      });
  }

  return { openMutationDetailsWindow, fetchDetailsData, COLUMN_MAP }
}));
