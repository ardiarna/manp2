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
	case "save";
		save();
	break;
	case "delete";
		delete();
	break;
}

function view(){
	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval", "X" => "Cancel");

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';


	$i=1;
	$sql = "SELECT a.*, b.amm_desc AS wr_assetname, c.se_name AS wr_request_byname
		FROM tbl_wr a
		LEFT JOIN assets_master_main b ON (a.wr_asset = b.amm_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code) 
		WHERE a.wr_date >= '{$tglfrom}' AND a.wr_date <= '{$tglto}' ORDER BY a.wr_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		echo ("<row id='".$row['wr_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wr_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_urgency']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_assetname']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_request_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_request_byname']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_to_department']."]]></cell>");
		print("<cell><![CDATA[".$wr_approve_status."]]></cell>");
		print("<cell><![CDATA[".$row['wr_reason_reject']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_date']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function save(){	
	global $conn;

	$stat = $_GET['stat'];
	$wr_urgency = $_POST['urgency'];
	$wr_due = $_POST['duerequest'];
	$wr_desc = $_POST['note'];
	$wr_request_by = $_POST['reqbycode'];
	$wr_to_department = $_POST['departemen'];
	$wr_asset = $_POST['assetcode'];
	if($stat=='ubah'){
		$user_modify = $_SESSION["user"];
		$date_modify = date("Y-m-d H:i:s");
		$wr_code = $_POST['requestno'];
		$sql_u = "UPDATE tbl_wr SET wr_urgency = '{$wr_urgency}', wr_due = '{$wr_due}', wr_desc = '{$wr_desc}', wr_request_by = '{$wr_request_by}', wr_to_department = '{$wr_to_department}', wr_asset = '{$wr_asset}', user_modify = '{$user_modify}', date_modify = '{$date_modify}' WHERE wr_code = '{$wr_code}'";
	} else {
		$user_create = $_SESSION["user"];
		$date_create = date("Y-m-d H:i:s");
		$formatwrkode = "WR-".date("y")."-".date("m");
		$thnbln = date("Y-m");
		$sql = "SELECT max(wr_code) as wr_code_max from tbl_wr where to_char(wr_date, 'YYYY-MM') = '{$thnbln}'";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx[wr_code_max] == ''){
			$mx[wr_code_max] = 0;
		} else {
			$mx[wr_code_max] = substr($mx[wr_code_max],-4);
		}
		$urutbaru = $mx[wr_code_max]+1;
		$wr_code = $formatwrkode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);

		$sql_u = "INSERT INTO tbl_wr (wr_code, wr_date, wr_urgency, wr_due, wr_desc, wr_request_by, wr_to_department, wr_asset, wr_approve_status, user_create, date_create) VALUES ('{$wr_code}', '{$date_create}', '{$wr_urgency}', '{$wr_due}', '{$wr_desc}', '{$wr_request_by}', '{$wr_to_department}', '{$wr_asset}', 'W', '{$user_create}', '{$date_create}');";
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
	
	$wr_code = $_POST['requestno'];
	$sql_u = "UPDATE tbl_wr SET wr_approve_status = 'X' WHERE wr_code = '{$wr_code}'";
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