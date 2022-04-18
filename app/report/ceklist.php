<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Report Check List</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../../assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/libs/bootstrap/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="../../assets/fonts/font_awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../../assets/libs/ionicons/ionicons.min.css">
  <link rel="stylesheet" href="../../assets/libs/adminlte/css/AdminLTE.css">
  <link rel="stylesheet" href="../../assets/libs/jquery/ui.jqgrid-bootstrap.css">
  <link rel="stylesheet" href="../../assets/libs/jquery/select2.min.css">
  <script src="../../assets/libs/jquery/jquery.min.js"></script>
  <script src="../../assets/libs/bootstrap/js/bootstrap.min.js"></script>
  <script src="../../assets/libs/bootstrap/js/bootstrap-datepicker.min.js"></script>
  <script src="../../assets/libs/adminlte/js/adminlte.js"></script>
  <script src="../../assets/libs/moment/moment-with-locales.min.js"></script>
  <script src="../../assets/libs/jquery//i18n/grid.locale-id.js"></script>
  <script src="../../assets/libs/jquery/jquery.jqGrid.min.js"></script>
  <script src="../../assets/libs/jquery/jquery.resize.js"></script>
  <script src="../../assets/libs/jquery/jquery.validate.js"></script> 
  <script src="../../assets/libs/jquery/select2.min.js"></script>
  <style type="text/css">
    #tblsm,
    .ui-jqgrid-htable {
        font-size:11px;
   }
    th {
      text-align:center;
   }     
  select {
    font-family: 'FontAwesome', 'sans-serif';
  }
  </style>
</head>
<body class="hold-transition skin-blue-light sidebar-mini fixed">
<section class="content-header" style="margin-bottom: -35px;">
  <div class="col-md=12" style="" id="kepala">
        <ol class="breadcrumb" style="background-color: #3c8dbc">
            <li class="active" style="color: #fff"><i class="fa fa-cube"></i> <span id="kontenKepala">Report Check List</span> </li>
        </ol>
  </div>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Info Inputan Data</h4>
                        </div>
                        <div class="modal-body table-responsive" id="isiModal">...Sedang Memuat Data...</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body" id="boxAwal">
                <form class="form-horizontal" id="frCari">
                    <div class="form-group">
                        <div class="col-sm-5" style="margin-top:3px;">
                            <div class="input-group">
                                <div class="input-group-addon"> Asset : </div>
                                <select class="form-control input-sm" name="cmbAsset" id="cmbAsset"></select>  
                            </div>
                        </div>
                        <div class="col-sm-3" style="margin-top:3px;">
                            <div class="input-group">
                                <div class="input-group-addon"> Month : </div>
                                <select class="form-control input-sm" name="cmbMonth" id="cmbMonth">
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
                            </div>
                        </div>
                        <div class="col-sm-3" style="margin-top:3px;">
                            <div class="input-group">
                                <div class="input-group-addon"> Year : </div>
                                <select class="form-control input-sm" name="cmbYear" id="cmbYear">
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
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-top:3px;">
                            <button type="button" id="btnCari" class="btn btn-primary btn-sm"><span>GO</span></button>
                        </div>
                    </div>
                </form>
                <form class="form-horizontal" id="frExport" style="display:none;">
                    <div class="form-group">
                        <div class="col-sm-3" style="margin-top:3px;">
                            <div class="input-group">
                                <div class="input-group-addon"> Ekspor ke : </div>
                                <select class="form-control input-sm" id="cmbExport">
                                    <option value="XLS">Excel 97-2003 (.xls)</option>
                                    <!-- <option value="XLSX">Excel (.xlsx)</option> -->
                                    <!-- <option value="PDF">PDF</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-top:3px;">
                            <button type="button" id="btnExport" class="btn btn-success btn-sm"><span>Ekspor</span></button>
                        </div>
                    </div>
                </form>
                <div class="box box-default box-solid" id="dvLoading" style="display:none;">
                    <div class="box-header with-border">
                        <center><strong>..Sedang Memuat Data..</strong></center>
                    </div>
                </div>
                <div id="kontensm"></div>
                <div id="dvInfo" style="display:none;"></div>
            </div>
        </div>
    </div>
  </div>
</section>
<script type="text/javascript">
var frm = "include/ceklist.php";

function uraiData() {
  $("#kontensm").html("");
  $("#frExport, #dvInfo").hide();
  $("#dvLoading").show();
  $.post(frm+"?mode=urai", {asset:$("#cmbAsset").val(),bln:$("#cmbMonth").val(),thn:$("#cmbYear").val()},function(resp,stat){
    var o = JSON.parse(resp);
    if(o.detailtabel == 'TIDAKADA') {
      $("#kontensm").html('<div style="background-color:orange;"><center><strong>..Tidak Ada Data..</strong></center></div>');
    } else {
      $("#kontensm").html(o.detailtabel);
      $("#frExport, #dvInfo").show();    
    }
    $("#dvLoading").hide();
  });
}

$(document).ready(function () {
  $("#cmbAsset").select2();
  $("#cmbMonth").select2();
  $("#cmbYear").select2();
  $("#cmbMonth").val(moment().format("M"));
  $("#cmbMonth").trigger('change');
  $("#cmbYear").val(moment().format("YYYY"));
  $("#cmbYear").trigger('change');

  $.post(frm+"?mode=cboasset", function(resp,stat){
    $("#cmbAsset").html(resp);
    var rulenya = {
      cmbAsset:{required:true},
      cmbMonth:{required:true},
      cmbYear:{required:true}
    };
    $("#frCari").validate({rules:rulenya});
  });

  $('#btnCari').click(function(){
    if($("#frCari").valid()) {
      uraiData();
    }
  });

  $('#btnExport').click(function(){
        var frmt = $("#cmbExport").val();
        if (frmt == 'XLS') {
            opsi = "width=900,height=600,screenX=500,toolbars=1,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable='no'";
            window.open(frm+"?mode=excel&asset=" +$("#cmbAsset").val()+"&bln=" +$("#cmbMonth").val()+"&thn=" +$("#cmbYear").val(),"",opsi);    
        } 
    });
    
});

</script>
</body>
</html>
