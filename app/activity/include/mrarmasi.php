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
	case "deletemr";
		deletemr();
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
	case "cetakmemo";
		cetakmemo();
	break;
	case "cekeditable";
		cekeditable();
	break;
	case "cekhapus";
		cekhapus();
	break;
	case "cekhapusmr";
		cekhapusmr();
	break;
	case "sqlupdatesch";
		sqlupdatesch();
	break;
}

function view(){
	global $conn, $app_plan_id, $armasi_conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];

	$arr_kode_produksi = array('AB' => 'Alat Berat', 'AT' => 'ATK & IT', 'BP' => 'Body prep', 'GL' => 'Glazing Lime', 'GP' => 'Glaze Prep', 'HD' => 'Horizontal Dryer', 'HO' => 'Head Office', 'KL' => 'Kiln', 'PR' => 'Press', 'QC' => 'Quality Control', 'SP' => 'Sorting Packing', 'SQ' => 'Squaring', 'UK' => 'Unloading Kiln', 'UL' => 'Utility', 'UM' => 'Umum', 'WE' => 'Workshop Elektrik', 'WM' => 'Workshop Mekanik');
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i = 1;
	$sql = "SELECT a.mrequest_kode, a.item_kode, a.notes, b.wo_code
		FROM tbl_mreqitem a 
		JOIN tbl_mrequest b ON (a.mrequest_kode = b.mrequest_kode) 
		WHERE b.tgl >= '{$tglfrom}' AND b.tgl <= '{$tglto}' ORDER BY a.mrequest_kode";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$sql_armasi = "SELECT a.item_kode, c.item_nama, c.satuan, a.qty_, a.qty, a.mrequest_kode, b.departemen_kode, d.departemen_nama, a.kode_produksi, b.requester, a.tgl_kebutuhan, a.approve_status, a.approve_by, a.approve_date, a.approve_note, b.tgl, a.request_kode, a.notes, e.porder_kode
		FROM mreqitem a 
		JOIN mrequest b ON (a.mrequest_kode = b.mrequest_kode) 
		JOIN item c ON (a.item_kode = c.item_kode)
		JOIN departemen d ON (b.departemen_kode = d.departemen_kode)
		LEFT JOIN preqitem e ON(a.request_kode = e.request_kode AND a.item_kode = e.item_kode)
		WHERE a.mrequest_kode = '".$row['mrequest_kode']."' AND a.item_kode = '".$row['item_kode']."' AND a.notes = '".$row['notes']."'";
		$query_armasi = pg_query($armasi_conn, $sql_armasi);
		$ra = pg_fetch_array($query_armasi);
		$ra['qtypr'] = $ra['qty_'] - $ra['qty'];
		if($ra['approve_status'] == 't') {
			$statsapprove = "Approve";
		    $userapprove  = $ra['approve_by'];
		    $dateapprove  = substr($ra['approve_date'],0,10);	
		} else if($ra['approve_status'] == 'f') {
			$statsapprove = "Not Approve";
		    $userapprove  = $ra['approve_by'];
		    $dateapprove  = substr($ra['approve_date'],0,10);	
		} else {
			$statsapprove = "";
		    $userapprove  = "";
		    $dateapprove  = "";
		}
		echo ("<row id='".$row['mrequest_kode'].'@@'.$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['item_kode']."]]></cell>");
		print("<cell><![CDATA[".$ra['item_nama']."]]></cell>");
		print("<cell><![CDATA[".$ra['satuan']."]]></cell>");
		print("<cell><![CDATA[".$ra['qty_']."]]></cell>");
		print("<cell><![CDATA[".$ra['qtypr']."]]></cell>");
		print("<cell><![CDATA[".$ra['qty']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['mrequest_kode']."]]></cell>");
		print("<cell><![CDATA[".$ra['request_kode']."]]></cell>");
		print("<cell><![CDATA[".$ra['porder_kode']."]]></cell>");
		print("<cell><![CDATA[".$ra['departemen_nama']."]]></cell>");
		print("<cell><![CDATA[".$ra['kode_produksi']." - ".$arr_kode_produksi[$ra['kode_produksi']]."]]></cell>");
		print("<cell><![CDATA[".$ra['requester']."]]></cell>");
		print("<cell><![CDATA[".$ra['tgl_kebutuhan']."]]></cell>");
		print("<cell><![CDATA[".$statsapprove."]]></cell>");
		print("<cell><![CDATA[".$userapprove."]]></cell>");
		print("<cell><![CDATA[".$dateapprove."]]></cell>");
		print("<cell><![CDATA[".$ra['approve_note']."]]></cell>");
		print("<cell><![CDATA[".$row['notes']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function load(){	
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$mrequest_kode = $_GET['mrequest_kode'];
	$sql = "SELECT a.*, b.wo_date, b.wo_asset, c.amm_desc, b.wo_desc, b.wo_scheduled
		FROM tbl_mrequest a
		JOIN tbl_wo b ON(a.wo_code = b.wo_code)
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		WHERE a.mrequest_kode = '{$mrequest_kode}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<mr_jenis>".substr($row['mrequest_kode'], 0, 3)."</mr_jenis>");
	print("<mrequest_kode>".$row['mrequest_kode']."</mrequest_kode>");
	print("<tgl>".$row['tgl']."</tgl>");
	print("<wo_code>".$row['wo_code']."</wo_code>");
	print("<wo_date>".$row['wo_date']."</wo_date>");
	print("<wo_scheduled>".$row['wo_scheduled']."</wo_scheduled>");
	print("<requester>".$row['requester']."</requester>");
	print("<asset_code>".$row['wo_asset']."</asset_code>");
	print("<asset_name>".$row['amm_desc']."</asset_name>");
	print("<wo_desc>".$row['wo_desc']."</wo_desc>");
	print("<departemen_kode>".$row['departemen_kode']."</departemen_kode>");
	print("<departemen_nama>".$row['departemen_nama']."</departemen_nama>");
	print('</data>');
}

function loadetail(){
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';	
	
	$mrequest_kode = $_GET['mrequest_kode'];
	$item_kode = $_GET['item_kode'];
	$notes = $_GET['notes'];
	if($item_kode == 'ALL' && $notes == 'ALL') {
		$sql = "SELECT * FROM tbl_mreqitem WHERE mrequest_kode = '{$mrequest_kode}'";
	} else {
		$sql = "SELECT * FROM tbl_mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$item_kode}' AND notes = '{$notes}'";
	}
		
	$query = pg_query($conn, $sql);
	$i = 1;
	while($row = pg_fetch_array($query)){
		$sqlwo = "SELECT item_name, unit FROM tbl_wo_detail WHERE item_code = '{$row[item_kode]}' ORDER BY wo_code DESC LIMIT 1";
		$querywo = pg_query($conn, $sqlwo);
		$r = pg_fetch_array($querywo);
		$sqlstok = "SELECT SUM(qty) AS ada FROM tbl_mutasi_dead_stock WHERE item_kode = '".$row['item_kode']."'";
		$querystok = pg_query($armasi_conn, $sqlstok);
		$rstok = pg_fetch_array($querystok);
		$stockDS = $rstok['ada'] > 0 ? "Dead Stock" : "Stock";
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[1]]></cell>");
		print("<cell><![CDATA[".$row['item_kode']."]]></cell>");
		print("<cell><![CDATA[".$r['item_name']."]]></cell>");
		print("<cell><![CDATA[".$r['unit']."]]></cell>");
		print("<cell><![CDATA[".$stockDS."]]></cell>");
		print("<cell><![CDATA[".$row['qty_']."]]></cell>");
		print("<cell><![CDATA[".$row['kode_produksi']."]]></cell>");
		print("<cell><![CDATA[".$row['notes']."]]></cell>");
		print("<cell><![CDATA[".$row['tgl_kebutuhan']."]]></cell>");
		print("<cell><![CDATA[".$row['skala_prioritas']."]]></cell>");
		print("<cell><![CDATA[".$row['notes']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function cekeditable() {
	global $conn, $app_plan_id, $armasi_conn;
	$mrequest_kode = $_POST['mrequest_kode'];
	$item_kode = $_POST['item_kode'];
	$notes = $_POST['notes'];
	
	$sql = "SELECT request_kode, approve_status FROM mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$item_kode}' AND notes = '{$notes}'; ";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	if($r['approve_status'] == 't') {
		$ret = "Maaf kode barang sudah di-Approve, tidak bisa diedit";
	} else if($r['approve_status'] == 'f') {
		$ret = "Maaf kode barang sudah di-Not Approve, tidak bisa diedit";
	} else if($r['request_kode'] <> '') {
		$ret = "Maaf kode barang sudah dibuat PR, tidak bisa diedit";
	} else{
		$ret = "OK";
	}
	echo $ret;
}

function save() {	
	global $conn, $app_plan_id, $armasi_conn;
	$stat = $_GET['stat'];
	$tgl = $_POST['tgl'];
	$wo_code = $_POST['wo_code'];
	$wo_desc = $_POST['wo_desc'];
	$wo_scheduled = $_POST['wo_scheduled'];
	$requester = $_POST['requester'];
	$arr_item_code = json_decode($_POST['sparepartlist'], false);
	$departemen_kode = $_POST['departemen_kode'];
	$departemen_nama = $_POST['departemen_nama'];
	$asset_code = $_POST['asset_code'];
	$asset_name = $_POST['asset_name'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d");
	if($stat=='ubah'){
		$mrequest_kode = $_POST['mrequest_kode'];
		$sql_armasi = "UPDATE mrequest SET departemen_kode = '{$departemen_kode}', tgl = '{$tgl}', requester = '{$requester}', modiby = '{$user}', modidate = '{$hari_ini}' WHERE mrequest_kode = '{$mrequest_kode}'; ";
		foreach ($arr_item_code as $r) {
			$sql_armasi .= "UPDATE mreqitem SET qty = '{$r->qty}', qty_ = '{$r->qty}', notes = '{$r->notes}', tgl_kebutuhan = '{$r->tgl_kebutuhan}', kode_produksi = '{$r->kode_produksi}', skala_prioritas = '{$r->skala_prioritas}' WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$r->item_kode}' AND notes = '{$r->notes_lm}'; ";
		}
	} else {
		$thn = date("Y");
		$mr_jenis = strtoupper($_POST['mr_jenis']);
		$formatmrkode = $mr_jenis."/".$app_plan_id."/".date("y")."/";
		$sql = "SELECT max(mrequest_kode) AS mrequest_kode_max FROM mrequest WHERE mrequest_kode like '%$formatmrkode%' AND date_part('year',tgl) = {$thn}";
		$query = pg_query($armasi_conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['mrequest_kode_max'] == ''){
			$mx['mrequest_kode_max'] = 0;
		} else {
			$mx['mrequest_kode_max'] = substr($mx['mrequest_kode_max'],-5);
		}
		$urutbaru = $mx['mrequest_kode_max']+1;
		$mrequest_kode = $formatmrkode.str_pad($urutbaru,5,"0",STR_PAD_LEFT);
		$sql_armasi = "INSERT INTO mrequest (mrequest_kode, departemen_kode, tgl, create_by, modiby, modidate, requester, status, wo_kode, wo_desc, asset_kode, asset_nama, wo_scheduled) VALUES ('{$mrequest_kode}', '{$departemen_kode}', '{$tgl}', '{$user}', '{$user}', '{$hari_ini}', '{$requester}', 'MR', '{$wo_code}', '{$wo_desc}', '{$asset_code}', '{$asset_name}', '{$wo_scheduled}'); ";
		foreach ($arr_item_code as $r) {
			$sql_armasi .= "INSERT INTO mreqitem (mrequest_kode, item_kode, qty, notes, status, tgl_kebutuhan, modiby, modidate, requester, vol, qty_, kode_produksi, skala_prioritas) VALUES ('{$mrequest_kode}', '{$r->item_kode}', '{$r->qty}', '{$r->notes}', 'MR', '{$r->tgl_kebutuhan}', '{$user}', '{$hari_ini}', '{$requester}', '{$r->satuan}', '{$r->qty}', '{$r->kode_produksi}', '{$r->skala_prioritas}'); ";
		}
	}
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		if($stat=='ubah'){
			$sql_u = "UPDATE tbl_mrequest SET departemen_kode = '{$departemen_kode}', tgl = '{$tgl}', requester = '{$requester}', modiby = '{$user}', modidate = '{$hari_ini}', departemen_nama = '{$departemen_nama}' WHERE mrequest_kode = '{$mrequest_kode}'; ";
			foreach ($arr_item_code as $r) {
				$sql_u .= "UPDATE tbl_mreqitem SET qty = '{$r->qty}', qty_ = '{$r->qty}', notes = '{$r->notes}', tgl_kebutuhan = '{$r->tgl_kebutuhan}', kode_produksi = '{$r->kode_produksi}', skala_prioritas = '{$r->skala_prioritas}' WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$r->item_kode}' AND notes = '{$r->notes_lm}'; ";
			}
		} else { 
			$sql_u = "INSERT INTO tbl_mrequest (mrequest_kode, departemen_kode, tgl, create_by, modiby, modidate, requester, status, wo_code, departemen_nama) VALUES ('{$mrequest_kode}', '{$departemen_kode}', '{$tgl}', '{$user}', '{$user}', '{$hari_ini}', '{$requester}', 'MR', '{$wo_code}', '{$departemen_nama}'); ";
			foreach ($arr_item_code as $r) {
				$sql_u .= "INSERT INTO tbl_mreqitem (mrequest_kode, item_kode, qty, notes, status, tgl_kebutuhan, modiby, modidate, requester, vol, qty_, kode_produksi, skala_prioritas) VALUES ('{$mrequest_kode}', '{$r->item_kode}', '{$r->qty}', '{$r->notes}', 'MR', '{$r->tgl_kebutuhan}', '{$user}', '{$hari_ini}', '{$requester}', '{$r->satuan}', '{$r->qty}', '{$r->kode_produksi}', '{$r->skala_prioritas}'); ";
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
	$mrequest_kode = $_POST['mrequest_kode'];
	$item_kode = $_POST['item_kode'];
	$notes = $_POST['notes'];
	
	$sql = "SELECT COUNT(*) AS jml FROM mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$item_kode}' AND notes = '{$notes}' AND request_kode <> ''; ";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	if($r['jml'] > 0) {
		$ret = "Maaf kode barang sudah dibuat PR, tidak bisa dihapus/diclose";
	} else{
		$ret = "OK";
	}
	echo $ret;
}

function cekhapusmr() {
	global $conn, $app_plan_id, $armasi_conn;
	$mrequest_kode = $_POST['mrequest_kode'];
	
	$sql = "SELECT COUNT(*) AS jml FROM mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND request_kode <> ''; ";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	if($r['jml'] > 0) {
		$ret = "MAAF!!!... KODE MR ".$mrequest_kode." tidak bisa di hapus karena sebagian sudah di buat PR";
	} else{
		$ret = "OK";
	}
	echo $ret;
}

function delete(){	
	global $conn, $app_plan_id, $armasi_conn;
	
	$mrequest_kode = $_POST['mrequest_kode'];
	$item_kode = $_POST['item_kode'];
	$notes = $_POST['notes'];

	$sql_armasi = "DELETE FROM mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$item_kode}' AND notes = '{$notes}';";
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		$sql_u = "DELETE FROM tbl_mreqitem WHERE mrequest_kode = '{$mrequest_kode}' AND item_kode = '{$item_kode}' AND notes = '{$notes}';";
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

function deletemr(){	
	global $conn, $app_plan_id, $armasi_conn;
	
	$mrequest_kode = $_POST['mrequest_kode'];
	
	$sql_armasi = "DELETE FROM mreqitem WHERE mrequest_kode = '{$mrequest_kode}'; DELETE FROM mrequest WHERE mrequest_kode = '{$mrequest_kode}';";
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		$sql_u = "DELETE FROM tbl_mreqitem WHERE mrequest_kode = '{$mrequest_kode}'; DELETE FROM tbl_mrequest WHERE mrequest_kode = '{$mrequest_kode}';";
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
	$sql = "SELECT b.wo_code, b.wo_date, b.wo_desc, b.wo_asset, c.amm_desc, b.wo_pic1, b.wo_scheduled, count(d.item_code)
		FROM tbl_wo b
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		JOIN tbl_wo_detail d ON(b.wo_code = d.wo_code)
		WHERE b.wo_status IN('O','S') AND b.wo_code NOT IN (SELECT wo_code FROM tbl_mrequest)
		GROUP BY b.wo_code, b.wo_date, b.wo_desc, b.wo_asset, c.amm_desc, b.wo_pic1, b.wo_scheduled
		ORDER BY b.wo_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_pic1']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
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
	$sql = "SELECT wd.*, w.wo_desc, TO_CHAR(w.wo_scheduled, 'YYYY-MM-DD') AS wo_scheduled 
		FROM tbl_wo_detail wd
		JOIN tbl_wo w ON (wd.wo_code = w.wo_code) 
		WHERE wd.wo_code = '{$wo_code}' ORDER BY wd.item_name";
	$query = pg_query($conn,$sql);
	$i = 1;
	while($row = pg_fetch_array($query)){
		$sqlstok = "SELECT SUM(qty) AS ada FROM tbl_mutasi_dead_stock WHERE item_kode = '".$row['item_code']."'";
		$querystok = pg_query($armasi_conn, $sqlstok);
		$rstok = pg_fetch_array($querystok);
		$stockDS = $rstok['ada'] > 0 ? "Dead Stock" : "Stock";
		$sqlinactive = "SELECT inactive from item where item_kode = '".$row['item_code']."'";
		$queryinactive = pg_query($armasi_conn, $sqlinactive);
		$rinactive = pg_fetch_array($queryinactive);
		if($rinactive['inactive'] == 't' || $rinactive['inactive'] == 'true') {
			
		} else {
			echo ("<row id='".$i."'>");
			print("<cell><![CDATA[1]]></cell>");
			print("<cell><![CDATA[".$row['item_code']."]]></cell>");
			print("<cell><![CDATA[".$row['item_name']."]]></cell>");
			print("<cell><![CDATA[".$row['unit']."]]></cell>");
			print("<cell><![CDATA[".$stockDS."]]></cell>");
			print("<cell><![CDATA[".$row['qty']."]]></cell>");
			print("<cell><![CDATA[]]></cell>");
			print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
			print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
			print("<cell><![CDATA[]]></cell>");
			print("<cell><![CDATA[]]></cell>");
			print("</row>");
			$i++;
		}
	}
	echo '</rows>';
}

function cetakmemo() {
	global $app_plan_id, $armasi_conn;

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
		}elseif($r_[kode_produksi]=='QC'){
			$detail_kode="- Quality Control";
		}elseif($r_[kode_produksi]=='SP'){
			$detail_kode="- Sorting Packing";
		}elseif($r_[kode_produksi]=='UM'){
			$detail_kode="- Umum";
		}elseif($r_[kode_produksi]=='WE'){
			$detail_kode="- Workshop Elektrik";
		}elseif($r_[kode_produksi]=='WM'){
			$detail_kode="- Workshop Mekanik";
		}
		
	$txtout.="<td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[notes]</font></td>
		<td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[kode_produksi] $detail_kode</font></td>
		<td valign=top><font size=1 face=Times New Roman, Times, serif>$r_[tgl_kebutuhan]</font></td></tr>";
			//if($filter_print!="QBB"){
				$txtout.="
					<tr bgcolor=#FFFFFF >	";	
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
	echo $txtout;
}

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

function sqlupdatesch() {	
	global $conn, $app_plan_id, $armasi_conn;
	$sql_armasi = "SELECT mrequest_kode, wo_kode FROM mrequest WHERE wo_kode IS NOT NULL AND wo_scheduled IS NULL";
	$query_armasi = pg_query($armasi_conn, $sql_armasi);
	while($r = pg_fetch_array($query_armasi)) {
		$sql = "SELECT wo_scheduled FROM tbl_wo WHERE wo_code = '{$r[wo_kode]}'";
		$query = pg_query($conn, $sql);
		$row = pg_fetch_array($query);
		if($row[wo_scheduled]) {
			$sql_update .= "UPDATE mrequest SET wo_scheduled = '{$row[wo_scheduled]}' WHERE mrequest_kode = '{$r[mrequest_kode]}' AND wo_kode = '{$r[wo_kode]}'; ";	
		}
	}
	$res_armasi = pg_query($armasi_conn, $sql_update);
	if($res_armasi){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($armasi_conn);
	}
	pg_close();
	echo $ret;
}

?>