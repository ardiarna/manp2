<?php

require_once '../init.php';
require_once '../konfigurasiarmasi.php'; 
include("mpdf.php");

$txtout = '<style>@page {
     margin: 5px;
    }</style>';


    $psp_code = $_GET['kd'];
    $sql = "SELECT * from tbl_permintaan_barang where bon_kode='{$psp_code}'";
    $res = pg_query($armasi_conn, $sql);
    $r = pg_fetch_array($res);

    $sql = "SELECT departemen_nama from departemen where departemen_kode='{$r[departemen_kode]}'";
    $resdep = pg_query($armasi_conn, $sql);
    $dep = pg_fetch_array($resdep);

    if ($app_plan_id == '2' or $app_plan_id == '1' or $app_plan_id == '3' or $app_plan_id == '4') {
       $txtout.="<table width=98% border=0 align=center cellpadding=0 cellspacing=0>
                  <tr>
                    <td bgcolor=#000000>
                        <table width=100% border=0 cellspacing=1 cellpadding=0>
                            <tr bgcolor=#FFFFFF>
                                <td height=149 colspan=6>
                                    <table width=100% border=0 cellspacing=0 cellpadding=0>
                                        <tr>
                                            <td colspan=5 valign=bottom><font size=2 face=Times New Roman, Times, serif>&nbsp;ARWANA - PLANT $app_plan_id</font></td>";
                                                //start update by fajar 2016-04-21
                                                if($app_plan_id == '2'){
                                                    $txtout.="   
                                                    <td align=right><font size=2 face=Times New Roman, Times, serif>F.901.AMR.LG.02&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td> ";
                                                }
                                                //end update
                                            $txtout.="
                                        </tr>
                                    <tr>
                  <td colspan=4 valign=top><font size=2 face=Times New Roman, Times, serif>&nbsp;";
        /* $txtout.="<br><table width=98% border=0 align=center cellpadding=0 cellspacing=0>
                  <tr>
                  <td bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                  <tr bgcolor=#FFFFFF>
                  <td height=149 colspan=5><table width=100% border=0 cellspacing=0 cellpadding=0>";
                  //Update Fajar 20/4/2016
              if($app_plan_id == '2'){
                    $txtout.="  
                <tr>
                    <td colspan=4 valign=center><font size=2 face=Times New Roman, Times, serif>&nbsp;F.901.AMR.LG.02</font></td>
                    </tr>";
              }*/
                  //========================                        
                  $txtout.="
                  <tr>
                  <td colspan=4 valign=bottom><font size=2 face=Times New Roman, Times, serif>&nbsp;ARWANA - PLANT $app_plan_id</font></td>
                  <td colspan=2 rowspan=2><table width=100% border=0 cellspacing=0 cellpadding=0>
                  <tr>
                  <td colspan=3 height=10></td>
                  </tr>
                  <tr>
                  <td width=10>&nbsp;</td>
                  <td >&nbsp;</td>
                  <td width=10>&nbsp;</td>
                  </tr>
                  <tr>
                  <td colspan=3 height=10></td>
                  </tr>
                  </table></td>
                  </tr>
                  <tr>
                  <td colspan=4 valign=top><font size=2 face=Times New Roman, Times, serif>&nbsp;";  
    
        if($app_plan_id == '2'){$cplan_alamat_tmp = "Cikande - Serang";}elseif($app_plan_id == '1'){$cplan_alamat_tmp = "Jl. EZ. Muttaqin Desa Alam Jaya Pasar Doyong Tangerang 15133";}    
        $txtout.= $cplan_alamat_tmp."</font></td></tr>
                  <tr>
                  <td colspan=6 style='text-align:center;'><div align=center><font size=3 face=Times New Roman, Times, serif><strong>FORM PERMINTAAN BARANG</strong></font></div></td>
                  </tr>
                  <tr>
                  <td colspan=6 height=10></td>
                  </tr>
                  <tr>
                  <td colspan=3><font size=2 face=Times New Roman, Times, serif><strong>&nbsp;$r[wo_kode] / $r[asset_nama]</strong></font></td>
                  <td width=10%><font size=2 face=Times New Roman, Times, serif>Nomor</font></td>
                  <td width=1%><font size=2 face=Times New Roman, Times, serif>:</font></td>
                  <td width=40%><font size=2 face=Times New Roman, Times, serif>$r[bon_kode]</font></td>
                  </tr>
                  <tr>
                  <td colspan=3 rowspan=2 valign=top><font size=1 face=Times New Roman, Times, serif>&nbsp;$r[wo_desc]</font></td>
                  <td><font size=2 face=Times New Roman, Times, serif>Tanggal</font></td>
                  <td><font size=2 face=Times New Roman, Times, serif>:</font></td>
                  <td><font size=2 face=Times New Roman, Times, serif>".$r[tanggal]."</font></td>
                  </tr>
                  <tr>
                  <td><font size=2 face=Times New Roman, Times, serif>Dept. Pemakai</font></td>
                  <td width=10><font size=2 face=Times New Roman, Times, serif>:</font></td>
                  <td><font size=2 face=Times New Roman, Times, serif>".$dep[departemen_nama]."</font></td>
                  </tr>
                  <tr>
                  <td colspan=6 height=10></td>
                  </tr>
                  </table></td>
                  </tr>
                  <!-- update by riefqi ali haulani 19 Juni 2015 10:16-->
                  <tr bgcolor=#FFFFFF style=padding:3px>
                  <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Kode</font></div></td>
                  <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Nama dan Spesifikasi</font></div></td>"; 
                  $txtout.="
                  <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Jumlah</font></div></td>
                  <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Untuk Pekerjaan</font></div></td>";
                    if($app_plan_id == '2'){
                        $txtout.="<td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Ket Brg Kembali</font></div></td>";
                    }
                    $txtout.="<td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>Lokasi</font></div></td>";
                  $txtout.="</tr>";
                  if($app_plan_id=="3"){
                        $vplan = '-'.$app_plan_id;
                    } else {
                        $vplan = '-0'.$app_plan_id;
                    }
                  $sql_="select a.*,b.locker_nama 
                    from item_permintaan_barang a 
                    left join (select z.item_kode, max(z.locker_nama) as locker_nama from (select item_kode,max(modidate) as modidate from item_locker where warehouse_kode like '%$vplan%' group by item_kode) y inner join item_locker z on(z.item_kode = y.item_kode and z.modidate = y.modidate) group by z.item_kode) as b on(a.item_kode=b.item_kode)
                    where a.bon_kode='$r[bon_kode]'";
                  $res_=pg_query($armasi_conn, $sql_);
                  while($r_=pg_fetch_array($res_)){
                    $sql = "SELECT item_nama, satuan from item where item_kode='$r_[item_kode]'";
                    $resitem = pg_query($armasi_conn, $sql);
                    $item = pg_fetch_array($resitem);

                     $txtout.="<tr bgcolor=#FFFFFF style=padding:3px>
                     <td valign=top align=center><font size=2 face=Times New Roman, Times, serif>$r_[item_kode]</font></td>
                     <td valign=top ><font size=2 face=Times New Roman, Times, serif>&nbsp;&nbsp;".$item[item_nama]."</font></td>"; 
                    $txtout.="
                     <td valign=top align=center><font size=2 face=Times New Roman, Times, serif>".number_format($r_[qty],2,',','.')."&nbsp;".$item[satuan]."</font></td>
                     <td valign=top align=center><font size=2 face=Times New Roman, Times, serif>$r_[keterangan]</font></td>";
                     if($app_plan_id == '2'){
                            $txtout.="<td valign=top ><font size=2 face=Times New Roman, Times, serif>&nbsp;&nbsp;$r_[ket_kembali]</font></td>";
                        }
                    $txtout.="<td valign=top ><font size=2 face=Times New Roman, Times, serif>&nbsp;&nbsp;$r_[locker_nama]</font></td>";
                     $txtout.="</tr>";
                     //update end
                  }
        //update by herry henowo hp 16 Desember 2013 08:15
        $txtout.="<tr bgcolor=#FFFFFF>
                  <td colspan=6>
                  ";
                  // start update by riefqi ali haulani 18-19-2015
                  if($app_plan_id=='2'){
                            $txtout.="<table width=100% border=0 cellspacing=0 cellpadding=0>
                              <tr>
                              <td colspan=6 height=35></td>
                              </tr>
                            <tr>
                        
                          <td align=center style='text-align:center;'><font size=2 face=Times New Roman, Times, serif>Dibuat,</font></div></td>
                          <td align=center style='text-align:center;'><font size=2 face=Times New Roman, Times, serif>Disetujui,</font></div></td>
                          </tr>
                          <tr>
                          <td height=35 style='text-align:center;'><div align=center></div></td>
                          <td style='text-align:center;'><div align=center></div></td>
                         
                          
                          </tr>
                          <tr>
                            
                          <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$r[requester]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</font></div></td>
                          <td style='text-align:center;'><div align=center><font size=2 face=Times New Roman, Times, serif>( ____________ )</font></div></td>
                          
                          </tr>";
                        } else {
                      
                            $txtout.="<table width=100% border=0 cellspacing=0 cellpadding=0>
                              <tr>
                              <td colspan=4 height=35></td>
                              </tr>
                              <tr><td><div align=center><font size=2 face=Times New Roman, Times, serif>&nbsp;</font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>Dibuat,</font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>Disetujui,</font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>Mengetahui,</font></div></td>
                              </tr>
                              <tr>
                              <td height=35><div align=center></div></td>
                              <td><div align=center></div></td>
                              <td><div align=center></div></td>
                              <td><div align=center></div></td>
                              </tr>
                              <tr>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif></font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>( $r[requester] )</font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>( ________________ )</font></div></td>
                              <td><div align=center><font size=2 face=Times New Roman, Times, serif>( ________________ )</font></div></td>
                              </tr>";
                    }
                    // end update by riefqi ali haulani 18-19-2015
                  $txtout.="<tr>
                  <td colspan=4 height=10>&nbsp;</td>
                  </tr>
                  <tr>
                  <td colspan=4 height=10><font size=2 face=Times New Roman, Times, serif>&nbsp;Catatan : Suku cadang rusak harus diserahkan ke gudang pada saat suku cadang baru diambil dari gudang.</font></td>
                  </tr>
                  </table></td>
                  </tr>
                  </table></td>
                  </tr>
                  </table>
                  <table width=98% border=0 align=center cellpadding=0 cellspacing=0>
                  <tr>
                  <td><font size=2 face=Times New Roman, Times, serif>*) Lembar ke-1 : Gudang,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*) Lembar ke- 2 : Pemohon,</font></td>
                  </tr>
                  </table>";
            //update end
    } else {     // untuk PLAN KE 1 dan 3
        $sql = "SELECT no_iso from tbl_iso where jenis_dokumen='Pengeluaran Spare Part' and plan_kode='{$app_plan_id}'";
        $resiso = pg_query($armasi_conn, $sql);
        $iso = pg_fetch_array($resdep);
        $noiso="No.F.$iso[no_iso]<br>Hal. 1/1";

         $txtout.="<table width=98% border=0 align=center cellpadding=0 cellspacing=0>
                   <tr>
                   <td bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                   <tr bgcolor=#FFFFFF>
                   <td colspan=6><table width=100% border=0 cellspacing=0 cellpadding=0>
                   <tr>
                   <td colspan=4 valign=bottom><font size=2 face=Times New Roman, Times, serif>&nbsp;ARWANA - PLANT $app_plan_id</font></td>
                   <td colspan=2 rowspan=2><table width=100% border=0 cellspacing=0 cellpadding=0>
                   <tr>
                   <td colspan=3 height=10></td>
                   </tr>
                   <tr>
                   <td width=10>&nbsp;</td>
                   <td bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                   <tr style=padding:3px>
                   <td bgcolor=#FFFFFF><font size=2 face=Times New Roman, Times, serif>".$noiso."</font></td>
                   </tr>
                   </table></td>
                   <td width=10>&nbsp;</td>
                   </tr>
                   <tr>
                   <td colspan=3 height=10></td>
                   </tr>
                   </table></td>
                   </tr>
                   <tr>
                   <td colspan=4 valign=top><font size=2 face=Times New Roman, Times, serif>&nbsp;$cplan_alamat</font></td>
                   </tr>
                   <tr>
                   <td colspan=6><div align=center><font size=3 face=Times New Roman, Times, serif><strong>FORM PERMINTAAN BARANG</strong></font></div></td>
                   </tr>
                   <tr>
                   <td colspan=6 height=10></td>
                   </tr>
                   <tr>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td><font size=2 face=Times New Roman, Times, serif>Nomor</font></td>
                   <td><font size=2 face=Times New Roman, Times, serif>:</font></td>
                   <td><font size=2 face=Times New Roman, Times, serif>$r[bon_kode]</font></td>
                   </tr>
                   <tr>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td><font size=2 face=Times New Roman, Times, serif>Tanggal</font></td>
                   <td><font size=2 face=Times New Roman, Times, serif>:</font></td>
                   <td><font size=2 face=Times New Roman, Times, serif>".$r[tanggal]."</font></td>
                   </tr>
                   <tr>
                   <td><table width=100% border=0 cellspacing=0 cellpadding=0>
                   <tr>
                   <td width=10>&nbsp;</td>
                   <td width=20 height=20 bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                   <tr>
                   <td bgcolor=#FFFFFF>&nbsp;</td>
                   </tr>
                   </table></td>
                   <td width=10>&nbsp;</td>
                   <td><font size=2 face=Times New Roman, Times, serif>Spare Parts</font></td>
                   </tr>
                   </table></td>
                   <td><table width=100% border=0 cellspacing=0 cellpadding=0>
                   <tr>
                   <td width=10>&nbsp;</td>
                   <td width=20 height=20 bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                   <tr>
                   <td bgcolor=#FFFFFF>&nbsp;</td>
                   </tr>
                   </table>
                   </td>
                   <td width=10>&nbsp;</td>
                   <td><font size=2 face=Times New Roman, Times, serif>Bahan Bakar Mesin, Elpiji</font></td>
                   </tr>
                   </table></td>
                   <td><table width=100% border=0 cellspacing=0 cellpadding=0>
                   <tr>
                   <td width=10>&nbsp;</td>
                   <td width=20 height=20 bgcolor=#000000><table width=100% border=0 cellspacing=1 cellpadding=0>
                   <tr>
                   <td bgcolor=#FFFFFF>&nbsp;</td>
                   </tr>
                   </table>
                   </td>
                   <td width=10>&nbsp;</td>
                   <td><font size=2 face=Times New Roman, Times, serif>Lain - lain</font></td>
                   </tr>
                   </table></td>
                   <td><font size=2 face=Times New Roman, Times, serif>Dept. Pemakai</font></td>
                   <td width=10><font size=2 face=Times New Roman, Times, serif>:</font></td>
                   <td><font size=2 face=Times New Roman, Times, serif>".$dep[departemen_nama]."</font></td>
                   </tr>
                   <tr>
                   <td colspan=6 height=10></td>
                   </tr>
                   </table></td>
                   </tr>
                   <tr bgcolor=#FFFFFF style=padding:3px>
                   <td colspan=2><div align=center></div>          
                   <div align=center><font size=2 face=Times New Roman, Times, serif>BARANG</font></div></td>
                   <td colspan=2><div align=center></div>          
                   <div align=center><font size=2 face=Times New Roman, Times, serif>KUANTITAS (Qty)</font></div></td>
                   <td rowspan=2><div align=center><font size=2 face=Times New Roman, Times, serif>Untuk Pekerjaan</font></div></td>
                   <td rowspan=2><div align=center><font size=2 face=Times New Roman, Times, serif>Lokasi</font></div></td>
                   </tr>
                   <tr bgcolor=#FFFFFF style=padding:3px>
                   <td><div align=center><font size=2 face=Times New Roman, Times, serif>Kode</font></div></td>
                   <td><div align=center><font size=2 face=Times New Roman, Times, serif>Nama dan Spesifikasi</font></div></td>
                   <td><div align=center><font size=2 face=Times New Roman, Times, serif>Jumlah</font></div></td>
                   <td><div align=center><font size=2 face=Times New Roman, Times, serif>Terbilang</font></div></td>
                   </tr>";
                   $sql_="select a.*,b.locker_nama 
                    from item_permintaan_barang a 
                    left join (select z.item_kode, max(z.locker_nama) as locker_nama from (select item_kode,max(modidate) as modidate from item_locker where warehouse_kode like '%$app_plan_id%' group by item_kode) y inner join item_locker z on(z.item_kode = y.item_kode and z.modidate = y.modidate) group by z.item_kode) as b on(a.item_kode=b.item_kode)
                    where a.bon_kode='$r[bon_kode]'";
                   $res_=pg_query($armasi_conn, $sql_);
                   while($r_=pg_fetch_array($res_)){
                    $sql = "SELECT item_nama, satuan from item where item_kode='$r_[item_kode]'";
                    $resitem = pg_query($armasi_conn, $sql);
                    $item = pg_fetch_array($resitem);

                       $txtout.="<tr bgcolor=#FFFFFF style=padding:3px>
                                 <td valign=top><font size=2 face=Times New Roman, Times, serif>$r_[item_kode]</font></td>
                                 <td valign=top align=left><font size=2 face=Times New Roman, Times, serif>&nbsp;&nbsp;".$item[item_nama]."</font></td>
                                 <td valign=top align=right><font size=2 face=Times New Roman, Times, serif>".number_format($r_[qty],2,',','.')."&nbsp;".$item[satuan]."</font></td>
                                 <td valign=top><font size=2 face=Times New Roman, Times, serif>&nbsp;</font></td>
                                 <td valign=top><font size=2 face=Times New Roman, Times, serif>$r_[keterangan]</font></td>
                                 <td valign=top><font size=2 face=Times New Roman, Times, serif>$r_[locker_nama]</font></td>
                                 </tr>";
                   }
        $txtout.="<tr bgcolor=#FFFFFF>
                  <td colspan=6><font size=2 face=Times New Roman, Times, serif>&nbsp;Catatan: Suku cadang rusak harus diserahkan ke gudang pada saat suku cadang baru diambil dari gudang.</font></td>
                  </tr>
                  <tr bgcolor=#FFFFFF>
                  <td colspan=6><table width=100% border=0 cellspacing=0 cellpadding=0>
                  <tr>
                  <td colspan=4 height=10></td>
                  </tr>
                  <tr>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Dibuat Oleh,</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Diketahui Oleh,</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Diserahkan Oleh,</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Diperiksa Oleh,</font></div></td>
                  </tr>
                  <tr>
                  <td height=75><div align=center></div></td>
                  <td><div align=center></div></td>
                  <td><div align=center></div></td>
                  <td><div align=center></div></td>
                  </tr>
                  <tr>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>( $r[requester] )</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>( Ka. Bag Pemohon )</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>( $r[diserahkan] )</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>( Ka.Bag. Logistik )</font></div></td>
                  </tr>
                  <tr>
                  <td colspan=4 height=10></td>
                  </tr>
                  </table></td>
                  </tr>
                  </table></td>
                  </tr>
                  </table>
                  <table width=98% border=0 align=center cellpadding=0 cellspacing=0>
                  <tr>
                  <td><font size=2 face=Times New Roman, Times, serif>*) Lembar ke-1 : Akutansi,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*) Lembar ke- 2 : Gudang,</font></td>
                  </tr>
                  </table>";
    }

$mpdf = new mPDF('','A4');
$mpdf->WriteHTML($txtout);
$mpdf->Output($kd.'.pdf', 'I');

?>