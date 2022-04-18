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
	case "savemain";
		savemain();
	break;
	case "savespec";
		savespec();
	break;
	case "savepart";
		savepart();
	break;
	case "savesparepart";
		savesparepart();
	break;
	case "savemaintenance";
		savemaintenance();
	break;
	case "savemaintenancepart";
		savemaintenancepart();
	break;
	case "delete";
		delete();
	break;
	case "loadmain";
		loadmain();
	break;
	case "loadspec";
		loadspec();
	break;
	case "loadpart";
		loadpart();
	break;
	case "loadsparepart";
		loadsparepart();
	break;
	case "loadmaintenance";
		loadmaintenance();
	break;
	case "loadmaintenancepart";
		loadmaintenancepart();
	break;
	case "cmbpart";
		cmbpart();
	break;
}

function view(){
	global $conn;
	header("Content-type: text/xml");
	//encoding may be different in your case
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.*, b.sac_desc,  d.sl_desc, e.ssl_desc, f.sag_desc
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on (a.amm_category = b.sac_code)
		LEFT JOIN sett_location d on (a.amm_location = d.sl_code)
		LEFT JOIN sett_sub_location e on (a.amm_location = e.ssl_location_code and a.amm_sub_location = e.ssl_code)
		LEFT JOIN sett_assets_group f on (a.amm_group = f.sag_code)
		ORDER BY a.amm_number, a.amm_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_number']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
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

function savemain() {	
	global $conn, $app_plan_id;

	$stat=$_GET['stat'];
	$amm_number=$_POST['amm_number'];
	$assetdesc=$_POST['assetdesc'];
	
	$kodecate=$_POST['kodecate'];
	$assetmanucode=$_POST['assetmanucode'];
	$assetmodel=$_POST['assetmodel'];
	$assetserialno=$_POST['assetserialno'];
	$assetyear=$_POST['assetyear'];
	
	$assettype=$_POST['assettype'];
	$kodelocation=$_POST['kodelocation'];
	$kodesublocation=$_POST['kodesublocation'];
	$kodegroup=$_POST['kodegroup'];
	$kodeparent=$_POST['kodeparent'];
	
	$assetstatus=$_POST['assetstatus'];
	$kodeoperator=$_POST['kodeoperator'];
		
	if($stat == 'ubah'){
		$assetcode = $_POST['assetcode'];
		$sql = "UPDATE assets_master_main SET amm_number = '{$amm_number}', amm_desc = '{$assetdesc}', amm_category = '{$kodecate}', amm_manufacture = '{$assetmanucode}', amm_model = '{$assetmodel}', amm_serial_no = '{$assetserialno}', amm_year = '{$assetyear}', amm_type = '{$assettype}', amm_location = '{$kodelocation}', amm_sub_location = '{$kodesublocation}', amm_group = '{$kodegroup}', amm_parent = '{$kodeparent}', amm_status = '{$assetstatus}', amm_operator = '{$kodeoperator}', amm_edit_user = '".$_SESSION["user"]."', amm_edit_date = '".date("Y-m-d H:i:s")."' WHERE amm_code = '{$assetcode}'";
	} else if($stat == 'create') {
		$sqlcek = "SELECT max(amm_code) as id_max from assets_master_main where left(amm_code,1) = '{$app_plan_id}'";
		$query = pg_query($conn, $sqlcek);
		$mx = pg_fetch_array($query);
		if($mx[id_max] == ''){
			$mx[id_max] = 0;
		} else {
			$mx[id_max] = substr($mx[id_max],-5);
		}
		$urutbaru = $mx[id_max]+1;
		$assetcode = $app_plan_id.str_pad($urutbaru,5,"0",STR_PAD_LEFT);		
		$sql = "INSERT INTO assets_master_main (amm_code, amm_number, amm_desc, amm_category, amm_manufacture, amm_model, amm_serial_no, amm_year, amm_type, amm_location, amm_sub_location, amm_group, amm_parent, amm_status, amm_operator, amm_add_user, amm_add_date, amm_edit_user, amm_edit_date) VALUES ('{$assetcode}', '{$amm_number}', '{$assetdesc}', '{$kodecate}', '{$assetmanucode}', '{$assetmodel}', '{$assetserialno}', '{$assetyear}', '{$assettype}', '{$kodelocation}', '{$kodesublocation}', '{$kodegroup}', '{$kodeparent}', '{$assetstatus}', '{$kodeoperator}', '".$_SESSION["user"]."', '".date("Y-m-d H:i:s")."', '".$_SESSION["user"]."', '".date("Y-m-d H:i:s")."');";
		if($_POST['assetcode'] <> '') {
			$from_assetcode = $_POST['assetcode'];
			$sql .= "INSERT INTO assets_master_spesification (ams_code, amm_color, amm_length, amm_width, amm_height, amm_gross_height, amm_custom_field1, amm_custom_field11, amm_custom_field2, amm_custom_field21, amm_custom_field3, amm_custom_field31, amm_custom_field4, amm_custom_field41, amm_custom_field5, amm_custom_field51)
				SELECT '{$assetcode}' AS kd, amm_color, amm_length, amm_width, amm_height, amm_gross_height, amm_custom_field1, amm_custom_field11, amm_custom_field2, amm_custom_field21, amm_custom_field3, amm_custom_field31, amm_custom_field4, amm_custom_field41, amm_custom_field5, amm_custom_field51
				FROM assets_master_spesification WHERE ams_code = '{$from_assetcode}';
				INSERT INTO assets_master_part (amp_code, amp_part, amp_description, amm_qty, amm_unit)
				SELECT '{$assetcode}' AS kd, amp_part, amp_description, amm_qty, amm_unit
				FROM assets_master_part WHERE amp_code = '{$from_assetcode}';
				INSERT INTO assets_master_sparepart (amsp_code, amsp_sparepart_code, amsp_sparepart_desc, amsp_unit)
				SELECT '{$assetcode}' AS kd, amsp_sparepart_code, amsp_sparepart_desc, amsp_unit
				FROM assets_master_sparepart WHERE amsp_code = '{$from_assetcode}';
				INSERT INTO assets_master_maintenance (amm_code, amms_code, amms_description, amms_intv_cycle, amms_type_cycle, amms_part, amms_add_user, amms_add_date, amms_edit_user, amms_edit_date, amms_next_wo)
				SELECT '{$assetcode}' AS kd, amms_code, amms_description, amms_intv_cycle, amms_type_cycle, amms_part, amms_add_user, amms_add_date, amms_edit_user, amms_edit_date, amms_next_wo
				FROM assets_master_maintenance WHERE amm_code = '{$from_assetcode}';
				INSERT INTO assets_master_maintenance_detail (amm_code, amms_code, item_code, item_name, unit, qty)
				SELECT '{$assetcode}' AS kd, amms_code, item_code, item_name, unit, qty
				FROM assets_master_maintenance_detail WHERE amm_code = '{$from_assetcode}';";
		}
	}

	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savespec() {	
	global $conn;

	$assetcode = $_GET['assetcode'];
	$assetcolor = $_POST['assetcolor'];
	$assetlength = $_POST['assetlength'];
	$assetwidth = $_POST['assetwidth'];
	$assetheight = $_POST['assetheight'];
	$assetweight = $_POST['assetweight'];
	$assetcustom1 = $_POST['assetcustom1'];
	$assetcustom2 = $_POST['assetcustom2'];
	$assetcustom3 = $_POST['assetcustom3'];
	$assetcustom4 = $_POST['assetcustom4'];
	$assetcustom5 = $_POST['assetcustom5'];
	$assetcustom11 = $_POST['assetcustom11'];
	$assetcustom21 = $_POST['assetcustom21'];
	$assetcustom31 = $_POST['assetcustom31'];
	$assetcustom41 = $_POST['assetcustom41'];
	$assetcustom51 = $_POST['assetcustom51'];
	
	$sql = "DELETE FROM assets_master_spesification WHERE ams_code = '{$assetcode}';
		INSERT INTO assets_master_spesification (ams_code, amm_color, amm_length, amm_width, amm_height, amm_gross_height, amm_custom_field1, amm_custom_field11, amm_custom_field2, amm_custom_field21, amm_custom_field3, amm_custom_field31, amm_custom_field4, amm_custom_field41, amm_custom_field5, amm_custom_field51) VALUES ('{$assetcode}', '{$assetcolor}', '{$assetlength}', '{$assetwidth}', '{$assetheight}', '{$assetweight}', '{$assetcustom1}', '{$assetcustom11}', '{$assetcustom2}', '{$assetcustom21}', '{$assetcustom3}', '{$assetcustom31}', '{$assetcustom4}', '{$assetcustom41}', '{$assetcustom5}', '{$assetcustom51}');";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savepart() {	
	global $conn;

	$assetcode = $_GET['assetcode'];
	$prm = json_decode($_POST["prm"], false);
	
	$sql = "DELETE FROM assets_master_part WHERE amp_code = '{$assetcode}'; ";
	foreach ($prm as $r) {
		$sql .= "INSERT INTO assets_master_part (amp_code, amp_part, amp_description, amm_qty, amm_unit) VALUES ('{$assetcode}', '{$r->amp_part}', '{$r->amp_description}', '{$r->amm_qty}', '{$r->amm_unit}'); ";
	}
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savesparepart() {	
	global $conn;

	$assetcode = $_GET['assetcode'];
	$prm = json_decode($_POST["prm"], false);
	
	$sql = "DELETE FROM assets_master_sparepart WHERE amsp_code = '{$assetcode}'; ";
	foreach ($prm as $r) {
		$sql .= "INSERT INTO assets_master_sparepart (amsp_code, amsp_sparepart_code, amsp_sparepart_desc, amsp_unit) VALUES ('{$assetcode}', '{$r->amsp_sparepart_code}', '{$r->amsp_sparepart_desc}', '{$r->amsp_unit}'); ";
	}
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savemaintenance() {	
	global $conn;

	$stat = $_GET['stat'];
	$assetcode = $_REQUEST['assetcode'];
	$amms_code_lm = $_REQUEST['amms_code_lm'];
	$amms_code = $_REQUEST['amms_code'];
	$amms_description = $_REQUEST['amms_description'];
	$amms_part = $_REQUEST['amms_part'];
	$amms_intv_cycle = $_REQUEST['amms_intv_cycle'];
	$amms_type_cycle = $_REQUEST['amms_type_cycle'];
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");

	if($stat == 'tambah') { 
		$sql = "INSERT INTO assets_master_maintenance (amm_code, amms_code, amms_description, amms_part, amms_intv_cycle, amms_type_cycle, amms_add_user, amms_add_date) VALUES ('{$assetcode}', '{$amms_code}', '{$amms_description}', '{$amms_part}', {$amms_intv_cycle}, '{$amms_type_cycle}', '{$user}', '{$hari_ini}');";
	} else if($stat == 'ubah') {
		$sql = "UPDATE assets_master_maintenance SET amms_code = '{$amms_code}', amms_description = '{$amms_description}', amms_part = '{$amms_part}', amms_intv_cycle = {$amms_intv_cycle}, amms_type_cycle = '{$amms_type_cycle}', amms_edit_user = '{$user}', amms_edit_date = '{$hari_ini}' WHERE amm_code = '{$assetcode}' AND amms_code = '{$amms_code_lm}';";
	} else if($stat == 'hapus') {
		$sql = "DELETE FROM assets_master_maintenance WHERE amm_code = '{$assetcode}' AND amms_code = '{$amms_code}';";
	}
	
	$res = pg_query($conn, $sql);
	if($res){
		if($stat == 'hapus') {
			$ret = "OK";
		} else if($amms_type_cycle == 'Days' || $amms_type_cycle == 'Months' || $amms_type_cycle == 'Years') {
			$intv_cycle = $amms_intv_cycle." ".$amms_type_cycle;
			if($stat == 'tambah') {
				$sql = "UPDATE assets_master_maintenance SET amms_next_wo = (amms_add_date + INTERVAL '{$intv_cycle}') WHERE amm_code = '{$assetcode}' AND amms_code = '{$amms_code}';";
			} else if($stat == 'ubah') {
				$sql = "UPDATE assets_master_maintenance SET amms_next_wo = (amms_edit_date + INTERVAL '{$intv_cycle}') WHERE amm_code = '{$assetcode}' AND amms_code = '{$amms_code}';";
			}
			$res = pg_query($conn, $sql);
			if($res){
				$ret = "OK";		
			} else {
				$ret = pg_errormessage($conn);	
			}
		} else {
			$ret = "OK";
		}
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savemaintenancepart() {
	global $conn;

	$assetcode = $_POST['assetcode'];
	$amms_code = $_POST['amms_code'];
	$arr_item_code = json_decode($_POST['sparepartlist'], false);
	$sql = "DELETE FROM assets_master_maintenance_detail WHERE amm_code = '{$assetcode}' AND amms_code = '{$amms_code}'; ";
	foreach ($arr_item_code as $r) {
		$sql .= "INSERT INTO assets_master_maintenance_detail (amm_code, amms_code, item_code, item_name, unit, qty) VALUES ('{$assetcode}', '{$amms_code}', '{$r->item_code}', '{$r->item_name}', '{$r->unit}', '{$r->qty}'); ";
	}
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function delete() {	
	global $conn;

	$assetcode = $_POST['assetcode'];
	$sql = "DELETE FROM assets_master_main WHERE amm_code = '{$assetcode}';
		DELETE FROM assets_master_spesification WHERE ams_code = '{$assetcode}';
		DELETE FROM assets_master_part WHERE amp_code = '{$assetcode}';
		DELETE FROM assets_master_sparepart WHERE amsp_code = '{$assetcode}';
		DELETE FROM assets_master_maintenance WHERE amm_code = '{$assetcode}';
		DELETE FROM assets_master_maintenance_detail WHERE amm_code = '{$assetcode}';";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function loadmain(){	
	global $conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$kd = $_GET['kd'];
	$sql = "SELECT a.*, b.sac_desc, c.sm_desc,  d.sl_desc, e.ssl_desc, f.sag_desc, g.amm_desc as parent_desc, h.se_name
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on (a.amm_category = b.sac_code)
		LEFT JOIN sett_manufacture c on (a.amm_manufacture = c.sm_code)
		LEFT JOIN sett_location d on (a.amm_location = d.sl_code)
		LEFT JOIN sett_sub_location e on (a.amm_location = e.ssl_location_code and a.amm_sub_location = e.ssl_code)
		LEFT JOIN sett_assets_group f on (a.amm_group = f.sag_code)
		LEFT JOIN assets_master_main g on (a.amm_parent = g.amm_code)
		LEFT JOIN sett_employee h on (a.amm_operator = h.se_code) 
		WHERE a.amm_code = '{$kd}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<assetcode>".$row['amm_code']."</assetcode>");
	print("<amm_number>".$row['amm_number']."</amm_number>");
	print("<assetdesc>".$row['amm_desc']."</assetdesc>");
	print("<kodecate>".$row['amm_category']."</kodecate>");
	print("<namecate>".$row['sac_desc']."</namecate>");
	print("<assetmanucode>".$row['amm_manufacture']."</assetmanucode>");
	print("<assetmanuname>".$row['sm_desc']."</assetmanuname>");
	print("<assetmodel>".$row['amm_model']."</assetmodel>");
	print("<assetserialno>".$row['amm_serial_no']."</assetserialno>");
	print("<assetyear>".$row['amm_year']."</assetyear>");
	print("<assettype>".$row['amm_type']."</assettype>");
	print("<kodelocation>".$row['amm_location']."</kodelocation>");
	print("<namelocation>".$row['sl_desc']."</namelocation>");
	print("<kodesublocation>".$row['amm_sub_location']."</kodesublocation>");
	print("<namesublocation>".$row['ssl_desc']."</namesublocation>");
	print("<kodegroup>".$row['amm_group']."</kodegroup>");
	print("<namegroup>".$row['sag_desc']."</namegroup>");
	print("<kodeparent>".$row['amm_parent']."</kodeparent>");
	print("<nameparent>".$row['parent_desc']."</nameparent>");
	print("<kodeoperator>".$row['amm_operator']."</kodeoperator>");
	print("<nameoperator>".$row['se_name']."</nameoperator>");
	print("<assetstatus>".$row['amm_status']."</assetstatus>");
	
	print('</data>');
}

function loadspec(){	
	global $conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM assets_master_spesification  WHERE ams_code = '{$kd}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<assetcolor>".$row['amm_color']."</assetcolor>");
	print("<assetlength>".$row['amm_length']."</assetlength>");
	print("<assetwidth>".$row['amm_width']."</assetwidth>");
	print("<assetheight>".$row['amm_height']."</assetheight>");
	print("<assetweight>".$row['amm_gross_height']."</assetweight>");
	print("<assetcustom1>".$row['amm_custom_field1']."</assetcustom1>");
	print("<assetcustom2>".$row['amm_custom_field2']."</assetcustom2>");
	print("<assetcustom3>".$row['amm_custom_field3']."</assetcustom3>");
	print("<assetcustom4>".$row['amm_custom_field4']."</assetcustom4>");
	print("<assetcustom5>".$row['amm_custom_field5']."</assetcustom5>");
	print("<assetcustom11>".$row['amm_custom_field11']."</assetcustom11>");
	print("<assetcustom21>".$row['amm_custom_field21']."</assetcustom21>");
	print("<assetcustom31>".$row['amm_custom_field31']."</assetcustom31>");
	print("<assetcustom41>".$row['amm_custom_field41']."</assetcustom41>");
	print("<assetcustom51>".$row['amm_custom_field51']."</assetcustom51>");
	
	print('</data>');
}

function loadpart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM assets_master_part WHERE amp_code = '{$kd}' ORDER BY amp_part";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amp_part']."'>");
		print("<cell><![CDATA[".$row['amp_part']."]]></cell>");
		print("<cell><![CDATA[".$row['amp_description']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_qty']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_unit']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function loadsparepart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM assets_master_sparepart WHERE amsp_code = '{$kd}' ORDER BY amsp_sparepart_desc";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amsp_sparepart_code']."'>");
		print("<cell><![CDATA[".$row['amsp_sparepart_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amsp_sparepart_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amsp_unit']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function loadmaintenance(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM assets_master_maintenance WHERE amm_code = '{$kd}' ORDER BY amms_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amms_code']."'>");
		print("<cell><![CDATA[".$row['amms_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_description']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_part']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_intv_cycle']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_type_cycle']."]]></cell>");
		// print("<cell><![CDATA[<a><img onclick=\"editSparepartPM()\" src='../../assets/imgs/calendar.gif'></a>]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function loadmaintenancepart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$kd = $_GET['kd'];
	$amms_code = $_GET['amms'];
	$sql = "SELECT * FROM assets_master_maintenance_detail WHERE amm_code = '{$kd}' AND amms_code = '{$amms_code}'";
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

function cmbpart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<complete>';

	$kd = $_GET['kd'];
	$id = $_GET['id'];
	$sql = "SELECT * FROM assets_master_part WHERE amp_code = '{$kd}'";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		$amp_part = htmlentities($row['amp_part']);
		$amp_description = htmlentities($row['amp_description']);
		if($row['amp_part'] == $id) {
			echo '<option value="'.$amp_part.'" selected="true">'.$amp_description.'</option>';
		} else {
			echo '<option value="'.$amp_part.'">'.$amp_description.'</option>';	
		}
		
	}

	echo '</complete>';
}

?>