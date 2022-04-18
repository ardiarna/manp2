<?php
require_once '../../../libs/init.php';
require_once '../../../libs/konfigurasiarmasi.php';


$user = $_SESSION["user"];
//$pageName = "include/".basename($_SERVER['PHP_SELF']);
//echo $pageName;
$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "load";
		load();
	break;
	case "loadetail";
		loadetail();
	break;
	case "save";
		save();
	break;
	case "delete";
		delete();
	break;
	case "listwo";
		listwo();
	break;
	case "listdep";
		listdep();
	break;
	case "loadsparepart";
		loadsparepart();
	break;
	case "cetakpsp";
		cetakpsp();
	break;
	case "cekeditable";
		cekeditable();
	break;
	case "cekhapus";
		cekhapus();
	break;
}

function view(){
	global $conn, $app_plan_id, $armasi_conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];

	$arr_status = array('1' => 'Pending', '2' => 'Received', '3' => 'Approve', '4' => 'Cancel');
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.*
		FROM tbl_psp a
		WHERE a.tanggal >= '{$tglfrom}' AND a.tanggal <= '{$tglto}' ORDER BY a.psp_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$sql_armasi = "SELECT status, bon_kode_real, alasan_cancel, approve_status, approve_by, approve_date FROM tbl_permintaan_barang WHERE bon_kode = '".$row['psp_code']."'";
		$query_armasi = pg_query($armasi_conn, $sql_armasi);
		$ra = pg_fetch_array($query_armasi);
		if($ra['status'] == '4') {
			$ra['bon_kode_real'] = $ra['alasan_cancel'];	
		}
		if($ra['approve_status'] == 't') {
			$statsapprove = "APPROVE";
		    $userapprove  = $ra['approve_by'];
		    $dateapprove  = substr($ra['approve_date'],0,10);	
		} else {
			$statsapprove = "";
		    $userapprove  = "";
		    $dateapprove  = "";
		}
		echo ("<row id='".$row['psp_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['psp_code']."]]></cell>");
		print("<cell><![CDATA[".$row['tanggal']."]]></cell>");
		print("<cell><![CDATA[".$arr_status[$ra['status']]."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$ra['bon_kode_real']."]]></cell>");
		print("<cell><![CDATA[".$row['requester']."]]></cell>");
		print("<cell><![CDATA[".$row['user_create']."]]></cell>");
		print("<cell><![CDATA[".$statsapprove."]]></cell>");
		print("<cell><![CDATA[".$userapprove."]]></cell>");
		print("<cell><![CDATA[".$dateapprove."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function load(){	
	global $conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$psp_code = $_GET['kd'];
	$sql = "SELECT a.*, b.wo_date, b.wo_asset, c.amm_desc, b.wo_desc
		FROM tbl_psp a
		JOIN tbl_wo b ON(a.wo_code = b.wo_code)
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		WHERE a.psp_code = '{$psp_code}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<psp_code>".$row['psp_code']."</psp_code>");
	print("<tanggal>".$row['tanggal']."</tanggal>");
	print("<wo_code>".$row['wo_code']."</wo_code>");
	print("<wo_date>".$row['wo_date']."</wo_date>");
	print("<wo_pic1>".$row['requester']."</wo_pic1>");
	print("<asset_code>".$row['wo_asset']."</asset_code>");
	print("<asset_name>".$row['amm_desc']."</asset_name>");
	print("<wo_desc>".$row['wo_desc']."</wo_desc>");
	print("<departemen_code>".$row['departemen_code']."</departemen_code>");
	print("<departemen_name>".$row['departemen_name']."</departemen_name>");
	print('</data>');
}

function loadetail(){
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';


	$psp_code = $_GET['kd'];
	$tahun = date('Y');
	$sql = "SELECT * FROM tbl_psp_detail WHERE psp_code = '{$psp_code}' ORDER BY item_name";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		$sqlstok = "SELECT d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12 AS stok
			FROM tbl_stock_bulanan 
			WHERE tahun = {$tahun} AND plan_kode = {$app_plan_id} AND item_kode = '".$row['item_code']."'";
		$querystok = pg_query($armasi_conn, $sqlstok);
		$rstok = pg_fetch_array($querystok);
		$stok = $rstok['stok'] ? $rstok['stok'] : 0;
		$sqlbook = "SELECT SUM(b.qty) AS book 
			FROM tbl_permintaan_barang a JOIN item_permintaan_barang b ON (a.bon_kode=b.bon_kode)
			WHERE a.bon_kode_real IS NULL AND a.bon_kode LIKE '%/{$app_plan_id}/%' AND a.status NOT IN ('4') AND b.item_kode = '".$row['item_code']."'";
		$querybook = pg_query($armasi_conn, $sqlbook);
		$rbook = pg_fetch_array($querybook);
		$book = $rbook['book'] ? $rbook['book'] : 0;
		$avail = $stok-$book;
		echo ("<row id='".$row['item_code']."'>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[".$row['keterangan']."]]></cell>");
		print("<cell><![CDATA[".$row['ket_kembali']."]]></cell>");
		print("<cell><![CDATA[".$stok."]]></cell>");
		print("<cell><![CDATA[".$book."]]></cell>");
		print("<cell><![CDATA[".$avail."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function cekeditable() {
	global $conn, $app_plan_id, $armasi_conn;
	$psp_code =	$_POST['kd'];
	$sql = "SELECT date_part('month',tanggal) AS bln, date_part('year',tanggal) AS thn, * FROM tbl_permintaan_barang WHERE bon_kode = '{$psp_code}'";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	$sql_lock = "SELECT bulan_$r[bln] AS bln_locked FROM tbl_lock WHERE tahun = '{$r[thn]}' AND plan_kode = '{$app_plan_id}' AND urutan = '1'";
	$query_lock = pg_query($armasi_conn, $sql_lock);
	$r_lock = pg_fetch_array($query_lock);
	if($r_lock['bln_locked'] == "t") {
		$ret = "Data tidak bisa diubah karena Accounting telah tutup buku !";
	} else {
		if($r['status'] == '4') {
			$ret = "MAAF!!!... PSP No. ".$psp_code." tidak bisa di Edit karena sudah di Cancel";
		} else if($r['status'] <> '1') {
			$ret = "MAAF!!!... PSP No. ".$psp_code." tidak bisa di Edit karena sudah di buat KSP";
		} else {
			$ret = "OK";
		} 
	}
	echo $ret;
}

function save() {	
	global $conn, $app_plan_id, $armasi_conn;
	$stat = $_GET['stat'];
	$tanggal = $_POST['tanggal'];
	$wo_code = $_POST['wo_code'];
	$requester = $_POST['wo_pic1'];
	$wo_desc = $_POST['wo_desc'];
	$arr_item_code = json_decode($_POST['sparepartlist'], false);
	$departemen_code = $_POST['departemen_code'];
	$departemen_name = $_POST['departemen_name'];
	$asset_code = $_POST['asset_code'];
	$asset_name = $_POST['asset_name'];
	$sub_plant = $_POST['sub_plant'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");
	if($stat=='ubah'){
		$psp_code =	$_POST['psp_code'];
		$sql_armasi = "UPDATE tbl_permintaan_barang SET departemen_kode = '{$departemen_code}', tanggal = '{$tanggal}', modiby = '{$user}', modidate = '{$hari_ini}', requester = '{$requester}', sub_plant = '{$sub_plant}' WHERE bon_kode = '{$psp_code}';";
		foreach ($arr_item_code as $r) {
			$sql_armasi .= "UPDATE item_permintaan_barang SET qty = '{$r->qty}', keterangan = '{$r->keterangan}', ket_kembali = '{$r->ket_kembali}', tanggal = '{$tanggal}', amount = '{$r->qty}' WHERE bon_kode = '{$psp_code}' AND item_kode = '{$r->item_code}';";
		}
	} else {
		$thn = date("Y");
		$formatpspkode = "PSP/".$app_plan_id."/".date("y")."/";
		$sql = "SELECT max(bon_kode) AS psp_code_max FROM tbl_permintaan_barang WHERE bon_kode like '%$formatpspkode%' AND date_part('year',tanggal) = {$thn}";
		$query = pg_query($armasi_conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['psp_code_max'] == ''){
			$mx['psp_code_max'] = 0;
		} else {
			$mx['psp_code_max'] = substr($mx['psp_code_max'],-5);
		}
		$urutbaru = $mx['psp_code_max']+1;
		$psp_code = $formatpspkode.str_pad($urutbaru,5,"0",STR_PAD_LEFT);
		$sql_armasi = "INSERT INTO tbl_permintaan_barang (bon_kode, departemen_kode, tanggal, create_by, modiby, modidate, requester, closed, wo_kode, wo_desc, asset_kode, asset_nama, sub_plant) VALUES ('{$psp_code}', '{$departemen_code}', '{$tanggal}', '{$user}', '{$user}', '{$hari_ini}', '{$requester}', 'f', '{$wo_code}', '{$wo_desc}', '{$asset_code}', '{$asset_name}', '{$sub_plant}');";
		foreach ($arr_item_code as $r) {
			$sql_armasi .= "INSERT INTO item_permintaan_barang (bon_kode, item_kode, qty, keterangan, ket_kembali, qty_diterima, tanggal, harga, amount) VALUES ('{$psp_code}', '{$r->item_code}', '{$r->qty}', '{$r->keterangan}', '{$r->ket_kembali}', 0, '{$tanggal}', 1, '{$r->qty}');";
		}
	}
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		if($stat=='ubah'){
			$sql_u = "UPDATE tbl_psp SET tanggal = '{$tanggal}', requester = '{$requester}', departemen_code = '{$departemen_code}', departemen_name = '{$departemen_name}', sub_plant = '{$sub_plant}', user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE psp_code = '{$psp_code}';";
			foreach ($arr_item_code as $r) {
				$sql_u .= "UPDATE tbl_psp_detail SET qty = '{$r->qty}', keterangan = '{$r->keterangan}', ket_kembali = '{$r->ket_kembali}' WHERE psp_code = '{$psp_code}' AND item_code = '{$r->item_code}'; ";
			}
		} else { 
			$sql_u = "INSERT INTO tbl_psp (psp_code, tanggal, wo_code, requester, departemen_code, departemen_name, sub_plant, user_create, date_create) VALUES ('{$psp_code}', '{$tanggal}', '{$wo_code}', '{$requester}', '{$departemen_code}', '{$departemen_name}', '{$sub_plant}', '{$user}', '{$hari_ini}');";
			foreach ($arr_item_code as $r) {
				$sql_u .= "INSERT INTO tbl_psp_detail (psp_code, item_code, item_name, qty, keterangan, ket_kembali) VALUES ('{$psp_code}', '{$r->item_code}', '{$r->item_name}', '{$r->qty}', '{$r->keterangan}', '{$r->ket_kembali}'); ";
			}
		}
		$res = pg_query($conn, $sql_u);
		if($res){
			$ret = "OK";
		} else {
			$ret = pg_errormessage($conn);
		}
	}else{
		$ret = pg_errormessage($armasi_conn);
	}
	pg_close();
	echo $ret;
}

function cekhapus() {
	global $conn, $app_plan_id, $armasi_conn;
	$psp_code =	$_POST['kd'];
	$sql = "SELECT date_part('month',tanggal) AS bln, date_part('year',tanggal) AS thn, * FROM tbl_permintaan_barang WHERE bon_kode = '{$psp_code}'";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	$sql_lock = "SELECT bulan_$r[bln] AS bln_locked FROM tbl_lock WHERE tahun = '{$r[thn]}' AND plan_kode = '{$app_plan_id}' AND urutan = '1'";
	$query_lock = pg_query($armasi_conn, $sql_lock);
	$r_lock = pg_fetch_array($query_lock);
	if($r_lock['bln_locked'] == "t") {
		$ret = "Data tidak bisa dicancel karena Accounting telah tutup buku !";
	} else {
		if($r['status'] == '4') {
			$ret = "MAAF!!!... PSP No. ".$psp_code." tidak bisa di batalkan karena Status sudah Batal";
		} else if($r['status'] <> '1') {
			$ret = "MAAF!!!... PSP No. ".$psp_code." tidak bisa di batalkan karena sudah di buat KSP";
		} else {
			$ret = "OK";
		} 
	}
	echo $ret;
}

function delete(){	
	global $conn, $app_plan_id, $armasi_conn;
	
	$psp_code = $_POST['psp_code'];
	$alasan_cancel = $_POST['alasan_cancel'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");

	$sql_armasi = "UPDATE tbl_permintaan_barang SET status = '4', alasan_cancel = '{$alasan_cancel}' WHERE bon_kode = '{$psp_code}';
		UPDATE item_permintaan_barang SET qty = 0 WHERE bon_kode = '{$psp_code}';";
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		$sql_u = "UPDATE tbl_psp SET status = '4', alasan_cancel = '{$alasan_cancel}', user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE psp_code = '{$psp_code}';
			UPDATE tbl_psp_detail SET qty = 0 WHERE psp_code = '{$psp_code}';";
		$res = pg_query($conn, $sql_u);
		if($res){
			$ret = "OK";
		} else {
			$ret = pg_errormessage($conn);
		}
	} else {
		$ret = pg_errormessage($armasi_conn);	
	}
	pg_close();
	echo $ret;
}

function listwo(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	$i=1;
	$sql = "SELECT b.wo_code, b.wo_date, b.wo_desc, b.wo_asset, c.amm_desc, b.wo_pic1, b.sub_plant, count(d.item_code)
		FROM tbl_wo b
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		JOIN tbl_wo_detail d ON(b.wo_code = d.wo_code)
		WHERE b.wo_status IN('O','S') AND b.wo_code NOT IN (SELECT wo_code FROM tbl_psp)
		GROUP BY b.wo_code, b.wo_date, b.wo_desc, b.wo_asset, c.amm_desc, b.wo_pic1, b.sub_plant
		ORDER BY b.wo_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['sub_plant']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_pic1']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function listdep(){
	global $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	$i=1;
	$sql = "SELECT departemen_kode, departemen_nama FROM departemen WHERE plan_kode = '{$app_plan_id}' AND departemen_nama <> 'AFILIASI' AND inactive = true ORDER BY departemen_nama";
	$query=pg_query($armasi_conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['departemen_kode']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['departemen_nama']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function loadsparepart(){
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';


	$wo_code = $_GET['kd'];
	$tahun = date('Y');
	$sql = "SELECT * FROM tbl_wo_detail WHERE wo_code = '{$wo_code}' ORDER BY item_name";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		$sqlstok = "SELECT d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12 AS stok
			FROM tbl_stock_bulanan 
			WHERE tahun = {$tahun} AND plan_kode = {$app_plan_id} AND item_kode = '".$row['item_code']."'";
		$querystok = pg_query($armasi_conn, $sqlstok);
		$rstok = pg_fetch_array($querystok);
		$stok = $rstok['stok'] ? $rstok['stok'] : 0;
		$sqlbook = "SELECT SUM(b.qty) AS book 
			FROM tbl_permintaan_barang a JOIN item_permintaan_barang b ON (a.bon_kode=b.bon_kode)
			WHERE a.bon_kode_real IS NULL AND a.bon_kode LIKE '%/{$app_plan_id}/%' AND a.status NOT IN ('4') AND b.item_kode = '".$row['item_code']."'";
		$querybook = pg_query($armasi_conn, $sqlbook);
		$rbook = pg_fetch_array($querybook);
		$book = $rbook['book'] ? $rbook['book'] : 0;
		$avail = $stok-$book;
		echo ("<row id='".$row['item_code']."'>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[]]></cell>");
		print("<cell><![CDATA[]]></cell>");
		print("<cell><![CDATA[".$stok."]]></cell>");
		print("<cell><![CDATA[".$book."]]></cell>");
		print("<cell><![CDATA[".$avail."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function cetakpsp() {
	global $app_plan_id, $armasi_conn;

	$psp_code = $_GET['kd'];
    $sql = "SELECT * from tbl_permintaan_barang where bon_kode='{$psp_code}'";
    $res = pg_query($armasi_conn, $sql);
    $r = pg_fetch_array($res);

    $sql = "SELECT departemen_nama from departemen where departemen_kode='{$r[departemen_kode]}'";
    $resdep = pg_query($armasi_conn, $sql);
    $dep = pg_fetch_array($resdep);

    echo "<script>";
	echo "function printContent(){";
	echo "var restorepage = document.body.innerHTML;";
	echo "var printcontent = document.getElementById('div1').innerHTML;";
	echo "document.body.innerHTML = printcontent;";
	echo "window.print();";
	echo "document.body.innerHTML = restorepage;";
	echo "}";
	echo "</script>";
	echo "<button onclick='printContent()'>PRINT</button>";
	echo "<div id='div1' style='margin-left: 3px; margin-right: 3px; margin-top: 3px; margin-bottom: 3px;'>";

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
                  <td colspan=6><div align=center><font size=3 face=Times New Roman, Times, serif><strong>FORM PERMINTAAN BARANG</strong></font></div></td>
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
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Kode</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Nama dan Spesifikasi</font></div></td>"; 
                  $txtout.="
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Jumlah</font></div></td>
                  <td><div align=center><font size=2 face=Times New Roman, Times, serif>Untuk Pekerjaan</font></div></td>";
                    if($app_plan_id == '2'){
                        $txtout.="<td><div align=center><font size=2 face=Times New Roman, Times, serif>Ket Brg Kembali</font></div></td>";
                    }
                    $txtout.="<td><div align=center><font size=2 face=Times New Roman, Times, serif>Lokasi</font></div></td>";
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
                        
                          <td align=center><font size=2 face=Times New Roman, Times, serif>Dibuat,</font></div></td>
                          <td align=center><font size=2 face=Times New Roman, Times, serif>Disetujui,</font></div></td>
                          </tr>
                          <tr>
                          <td height=35><div align=center></div></td>
                          <td><div align=center></div></td>
                         
                          
                          </tr>
                          <tr>
                            
                          <td><div align=center><font size=2 face=Times New Roman, Times, serif>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$r[requester]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</font></div></td>
                          <td><div align=center><font size=2 face=Times New Roman, Times, serif>( ____________ )</font></div></td>
                          
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

    $txtout .= '<div style="page-break-before:always;">';

    echo $txtout;
}

?>