<?php
require_once '../../../libs/init.php'; 

$user = $_SESSION["user"];

$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "viewdetail";
		viewdetail();
	break;
}

function view() {
	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";;
	$tglto = $_GET['to_date']." 23:59:59";;
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.*, b.wo_date, b.wo_asset, b.wo_desc, b.wo_scheduled, c.amm_desc, b.wo_location ||' - '||d.sl_desc AS asset_location
		FROM tbl_mr a
		JOIN tbl_wo b ON (a.wo_code = b.wo_code)
		LEFT JOIN assets_master_main c ON (b.wo_asset = c.amm_code)
		LEFT JOIN sett_location d ON (b.wo_location = d.sl_code) 
		WHERE a.mr_date >= '{$tglfrom}' AND a.mr_date <= '{$tglto}' ORDER BY a.mr_code DESC";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['mr_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['mr_code']."]]></cell>");
		print("<cell><![CDATA[".$row['mr_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['asset_location']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function viewdetail(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$mr_code = $_GET['kd'];
	$sql = "SELECT * FROM tbl_mr_detail WHERE mr_code = '{$mr_code}' ORDER BY item_name";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['item_code']."'>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}


?>