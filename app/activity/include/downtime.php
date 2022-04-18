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
	$sql = "SELECT a.*, b.amm_desc, c.se_name
		FROM tbl_downtime a
		JOIN assets_master_main b ON (a.amm_code = b.amm_code)
		LEFT JOIN sett_employee c ON (a.dt_personil = c.se_code)
		WHERE a.tanggal >= '{$tglfrom}' AND a.tanggal <= '{$tglto}' ORDER BY a.dt_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		echo ("<row id='".$row['dt_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['dt_code']."]]></cell>");
		print("<cell><![CDATA[".$row['tanggal']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['dt_value']."]]></cell>");
		print("<cell><![CDATA[".$row['dt_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['se_name']."]]></cell>");
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

	$dt_code = $_GET['kd'];
	$sql = "SELECT a.*, b.amm_desc, c.se_name
		FROM tbl_downtime a
		JOIN assets_master_main b ON (a.amm_code = b.amm_code)
		LEFT JOIN sett_employee c ON (a.dt_personil = c.se_code)
		WHERE a.dt_code = '{$dt_code}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<dtcode>".$row['dt_code']."</dtcode>");
	print("<tanggal>".$row['tanggal']."</tanggal>");
	print("<assetcode>".$row['amm_code']."</assetcode>");
	print("<assetname>".$row['amm_desc']."</assetname>");
	print("<dtvalue>".$row['dt_value']."</dtvalue>");
	print("<dtdesc>".$row['dt_desc']."</dtdesc>");
	print("<dtpersonil>".$row['dt_personil']."</dtpersonil>");
	print("<personilname>".$row['se_name']."</personilname>");
	print('</data>');
}

function save(){	
	global $conn;

	$stat = $_GET['stat'];
	$amm_code = $_POST['assetcode'];
	$tanggal = $_POST['tanggal'];
	$dt_value = $_POST['dtvalue'];
	$dt_desc = $_POST['dtdesc'];
	$dt_personil = $_POST['dtpersonil'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");
		
	if($stat=='ubah'){
		$dt_code = $_POST['dtcode'];
		$sql_u = "UPDATE tbl_downtime SET amm_code = '{$amm_code}', tanggal = '{$tanggal}', dt_value = {$dt_value}, dt_desc = '{$dt_desc}', dt_personil = '{$dt_personil}', user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE dt_code = '{$dt_code}';";
	} else {
		$formatwokode = "DT".date("y").date("m");
		$thnbln = date("Y-m");
		$sql = "SELECT max(dt_code) as dt_code_max from tbl_downtime where to_char(tanggal, 'YYYY-MM') = '{$thnbln}'";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['dt_code_max'] == ''){
			$mx['dt_code_max'] = 0;
		} else {
			$mx['dt_code_max'] = substr($mx['dt_code_max'],-6);
		}
		$urutbaru = $mx['dt_code_max']+1;
		$dt_code = $formatwokode.str_pad($urutbaru,6,"0",STR_PAD_LEFT);
		$sql_u = "INSERT INTO tbl_downtime (dt_code, amm_code, tanggal, dt_value, dt_desc, dt_personil, user_create, date_create) VALUES ('{$dt_code}', '{$amm_code}', '{$tanggal}', {$dt_value}, '{$dt_desc}', '{$dt_personil}', '{$user}', '{$hari_ini}');";
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
	
	$dt_code = $_POST['kd'];
	$sql_u = "DELETE FROM tbl_downtime WHERE dt_code = '{$dt_code}';";
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