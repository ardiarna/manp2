<?php
require_once '../../../libs/init.php'; 

$user = $_SESSION["user"];
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
	case "loadsparepart";
		loadsparepart();
	break;
	case "dtasset";
		dtasset();
	break;
}

function view(){
	global $conn;
	$status = $_GET['status'];
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval");
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$sql = "SELECT d.wo_code, d.wo_date, d.wr_code, a.wr_date, a.wr_request_by, c.se_name AS wr_request_byname, d.wo_urgency, d.wo_type, d.wo_type_code, a.wr_due, d.wo_desc, d.wo_asset, d.wo_duration, d.wo_unit_duration, d.wo_scheduled, d.wo_status, a.wr_approve_by, d.wo_asset||' - '||b.amm_desc AS wo_asset_lbl, d.wo_due, d.wo_pic_type, d.wo_source, e.mrequest_kode, f.psp_code
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code)
		LEFT JOIN tbl_mrequest e ON (d.wo_code = e.wo_code)
		LEFT JOIN tbl_psp f ON (d.wo_code = f.wo_code)
		WHERE d.wo_status = '{$status}' AND d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' ORDER BY d.wo_code";
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
		print("<cell><![CDATA[".$row['mrequest_kode']."]]></cell>");
		print("<cell><![CDATA[".$row['psp_code']."]]></cell>");
		print("<cell><![CDATA[".$wo_source."]]></cell>");
		print("<cell><![CDATA[".$wo_status."]]></cell>");
		print("<cell><![CDATA[".$row['wo_urgency']."]]></cell>");
		print("<cell><![CDATA[".htmlentities($row['wo_desc'])."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset_lbl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_duration']." ".$row['wo_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_request_byname']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_pic_type']."]]></cell>");
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

	$kd = $_GET['kd'];
	$sql = "SELECT d.wo_code, d.wo_date, d.wr_code, a.wr_date, a.wr_request_by, c.se_name AS wr_request_byname, d.sub_plant, d.wo_urgency, d.wo_type, d.wo_type_code, a.wr_due, d.wo_desc, d.wo_asset, d.wo_location, d.wo_maintenance, b.amm_desc AS wo_assetname, b.amm_number, d.wo_duration, d.wo_unit_duration, d.wo_scheduled, d.wo_instruction, d.wo_pic_type, d.wo_pic1, d.wo_pic2, d.wo_pic3, d.wo_due, e.smt_description, f.sl_desc, g.amms_description
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code)
		LEFT JOIN sett_maintenance_type e ON (d.wo_type = e.smt_work_type AND d.wo_type_code = e.smt_code) 
		LEFT JOIN sett_location f ON (d.wo_location = f.sl_code)
		LEFT JOIN assets_master_maintenance g ON (d.wo_asset = g.amm_code AND d.wo_maintenance = g.amms_code)
		WHERE d.wo_code = '{$kd}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<wono>".$row['wo_code']."</wono>");
	print("<wodate>".$row['wo_date']."</wodate>");
	print("<requestno>".$row['wr_code']."</requestno>");
	print("<reqdate>".$row['wr_date']."</reqdate>");
	print("<reqbyname>".$row['wr_request_byname']."</reqbyname>");
	print("<reqbycode>".$row['wr_request_by']."</reqbycode>");
	print("<sub_plant>".$row['sub_plant']."</sub_plant>");
	print("<urgency>".$row['wo_urgency']."</urgency>");
	print("<worktypename>".$row['wo_type']." - ".$row['smt_description']."</worktypename>");
	print("<worktype>".$row['wo_type']."</worktype>");
	print("<worktypecode>".$row['wo_type_code']."</worktypecode>");
	print("<duerequest>".$row['wo_due']."</duerequest>");
	print("<note>".htmlentities($row['wo_desc'])."</note>");
	print("<assetname>".$row['wo_assetname']."</assetname>");
	print("<assetcode>".$row['wo_asset']."</assetcode>");
	print("<amm_number>".$row['amm_number']."</amm_number>");
	print("<kodelocation>".$row['wo_location']."</kodelocation>");
	print("<namelocation>".$row['sl_desc']."</namelocation>");
	print("<kodemaintenance>".$row['wo_maintenance']."</kodemaintenance>");
	print("<namemaintenance>".$row['amms_description']."</namemaintenance>");
	print("<duration>".$row['wo_duration']."</duration>");
	print("<durationsat>".$row['wo_unit_duration']."</durationsat>");
	print("<schdate>".$row['wo_scheduled']."</schdate>");
	print("<woinstruction>".htmlentities($row['wo_instruction'])."</woinstruction>");
	print("<pictype>".$row['wo_pic_type']."</pictype>");
	print("<pic1>".$row['wo_pic1']."</pic1>");
	print("<pic2>".$row['wo_pic2']."</pic2>");
	print("<pic3>".$row['wo_pic3']."</pic3>");
	
	print('</data>');
}

function save(){	
	global $conn;

	$stat = $_GET['stat'];
	$wo_urgency = $_POST['urgency'];
	$wo_type = $_POST['worktype'];
	$wo_type_code = $_POST['worktypecode'];
	$wo_due = $_POST['duerequest'];
	$wo_desc = $_POST['note'];
	$wo_asset = $_POST['assetcode'];
	$wo_location = $_POST['kodelocation'];
	$wo_maintenance = $_POST['kodemaintenance'];
	$wo_duration = $_POST['duration'];
	$wo_unit_duration = $_POST['durationsat'];
	if($wo_duration == '') {
		$wo_duration = 0;
	}
	$wo_scheduled = $_POST['schdate'];
	$wo_status = $wo_scheduled ? 'S' : 'O';
	$wo_instruction = $_POST['woinstruction'];
	$wo_pic_type = $_POST['pictype'];
	$wo_pic1 = $_POST['pic1'];
	$wo_pic2 = $_POST['pic2'];
	$wo_pic3 = $_POST['pic3'];
	$arr_item_code = json_decode($_POST['sparepartlist'], false);
	$sub_plant = $_POST['sub_plant'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");

	if($stat=='tambah') {
		$formatwokode = "WO-".date("y")."-".date("m");
		$thnbln = date("Y-m");
		$sql = "SELECT max(wo_code) as wo_code_max from tbl_wo where to_char(wo_date, 'YYYY-MM') = '{$thnbln}'";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['wo_code_max'] == ''){
			$mx['wo_code_max'] = 0;
		} else {
			$mx['wo_code_max'] = substr($mx['wo_code_max'],-4);
		}
		$urutbaru = $mx['wo_code_max']+1;
		$wo_code = $formatwokode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);
		$sql_u = "INSERT INTO tbl_wo (wo_code, wo_date, wo_source, wo_status, wo_urgency, wo_due, wo_desc, wo_type, wo_type_code, wo_asset, wo_location, wo_maintenance, wo_scheduled, wo_duration, wo_unit_duration, wo_instruction, wo_pic_type, wo_pic1, wo_pic2, wo_pic3, sub_plant, user_create, date_create) VALUES ('{$wo_code}', '{$hari_ini}', 'MT', '{$wo_status}', '{$wo_urgency}', '{$wo_due}', '{$wo_desc}', '{$wo_type}', '{$wo_type_code}', '{$wo_asset}', '{$wo_location}', '{$wo_maintenance}', '{$wo_scheduled}', '{$wo_duration}', '{$wo_unit_duration}', '{$wo_instruction}', '{$wo_pic_type}', '{$wo_pic1}', '{$wo_pic2}', '{$wo_pic3}', '{$sub_plant}', '{$user}', '{$hari_ini}');";
	} else if($stat=='ubah') {
		$wo_code = $_POST['wono'];
		$sql_u = "UPDATE tbl_wo SET wo_status = '{$wo_status}', wo_urgency = '{$wo_urgency}', wo_due = '{$wo_due}', wo_desc = '{$wo_desc}', wo_type = '{$wo_type}', wo_type_code = '{$wo_type_code}', wo_asset = '{$wo_asset}', wo_location = '{$wo_location}', wo_maintenance = '{$wo_maintenance}', wo_scheduled = '{$wo_scheduled}', wo_duration = {$wo_duration}, wo_unit_duration = '{$wo_unit_duration}', wo_instruction = '{$wo_instruction}', wo_pic_type = '{$wo_pic_type}', wo_pic1 = '{$wo_pic1}', wo_pic2 = '{$wo_pic2}', wo_pic3 = '{$wo_pic3}', sub_plant = '{$sub_plant}', user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE wo_code = '{$wo_code}'; DELETE FROM tbl_wo_detail WHERE wo_code = '{$wo_code}'; ";
	}
	$is_mr = 'NO';
	foreach ($arr_item_code as $r) {
		$sql_u .= "INSERT INTO tbl_wo_detail (wo_code, item_code, item_name, unit, qty) VALUES ('{$wo_code}', '{$r->item_code}', '{$r->item_name}', '{$r->unit}', '{$r->qty}'); ";
		if($r->qty > $r->stock_qty) {
			$is_mr = 'YA';
		}
	}	
	$res = pg_query($conn, $sql_u);
	if($res){
		if($stat=='tambah') {
			if($is_mr == 'YA') {
				$formatmrkode = "MR-".date("y")."-".date("m");
				$thnbln = date("Y-m");
				$sql = "SELECT max(mr_code) as mr_code_max from tbl_mr where to_char(mr_date, 'YYYY-MM') = '{$thnbln}'";
				$query = pg_query($conn, $sql);
				$mx = pg_fetch_array($query);
				if($mx['mr_code_max'] == ''){
					$mx['mr_code_max'] = 0;
				} else {
					$mx['mr_code_max'] = substr($mx['mr_code_max'],-4);
				}
				$urutbaru = $mx['mr_code_max']+1;
				$mr_code = $formatmrkode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);
				$sql_mr = "INSERT INTO tbl_mr (mr_code, mr_date, wo_code, mr_status, user_create, date_create) VALUES ('{$mr_code}', '{$hari_ini}', '{$wo_code}', 'O', '{$user}', '{$hari_ini}');";
				foreach ($arr_item_code as $r) {
					if($r->qty > $r->stock_qty) {
						$qty_mr = $r->qty - $r->stock_qty;
						$sql_mr .= "INSERT INTO tbl_mr_detail (mr_code, item_code, item_name, unit, qty) VALUES ('{$mr_code}', '{$r->item_code}', '{$r->item_name}', '{$r->unit}', '{$qty_mr}'); ";  
					}	
				}
				$res = pg_query($conn, $sql_mr);
			}
			if($wo_pic_type == 'E') {
				$formatspkmrkode = "MSPK-".date("y")."-".date("m");
				$thnbln = date("Y-m");
				$sql = "SELECT max(spkmr_code) as spkmr_code_max from tbl_mrspk where to_char(spkmr_date, 'YYYY-MM') = '{$thnbln}'";
				$query = pg_query($conn, $sql);
				$mx = pg_fetch_array($query);
				if($mx['spkmr_code_max'] == ''){
					$mx['spkmr_code_max'] = 0;
				} else {
					$mx['spkmr_code_max'] = substr($mx['spkmr_code_max'],-4);
				}
				$urutbaru = $mx['spkmr_code_max']+1;
				$spkmr_code = $formatspkmrkode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);
				$sql_spkmr = "INSERT INTO tbl_mrspk (spkmr_code, spkmr_date, wo_code, spkmr_status, spkmr_desc, user_create, date_create) VALUES ('{$spkmr_code}', '{$hari_ini}', '{$wo_code}', 'O', '{$wo_desc}', '{$user}', '{$hari_ini}'); INSERT INTO tbl_mrspk_detail (spkmr_code, item_name, qty) VALUES ('{$spkmr_code}', '{$wo_desc}', 1);";
				$res = pg_query($conn, $sql_spkmr);
			}
		}
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "UPDATE tbl_wo SET wo_status = 'X' WHERE wo_code = '{$kd}';";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function loadsparepart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$wo_code = $_GET['kd'];
	$sql = "SELECT * FROM tbl_wo_detail WHERE wo_code = '{$wo_code}'";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['item_code']."'>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[0]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function dtasset(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	$i=1;
	$sql = "SELECT a.amm_code, a.amm_number, a.amm_desc, a.amm_status, a.amm_location, b.sac_desc, c.sl_desc
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on(a.amm_category = b.sac_code)
		LEFT JOIN sett_location c on(a.amm_location = c.sl_code)
		ORDER BY a.amm_code";
	$query=pg_query($conn,$sql);
		while($row=pg_fetch_array($query)){
			echo ("<row id='".$row['amm_code']."'>");
			print("<cell><![CDATA[".$i."]]></cell>");
			print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
			print("<cell><![CDATA[".$row['amm_number']."]]></cell>");
			print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
			print("<cell><![CDATA[".$row['amm_location']."]]></cell>");
			print("<cell><![CDATA[".$row['sl_desc']."]]></cell>");
			print("<cell><![CDATA[".$row['sac_desc']."]]></cell>");
			print("<cell><![CDATA[".$row['amm_status']."]]></cell>");
			print("</row>");
			$i++;
		}
	echo '</rows>';
}

?>