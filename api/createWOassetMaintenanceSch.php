<?php
require_once '../libs/init.php';

$user = $_SESSION["user"];
$hari_ini_full = date("Y-m-d H:i:s");
$hari_ini = date("Y-m-d");
// $hari_ini = "2020-02-23";
$next_wo = date('Y-m-d', strtotime($hari_ini. ' + 14 days'));
$awal_hari = $next_wo." 00:00:00";
$ahir_hari = $next_wo." 23:59:59";

$sql_cek = "SELECT am.amm_code AS asset, am.amms_code AS mtc, am.amms_description, am.amms_next_wo, am.amms_type_cycle, am.amms_intv_cycle, b.amm_location, ad.item_code, ad.item_name, ad.unit, ad.qty
	FROM assets_master_maintenance am
	JOIN assets_master_main b ON (b.amm_code = am.amm_code)
	LEFT JOIN assets_master_maintenance_detail ad ON(ad.amm_code = am.amm_code AND ad.amms_code = am.amms_code)
	WHERE b.amm_status IN ('Active') AND am.amms_next_wo >= '{$awal_hari}' AND am.amms_next_wo <= '{$ahir_hari}'
	ORDER BY am.amm_code, am.amms_code";
$query = pg_query($conn, $sql_cek);
while($r = pg_fetch_array($query)) {
	$arr_asset["$r[asset]"]["$r[mtc]"] = $r[amms_description]."@@".$r[amms_next_wo]."@@".$r[amm_location]."@@".$r[amms_intv_cycle]."@@".$r[amms_type_cycle];
	$arr_item["$r[asset]"]["$r[mtc]"]["$r[item_code]"] = $r[item_name]."@@".$r[unit]."@@".$r[qty];
}

$formatwokode = "WO-".date("y")."-".date("m");
$thnbln = date("Y-m");
$sqlwo = "SELECT max(wo_code) as wo_code_max from tbl_wo where to_char(wo_date, 'YYYY-MM') = '{$thnbln}'";
foreach ($arr_asset as $asset => $a_mtc) {
	foreach ($a_mtc as $mtc => $value) {
		$v = explode("@@", $value);
		$query = pg_query($conn, $sqlwo);
		$mx = pg_fetch_array($query);
		if($mx[wo_code_max] == ''){
			$mx[wo_code_max] = 0;
		} else {
			$mx[wo_code_max] = substr($mx[wo_code_max],-4);
		}
		$urutbaru = $mx[wo_code_max]+1;
		$wo_code = $formatwokode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);
		$sql_i = "INSERT INTO tbl_wo (wo_code, wo_date, wo_source, wo_status, wo_urgency, wo_due, wo_desc, wo_type, wo_type_code, wo_asset, wo_location, wo_maintenance, user_create, date_create) VALUES ('{$wo_code}', '{$hari_ini_full}', 'SM', 'O', 'Normal', '{$v[1]}', '{$v[0]}', 'Preventive', '01', '{$asset}', '{$v[2]}', '{$mtc}', '{$user}', '{$hari_ini_full}');";
		foreach ($arr_item[$asset][$mtc] as $item_code => $value2) {
			$i = explode("@@", $value2);
			$sql_i .= "INSERT INTO tbl_wo_detail (wo_code, item_code, item_name, unit, qty) VALUES ('{$wo_code}', '{$item_code}', '{$i[0]}', '{$i[1]}', '{$i[2]}'); ";
		}
		$res = pg_query($conn, $sql_i);
		if($res) {
			$intv_cycle = $v[3]." ".$v[4];
			$sql_u = "UPDATE assets_master_maintenance SET amms_next_wo = (amms_next_wo + INTERVAL '{$intv_cycle}'), amms_edit_user = 'AF' WHERE amm_code = '{$asset}' AND amms_code = '{$mtc}';";
			pg_query($conn, $sql_u);
		}
	}
}

?>