<?php
require_once '../../../libs/init.php'; 

$mode = $_GET['mode'];
switch ($mode) {
	case "detail";
		detail();
	break;
	case "rekapasset";
		rekapasset();
	break;
	case "rekapmtc";
		rekapmtc();
	break;
}

function detail() {
	global $conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$sql = "SELECT *
		FROM (
			SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, w.wo_asset, a.amm_desc,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime,
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_type||' - '||m.smt_description AS mtc_type, w.wo_desc
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C'
			UNION ALL
			SELECT tanggal::text, d.amm_code, a.amm_desc, d.dt_value, d.dt_code, '' AS wo_date, 'Production' AS mtc_type, dt_desc
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
		) AS a
		WHERE a.tgl >= '{$tglfrom}' AND a.tgl <= '{$tglto}'
		ORDER BY a.tgl, a.wo_asset, a.wo_code";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['tgl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['downtime']." Min]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['mtc_type']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function rekapasset() {
	global $conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$sql = "SELECT a.tgl, a.wo_asset, a.amm_desc, sum(a.downtime) AS downtime
		FROM (
			SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, w.wo_asset, a.amm_desc,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime,
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_type||' - '||m.smt_description AS mtc_type, w.wo_desc
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C'
			UNION ALL
			SELECT tanggal::text, d.amm_code, a.amm_desc, d.dt_value, d.dt_code, '' AS wo_date, 'Production' AS mtc_type, dt_desc
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
		) AS a
		WHERE a.tgl >= '{$tglfrom}' AND a.tgl <= '{$tglto}'
		GROUP BY a.tgl, a.wo_asset, a.amm_desc
		ORDER BY a.tgl, a.wo_asset";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['tgl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['downtime']." Min]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function rekapmtc() {
	global $conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$sql = "SELECT a.tgl, a.wo_asset, a.amm_desc, a.mtc_type, sum(a.downtime) AS downtime
		FROM (
			SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, w.wo_asset, a.amm_desc,
			CASE WHEN w.wo_unit_duration = 'Hours' THEN (w.wo_real_duration*60) ELSE w.wo_real_duration END AS downtime,
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_type||' - '||m.smt_description AS mtc_type, w.wo_desc
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C'
			UNION ALL
			SELECT tanggal::text, d.amm_code, a.amm_desc, d.dt_value, d.dt_code, '' AS wo_date, 'Production' AS mtc_type, dt_desc
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
		) AS a
		WHERE a.tgl >= '{$tglfrom}' AND a.tgl <= '{$tglto}'
		GROUP BY a.tgl, a.wo_asset, a.amm_desc, a.mtc_type
		ORDER BY a.tgl, a.wo_asset, a.mtc_type";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['tgl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['mtc_type']."]]></cell>");
		print("<cell><![CDATA[".$row['downtime']." Min]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

?>