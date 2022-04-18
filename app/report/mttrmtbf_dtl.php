<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../../assets/libs/materialize/css/materialize.css">
  <link rel="stylesheet" href="../../assets/fonts/font_material/material-icon.css"/>
  <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
  <style type="text/css">
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
    <div class="row" style="margin-bottom: 0px">
      <div class="col m12 s12">
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
        <div class="col m7 offset-m1">
          <div class="card-panel teal white-text" style="padding: 10px; padding-bottom: 1px;">
            
            <div class="row" style="margin-bottom: 0px;">
              <div class="col m1">
                &nbsp;
              </div>
              <div class="col m11">
                <span class="col m12" style="font-weight: bold;">MTTR : <span id="spMTTR"></span> Menit </span>
              </div>
            </div>
            <div class="row">
              <div class="col m1">
                <i class="material-icons">access_time</i>
              </div>
              <div class="col m11">
                <span class="col m4" style="font-size: 12px;"> <span id="spAsset"></span> <br> Tahun : <span id="spTahun"></span> </span>
                <span class="col m8" style="font-size: 12px;">Total Waktu Maintenance : <span id="spTotDowntime"></span> Menit <br> Total Jumlah Perbaikan : <span id="spTotJmlPerbaikan"></span> </span>
              </div>
            </div>
          
          </div>
        </div>
      </div>
    </div>
    <div class="row" style="margin-bottom: 0px">
      <div class="col m12 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
    </div>
    <div class="row" style="margin-bottom: 0px">
      <div class="col m12 s12">
        <blockquote class="teal lighten-5 z-depth-4">
          <div id="kontener2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </blockquote>
      </div>
    </div>


  <script src="../../assets/libs/jquery/jquery.min.js"></script>
  <script src="../../assets/libs/materialize/js/materialize.min.js"></script>
  <script src="../../assets/libs/highcharts/highcharts.min.js"></script>
  <script src="../../assets/libs/moment/moment-with-locales.min.js"></script>
  <script type="text/javascript">
    const FRM = 'include/mttrmtbf.php';
    const vAsset = "<?= $_GET['asset'] ?>";

    setInterval(function(){
      loadChart();    
    },5*60*1000);

    $(document).ready(function () {
      $("#cmbYear").val(moment().format("YYYY"));
      
      $('.modal').modal();
      $('select').formSelect();

      loadChart();
    })

    function tampilChartColumn(grfnya, judulnya, datanya) {
      Highcharts.chart(grfnya, {
        chart: {
          type: 'column'
        },
        title: {
          text: judulnya[0]
        },
        subtitle: {
          text: judulnya[1]
        },
        xAxis: {
          type:'category',
          crosshair:true
        },
        yAxis: {
          title: {
            text: 'menit'
          }
        },
        legend: {
          enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.y:.f} Menit</b><br/>'
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
          series: {
            cursor: 'pointer',
            point: {
                events: {
                    click: function() {
                        loadChartBulan(datanya[1], this.name);
                    }
                }
            }
          }
        },
        series: datanya[0]
      });
    }
      
    function tampilChartLine(grfnya, judulnya, datanya) {
      Highcharts.chart(grfnya, {
        title: {
          text: judulnya[0]
        },
        subtitle: {
          text: judulnya[1]
        },
        xAxis: {
          type:'category'
        },
        yAxis: {
          title: {
            text: 'menit'
          }
        },
        legend: {
          enabled: false
        },
        credits : {
          enabled : false
        },
        plotOptions: {
          line: {
            dataLabels: {
              enabled: true
            },
            enableMouseTracking: false
          },
          series: {
            label: {
                connectorAllowed: false
            }, 
            pointStart: 1
          }
        },
        series: datanya
      });
    }

    function loadChart() {
      var cmbYear = $("#cmbYear").val();
      
      $.ajax({
        url: FRM+ "?mode=dtl&asset="+vAsset+"&thn="+cmbYear, 
        success: function(result){
          var o = JSON.parse(result);
          $("#spAsset").html(o.asset);
          $("#spTahun").html(o.thn);
          $("#spMTTR").html(o.mttr_thn);
          $("#spTotDowntime").html(o.downtime_thn);
          $("#spTotJmlPerbaikan").html(o.jml_thn);
          tampilChartColumn('kontener1', ['MTTR BULANAN','TAHUN '+o.thn], [o.dtl_thn,o.thn]);
          tampilChartLine('kontener2', ['MTTR HARIAN',o.bln], o.dtl_bln);
        }
      });
     }

    function loadChartBulan(tahun, bulan) {
      $.ajax({
        url: FRM+ "?mode=dtlbulan&asset="+vAsset+"&thn="+tahun+"&bln="+bulan, 
        success: function(result){
          var o = JSON.parse(result);
          tampilChartLine('kontener2', ['MTTR HARIAN',o.bln], o.dtl_bln);
        }
      });
    }
    
  </script>
</body>
</html>
