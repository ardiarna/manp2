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

<div class="row">
  <div class="col-md-12">
      <div class="box box-info">
          <div class="box-body" id="boxAwal" style="display: ;">
              <form class="form-horizontal" id="frCari">
                  <div class="form-group">
                      <label class="col-sm-1 control-label" style="text-align:left;">Dari : </label>
                      <div class="col-sm-2" style="margin-top:3px;">
                          <input class="form-control input-sm" type="text" name="tglFrom" id="tglFrom">
                      </div>
                      <label class="col-sm-1 control-label" style="text-align:left;margin-top:3px;">s/d : </label>
                      <div class="col-sm-2" style="margin-top:3px;">
                          <input class="form-control input-sm" type="text" name="tglTo" id="tglTo">
                      </div>
                      <div class="col-sm-4" style="margin-top:3px;">
                          <button type="button" id="btnCari" class="btn btn-primary btn-sm"><span>GO</span></button>
                      </div>
                  </div>
              </form>
              <div id="kontensm">
                  <table id="tblsm"></table>
                  <div id="pgrsm"></div>        
              </div>
          </div>
          <div class="box-body" id="boxEdit" style="display: ;">
              <form class="form-horizontal" id="frEdit">
                  <input type="hidden" name="aded" id="aded" readonly>
                  <input type="hidden" name="asset_code_lm" id="asset_code_lm" readonly>
                  <input type="hidden" name="tanggal_lm" id="tanggal_lm" readonly>
                  <div class="form-group">
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label class="col-sm-2 control-label" style="text-align:left;">ASSET</label>
                              <div class="col-sm-8" style="margin-top:3px;">
                                  <select class="form-control input-sm" id="asset_code" name="asset_code"></select>   
                              </div>  
                          </div>
                          <div class="form-group">
                              <label class="col-sm-2 control-label" style="text-align:left;">TANGGAL</label>
                              <div class="col-sm-4" style="margin-top:3px;">  
                                  <input class="form-control input-sm" type="text" name="tanggal" id="tanggal" readonly>
                              </div>    
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label class="col-sm-2 control-label" style="text-align:left;">Check List</label>
                              <div class="col-sm-8" style="margin-top:3px;">
                                  <select class="form-control input-sm" id="ceklist_code" name="ceklist_code"></select>   
                              </div>  
                          </div>
                      </div>
                  </div>
                  <div class="table-responsive" id="divdetail"></div>
                  <div class="form-group">
                      <div class="col-sm-12" style="margin-top:3px;text-align:center;">
                          <button type="button" class="btn btn-primary btn-sm" onclick="simpanData()" id="btnSimpan" style="display:none;">Simpan</button> <button type="button" class="btn btn-warning btn-sm" onclick="formAwal()">Batal</button>
                      </div>
                  </div>
              </form>    
          </div>
      </div>
  </div>
</div>
<script type="text/javascript">
var frm = "include/inceklist.php";
var vdropmenu = false;
var validator = "";

function tampilTabel(tblnya, pgrnya, pTanggal){
    var topnya = tblnya+"_toppager";
    var vshrinktofit = true;
    var vpanjanglayar = 150;
    if($(window).height() >= 520){vpanjanglayar = $(window).height()-(250+$("#frCari").height()+$(".content-header").height());}
    if($(window).width() <= 800){vshrinktofit = false;}
    jQuery(tblnya).jqGrid({
        url:frm + "?mode=urai&tanggal="+pTanggal,
        mtype:"POST",
        datatype:"json",
        colModel:[
            {label:"ASSET#", name:'asset_code', index:'asset_code', width:55, align:'center'},
            {label:"ASSET NAME", name:'asset_name', index:'asset_name', width:175, align:'left'},
            {label:"TANGGAL", name:'tanggal', index:'tanggal', width:70, align:'center'},
            {label:"KONTROL", name:'kontrol', index:'kontrol', width:90, align:'center'}
        ],
        sortname:"asset_code, tanggal",
        sortorder:'desc', 
        styleUI:"Bootstrap",
        hoverrows:false,
        loadonce:false,
        height:vpanjanglayar,
        rowNum:-1,
        rowList:[5,10,15,20,"-1:All"],
        rownumbers:true,
        pager:pgrnya,
        editurl:frm,
        altRows:true,
        viewrecords:true,
        autowidth:true,
        shrinkToFit:vshrinktofit,
        toppager:true,
    });

    jQuery(tblnya).jqGrid('navGrid', topnya,
        {
            add:false,
            edit:false,
            del:false,
            view:false,
            search:false,
            refresh:false,
            alertwidth:250,
            dropmenu:vdropmenu
       }, //navbar
        {}, //edit
        {}, //new
        {}, //del
        {}, //serch
        {}, //view
    );
    jQuery(tblnya).jqGrid('filterToolbar');
    $('.ui-search-toolbar').hide();
    $(topnya+"_center").hide();
    $(topnya+"_right").hide();
    $(topnya+"_left").attr("colspan", "3");

    jQuery(tblnya).jqGrid('navButtonAdd', topnya+"_left", {caption:"", buttonicon:'glyphicon-plus-sign', title:"Tambah data", onClickButton:tambahData});

    jQuery(tblnya).jqGrid('navButtonAdd', topnya+"_left", {
        caption:"", buttonicon:'glyphicon-search', title:"Tampilkan baris pencarian",
        onClickButton:function () {
            this.toggleToolbar();
       }
    });

    $(pgrnya+"_center").hide();
}

function formAwal(){
  $("#aded").val("");
  $("#asset_code, #asset_code_lm").val("");
  $("#asset_code").trigger('change');
  $("#tanggal, #tanggal_lm").val("");
  $("#ceklist_code").html("");
  $("#divdetail").html("");
  $("#asset_code, #ceklist_code").attr('disabled',false);
  $("#boxEdit, #btnSimpan").hide();
  $("#boxAwal").show();
}

function tambahData() {
  $("#aded").val("add");
  $("#tanggal").datepicker('setDate',moment().format("DD-MM-YYYY"));
  $("#boxAwal").hide();
  $("#boxEdit, #btnSimpan").show();  
}

function lihatData(kode){
    $.post(frm+"?mode=detailtabel", {stat:"view",kode:kode}, function(resp,stat){
        var o = JSON.parse(resp);
        $("#asset_code, #asset_code_lm").val(o.asset_code);
        $("#asset_code").trigger('change');
        $("#tanggal, #tanggal_lm").val(o.tanggal);
        $.post(frm+"?mode=cboceklist", {asset_code:o.asset_code}, function(resp,stat){
          $("#ceklist_code").html(resp);
          $("#ceklist_code").val(o.ceklist_code);
          $("#ceklist_code").trigger('change');
        });
        $("#divdetail").html(o.detailtabel);
        $("#asset_code, #ceklist_code").attr('disabled',true);
        $("#boxAwal, #btnSimpan").hide();
        $("#boxEdit").show();  
    });
}

function editData(kode){
    $.post(frm+"?mode=detailtabel", {stat:"edit",kode:kode}, function(resp,stat){
        var o = JSON.parse(resp);
        $("#aded").val("edit");
        $("#asset_code, #asset_code_lm").val(o.asset_code);
        $("#asset_code").trigger('change');
        $("#tanggal, #tanggal_lm").val(o.tanggal);
        $.post(frm+"?mode=cboceklist", {asset_code:o.asset_code}, function(resp,stat){
          $("#ceklist_code").html(resp);
          $("#ceklist_code").val(o.ceklist_code);
          $("#ceklist_code").trigger('change');
        });
        $("#divdetail").html(o.detailtabel);
        $("#asset_code, #ceklist_code").attr('disabled',true);
        $("#boxAwal").hide();
        $("#boxEdit, #btnSimpan").show();  
    });
}

function hapusData(kode) {
  var kd = kode.split("@@");
  var r = confirm("Hapus data checklist asset "+kd[0]+" tanggal "+kd[1]+" ?");
  if (r == true) {
    $.post(frm+"?mode=hapus", {kode:kode}, function(resp,stat){
      if (resp=="OK") {
        alert("Data checklist asset "+kd[0]+" tanggal "+kd[1]+" berhasil dihapus");
        $.jgrid.gridUnload("#tblsm");
        tampilTabel("#tblsm","#pgrsm",$("#tglFrom").val()+"@"+$("#tglTo").val());
      } else {
        alert(resp);
      }  
    });
  } else {
    return false;
  }
}

function simpanData() {
  var rulenya = {
    asset_code:{required:true},
    tanggal:{required:true},
    ceklist_code:{required:true}
  };
    
  if(validator != "") {
    validator.destroy();
  }
    
  validator = $("#frEdit").validate({rules:rulenya});
    
  if($("#frEdit").valid()) {
    var mode = $("#aded").val();
    $.post(frm+"?mode="+mode, $("#frEdit").serialize(), function(resp,stat){
      if (resp=="OK") {
        if (mode == "add") {
          alert("Data berhasil disimpan");
        } else {
          alert("Perubahan data "+$("#asset_code").val()+" - "+$("#tanggal").val()+" berhasil disimpan");
        }
        formAwal();
        $.jgrid.gridUnload("#tblsm");
        tampilTabel("#tblsm","#pgrsm",$("#tglFrom").val()+"@"+$("#tglTo").val());
      }else{
        alert(resp);
      }
    });
  } 
}

function showCeklist(ceklist_code) {
  $.post(frm+"?mode=loadceklist", {ceklist_code:ceklist_code}, function(resp,stat){
    $("#divdetail").html(resp); 
  });
}

function tampilTrNote(cd_code) {
  $("#trnote_"+cd_code).show();
}

$(document).ready(function () {
  var ubahUkuranJqGrid = function(){
    var vukur = $('#kontensm').width(); 
    if(vukur <= 800){
      $("#tblsm").setGridWidth(vukur, false); 
    } else {
      $("#tblsm").setGridWidth(vukur, true);
    }
  };
  $('#kontensm').resize(ubahUkuranJqGrid);
  var ubahTinggiJqGrid = function(){
    var vpanjanglayar = 150;
    if($(window).height() >= 520){
      vpanjanglayar = $(window).height()-(190+$("#frCari").height()+$(".content-header").height());
    }
    $("#tblsm").setGridHeight(vpanjanglayar);
  };
  $('#frCari').resize(ubahTinggiJqGrid);

  $("#tglFrom").datepicker({
    autoclose:true,
    format:'dd-mm-yyyy',
    todayHighlight:true,
    endDate:'date'
  }).on('changeDate', function(e) {
    var tglTo = $("#tglTo").val().split("-");
    var tglb = new Date(tglTo[2], parseInt(tglTo[1])-1, tglTo[0]);
    var tgla = new Date(e.date.getFullYear(), e.date.getMonth(), e.date.getDate());
    $("#tglTo").datepicker('setStartDate', tgla);
    if(tgla > tglb) {
        alert('Tanggal Dari tidak boleh lebih cepat dari Tanggal s/d, mohon ubah Tanggal s/d.');
        $("#tglTo").datepicker('show');
    }
  }).val(moment().format("01-MM-YYYY"));

  $("#tglTo").datepicker({
    autoclose:true,
    format:'dd-mm-yyyy',
    todayHighlight:true,
    endDate:'date',
    startDate:'date'
  }).val(moment().format("DD-MM-YYYY"));

  $("#tanggal").datepicker({
    autoclose:true,
    format:'dd-mm-yyyy',
    endDate:'date',
    // startDate:'-1d'
  });

  tampilTabel("#tblsm","#pgrsm",$("#tglFrom").val()+"@"+$("#tglTo").val());

  $("#asset_code").select2();
  $("#ceklist_code").select2();
  $.post(frm+"?mode=cboasset", function(resp,stat){
    $("#asset_code").html(resp);
  });
  
  $('#asset_code').change(function(){
    var aded = $('#aded').val();
    if (aded == "add") {
      $.post(frm+"?mode=cboceklist", {asset_code:this.value}, function(resp,stat){
        $("#ceklist_code").html(resp);
        showCeklist($("#ceklist_code").val());  
      });
    }
  });

  $('#ceklist_code').change(function() {
    var aded = $('#aded').val();
    if (aded == "add") {
      showCeklist(this.value);
    }
  });

  $("#boxEdit").hide(); 
    
});

</script>

