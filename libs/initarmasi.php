<?php

require_once 'init.php';
require_once 'konfigurasiarmasi.php'; 

$mode=$_GET['mode'];
switch ($mode) {
	case "dtsparepart";
		dtsparepart();
	break;
	case "dtsparepartlokal";
		dtsparepartlokal();
	break;
	case "caristok";
		caristok();
	break;	
}

function dtsparepart(){
	global $armasi_conn, $app_plan_id;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	
	$txt_cari = strtoupper($_REQUEST['prm']);
	$tahun = date("Y");

	$sql = "SELECT a.item_kode, a.item_nama, a.satuan, COALESCE(b.stok, 0) AS stok
		FROM item a
		LEFT JOIN (
			SELECT item_kode, d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12 AS stok
			FROM tbl_stock_bulanan 
			WHERE tahun = {$tahun} AND plan_kode = {$app_plan_id}
		) AS b ON(a.item_kode = b.item_kode)
		WHERE upper(a.item_kode) LIKE '%{$txt_cari}%' OR upper(a.item_nama) LIKE '%{$txt_cari}%'
		AND a.item_kode NOT LIKE '%777%'
		ORDER BY stok desc, a.item_nama";
	$query=pg_query($armasi_conn, $sql);
	while($row=pg_fetch_array($query)){
		$sqlinactive = "SELECT inactive from item where item_kode = '".$row['item_kode']."'";
		$queryinactive = pg_query($armasi_conn, $sqlinactive);
		$rinactive = pg_fetch_array($queryinactive);
		if($rinactive['inactive'] == 't' || $rinactive['inactive'] == 'true') {
			if($row['stok'] > 0) {
				echo ("<row id='".$row['item_kode']."'>");
				print("<cell><![CDATA[".$row['item_kode']."]]></cell>");
				print("<cell><![CDATA[".$row['item_nama']."]]></cell>");
				print("<cell><![CDATA[".$row['satuan']."]]></cell>");
				print("<cell><![CDATA[".$row['stok']."]]></cell>");
				print("</row>");	
			}	
		} else {
			echo ("<row id='".$row['item_kode']."'>");
			print("<cell><![CDATA[".$row['item_kode']."]]></cell>");
			print("<cell><![CDATA[".$row['item_nama']."]]></cell>");
			print("<cell><![CDATA[".$row['satuan']."]]></cell>");
			print("<cell><![CDATA[".$row['stok']."]]></cell>");
			print("</row>");
		}	
	}
	echo '</rows>';
}

function dtsparepartlokal(){
	global $armasi_conn, $conn, $app_plan_id;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	
	$kd = $_REQUEST['kd'];
	$tahun = date("Y");
		
	$sql = "SELECT * FROM assets_master_sparepart WHERE amsp_code = '{$kd}' AND amsp_sparepart_code NOT LIKE '%777%'";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		// $sqlstok = "SELECT d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12 AS stok
		// 	FROM tbl_stock_bulanan 
		// 	WHERE tahun = {$tahun} AND plan_kode = {$app_plan_id} AND item_kode = '".$row['amsp_sparepart_code']."'";
		// $querystok = pg_query($armasi_conn, $sqlstok);
		// $rstok = pg_fetch_array($querystok);
		// $stok = $rstok['stok'] ? $rstok['stok'] : 0;
		$stok = 0;
		// $sqlinactive = "SELECT inactive from item where item_kode = '".$row['amsp_sparepart_code']."'";
		// $queryinactive = pg_query($armasi_conn, $sqlinactive);
		// $rinactive = pg_fetch_array($queryinactive);
		// if($rinactive['inactive'] == 't' || $rinactive['inactive'] == 'true') {
		// 	if($stok > 0) {
		// 		echo ("<row id='".$row['amsp_sparepart_code']."'>");
		// 		print("<cell><![CDATA[".$row['amsp_sparepart_code']."]]></cell>");
		// 		print("<cell><![CDATA[".$row['amsp_sparepart_desc']."]]></cell>");
		// 		print("<cell><![CDATA[".$row['amsp_unit']."]]></cell>");
		// 		print("<cell><![CDATA[".$stok."]]></cell>");
		// 		print("</row>");	
		// 	}	
		// } else {
			echo ("<row id='".$row['amsp_sparepart_code']."'>");
			print("<cell><![CDATA[".$row['amsp_sparepart_code']."]]></cell>");
			print("<cell><![CDATA[".$row['amsp_sparepart_desc']."]]></cell>");
			print("<cell><![CDATA[".$row['amsp_unit']."]]></cell>");
			print("<cell><![CDATA[".$stok."]]></cell>");
			print("</row>");
		// }
	}
	echo '</rows>';
}	

function caristok(){
	global $armasi_conn, $app_plan_id;
	
	$kode = $_REQUEST['kode'];
	$tahun = date("Y");

	$sql = "SELECT d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12 AS stok
		FROM tbl_stock_bulanan 
		WHERE tahun = {$tahun} AND plan_kode = {$app_plan_id} AND item_kode = '{$kode}'";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	$stok = $r['stok'] ? $r['stok'] : 0; 
	echo $stok;
}

?>
