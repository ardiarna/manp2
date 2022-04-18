<?php

require_once '../init.php';
require_once '../konfigurasiarmasi.php'; 
include("mpdf.php");

function dateadd($per,$n,$d) {
    switch($per) {
        case "yyyy": $n*=12;
        case "m":
            $d=mktime(date("H",$d),date("i",$d)
                    ,date("s",$d),date("n",$d)+$n
                    ,date("j",$d),date("Y",$d));
        $n=0; break;
        case "ww": $n*=7;
        case "d": $n*=24;
        case "h": $n*=60;
        case "n": $n*=60;
    }
    return $d+$n;
}

function baliktgl($date) {
    if($date<>''){
        $date_x=explode("-",$date);
        $tgl=$date_x[2]."/".$date_x[1]."/".$date_x[0];
    }
    return $tgl;
}

function cari_nilai($sql){
  global $app_plan_id, $armasi_conn;

    $res=pg_query($armasi_conn, $sql);
    $r=pg_fetch_row($res);
    return $r[0]; 
}

function format($nilai,  $jmldesimal = 2){
    return number_format($nilai, $jmldesimal);
}

$txtout = '<style>@page {
     margin: 5px;
    }</style>';


    $mrequest_kode = $_GET['mrequest_kode'];
    $sql = "SELECT * from mrequest where mrequest_kode='{$mrequest_kode}'";
    $res = pg_query($armasi_conn, $sql);
    $r = pg_fetch_array($res);

  $arr=explode("-",$r[tgl]);
  $arr_bulan=array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
  $arr_bln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"May","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
  if($app_plan_id==1){
    $kota="Tangerang";
  }
  if($app_plan_id==2){
    $kota="Serang";
  }
  if($app_plan_id==3){
    $kota="Gresik";
  }

  $bulan=date('n');
  $tahun=date('Y');
  $periode=dateadd("d",-1,dateadd("m",1,mktime(0,0,0,$bulan,1,$tahun)));
  $tahun1=$tahun-1;
  $hari=date("d",$periode);
  $tanggal="and tanggal>='$tahun1/$bulan/1' and tanggal<='$tahun/$bulan/$hari'";
  $txtout.="<br><table width=98% border=0 align=center cellpadding=0 cellspacing=0>";
    /* //Update Fajar 20/4/2016
    if($app_plan_id == '2'){
    $txtout.="  
    <tr>
      <td colspan=6><font size=2 face=Times New Roman, Times, serif>&nbsp;F.1602.LG.01</font></td>
    </tr>";
    }
    //===================== */
      $txtout.="
    <tr>
    <td colspan=3><font size=2 face=Times New Roman, Times, serif>&nbsp;ARWANA - PLANT $app_plan_id</font></td>
    <td colspan=3 align='right'><font size=2 face=Times New Roman, Times, serif> ";
          if($app_plan_id == '2'){
            $txtout.="
              &nbsp;F.1602.LG.01 &nbsp;";
            }
          $txtout.="
        </font></td>
    </tr>
    <tr>
    <td colspan=6><font size=2 face=Times New Roman, Times, serif>&nbsp;$cplan_alamat</font></td>
    </tr>
    <tr>
    <td colspan=6 height=10></td>
    </tr>
    <tr>
    <td colspan=6><div align=center><font size=3 face=Times New Roman, Times, serif><strong>";
    $txtout.="MEMO REQUEST";
  
  if ($inp[id]=="save") {
    $varBgColor = "#ffffff";
    $varTblBorder = "1";        
  } else {
    $varBgColor = "#000000";
    $varTblBorder = "0";
  }
  if($r[wo_kode]) {
    $wo_header = $r[wo_kode]." / ".$r[asset_nama];
  } 
  $txtout.="</strong></font></div></td>
    </tr>
    <tr>
    <td colspan=6><div align=center><font size=2 face=Times New Roman, Times, serif>&nbsp;</font></div></td>
    </tr>
    <tr>
      <td width=150><font size=2 face=Times New Roman, Times, serif>DATE ORDER</font></td>
      <td width=10><font size=2 face=Times New Roman, Times, serif>:</font></td>
      <td width=100><font size=2 face=Times New Roman, Times, serif>$arr[2] ".$arr_bulan["$arr[1]"]." $arr[0]</font></td>
      <td colspan=3 style='text-align:right;'><font size=2 face=Times New Roman, Times, serif>$wo_header</font></td>
    </tr>
    <tr>
      <td><font size=2 face=Times New Roman, Times, serif>NO. MR</font></td>
      <td><font size=2 face=Times New Roman, Times, serif>:</font></td>
      <td><font size=2 face=Times New Roman, Times, serif>$r[mrequest_kode]</font></td>
      <td colspan=3 style='text-align:right;'><font size=1 face=Times New Roman, Times, serif>$r[wo_desc]</font></td>
    </tr>
    <tr>
    <td colspan=6 height=10></td>
    </tr>
    <tr bgcolor=$varBgColor >
    <td colspan=6><table width=100% border=$varTblBorder cellspacing=1 cellpadding=0>
    <tr bgcolor=#FFFFFF style=padding:3px>
    <td width=30 nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>NO</font></div></td>
    <!-- <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>CODE</font></div></td> -->
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>DESCRIPTION</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>UNIT</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>ORDER</font></div></td>
    <!-- <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>DELIVERY<br>
    REQUIRED</font></div></td> -->
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>ENDING<br>
    STOCK</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>MIN<br>
    QTY</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>TOT PEMAKAIAN<br>
    ".date("M")." ".sprintf("%02d",date("y")-1)." s/d ".date("M")." ".date("y")."</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>Rata-rata<br>per Bulan</font></div></td>";
  for ($i=0; $i<=12; $i++){
    $bln=$bulan+$i>12?$bulan+$i-12:$bulan+$i;
    $thn=$bulan+$i>12?$tahun:$tahun-1;
    $txtout.="<td><div align=center><font size=1 face=Times New Roman, Times, serif>".$arr_bln["$bln"]."<br>$thn</font></div></td>";
  }
  $txtout.="<td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>REMARK<br>
    INISIAL</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>KODE<br>
    PRODUKSI</font></div></td>
    <td nowrap><div align=center><font size=1 face=Times New Roman, Times, serif>TANGGAL <br> KEBUTUHAN</font></div></td>
    </tr>
    <tr bgcolor=#FFFFFF>
    <td height=10></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>";

  $sql1="select * from qry_bon_item where true $tanggal and bon_kode like '%/$app_plan_id/%'";
  $res1=pg_query($armasi_conn, $sql1);
  while($r1=pg_fetch_array($res1)){
    $arr_tgl1=explode("-",$r1[tanggal]);
    $arr_nilai1["$r1[item_kode]"]["$arr_tgl1[0]"]["$arr_tgl1[1]"]+=$r1[qty];
    $arr_jumlah1["$r1[item_kode]"]+=$r1[qty];
  }
  $sql2="select * from qry_bon1_item where true $tanggal and bon_material_kode like '%/$app_plan_id/%'";
  $res2=pg_query($armasi_conn, $sql2);
  while($r2=pg_fetch_array($res2)){
    $arr_tgl2=explode("-",$r2[tanggal]);
    $arr_nilai2["$r2[item_kode]"]["$arr_tgl2[0]"]["$arr_tgl2[1]"]+=$r2[qty];
    $arr_jumlah2["$r2[item_kode]"]+=$r2[qty];
  }


  $sql_="select * from mreqitem where mrequest_kode='$r[mrequest_kode]'";
  $res_=pg_query($armasi_conn, $sql_);
  $no=1;
  while($r_=pg_fetch_array($res_)){
    $jumlah=$arr_jumlah1["$r_[item_kode]"]+$arr_jumlah2["$r_[item_kode]"];
    $rata2=$jumlah/12;
    $sql3="select (d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12) as stock from tbl_stock_bulanan where item_kode='$r_[item_kode]' and tahun=$tahun and plan_kode='$app_plan_id'";
    $res3=pg_query($armasi_conn, $sql3);
    $r3=pg_fetch_array($res3);
    $sql4="select qty_min from item_locker where item_kode = '$r_[item_kode]' and (warehouse_kode ilike '%$app_plan_id%' or warehouse_kode ilike '%-II-%')";
    #echo $sql4;
    $res4=pg_query($armasi_conn, $sql4);
    $r4=pg_fetch_array($res4);
    
  
    $txtout.="<tr bgcolor=#FFFFFF style=padding:3px>
      <td valign=top><font size=1 face=Times New Roman, Times, serif>$no. </font></td>
    
      <td valign=top><font size=1 face=Times New Roman, Times, serif>".cari_nilai("select item_nama from item where item_kode='$r_[item_kode]'")."</font></td>
      <td valign=top><font size=1 face=Times New Roman, Times, serif>".cari_nilai("select satuan from item where item_kode='$r_[item_kode]'")."</font></td>
      <td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($r_[qty_])."</font></td>
    
      <td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($r3[stock])."</font></td>
      <td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($r4[qty_min])."</font></td>
      <td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($jumlah)."</font></td>
      <td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($rata2)."</font></td>
      ";

    for ($i=0; $i<=12; $i++){
      $bln=$bulan+$i>12?$bulan+$i-12:$bulan+$i;
      $thn=$bulan+$i>12?$tahun:$tahun-1;
      $b=strlen($bln)==1?"0$bln":$bln;
      $nilai=$arr_nilai1["$r_[item_kode]"]["$thn"]["$b"]+$arr_nilai2["$r_[item_kode]"]["$thn"]["$b"];
      $txtout.="<td valign=top align=right class=formatnum><font size=1 face=Times New Roman, Times, serif>".format($nilai)."</font></td>
        ";
    }
    $filter_print=substr($r[mrequest_kode],0,-11);  
    $sql_stock="select * from mreqitem_stock where mrequest_kode='$r[mrequest_kode]' and item_kode = '$r_[item_kode]'";
    $res_stock=pg_query($armasi_conn, $sql_stock);
    $r_stok=pg_fetch_array($res_stock);
    $stok_p1=$r_stok[p1_qty]==""?0:$r_stok[p1_qty];
    $stok_p2=$r_stok[p2_qty]==""?0:$r_stok[p2_qty];
    $stok_p3=$r_stok[p3_qty]==""?0:$r_stok[p3_qty];
    $stok_p4=$r_stok[p4_qty]==""?0:$r_stok[p4_qty];
    $stok_p5=$r_stok[p5_qty]==""?0:$r_stok[p5_qty];
    
    if($r_[kode_produksi]=='AB'){
      $detail_kode="- Alat Berat";
    }elseif($r_[kode_produksi]=='AT'){
      $detail_kode="- ATK & IT";
    }elseif($r_[kode_produksi]=='BP'){
      $detail_kode="- Body Prep";
    }elseif($r_[kode_produksi]=='GL'){
      $detail_kode="- Glazing Line";
    }elseif($r_[kode_produksi]=='GP'){
      $detail_kode="- Glaze Prep";
    }elseif($r_[kode_produksi]=='HD'){
      $detail_kode="- Horizontal Dryer";
    }elseif($r_[kode_produksi]=='HO'){
      $detail_kode="- Head Office";
    }elseif($r_[kode_produksi]=='KL'){
      $detail_kode="- Kiln";
    }elseif($r_[kode_produksi]=='PR'){
      $detail_kode="- Press";
    }elseif($r_[kode_produksi]=='SP'){
      $detail_kode="- Sorting Packing";
    }elseif($r_[kode_produksi]=='UM'){
      $detail_kode="- Umum";
    }
    
  $txtout.="<td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[notes]</font></td>
    <td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[kode_produksi] $detail_kode</font></td>
    <td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[tgl_kebutuhan]</font></td></tr>";
      //if($filter_print!="QBB"){
        $txtout.="
          <tr bgcolor=#FFFFFF > ";  
          $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>STOK</font></td>";
          
          if($app_plan_id=="1"){
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P2 .".format($stok_p2)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P3 .".format($stok_p3)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P4 .".format($stok_p4)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P5 .".format($stok_p5)."</font></td>";
            $txtout.="<td valign=top colspan=17><font size=1 face=Times New Roman, Times, serif></font></td>";
          }elseif($app_plan_id=="2"){
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P1 .".format($stok_p1)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P3 .".format($stok_p3)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P4 .".format($stok_p4)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P5 .".format($stok_p5)."</font></td>";
            $txtout.="<td valign=top colspan=17><font size=1 face=Times New Roman, Times, serif></font></td>";
          }elseif($app_plan_id=="3"){
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P1 .".format($stok_p1)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P2 .".format($stok_p2)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P4 .".format($stok_p4)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P5 .".format($stok_p5)."</font></td>";
            $txtout.="<td valign=top colspan=17><font size=1 face=Times New Roman, Times, serif></font></td>";
          }elseif($app_plan_id=="4"){
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P1 .".format($stok_p1)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P2 .".format($stok_p2)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P3 .".format($stok_p3)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P5 .".format($stok_p5)."</font></td>";
            $txtout.="<td valign=top colspan=17> <font size=1 face=Times New Roman, Times, serif></font></td>";
          }elseif($app_plan_id=="5"){
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P1 .".format($stok_p1)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P2 .".format($stok_p2)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P3 .".format($stok_p3)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P4 .".format($stok_p4)."</font></td>";
            $txtout.="<td valign=top colspan=17> <font size=1 face=Times New Roman, Times, serif></font></td>";
          }else{
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P1 .".format($stok_p1)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P2 .".format($stok_p2)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P3 .".format($stok_p3)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P4 .".format($stok_p4)."</font></td>";
            $txtout.="<td valign=top colspan=2><font size=1 face=Times New Roman, Times, serif>P5 .".format($stok_p5)."</font></td>";
            $txtout.="<td valign=top colspan=17><font size=1 face=Times New Roman, Times, serif></font></td>";
          //}   
          
          
        $txtout.="</tr> 
      ";
      }
    //update END 20 Maret 2014 herry
    $no++;
  }
  $txtout.="</table></td>
    </tr>
    <tr>
    <td colspan=6>&nbsp;</td>
    </tr>
    <tr>
    <td><div align=center><font size=2 face=Times New Roman, Times, serif>$kota, $arr[2] ".$arr_bulan["$arr[1]"]." $arr[0]</font></div></td>
    <td colspan=5><font size=2>&nbsp;</font></td>
    </tr>
    <tr>
    <td height=75 colspan=6><font size=2>&nbsp;</font></td>
    </tr>
    <tr>
    <td><div align=center><font size=2 face=Times New Roman, Times, serif>( $r[requester] )<br> ".baliktgl($r[modidate])."</font></div></td>
    <td colspan=5><font size=2>&nbsp;</font></td>
    </tr>
    </table>";

$mpdf = new mPDF('','A4-L');
$mpdf->WriteHTML($txtout);
$mpdf->Output($kd.'.pdf', 'I');

?>