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
	case "loadasset";
		loadasset();
	break;
	case "loadetail";
		loadetail();
	break;
	case "save";
		save();
	break;
	case "savedetail";
		savedetail();
	break;
	case "delete";
		delete();
	break;
	case "deletedetail";
		deletedetail();
	break;
	case "ubahsort";
		ubahsort();
	break;
}

function view(){
	global $conn;
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$sql = "SELECT a.ceklist_code, a.ceklist_name, string_agg(c.amm_desc,', ') AS asset_list
		FROM sett_ceklist a
		JOIN sett_ceklist_asset b ON(a.ceklist_code = b.ceklist_code)
		JOIN assets_master_main c ON(b.asset_code = c.amm_code)
		GROUP BY a.ceklist_code, a.ceklist_name
		ORDER BY a.ceklist_code";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		echo ("<row id='".$row['ceklist_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['ceklist_code']."]]></cell>");
		print("<cell><![CDATA[".$row['ceklist_name']."]]></cell>");
		print("<cell><![CDATA[".$row['asset_list']."]]></cell>");
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

	$ceklist_code = $_GET['kd'];
	$sql = "SELECT a.*
		FROM sett_ceklist a
		WHERE a.ceklist_code = '{$ceklist_code}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<ceklist_code>".$row['ceklist_code']."</ceklist_code>");
	print("<ceklist_name>".$row['ceklist_name']."</ceklist_name>");
	print('</data>');
}

function loadasset(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$ceklist_code = $_GET['kd'];
	$sql = "SELECT b.asset_code, c.amm_desc 
		FROM sett_ceklist_asset b
		JOIN assets_master_main c ON(b.asset_code = c.amm_code)
		WHERE ceklist_code = '{$ceklist_code}'
		ORDER BY c.amm_desc";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['asset_code']."'>");
		print("<cell><![CDATA[".$row['asset_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function loadetail(){
	global $conn;

	$ceklist_code = $_GET['kd'];

	$out = '<table class="adaborder"><tr><td colspan="3"><button class="button biru" onClick="editDetail(\''.$ceklist_code.'\',\'0\',\'create\')"> Add Detail</button></td></tr>';
	$out .= display_node($ceklist_code, 0, 10);
	$out .= "</table>";

	echo $out;
}

function display_node($ceklist_code, $parent, $padding) {
	global $conn;
	if($parent == '0') {
		$no = 0;
		$no2 = '';
	} else {
		$no = '';
		$no2 = '-';
	}
	
	$sql = "SELECT a.ceklist_code, a.cd_code, a.cd_name, a.cd_parent, a.cd_sort, b.am_count, c.max_sort
		FROM sett_ceklist_detail a 
		LEFT JOIN (SELECT cd_parent, count(*) AS am_count FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) b ON(a.cd_code = b.cd_parent)
		LEFT JOIN (SELECT cd_parent, max(cd_sort) AS max_sort FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) c ON(a.cd_parent = c.cd_parent)
		WHERE a.ceklist_code = '{$ceklist_code}' AND a.cd_parent = {$parent} ORDER BY a.cd_sort";
	$query=pg_query($conn,$sql);
	while($r=pg_fetch_array($query)) {
		if($parent == '0') {
			$no++;
		}
		$btn_tambah = '<button class="button biru" onClick="editDetail(\''.$ceklist_code.'\',\''.$r[cd_code].'\',\'create\')"><i class="fa fa-plus"></i></button> ';
		$btn_edit = '<button class="button hijau" onClick="editDetail(\''.$ceklist_code.'\',\''.$r[cd_code].'\',\'ubah\',\''.$r[cd_name].'\')"><i class="fa fa-edit"></i></button> ';
		$btn_hapus = '<button class="button merah" onClick="hapusDetail(\''.$ceklist_code.'\',\''.$r[cd_code].'\',\''.$r[cd_name].'\')"><i class="fa fa-times"></i></button> ';
		$btn_up = $r[cd_sort] != '1' ? '<button class="button ungu" onClick="ubahSort(\'naik\',\''.$ceklist_code.'\',\''.$r[cd_code].'\',\''.$r[cd_sort].'\',\''.$r[cd_parent].'\')"><i class="fa fa-angle-up"></i></button> ' : '';
		$btn_dn = $r[cd_sort] != $r[max_sort] ? '<button class="button ungu" onClick="ubahSort(\'turun\',\''.$ceklist_code.'\',\''.$r[cd_code].'\',\''.$r[cd_sort].'\',\''.$r[cd_parent].'\')"><i class="fa fa-angle-down"></i></button> ' : '';
		$out .= '<tr><td style="text-align:center;">'.$no.'</td><td style="padding-left:'.$padding.'px;">'.$no2.' '.$r[cd_name].'</td><td style="text-align:center;">'.$btn_tambah.$btn_edit.$btn_hapus.$btn_dn.$btn_up.'</td></tr>';
		if ($r[am_count] > 0) {
			$out .= display_node($r[ceklist_code], $r[cd_code], $padding+15);
        }
	}
	return $out;
}

function save(){	
	global $conn;

	$stat = $_GET['stat'];
	$ceklist_name = $_POST['ceklist_name'];
	$asset_list = json_decode($_POST['asset_list'], false);
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d H:i:s");
		
	if($stat=='ubah'){
		$ceklist_code = $_POST['ceklist_code'];
		$sql_u = "UPDATE sett_ceklist SET ceklist_name = '{$ceklist_name}', user_modify = '{$user}', date_modify = '{$hari_ini}' WHERE ceklist_code = '{$ceklist_code}'; DELETE FROM sett_ceklist_asset WHERE ceklist_code = '{$ceklist_code}';";
	} else {
		$sql = "SELECT max(ceklist_code) AS code_max FROM sett_ceklist";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['code_max'] == ''){
			$mx['code_max'] = 0;
		} else {
			$mx['code_max'] = substr($mx['code_max'],-5);
		}
		$urutbaru = $mx['code_max']+1;
		$ceklist_code = "CK".str_pad($urutbaru,5,"0",STR_PAD_LEFT);
		$sql_u = "INSERT INTO sett_ceklist (ceklist_code, ceklist_name, user_create, date_create) VALUES ('{$ceklist_code}', '{$ceklist_name}', '{$user}', '{$hari_ini}');";
	}
	foreach ($asset_list as $r) {
		$sql_u .= "INSERT INTO sett_ceklist_asset (ceklist_code, asset_code) VALUES ('{$ceklist_code}', '{$r->asset_code}');";
	}
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK".$ceklist_code;
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function savedetail(){	
	global $conn;

	$stat = $_GET['stat'];
	$ceklist_code = $_POST['ceklist_code'];
	$cd_name = $_POST['cd_name'];

	if($stat=='ubah'){
		$cd_code = $_POST['cd_parent'];
		$sql_u = "UPDATE sett_ceklist_detail SET cd_name = '{$cd_name}' WHERE ceklist_code = '{$ceklist_code}' AND cd_code = '{$cd_code}';";	
	} else {
		$cd_parent = $_POST['cd_parent'];
		$sql = "SELECT max(cd_code) AS code_max FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}'";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['code_max'] == ''){
			$mx['code_max'] = 0;
		}
		$cd_code = $mx['code_max']+1;
		$sql = "SELECT max(cd_sort) AS sort_max FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' AND cd_parent = '{$cd_parent}'";
		$query = pg_query($conn, $sql);
		$sort = pg_fetch_array($query);
		if($sort['sort_max'] == ''){
			$sort['sort_max'] = 0;
		}
		$cd_sort = $sort['sort_max']+1;
		$sql_u = "INSERT INTO sett_ceklist_detail (ceklist_code, cd_code, cd_name, cd_parent, cd_sort) VALUES ('{$ceklist_code}', '{$cd_code}','{$cd_name}', '{$cd_parent}', '{$cd_sort}');";
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
	$ceklist_code = $_POST['ceklist_code'];
	$sql_u = "DELETE FROM sett_ceklist WHERE ceklist_code = '{$ceklist_code}'; DELETE FROM sett_ceklist_asset WHERE ceklist_code = '{$ceklist_code}'; DELETE FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}';";
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function deletedetail(){	
	global $conn;
	$ceklist_code = $_POST['ceklist_code'];
	$cd_code = $_POST['cd_code'];
	$sql_u = "DELETE FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' AND cd_code = '{$cd_code}';";
	$sql_u .= delete_node($ceklist_code, $cd_code);
	$res = pg_query($conn, $sql_u);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function delete_node($ceklist_code, $parent) {
	global $conn;
	$sql = "SELECT a.ceklist_code, a.cd_code, b.am_count
		FROM sett_ceklist_detail a 
		LEFT JOIN (SELECT cd_parent, count(*) AS am_count FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) b ON(a.cd_code = b.cd_parent)
		WHERE a.ceklist_code = '{$ceklist_code}' AND a.cd_parent = {$parent} ORDER BY a.cd_code";
	$query=pg_query($conn,$sql);
	while($r=pg_fetch_array($query)) {
		$out .= "DELETE FROM sett_ceklist_detail WHERE ceklist_code = '{$r[ceklist_code]}' AND cd_code = '{$r[cd_code]}';";
		if ($r[am_count] > 0) {
			$out .= delete_node($r[ceklist_code], $r[cd_code]);
        }
	}
	return $out;
}

function ubahsort(){	
	global $conn;
	
	$stat = $_POST['stat'];
	$ceklist_code = $_POST['ceklist_code'];
	$cd_code = $_POST['cd_code'];
	$cd_sort = $_POST['cd_sort'];
	$cd_parent = $_POST['cd_parent'];
	$new_sort = $stat == "naik" ? $cd_sort - 1 : $cd_sort + 1;
	$sql_u = "UPDATE sett_ceklist_detail SET cd_sort = {$cd_sort} WHERE ceklist_code = '{$ceklist_code}' AND cd_parent = '{$cd_parent}' AND cd_sort = {$new_sort}; UPDATE sett_ceklist_detail SET cd_sort = {$new_sort} WHERE ceklist_code = '{$ceklist_code}' AND cd_code = '{$cd_code}';";
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