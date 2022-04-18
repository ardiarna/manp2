<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="assets/libs/materialize/css/materialize.css">
  <style type="text/css">
    /*.m6.s12 {
      padding-left: 0px;
      padding-right: 5px;
    }*/
    /*blockquote {
      margin: 10px 0px;
      padding-left: 10px;
    }*/
    /*.row {
      margin-bottom: 0px;
    }*/
    span.badge.new {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="modal modal-fixed-footer" id="myModal">
    <div class="modal-content">
      <h4 id="judulModal"></h4>
      <div id="isiModal">...Loading...</div>
    </div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
  </div>
  <div class="row">
    <div class="col m6 s12">
      <blockquote class="teal lighten-5 z-depth-4">
        <table class="highlight">
          <tbody>
          <tr>
            <th>Request Bulan Ini</th>
            <td><span class="new badge blue" data-badge-caption="" id="spReqBlnIni" onclick="detailReqBlnIni();">0</span></td>
          </tr>
          <tr>
            <th>Request Belum di-Approve</th>
            <td><span class="new badge lime darken-2" data-badge-caption="" id="spReqNotAppr" onclick="detailReqNotAppr();">0</span></td>
          </tr>
          <tr>
            <th>Request Belum di-Schedule-kan</th>
            <td><span class="new badge yellow darken-4" data-badge-caption="" id="spReqNotSch" onclick="detailReqNotSch();">0</span></td>
          </tr>
          <tr>
            <th>Request Cancel</th>
            <td><span class="new badge red" data-badge-caption="" id="spReqCancel" onclick="detailReqCancel();">0</span></td>
          </tr>
          </tbody>
        </table>
      </blockquote>
      <blockquote class="teal lighten-5 z-depth-4">
        <div id="kontener6" class="teal lighten-5" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
      </blockquote>
    </div>
    <div class="col m6 s12">
      <blockquote class="cyan lighten-5 z-depth-2">
        <table class="highlight">
          <tbody>
          <tr>
            <th>Work Order Bulan Ini</th>
            <td><span class="new badge blue" data-badge-caption="" id="spWoBlnIni" onclick="detailWoBlnIni();">0</span></td>
          </tr>
          <tr>
            <th>Work Order Completed Bulan Ini</th>
            <td><span class="new badge green" data-badge-caption="" id="spWoComp" onclick="detailWoComp();">0</span></td>
          </tr>
          <tr>
            <th>Work Order Belum Siap Dikerjakan</th>
            <td><span class="new badge lime darken-2" data-badge-caption="" id="spWoNotReady" onclick="detailWoNotReady();">0</span></td>
          </tr>
          <tr>
            <th>Work Order Belum Completed</th>
            <td><span class="new badge yellow darken-4" data-badge-caption="" id="spWoNotComp" onclick="detailWoNotComp();">0</span></td>
          </tr>
          <tr>
            <th>Work Order Cancel</th>
            <td><span class="new badge red" data-badge-caption="" id="spWoCancel" onclick="detailWoCancel();">0</span></td>
          </tr>
          </tbody>
        </table>
      </blockquote>
      <blockquote class="cyan lighten-5 z-depth-5">
        <div id="kontener7" class="cyan lighten-5" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
      </blockquote>
    </div>
  </div>
  
  <script src="assets/libs/jquery/jquery.min.js"></script>
  <script src="assets/libs/materialize/js/materialize.min.js"></script>
  <!-- <script src="assets/libs/bootstrap/js/bootstrap-datepicker.min.js"></script> -->
  <script src="assets/libs/highcharts/highcharts.js"></script>
  <script src="assets/libs/highcharts/modules/exporting.js"></script>
  <script src="assets/libs/moment/moment-with-locales.min.js"></script>
  <script type="text/javascript">
    const FRM = 'dashboard.inc.php';

    setInterval(function(){
      reqWO();    
    },5*60*1000);

    $(document).ready(function () {
      $('.modal').modal();
      reqWO();
    })
      
    function tampilChart(grfnya, typenya, axisx, judulnya, juduly, datanya) {
      Highcharts.chart(grfnya, {
        chart: {
          type: typenya
        },
        title: {
          text: judulnya
        },
        xAxis: {
          categories: axisx,
          crosshair: true
        },
        yAxis: {
          title: {
            text: juduly
          }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px;color:{series.color}">{series.name}</span><br>',
            pointFormat: ': <b>{point.y:f} Min</b><br/>'
        },
        credits : {
          enabled : false
        },
        plotOptions: {
          column: {
            dataLabels: {
              enabled: true
            }  
          },
          line: {
            dataLabels: {
              enabled: true
            },
            enableMouseTracking: false
          },
          series: {
            cursor: 'pointer',
            point: {
                events: {
                    click: function() {
                        viewDetailChart(grfnya, this.series.name);
                    }
                }
            }
          }
        },
        series: datanya
      });
    }

    function reqWO() {
      $.ajax({
        url: FRM+ "?mode=reqwo", 
        success: function(result){
          var o = JSON.parse(result);
          $("#spReqBlnIni").html(o.req_bln_ini);
          $("#spReqNotAppr").html(o.req_not_appr);
          $("#spReqNotSch").html(o.req_not_sch);
          $("#spReqCancel").html(o.req_cancel);
          $("#spWoBlnIni").html(o.wo_bln_ini);
          $("#spWoComp").html(o.wo_comp);
          $("#spWoNotReady").html(o.wo_not_ready);
          $("#spWoNotComp").html(o.wo_not_comp);
          $("#spWoCancel").html(o.wo_cancel);
          console.log(o.chart_req);
          tampilChart('kontener6', 'line', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 'Request Tahun '+moment().format("YYYY"), 'Jumlah', o.chart_req);
          tampilChart('kontener7', 'line', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 'Work Order Tahun '+moment().format("YYYY"), 'Jumlah', o.chart_wo);
        }
      });
    }

    function detailReqBlnIni() {
      $.ajax({
        url: FRM+ "?mode=dtlreqblnini", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Request Bulan Ini');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailReqNotAppr() {
      $.ajax({
        url: FRM+ "?mode=dtlreqnotappr", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Request Belum di-Approve');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailReqNotSch() {
      $.ajax({
        url: FRM+ "?mode=dtlreqnotsch", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Request Belum di-Schedule-kan');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailReqCancel() {
      $.ajax({
        url: FRM+ "?mode=dtlreqcancel", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Request Cancel');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailWoBlnIni() {
      $.ajax({
        url: FRM+ "?mode=dtlwoblnini", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Work Order Bulan Ini');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailWoComp() {
      $.ajax({
        url: FRM+ "?mode=dtlwocomp", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Work Order Completed Bulan Ini');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailWoNotComp() {
      $.ajax({
        url: FRM+ "?mode=dtlwonotcomp", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Work Order Belum Completed');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }

    function detailWoCancel() {
      $.ajax({
        url: FRM+ "?mode=dtlwocancel", 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Work Order Cancel');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');   
        }
      });
    }
  </script>
</body>
</html>
