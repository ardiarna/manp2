<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTC Mobile</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="../assets/fonts/font_awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/libs/ionicons/ionicons.min.css">
  <link rel="stylesheet" href="../assets/libs/adminlte/css/AdminLTE.css">
  <link rel="stylesheet" href="../assets/libs/adminlte/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="../assets/libs/jquery/ui.jqgrid-bootstrap.css">
  <link rel="stylesheet" href="../assets/libs/jquery/select2.min.css">
  <script src="../assets/libs/jquery/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/libs/bootstrap/js/bootstrap-datepicker.min.js"></script>
  <script src="../assets/libs/adminlte/js/adminlte.js"></script>
  <script src="../assets/libs/moment/moment-with-locales.min.js"></script>
  <script src="../assets/libs/jquery//i18n/grid.locale-id.js"></script>
  <script src="../assets/libs/jquery/jquery.jqGrid.min.js"></script>
  <script src="../assets/libs/jquery/jquery.resize.js"></script>
  <script src="../assets/libs/jquery/jquery.validate.js"></script> 
  <script src="../assets/libs/jquery/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $(".sidebar-menu").tree();
      $("a.menua").click(function(){
        $("a.menua").parent().removeClass("active");
        $(".treeview").removeClass("active");
        $("#kontenUtama").html("");    
        $(this).parent().addClass("active");
        $(this).parents(".treeview").addClass("active");
        $("#kontenUtama").load($(this).attr("lk")+".php");
        if($(window).width() <= 747){$('[data-toggle="push-menu"]').pushMenu('toggle');};
      });
    })

    function bodyload(){
      $("#kontenUtama").load("inceklist.php");
    }
  </script>
</head>
<body class="hold-transition skin-blue-light sidebar-mini fixed" onload="bodyload()">
<div class="wrapper">
  <header class="main-header">
    <a href="index.php" class="logo">
      <span class="logo-mini"><img src="logo_arwana.png"></span>
      <span class="logo-lg"><b>Arwana</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="lobarugo" style="float:left;width:51%;height:50px;text-align:center;line-height:50px;color:#fff;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;" href="index.php">
        <span><b>Arwana MTC</b></span>
      </a>
    </nav>
  </header>
  <aside class="main-sidebar">
    <section class="sidebar" data-widget="tree">
      <ul class="sidebar-menu tree" >
        <li class="treeview">
          <a href="#" class="menua" lk="inceklist"><i class="fa fa-dashboard"></i><span>Input Check List</span></a>
        </li>
        <li class="treeview">
          <a href="#" class="menua" lk="ceklist"><i class="fa fa-folder"></i><span>Report Check List</span></a>
        </li>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    <section class="content-header" style="margin-bottom: -35px;">
      <div class="col-md=12" style="display: none" id="kepala">
      </div>
    </section>
    <section class="content" id="kontenUtama">
    </section>
  </div>
  <footer class="main-footer fixed">
    <b>Arwana</b> - 2020
    <a href="#" class="back-to-top" title="Back to top"><i class="fa fa-angle-double-up fa-2x" style="color:#ffffff"></i></a>
  </footer>
  <aside class="control-sidebar control-sidebar-dark">
    <div class="tab-content">
      <div class="tab-pane" id="control-sidebar-home-tab"></div>
    </div>
  </aside>
  <div class="control-sidebar-bg"></div>


</div>
</body>
</html>
