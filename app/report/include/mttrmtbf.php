<?php
require_once '../../../libs/init.php';
require_once '../../../libs/initarmasi.php'; 

$user=$_SESSION["user"];
$mode=$_GET['mode'];

switch ($mode) {
	case "view";
		view();
	break;
	case "dtl";
		dtl();
	break;
	case "dtlbulan";
		dtlbulan();
	break;
	case "cmblocation";
		cmblocation();
	break;
	case "cmbsublocation";
		cmbsublocation();
	break;
}

function view(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$location = $_GET['location'];
	$sublocation = $_GET['sublocation'];
	$from_date = $_GET['from_date']." 00:00:00";;
	$to_date = $_GET['to_date']." 23:59:59";

	$sql0 = "SELECT a.no_asset, count(a.no_asset) as jml, sum(a.downtime) as downtime
		FROM (
			SELECT w.wo_asset AS no_asset,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			WHERE w.wo_status = 'C' AND w.wo_isdowntime = 'Y' AND w.wo_real_scheduled_end >= '{$from_date}' AND w.wo_real_scheduled_end <= '{$to_date}'
			UNION ALL
			SELECT d.amm_code, d.dt_value
			FROM tbl_downtime d
			WHERE d.tanggal >= '{$from_date}' AND d.tanggal <= '{$to_date}'
		) AS a
		GROUP BY a.no_asset
		HAVING a.no_asset <> '' AND a.no_asset IS NOT NULL
		ORDER BY a.no_asset";
	$query0 = pg_query($conn, $sql0);
	while($r = pg_fetch_array($query0)){
		$arr_asset["$r[no_asset]"] = $r[downtime] / $r[jml];
	}

	if($location <> '') {
		$whsatu .= " AND a.amm_location = '{$location}'";
	}

	if($sublocation <> '') {
		$whsatu .= " AND a.amm_sub_location = '{$sublocation}'";
	}
	
	$sql = "SELECT a.*, b.sac_desc,  d.sl_desc, e.ssl_desc, f.sag_desc
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on (a.amm_category = b.sac_code)
		LEFT JOIN sett_location d on (a.amm_location = d.sl_code)
		LEFT JOIN sett_sub_location e on (a.amm_location = e.ssl_location_code and a.amm_sub_location = e.ssl_code)
		LEFT JOIN sett_assets_group f on (a.amm_group = f.sag_code)
		WHERE 1=1 $whsatu ORDER BY a.amm_code";
	$query=pg_query($conn,$sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$arr_asset[$row['amm_code']]."]]></cell>");
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

function dtl() {
	$asset = $_GET['asset'];
	$thn = $_GET['thn'];

	$responce = dtl_tahun($asset, $thn);

	echo json_encode($responce);
}

function dtlbulan() {
	$asset = $_GET['asset'];
	$thn = $_GET['thn'];
	$bln = $_GET['bln'];

	$arr_bln = array('Jan' => '1', 'Feb' => '2', 'Mar' => '3', 'Apr' => '4', 'Mei' => '5', 'Jun' => '6', 'Jul' => '7', 'Agu' => '8', 'Sep' => '9', 'Okt' => '10', 'Nov' => '11', 'Des' => '12');

	$responce = dtl_bulan($asset, $thn, $arr_bln[$bln]);

	echo json_encode($responce);
}

function dtl_tahun($asset, $thn) {
	global $conn;
	$arr_bln = array('1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'Mei', '6' => 'Jun', '7' => 'Jul', '8' => 'Agu', '9' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des');

	$sql0 = "SELECT amm_desc AS asset_nama FROM assets_master_main WHERE amm_code = '{$asset}'";
	$query = pg_query($conn,$sql0);
	$r0 = pg_fetch_array($query);

	$sql = "SELECT count(a.downtime) as jml, sum(a.downtime) as downtime
		FROM (
			SELECT CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			WHERE w.wo_status = 'C' AND w.wo_isdowntime = 'Y' AND w.wo_asset = '{$asset}'
			AND date_part('year', w.wo_real_scheduled_end) = {$thn}
		) AS a";
	$query = pg_query($conn,$sql);
	$r = pg_fetch_array($query);
	if($r[jml]) {
		$jml_thn = intval($r[jml]);
		$downtime_thn = intval($r[downtime]);
	} else {
		$jml_thn = 0;
		$downtime_thn = 0;
	}
	$mttr_thn = $downtime_thn/$jml_thn;

	$sql = "SELECT a.bln, count(a.downtime) as jml, sum(a.downtime) as downtime
		FROM (
			SELECT date_part('month', w.wo_real_scheduled_end) AS bln,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			WHERE w.wo_status = 'C' AND w.wo_isdowntime = 'Y' AND w.wo_asset = '{$asset}'
			AND date_part('year', w.wo_real_scheduled_end) = {$thn}
		) AS a GROUP BY a.bln ORDER BY a.bln";
	$query = pg_query($conn,$sql);
	while($r1 = pg_fetch_array($query)) {
		$mttr = intval($r1[downtime])/intval($r1[jml]);
		$arr_nilai_bln[$r1[bln]] = round($mttr);
		$bln_akhir = $r1[bln];
	}
	foreach ($arr_bln as $key => $value) {
		$data_bln[] = array($value, $arr_nilai_bln[$key]); 
	}
	
	$responce = dtl_bulan($asset, $thn, $bln_akhir);

	$responce->asset = $r0[asset_nama];
	$responce->thn = $thn;
	$responce->mttr_thn = round($mttr_thn);
	$responce->downtime_thn = $downtime_thn;
	$responce->jml_thn = $jml_thn;
	$responce->dtl_thn[0]['name'] = 'BULAN';
	$responce->dtl_thn[0]['colorByPoint'] = true;
	$responce->dtl_thn[0]['data'] = $data_bln;

	return $responce;
}

function dtl_bulan($asset, $thn, $bln) {
	global $conn;
	$arr_bln = array('1' => 'JANUARI', '2' => 'FEBRUARI', '3' => 'MARET', '4' => 'APRIL', '5' => 'MEI', '6' => 'JUNI', '7' => 'JULI', '8' => 'AGUSTUS', '9' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER');

	$sql = "SELECT a.hari, count(a.downtime) as jml, sum(a.downtime) as downtime
		FROM (
			SELECT date_part('day', w.wo_real_scheduled_end) AS hari,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			WHERE w.wo_status = 'C' AND w.wo_isdowntime = 'Y' AND w.wo_asset = '{$asset}'
			AND date_part('year', w.wo_real_scheduled_end) = {$thn} AND date_part('month', w.wo_real_scheduled_end) = {$bln}
		) AS a GROUP BY a.hari ORDER BY a.hari";
	$query = pg_query($conn,$sql);
	while($r = pg_fetch_array($query)) {
		$mttr = intval($r[downtime])/intval($r[jml]);
		$arr_nilai_tgl[$r[hari]] = round($mttr);
	}
	
	for ($i=1; $i <= date('t',strtotime($thn."-".$bln."-1")); $i++) { 
		$arr_tgl[] = $arr_nilai_tgl[$i] ? $arr_nilai_tgl[$i] : 0; 
	}

	$responce->bln = $arr_bln[$bln]." ".$thn;
	$responce->dtl_bln[0]['name'] = 'Tgl';
	$responce->dtl_bln[0]['color'] = 'red';
	$responce->dtl_bln[0]['data'] = $arr_tgl;

	return $responce;
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

?>