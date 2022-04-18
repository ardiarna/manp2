<?php
require_once '../../../libs/init.php'; 

$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "approve";
		approve();
	break;
	case "reject";
		reject();
	break;
}

function view(){
	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$approve_status = $_GET['approvestatus'];
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval");
	if($approve_status != 'ALL') {
		$whsatu = "AND a.wr_approve_status = '{$approve_status}' ";
	}

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.*, b.amm_desc AS wr_assetname, c.se_name AS wr_request_byname
		FROM tbl_wr a
		LEFT JOIN assets_master_main b ON (a.wr_asset = b.amm_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code) 
		WHERE a.wr_date >= '{$tglfrom}' AND a.wr_date <= '{$tglto}' $whsatu ORDER BY a.wr_code";
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

function approve(){	
	global $conn;
	$wr_code = $_POST['requestno'];
	$wr_urgency = $_POST['urgency'];
	$wr_due = $_POST['duedate'];
	$wr_desc = $_POST['note'];
	$wr_asset = $_POST['assetcode'];
	
	$wr_approve_by = $_SESSION["user"];
	$wr_approve_date = date("Y-m-d");
	
	$user_create = $_SESSION["user"];
	$date_create = date("Y-m-d H:i:s");	
	$formatwokode = "WO-".date("y")."-".date("m");
	$thnbln = date("Y-m");
	$sql = "SELECT max(wo_code) as wo_code_max from tbl_wo where to_char(wo_date, 'YYYY-MM') = '{$thnbln}'";
	$query = pg_query($conn, $sql);
	$mx = pg_fetch_array($query);
	if($mx[wo_code_max] == ''){
		$mx[wo_code_max] = 0;
	} else {
		$mx[wo_code_max] = substr($mx[wo_code_max],-4);
	}
	$urutbaru = $mx[wo_code_max]+1;
	$wo_code = $formatwokode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);

	$sql_u = "UPDATE tbl_wr SET wr_approve_status = 'A', wr_approve_by = '{$wr_approve_by}', wr_approve_date = '{$wr_approve_date}' WHERE wr_code = '{$wr_code}'; INSERT INTO tbl_wo (wo_code, wo_date, wo_source, wo_status, wr_code, wo_urgency, wo_due, wo_desc, wo_asset, user_create, date_create) VALUES ('{$wo_code}', '{$date_create}', 'WR', 'O', '{$wr_code}', '{$wr_urgency}', '{$wr_due}', '{$wr_desc}', '{$wr_asset}', '{$user_create}', '{$date_create}');";
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function reject(){	
	global $conn;
	$wr_code = $_POST['requestno'];
	$wr_reason_reject = $_POST['reason'];
	$wr_approve_by = $_SESSION["user"];
	$wr_approve_date = date("Y-m-d");
	$sql_u = "UPDATE tbl_wr SET wr_approve_status = 'R', wr_reason_reject = '{$wr_reason_reject}', wr_approve_by = '{$wr_approve_by}', wr_approve_date = '{$wr_approve_date}' WHERE wr_code = '{$wr_code}'";
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