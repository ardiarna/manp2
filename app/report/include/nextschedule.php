<?php
require_once '../../../libs/init.php'; 

$user = $_SESSION["user"];
//$pageName = "include/".basename($_SERVER['PHP_SELF']);
//echo $pageName;
$mode = $_GET['mode'];
switch ($mode) {
	case "view";
		view();
	break;
	case "load";
		load();
	break;
	case "excel";
		excel();
	break;
}

function view(){
	global $conn;

	$tglfrom = date('Y-m-d')." 00:00:00";
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo,
			string_agg(c.item_name||' '||c.qty||' '||c.unit, ', ') as spareparts
		from assets_master_maintenance a
		join assets_master_main b on(a.amm_code = b.amm_code)
		left join assets_master_maintenance_detail c on(a.amm_code = c.amm_code and a.amms_code = c.amms_code)
		where a.amms_next_wo >= '{$tglfrom}'
		group by a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo
		order by a.amms_next_wo, a.amm_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_description']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_next_wo']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_part']."]]></cell>");
		print("<cell><![CDATA[".$row['spareparts']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function load(){
	global $conn;
	$tglfrom = $_GET['from_date']." 00:00:00";
	$tglto = $_GET['to_date']." 23:59:59";
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo,
			string_agg(c.item_name||' '||c.qty||' '||c.unit, ', ') as spareparts
		from assets_master_maintenance a
		join assets_master_main b on(a.amm_code = b.amm_code)
		left join assets_master_maintenance_detail c on(a.amm_code = c.amm_code and a.amms_code = c.amms_code)
		where a.amms_next_wo >= '{$tglfrom}' and a.amms_next_wo <= '{$tglto}'
		group by a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo
		order by a.amms_next_wo, a.amm_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_description']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_next_wo']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_part']."]]></cell>");
		print("<cell><![CDATA[".$row['spareparts']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function excel(){
	global $conn;

	$tglfrom = date('Y-m-d')." 00:00:00";
	
	header("Content-type: application/x-msexcel"); 
	header('Content-Disposition: attachment; filename="Mtc_Next_schedule.xls"');

	$out = '<style>td,th{padding-left:3px;padding-right:3px;}table.adaborder{border-collapse:collapse;}table.adaborder th,table.adaborder td{border:1px solid black;} .str{ mso-number-format:\@; } </style>';
	$out .= '<div style="text-align:center;font-size:20px;font-weight:bold;">MAINTENANCE NEXT SCHEDULE</div>';
	$out .= '<div style="overflow-x:auto;"><table class="adaborder" id="tbl01" border="1">';
	$out .= '<tr>
				<th>NO</th>
				<th>ASSET#</th>
				<th>ASSET NAME</th>
				<th>INSTRUCTION MAINTENANCE</th>
				<th>NEXT SCHEDULE DATE</th>
				<th>PART NAME</th>
				<th>SPAREPARTS LIST</th>
			';		
	$out .= '</tr>';

	$i=1;
	$sql = "SELECT a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo,
			string_agg(c.item_name||' '||c.qty||' '||c.unit, ', ') as spareparts
		from assets_master_maintenance a
		join assets_master_main b on(a.amm_code = b.amm_code)
		left join assets_master_maintenance_detail c on(a.amm_code = c.amm_code and a.amms_code = c.amms_code)
		where a.amms_next_wo >= '{$tglfrom}'
		group by a.amm_code, b.amm_desc, a.amms_description, a.amms_part, a.amms_next_wo
		order by a.amms_next_wo, a.amm_code";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$out .= "<tr>";
		$out .= "<td>".$i."</td>";
		$out .= "<td>".$row['amm_code']."</td>";
		$out .= "<td>".$row['amm_desc']."</td>";
		$out .= "<td>".$row['amms_description']."</td>";
		$out .= "<td>".$row['amms_next_wo']."</td>";
		$out .= "<td>".$row['amms_part']."</td>";
		$out .= "<td>".$row['spareparts']."</td>";
		$out .= "</tr>";
		$i++;
	}
	$out .= '</table></div>';
	echo $out;
}

?>