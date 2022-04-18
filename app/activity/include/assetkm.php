<?php
require_once '../../../libs/init.php'; 

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
	case "save";
		save();
	break;
	case "delete";
		delete();
	break;
}

function view(){
	global $conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.*, b.amm_desc
		FROM tbl_km_asset a
		JOIN assets_master_main b ON (a.amm_code = b.amm_code)
		WHERE a.tanggal >= '{$tglfrom}' AND a.tanggal <= '{$tglto}' ORDER BY a.amm_code, a.tanggal";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		echo ("<row id='".$row['amm_code']."@@".$row['tanggal']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['tanggal']."]]></cell>");
		print("<cell><![CDATA[".$row['km']."]]></cell>");
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

	$kd = explode('@@', $_GET['kd']);
	$amm_code = $kd[0];
	$tanggal = $kd[1];
	$sql = "SELECT a.*, b.amm_desc
		FROM tbl_km_asset a
		JOIN assets_master_main b ON (a.amm_code = b.amm_code)
		WHERE a.amm_code = '{$amm_code}' AND a.tanggal = '{$tanggal}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<assetcode>".$row['amm_code']."</assetcode>");
	print("<assetcode_lm>".$row['amm_code']."</assetcode_lm>");
	print("<tanggal>".$row['tanggal']."</tanggal>");
	print("<tanggal_lm>".$row['tanggal']."</tanggal_lm>");
	print("<km>".$row['km']."</km>");
	print("<assetname>".$row['amm_desc']."</assetname>");
	print('</data>');
}

function save(){	
	global $conn;

	$stat = $_GET['stat'];
	$amm_code = $_POST['assetcode'];
	$amm_code_lm = $_POST['assetcode_lm'];	
	$tanggal = $_POST['tanggal'];
	$tanggal_lm = $_POST['tanggal_lm'];
	$km = $_POST['km'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");
		
	if($stat=='ubah'){
		$sql_u = "UPDATE tbl_km_asset SET amm_code = '{$amm_code}', tanggal = '{$tanggal}', km = {$km}, user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE amm_code = '{$amm_code_lm}' AND tanggal = '{$tanggal_lm}'";
	} else {
		$sql_u = "INSERT INTO tbl_km_asset (amm_code, tanggal, km, user_create, date_create) VALUES ('{$amm_code}', '{$tanggal}', {$km}, '{$user}', '{$hari_ini}');";
	}
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function delete(){	
	global $conn;
	
	$kd = explode('@@', $_POST['kd']);
	$amm_code = $kd[0];
	$tanggal = $kd[1];
	$sql_u = "DELETE FROM tbl_km_asset WHERE amm_code = '{$amm_code}' AND tanggal = '{$tanggal}'";
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

?>