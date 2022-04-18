<?php
require_once '../../../libs/init.php';
require_once '../../../libs/initarmasi.php'; 
$user=$_SESSION["user"];
//$pageName = "include/".basename($_SERVER['PHP_SELF']);
//echo $pageName;
$mode=$_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "viewwo";
		viewwo();
	break;
	case "cmblocation";
		cmblocation();
	break;
	case "cmbsublocation";
		cmbsublocation();
	break;
	case "cmbgroup";
		cmbgroup();
	break;
}

function view(){
	global $conn;
	header("Content-type: text/xml");
	//encoding may be different in your case
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$location = $_GET['location'];
	$sublocation = $_GET['sublocation'];
	$group = $_GET['group'];

	if($location <> '') {
		$whsatu .= " AND a.amm_location = '{$location}'";
	}

	if($sublocation <> '') {
		$whsatu .= " AND a.amm_sub_location = '{$sublocation}'";
	}

	if($group <> '') {
		$whsatu .= " AND a.amm_group = '{$group}'";
	}
	
	$sql = "SELECT a.*, b.sac_desc,  d.sl_desc, e.ssl_desc, f.sag_desc, g.netcost
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on (a.amm_category = b.sac_code)
		LEFT JOIN sett_location d on (a.amm_location = d.sl_code)
		LEFT JOIN sett_sub_location e on (a.amm_location = e.ssl_location_code and a.amm_sub_location = e.ssl_code)
		LEFT JOIN sett_assets_group f on (a.amm_group = f.sag_code)
		LEFT JOIN (SELECT w.wo_asset, sum(wd.netcost) AS netcost
			FROM tbl_wo_detail wd
			JOIN tbl_wo w ON(wd.wo_code = w.wo_code)
			WHERE w.wo_asset IS NOT NULL AND w.wo_asset <> ''
			GROUP BY w.wo_asset
		) AS g ON (a.amm_code = g.wo_asset)
		WHERE 1=1 $whsatu ORDER BY a.amm_code";
	$query=pg_query($conn,$sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['netcost']."]]></cell>");
		print("<cell><![CDATA[".$row['sl_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['sag_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['sac_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_status']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function viewwo(){
	global $conn;
	$status = $_GET['status'];
	$assetcode = $_GET['assetcode'];
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval");
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	if($status == 'C') {
		$tglfrom = $_GET['from_date']." 00:00:00";
		$tglto = $_GET['to_date']." 23:59:59";
		$whsatu = " AND d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' ";
	}
	$sql = "SELECT d.wo_code, d.wo_date, d.wr_code, a.wr_date, a.wr_request_by, c.se_name AS wr_request_byname, d.wo_urgency, d.wo_type, d.wo_type_code, a.wr_due, d.wo_desc, d.wo_asset, d.wo_duration, d.wo_unit_duration, d.wo_scheduled, d.wo_status, a.wr_approve_by, d.wo_asset||' - '||b.amm_desc AS wo_asset_lbl, d.wo_due, d.wo_pic_type, d.wo_source, e.netcost
		FROM tbl_wo d
		JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code)
		LEFT JOIN (
			SELECT wd.wo_code, sum(wd.netcost) AS netcost
			FROM tbl_wo_detail wd
			GROUP BY wd.wo_code
		) AS e ON (d.wo_code = e.wo_code)
		WHERE d.wo_status = '{$status}' AND d.wo_asset = '{$assetcode}' $whsatu ORDER BY d.wo_code DESC";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		$wo_source = $arr_source[$row['wo_source']];
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_code']."]]></cell>");
		print("<cell><![CDATA[".$wo_source."]]></cell>");
		print("<cell><![CDATA[".$wo_status."]]></cell>");
		print("<cell><![CDATA[".$row['wo_urgency']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset_lbl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_duration']." ".$row['wo_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_request_byname']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("<cell><![CDATA[".$row['netcost']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function cmblocation(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<complete>';

	$sql = "SELECT * FROM sett_location ORDER BY sl_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo '<option value="'.$row['sl_code'].'">'.$row['sl_code'].' - '.$row['sl_desc'].'</option>';
	}
	echo '<option value=""></option>';
	echo '</complete>';
}

function cmbsublocation(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<complete>';

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM sett_sub_location WHERE ssl_location_code = '{$kd}' ORDER BY ssl_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo '<option value="'.$row['ssl_code'].'">'.$row['ssl_code'].' - '.$row['ssl_desc'].'</option>';
	}
	echo '<option value=""></option>';
	echo '</complete>';
}

function cmbgroup(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<complete>';

	$sql = "SELECT * FROM sett_assets_group ORDER BY sag_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo '<option value="'.$row['sag_code'].'">'.$row['sag_code'].' - '.$row['sag_desc'].'</option>';
	}
	echo '<option value=""></option>';
	echo '</complete>';
}

?>