<?php
require_once '../../../libs/init.php'; 

$user = $_SESSION["user"];
$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
}

function view(){
	global $conn;
	$status = $_GET['status'];
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval");
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	if($status == 'C') {
		$tglfrom = $_GET['from_date']." 00:00:00";
		$tglto = $_GET['to_date']." 23:59:59";
		$whsatu = " AND w.wo_status = 'C' AND w.wo_date >= '{$tglfrom}' AND w.wo_date <= '{$tglto}' ";
	} else {
		$whsatu = " AND w.wo_status IN ('O','S') ";
	}
	$sql = "SELECT wd.*, w.wo_date, w.wo_status, w.wo_scheduled 
		FROM tbl_wo_detail wd
		JOIN tbl_wo w ON (wd.wo_code = w.wo_code)
		WHERE 1=1 $whsatu ORDER BY wd.item_name";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		// $wo_status = $arr_status[$row['wo_status']];
		// $wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		// $wo_source = $arr_source[$row['wo_source']];
		echo ("<row id='".$row['wo_code'].$row['item_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

?>