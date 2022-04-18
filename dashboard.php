
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Dashboard</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

  <link rel="stylesheet" type="text/css" href="assets/libs/dhtmlx/dhtmlx.css"/>
  <link rel="stylesheet" type="text/css" href="assets/fonts/font_roboto/roboto.css"/>
  <link rel="stylesheet" type="text/css" href="assets/fonts/font_awesome/css/font-awesome.min.css"/>
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

  <script src="assets/libs/dhtmlx/dhtmlx.js"></script>
  <script src="assets/libs/moment/moment-with-locales.min.js"></script>
  <script src="assets/libs/pdfmake/pdfmake.min.js"></script>
  <script src="assets/libs/pdfmake/vfs_fonts.js"></script>
  <script src="assets/libs/lodash/lodash.min.js"></script>
  <script src="assets/js/date-utils.js"></script>
  <script src="assets/js/grid-utils.js"></script>
  <script src="assets/js/util.js"></script>
  <script>
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
        { id: "t1", text: "Request & WO", active: true },
        { id: "t2", text: "Downtime & Cost"}
      ]
    });

    tabbar.tabs('t1').attachURL('dashboard_t1.php');
    tabbar.tabs('t2').attachURL('dashboard_t2.php'); 

  }
  </script>
</head>
<body onload="doOnLoad()">

</body>
</html>