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
    }
    blockquote {
      margin: 10px 0px;
      padding-left: 10px;
    }
    .row {
      margin-bottom: 0px;
    }*/
    .input-field {
      margin-bottom: 0px;
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
      <div class="col m12 s12">
        <div class="col m2 input-field">
          <select id="cmbJenis">
              <option value="M">Monthly</option>
              <option value="Y" selected="true">Yearly</option>
          </select>
          <label></label>
        </div>
        <div class="col m3 input-field" id="dvMonth" style="display: none;">
          <select id="cmbMonth">
              <option value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
          </select>
          <label>Month : </label>
        </div>
        <div class="col m2 input-field">
          <select id="cmbYear">
            <option>2016</option>
            <option>2017</option>
            <option>2018</option>
            <option>2019</option>
            <option>2020</option>
            <option>2021</option>
            <option>2022</option>
            <option>2023</option>
            <option>2024</option>
            <option>2025</option>
            <option>2026</option>
            <option>2027</option>
            <option>2028</option>
            <option>2029</option>
            <option>2030</option>
          </select>
          <label>Year : </label>
        </div>
        <div class="col m1">
          <button type="button" class="waves-effect waves-light btn" style="margin-top:15px;" onclick="loadChart();">
            <span>GET</span>
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col m6 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
      <div class="col m6 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
    </div>
    <div class="row">
      <div class="col m6 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
      <div class="col m6 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener4" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
    </div>
    <div class="row">
      <div class="col m6 s12 offset-m3">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener5" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
    </div>


  <script src="assets/libs/jquery/jquery.min.js"></script>
  <script src="assets/libs/materialize/js/materialize.min.js"></script>
  <script src="assets/libs/highcharts/highcharts.js"></script>
  <script src="assets/libs/highcharts/modules/exporting.js"></script>
  <script src="assets/libs/moment/moment-with-locales.min.js"></script>
  <script type="text/javascript">
    const FRM = 'dashboard.inc.php';

    setInterval(function(){
      loadChart();    
    },5*60*1000);

    $(document).ready(function () {
      $("#cmbYear").val(moment().format("YYYY"));
      $("#cmbMonth").val(moment().format("M"));

      $('.modal').modal();
      $('select').formSelect();

      $("#cmbJenis").change(function(){
        if($(this).val() == 'M') {
          $("#dvMonth").show();
        } else {
          $("#dvMonth").hide();
        }
      });

      loadChart();
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

    function loadChart() {
      var cmbJenis = $("#cmbJenis").val();
      var cmbMonth = $("#cmbMonth").val();
      var cmbYear = $("#cmbYear").val();
      
      $.ajax({
        url: FRM+ "?mode=loadchart&jns="+cmbJenis+"&thn="+cmbYear+"&bln="+cmbMonth, 
        success: function(result){
          var o = JSON.parse(result);
          tampilChart('kontener1', 'column', [''], '5 Downtime Terbesar By Assets', 'Downtime (minutes)', o.data_a);
          tampilChart('kontener2', 'column', [''], '5 Downtime Terbesar By Maintenance Type', 'Downtime (minutes)', o.data_b);
          tampilChart('kontener3', 'column', [''], '5 Downtime Maintenance Terbesar', 'Downtime (minutes)', o.data_c);
          tampilChart('kontener4', 'column', [''], '5 Downtime Produksi Terbesar', 'Downtime (minutes)', o.data_d);
          tampilChart('kontener5', 'column', [''], '5 Asset Berbiaya Terbesar', 'Cost (rupiah)', o.data_e);            
        }
      });
    }

    function viewDetailChart(kontener, nilai) {
      var cmbJenis = $("#cmbJenis").val();
      var cmbMonth = $("#cmbMonth").val();
      var cmbYear = $("#cmbYear").val();
      
      $.ajax({
        url: FRM+ "?mode=dtlchart&jns="+cmbJenis+"&thn="+cmbYear+"&bln="+cmbMonth+"&ktn="+kontener+"&val="+nilai, 
        success: function(result){
          var o = JSON.parse(result);
          $("#judulModal").html('Detail');
          $("#isiModal").html(o.hasil);
          $('#myModal').modal('open');            
        }
      });
    }
    
  </script>
</body>
</html>
