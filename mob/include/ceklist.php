<?php
require_once '../../libs/init.php'; 

$user = $_SESSION["user"];
$mode = $_GET['mode'];
switch ($mode) {
	case "urai":
		urai();
	break;
	case "excel";
		excel();
	break;
	case "cboasset";
		cboasset();
	break;
}

function urai() {
	$asset_code = $_POST['asset'];
	$bln = $_POST['bln'];
	$thn = $_POST['thn'];

	$responce->detailtabel = detailtabel(false, $asset_code, $bln, $thn);
	echo json_encode($responce);
}

function excel() {
	$asset_code = $_GET['asset'];
	$bln = $_GET['bln'];
	$thn = $_GET['thn'];

	$arr_bln = array('1' => 'JANUARI', '2' => 'FEBRUARI', '3' => 'MARET', '4' => 'APRIL', '5' => 'MEI', '6' => 'JUNI', '7' => 'JULI', '8' => 'AGUSTUS', '9' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER');
	
	header("Content-type: application/x-msexcel"); 
	header('Content-Disposition: attachment; filename="CHECK_LIST_'.$asset_code.'_'.$arr_bln[$bln].'_'.$thn.'.xls"');
	echo detailtabel(true, $asset_code, $bln, $thn);
}

function detailtabel($adaborder, $asset_code, $bln, $thn) {
	global $conn;

	if($adaborder){
		$border = "border='1'";
	}else{
		$border = "";
	}

	$sql = "SELECT ceklist_code, count(*) AS count FROM tbl_ceklist WHERE asset_code = '{$asset_code}' AND date_part('year', tanggal) = {$thn} AND date_part('month', tanggal) = {$bln} GROUP BY ceklist_code";
	$query = pg_query($conn,$sql);
	$r = pg_fetch_array($query);
	if($r[count] > 0) {
		$arr_bln = array('1' => 'JANUARI', '2' => 'FEBRUARI', '3' => 'MARET', '4' => 'APRIL', '5' => 'MEI', '6' => 'JUNI', '7' => 'JULI', '8' => 'AGUSTUS', '9' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER');
		$sql2 = "SELECT amm_desc FROM assets_master_main WHERE amm_code = '{$asset_code}'";
		$query2 = pg_query($conn,$sql2);
		$r2 = pg_fetch_array($query2);
		
		$out = '<style>td,th{padding-left:3px;padding-right:3px;}table.adaborder{border-collapse:collapse;width:100%;}table.adaborder th,table.adaborder td{border:1px solid black;}.str{ mso-number-format:\@; }</style>';
		$out .= '<div style="text-align:center;font-size:20px;font-weight:bold;">CHECK LIST '.$r2[amm_desc].'</div>';
		$out .= '<div style="overflow-x:auto;"><table class="adaborder" '.$border.'><thead><tr><th colspan="2">BULAN : '.$arr_bln[$bln].' '.$thn.'</th><td colspan="32">&nbsp;</td></tr><tr><th rowspan="2">NO</th><th rowspan="2">NAMA</th><th colspan="31">TANGGAL</th><th rowspan="2">KETERANGAN</th></tr><tr>';
		
		for ($i=1; $i <= 31; $i++) { 
			$out .= '<th>'.$i.'</th>';	
		}
		$out .= '</tr></thead><tbody>';
		$out .= display_node($r[ceklist_code], 0, 10, $asset_code, $bln, $thn);
		$out .= '</tbody></table></div>';
	} else {
		$out = 'TIDAKADA';
	}

	return $out;
}

function display_node($ceklist_code, $parent, $padding, $asset_code, $bln, $thn) {
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
	$query = pg_query($conn,$sql);
	while($r = pg_fetch_array($query)) {
		if($parent == '0') {
			$no++;
		}
		$sql2 = "SELECT date_part('day', tanggal) AS hari, cd_value, cd_uty 
			FROM tbl_ceklist_detail 
			WHERE asset_code = '{$asset_code}' AND date_part('year', tanggal) = {$thn} AND date_part('month', tanggal) = {$bln} AND cd_code = {$r[cd_code]}
			ORDER BY tanggal";
		$query2 = pg_query($conn, $sql2);
		$arr_value = array();
		$arr_uty = array();
		while($r2 = pg_fetch_array($query2)) {
			$arr_value["$r2[hari]"] = $r2[cd_value];
			$arr_uty["$r2[hari]"] = $r2[cd_uty]; 
		}
		$out .= '<tr><td class="text-center" style="max-width:200px;">'.$no.'</td><td style="padding-left:'.$padding.'px;">'.$no2.' '.$r[cd_name].'</td>';
		for ($i=1; $i <= 31; $i++) {
			$nilai = $arr_value[$i] ? $arr_value[$i] : ''; 
			$uty = $arr_uty[$i] ? $arr_uty[$i] : '';
			$out .= '<td class="text-center">'.$nilai.' '.$uty.'</td>';	
		}
		$sql3 = "SELECT string_agg(cd_note, ', ') AS cd_note 
			FROM tbl_ceklist_detail 
			WHERE asset_code = '{$asset_code}' AND date_part('year', tanggal) = {$thn} AND date_part('month', tanggal) = {$bln} AND cd_code = {$r[cd_code]} AND cd_note <> '' AND cd_note IS NOT NULL";
		$query3 = pg_query($conn, $sql3);
		$r3 = pg_fetch_array($query3);
		$out .= '<td>'.$r3[cd_note].'</td></tr>';
		if ($r[am_count] > 0) {
			$out .= display_node($r[ceklist_code], $r[cd_code], $padding+15, $asset_code, $bln, $thn);
        }
	}
	return $out;
}

function cboasset(){
	global $conn;
	$sql = "SELECT amm_code, amm_code||' - '||amm_desc AS lbl FROM assets_master_main WHERE amm_status = 'Active' ORDER BY amm_desc";
	$query = pg_query($conn, $sql);
	$hasil = '<option value=""></option>';
	// $hasil .= '<option value="all">ALL</option>';
	while($r = pg_fetch_array($query)) {
		$hasil .='<option value="'.$r[amm_code].'">'.$r[lbl].'</option>';
	}
	echo $hasil;
}

?>