<?php
require_once '../../../libs/init.php';
require_once '../../../libs/initarmasi.php'; 

$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "export";
		export();
	break;
	case "load";
		load();
	break;
	case "complete";
		complete();
	break;
}

function view() {
	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$wo_status = $_GET['wostatus'];
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	if($wo_status != 'ALL') {
		$whsatu = "AND d.wo_status = '{$wo_status}' ";
	}

	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT d.*, a.wr_approve_by
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		WHERE d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' $whsatu ORDER BY d.wo_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)) {
		$wo_status = $arr_status[$row['wo_status']];
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$wo_status."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_duration']." ".$row['wo_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_real_duration']." ".$row['wo_real_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_real_scheduled_start']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_real_scheduled_end']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_complete_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_complete_date']."]]></cell>");
		
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function export() {
	header("Content-type: application/x-msexcel"); 
	header('Content-Disposition: attachment; filename="Completed Work Order.xls"');

	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$wo_status = $_GET['wostatus'];
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	
	$tgljudul = $_GET['from_date'] == $_GET['to_date'] ? $_GET['from_date'] : $_GET['from_date'].' s/d '.$_GET['to_date'];
	
	$out = '<style>td,th{padding-left:3px;padding-right:3px;}table.adaborder{border-collapse:collapse;}table.adaborder th,table.adaborder td{border:1px solid black;} .str{ mso-number-format:\@; } </style>';
	$out .= '
			  <div style="text-align:center;font-size:20px;font-weight:bold;">COMPLETE WORK ORDER</div>
			  <div style="text-align:center;font-size:14px;font-weight:bold;">TGL : '.$tgljudul.'</div>
			  <div style="text-align:center;font-size:14px;font-weight:bold;">STATUS : '.$arr_status[$wo_status].'</div><br>';


	$out .= '<div style="overflow-x:auto;"><table class="adaborder" id="tbl01" border="1">';
	$out .= '<tr>
				<th>NO</th>
				<th>WO#</th>
				<th>DATE</th>
				<th>STATUS</th>
				<th>DESCRIPTION</th>
				<th>DUE DATE</th>
				<th>DURATION EST</th>
				<th>DURATION REAL</th>
				<th>SCHEDULEDLED EST</th>
				<th>START DATE REAL</th>
				<th>END DATE REAL</th>
				<th>APPROVE BY</th>
				<th>COMPLETE BY</th>
				<th>COMPLETE DATE</th>
			';		
	$out .= '</tr>';


	if($wo_status != 'ALL') {
		$whsatu = "AND d.wo_status = '{$wo_status}' ";
	}

	$i=1;
	$sql = "SELECT d.*, a.wr_approve_by
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		WHERE d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' $whsatu ORDER BY d.wo_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)) {
		$wo_status = $arr_status[$row['wo_status']];
		$out .= "<tr>";
		$out .= "<td>".$i."</td>";
		$out .= "<td>".$row['wo_code']."</td>";
		$out .= "<td>".$row['wo_date']."</td>";
		$out .= "<td>".$wo_status."</td>";
		$out .= "<td>".$row['wo_desc']."</td>";
		$out .= "<td>".$row['wo_due']."</td>";
		$out .= "<td>".$row['wo_duration']." ".$row['wo_unit_duration']."</td>";
		$out .= "<td>".$row['wo_real_duration']." ".$row['wo_real_unit_duration']."</td>";
		$out .= "<td>".$row['wo_scheduled']."</td>";
		$out .= "<td>".$row['wo_real_scheduled_start']."</td>";
		$out .= "<td>".$row['wo_real_scheduled_end']."</td>";
		$out .= "<td>".$row['wr_approve_by']."</td>";
		$out .= "<td>".$row['wo_complete_by']."</td>";
		$out .= "<td>".$row['wo_complete_date']."</td>";
		$out .= "</tr>";
		$i++;
	}
	$out .= '</table>';
	echo $out;
}

function load() {	
	global $conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$kd = $_GET['kd'];
	$sql = "SELECT d.*, a.wr_approve_by, b.amm_desc AS wo_assetname, f.sl_desc, g.amms_description, g.amms_intv_cycle, g.amms_type_cycle
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN sett_location f ON (d.wo_location = f.sl_code)
		LEFT JOIN assets_master_maintenance g ON (d.wo_asset = g.amm_code AND d.wo_maintenance = g.amms_code)
		WHERE d.wo_code = '{$kd}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<wono>".$row['wo_code']."</wono>");
	print("<wodate>".$row['wo_date']."</wodate>");
	print("<note>".$row['wo_desc']."</note>");
	print("<assetname>".$row['wo_assetname']."</assetname>");
	print("<assetcode>".$row['wo_asset']."</assetcode>");
	print("<kodelocation>".$row['wo_location']."</kodelocation>");
	print("<namelocation>".$row['sl_desc']."</namelocation>");
	print("<kodemaintenance>".$row['wo_maintenance']."</kodemaintenance>");
	print("<namemaintenance>".$row['amms_description']."</namemaintenance>");
	print("<duration>".$row['wo_duration']."</duration>");
	print("<durationsat>".$row['wo_unit_duration']."</durationsat>");
	print("<schdate>".$row['wo_scheduled']."</schdate>");
	print("<realduration>".$row['wo_real_duration']."</realduration>");
	print("<realdurationsat>".$row['wo_real_unit_duration']."</realdurationsat>");
	print("<realschstart>".$row['wo_real_scheduled_start']."</realschstart>");
	print("<realschend>".$row['wo_real_scheduled_end']."</realschend>");
	print("<cycleintv>".$row['amms_intv_cycle']."</cycleintv>");
	print("<cycletype>".$row['amms_type_cycle']."</cycletype>");
	
	print('</data>');
}

function complete() {	
	global $conn, $armasi_conn, $app_plan_id;

	$wo_code = $_POST['wono'];
	$wo_real_duration = $_POST['realduration'];
	$wo_real_unit_duration = $_POST['realdurationsat'];
	$wo_real_scheduled_start = $_POST['realschstart'];
	$wo_real_scheduled_end = $_POST['realschend'];
	$wo_asset = $_POST['assetcode'];
	$wo_maintenance = $_POST['kodemaintenance'];
	$cycleintv = $_POST['cycleintv'];
	$cycletype = $_POST['cycletype'];
	$wo_note = $_POST['notewo'];
	$wo_isdowntime = $_POST['isdowntime'];
	$wo_complete_by = $_SESSION["user"];
	$wo_complete_date = date("Y-m-d");
	$sql_u = "UPDATE tbl_wo SET wo_status = 'C', wo_real_duration = '{$wo_real_duration}', wo_real_unit_duration = '{$wo_real_unit_duration}', wo_real_scheduled_start = '{$wo_real_scheduled_start}', wo_real_scheduled_end = '{$wo_real_scheduled_end}', wo_complete_by = '{$wo_complete_by}', wo_complete_date = '{$wo_complete_date}', wo_note = '{$wo_note}', wo_isdowntime = '{$wo_isdowntime}' WHERE wo_code = '{$wo_code}'; ";
	
	$sql = "SELECT item_code FROM tbl_wo_detail WHERE wo_code = '{$wo_code}'";
	$query=pg_query($conn, $sql);
	$thn = date("Y");
	$bln = date("n");
	while($r=pg_fetch_array($query)) {
		$armasi_sql = "SELECT item_kode, (d_bln_0-k_bln_0+d_bln_1-k_bln_1+d_bln_2-k_bln_2+d_bln_3-k_bln_3+d_bln_4-k_bln_4+d_bln_5-k_bln_5+d_bln_6-k_bln_6+d_bln_7-k_bln_7+d_bln_8-k_bln_8+d_bln_9-k_bln_9+d_bln_10-k_bln_10+d_bln_11-k_bln_11+d_bln_12-k_bln_12) AS stok, (sd_bln_0-sk_bln_0+sd_bln_1-sk_bln_1+sd_bln_2-sk_bln_2+sd_bln_3-sk_bln_3+sd_bln_4-sk_bln_4+sd_bln_5-sk_bln_5+sd_bln_6-sk_bln_6+sd_bln_7-sk_bln_7+sd_bln_8-sk_bln_8+sd_bln_9-sk_bln_9+sd_bln_10-sk_bln_10+sd_bln_11-sk_bln_11+sd_bln_12-sk_bln_12) AS harga, k_bln_$bln AS stok_keluar, sk_bln_$bln AS harga_keluar
		FROM tbl_stock_bulanan 
		WHERE tahun = {$thn} AND plan_kode = {$app_plan_id} and item_kode ='{$r[item_code]}'";
		$armasi_query = pg_query($armasi_conn, $armasi_sql);
		$ar = pg_fetch_array($armasi_query);
		if($ar[item_kode]) {
			$netcost = $ar[stok] == 0 ? 0 : $ar[harga]/$ar[stok];
			if($netcost == 0) {
				$netcost = $ar[stok_keluar] == 0 ? 0 : $ar[harga_keluar]/$ar[stok_keluar];
			}
		} else {
			$netcost = 0;
		}
		if($netcost == 0) {
			$sqlH = "SELECT * from
			(select tbl_penerimaan_barang.bpb_kode,tbl_penerimaan_barang.tanggal,diterima as qty,unitprice*nilai_kurs as netcost,total as total,company,'' as dept 
			from tbl_penerimaan_barang 
			inner join item_penerimaan_barang on tbl_penerimaan_barang.bpb_kode=item_penerimaan_barang.bpb_kode 
			left outer join porders on porders.porder_kode=tbl_penerimaan_barang.porder_kode 
			left outer join supplier on porders.supplier_kode=supplier.supplier_kode 
			where item_kode='$r[item_code]' and tbl_penerimaan_barang.bpb_kode like('%/$app_plan_id/%')
			union all 
			select tbl_penerimaan_retur.retur_kode,tbl_penerimaan_retur.tanggal,qty,harga,amount,'' as company,'' as dept 
			from tbl_penerimaan_retur 
			inner join item_penerimaan_retur on tbl_penerimaan_retur.retur_kode=item_penerimaan_retur.retur_kode 
			where item_kode='$r[item_code]' and tbl_penerimaan_retur.retur_kode like('%/$app_plan_id/%') 
			union all 
			select tbl_opname.kode_opname,tbl_opname.tanggal,qty,harga,amount,'' as company,'' as dept 
			from tbl_opname 
			inner join item_opname on tbl_opname.kode_opname=item_opname.kode_opname 
			where item_kode='$r[item_code]' and item_opname.jenis='PENERIMAAN' and tbl_opname.kode_opname like('%/$app_plan_id/%') 
			union all 
			select tbl_opname.kode_opname,tbl_opname.tanggal,qty*-1,harga,amount*-1,'' as company,'' as dept 
			from tbl_opname 
			inner join item_opname on tbl_opname.kode_opname=item_opname.kode_opname 
			where item_kode='$r[item_code]' and item_opname.jenis='PENGELUARAN' and tbl_opname.kode_opname like('%/$app_plan_id/%')
			union all 
			select tbl_bon.bon_kode,tbl_bon.tanggal,qty*-1,harga,amount*-1,'' as company,departemen_nama as dept 
			from tbl_bon 
			inner join item_bon on tbl_bon.bon_kode=item_bon.bon_kode 
			left outer join departemen on departemen.departemen_kode=tbl_bon.departemen_kode 
			where item_kode='$r[item_code]' and tbl_bon.bon_kode like('%/$app_plan_id/%') 
			union all 
			select tbl_bon_material.bon_material_kode,tbl_bon_material.tanggal,qty*-1,harga,amount*-1,'' as company,departemen_nama as dept 
			from tbl_bon_material 
			inner join item_bon1 on tbl_bon_material.bon_material_kode=item_bon1.bon_material_kode 
			left outer join departemen on departemen.departemen_kode=tbl_bon_material.departemen_kode 
			where item_kode='$r[item_code]' and tbl_bon_material.bon_material_kode like('%/$app_plan_id/%') 
			union all 
			select tbl_return.return_kode,tbl_return.tanggal,item_return.volume*-1,item_return.unitprice*item_return.nilai_kurs,item_return.amount*-1,'' as company,'' as dept 
			from tbl_return 
			inner join item_return on tbl_return.return_kode=item_return.return_kode 
			where item_kode='$r[item_code]' and tbl_return.return_kode like('%/$app_plan_id/%')
			order by tanggal desc) as a
			where netcost > 0
			limit 1";
			$armasi_query = pg_query($armasi_conn, $sqlH);
			$aH = pg_fetch_array($armasi_query);
			if($aH[netcost]) {
				$netcost = $aH[netcost];	
			}
		}
		$sql_u .= "UPDATE tbl_wo_detail SET netcost = {$netcost} WHERE wo_code = '{$wo_code}' AND item_code = '{$r[item_code]}'; ";
	}
	$res = pg_query($conn, $sql_u);
	if($res) {
		if($wo_asset && $wo_maintenance) {
			$intv_cycle = $cycleintv." ".$cycletype;
			$sql_u = "UPDATE assets_master_maintenance SET amms_next_wo = ('{$wo_complete_date}'::date + INTERVAL '{$intv_cycle}') WHERE amm_code = '{$wo_asset}' AND amms_code = '{$wo_maintenance}';";
			$res = pg_query($conn, $sql_u);
			if($res) {
				$ret = "OK";
			} else {
				$ret = pg_errormessage($conn);
			}	
		} else {
			$ret = "OK";
		}	
	} else {
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}


?>