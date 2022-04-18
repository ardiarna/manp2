<?php
require_once '../../../libs/init.php'; 

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
	

function export(){
	header("Content-type: application/x-msexcel"); 
	header('Content-Disposition: attachment; filename="Work Order.xls"');

	global $conn;
	

	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	$wo_status = $_GET['wostatus'];
	$arr_status = array('ALL' => 'ALL', "O" => "Open", "S" => "Scheduled", "C" => "Completed");
	$arr_pic = array('' => '', 'I' => 'Internal', 'E' => 'Eksternal');
	
	if($wo_status != 'ALL') {
		$whsatu = "AND d.wo_status = '{$wo_status}' ";
	}


	$tgljudul = $_GET['from_date'] == $_GET['to_date'] ? $_GET['from_date'] : $_GET['from_date'].' s/d '.$_GET['to_date'];


	$out = '<style>td,th{padding-left:3px;padding-right:3px;}table.adaborder{border-collapse:collapse;}table.adaborder th,table.adaborder td{border:1px solid black;} .str{ mso-number-format:\@; } </style>';
	$out .= '
			  <div style="text-align:center;font-size:20px;font-weight:bold;">WORK ORDER</div>
			  <div style="text-align:center;font-size:14px;font-weight:bold;">TGL : '.$tgljudul.'</div>
			  <div style="text-align:center;font-size:14px;font-weight:bold;">STATUS : '.$arr_status[$wo_status].'</div><br>';


	$out .= '<div style="overflow-x:auto;"><table class="adaborder" id="tbl01" border="1">';
	$out .= '<tr>
				<th>NO</th>
				<th>WO#</th>
				<th>WO DATE</th>
				<th>STATUS</th>
				<th>DESCRIPTION</th>
				<th>DUE DATE</th>
				<th>DURATION EST</th>
				<th>DURATION REAL</th>
				<th>SCHEDULEDLED EST</th>
				<th>START SCHEDULED REAL</th>
				<th>END SCHEDULED REAL</th>
				<th>WORK REQUEST</th>
				<th>WR DATE</th>
				<th>APPROVE BY</th>
				<th>COMPLETE BY</th>
				<th>COMPLETE DATE</th>
				<th>MR / MR-SPK</th>
				<th>PSP</th>
				<th>LOCATION</th>
				<th>ASSET</th>
				<th>MAINTENANCE WORK TYPE</th>
				<th>URGENCY</th>
				<th>PIC</th>
				<th>PIC 1</th>
				<th>PIC 2</th>
				<th>PIC 3</th>
			';		
	$out .= '</tr>';


	$i=1;
	$sql = "SELECT d.*, a.wr_approve_by, a.wr_date, coalesce(h.mrequest_kode,i.no_mr) AS mr_nya, b.amm_desc, b.amm_number, e.smt_description, f.sl_desc, k.psp_code
		FROM tbl_wo d
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN sett_maintenance_type e ON (d.wo_type = e.smt_work_type AND d.wo_type_code = e.smt_code)
		LEFT JOIN sett_location f ON (d.wo_location = f.sl_code)
		LEFT JOIN tbl_mrequest h ON (d.wo_code = h.wo_code)
		LEFT JOIN tbL_spkmr i ON (d.wo_code = i.wo_code)
		LEFT JOIN tbl_psp k ON(d.wo_code = k.wo_code)
		WHERE d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' $whsatu ORDER BY d.wo_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		$assetname = ($row['amm_number']) ? $row['amm_number']." - ".$row['amm_desc'] : $row['amm_desc'];
		$out .= "<tr>";
		$out .= "<td>".$i."</td>";
		$out .= "<td>".$row['wo_code']."</td>";
		$out .= "<td>".$row['wo_date']."</td>";
		$out .= "<td>".$wo_status."</td>";
		$out .= "<td>".$row['wo_desc']."</td>";
		$out .= "<td>".$row['wo_due']."</td>";
		$out .= "<td>".$row['wo_duration']." ".$row['wo_unit_duration']."</td>";
		$out .= "<td>".$row['wo_real_duration']." ".$row['wo_unit_duration']."</td>";
		$out .= "<td>".$row['wo_scheduled']."</td>";
		$out .= "<td>".$row['wo_real_scheduled_start']."</td>";
		$out .= "<td>".$row['wo_real_scheduled_end']."</td>";
		$out .= "<td>".$row['wr_code']."</td>";
		$out .= "<td>".$row['wr_date']."</td>";
		$out .= "<td>".$row['wr_approve_by']."</td>";
		$out .= "<td>".$row['wo_complete_by']."</td>";
		$out .= "<td>".$row['wo_complete_date']."</td>";
		$out .= "<td>".$row['mr_nya']."</td>";
		$out .= "<td>".$row['psp_code']."</td>";
		$out .= "<td>".$row['sl_desc']."</td>";
		$out .= "<td>".$assetname."</td>";
		$out .= "<td>".$row['wo_type']." - ".$row['smt_description']."</td>";
		$out .= "<td>".$row['wo_urgency']."</td>";
		$out .= "<td>".$arr_pic[$row['wo_pic_type']]."</td>";
		$out .= "<td>".$row['wo_pic1']."</td>";
		$out .= "<td>".$row['wo_pic2']."</td>";
		$out .= "<td>".$row['wo_pic3']."</td>";
		$out .= "</tr>";
		$i++;
	}
	$out .= '</table>';

	echo $out;
}


function view(){
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
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$wo_status."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_duration']." ".$row['wo_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_real_duration']." ".$row['wo_unit_duration']."]]></cell>");
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

function load(){	
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
	print("<realduration >".$row['wo_real_duration']."</realduration >");
	print("<realschstart>".$row['wo_real_scheduled_start']."</realschstart>");
	print("<realschend>".$row['wo_real_scheduled_end']."</realschend>");
	print("<cycleintv>".$row['amms_intv_cycle']."</cycleintv>");
	print("<cycletype>".$row['amms_type_cycle']."</cycletype>");
	
	print('</data>');
}

function complete(){	
	global $conn;
	$wo_code = $_POST['wono'];
	$wo_real_duration = $_POST['realduration'];
	$wo_real_scheduled_start = $_POST['realschstart'];
	$wo_real_scheduled_end = $_POST['realschend'];
	$wo_asset = $_POST['assetcode'];
	$wo_maintenance = $_POST['kodemaintenance'];
	$cycleintv = $_POST['cycleintv'];
	$cycletype = $_POST['cycletype'];
	$wo_complete_by = $_SESSION["user"];
	$wo_complete_date = date("Y-m-d");
	$sql_u = "UPDATE tbl_wo SET wo_status = 'C', wo_real_duration = '{$wo_real_duration}', wo_real_scheduled_start = '{$wo_real_scheduled_start}', wo_real_scheduled_end = '{$wo_real_scheduled_end}', wo_complete_by = '{$wo_complete_by}', wo_complete_date = '{$wo_complete_date}' WHERE wo_code = '{$wo_code}'";
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