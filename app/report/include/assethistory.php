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
	case "viewwo";
		viewwo();
	break;
	case "cmblocation";
		cmblocation();
	break;
	case "cmbsublocation";
		cmbsublocation();
	break;
	case "cmbgroup";
		cmbgroup();
	break;
	case "excel";
		excel();
	break;
}

function view(){
	global $conn;
	header("Content-type: text/xml");
	//encoding may be different in your case
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$location = $_GET['location'];
	$sublocation = $_GET['sublocation'];
	$group = $_GET['group'];

	if($location <> '') {
		$whsatu .= " AND a.amm_location = '{$location}'";
	}

	if($sublocation <> '') {
		$whsatu .= " AND a.amm_sub_location = '{$sublocation}'";
	}

	if($group <> '') {
		$whsatu .= " AND a.amm_group = '{$group}'";
	}
	
	$sql = "SELECT a.*, b.sac_desc,  d.sl_desc, e.ssl_desc, f.sag_desc
		FROM assets_master_main a
		LEFT JOIN sett_assets_category b on (a.amm_category = b.sac_code)
		LEFT JOIN sett_location d on (a.amm_location = d.sl_code)
		LEFT JOIN sett_sub_location e on (a.amm_location = e.ssl_location_code and a.amm_sub_location = e.ssl_code)
		LEFT JOIN sett_assets_group f on (a.amm_group = f.sag_code)
		WHERE 1=1 $whsatu 
		and amm_code in 
		(select wo_asset from (
select wo_asset,amm_desc,count(*) as hit from man.tbl_wo 
inner join man.assets_master_main on wo_asset=amm_code
group by wo_asset,amm_desc
) as a where hit>1)
	
		ORDER BY a.amm_code";
	$query=pg_query($conn,$sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
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

function viewwo(){
	global $conn;
	$status = $_GET['status'];
	$assetcode = $_GET['assetcode'];
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed");
	$arr_status_approve = array("A" => "Approve", "R" => "Reject", "W" => "Waiting Approval");
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	if($status == 'C') {
		$tglfrom = $_GET['from_date']." 00:00:00";
		$tglto = $_GET['to_date']." 23:59:59";
		$whsatu = " AND d.wo_date >= '{$tglfrom}' AND d.wo_date <= '{$tglto}' ";
	}
	$sql = "SELECT d.wo_code, d.wo_date, d.wr_code, a.wr_date, a.wr_request_by, c.se_name AS wr_request_byname, d.wo_urgency, d.wo_type, d.wo_type_code, a.wr_due, d.wo_desc, d.wo_asset, d.wo_duration, d.wo_unit_duration, d.wo_scheduled, d.wo_status, a.wr_approve_by, d.wo_asset||' - '||b.amm_desc AS wo_asset_lbl, d.wo_due, d.wo_pic_type, d.wo_source
		FROM tbl_wo d
		JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
		LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
		LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code)
		WHERE d.wo_status = '{$status}' AND d.wo_asset = '{$assetcode}' $whsatu ORDER BY d.wo_code DESC";
	$query=pg_query($conn, $sql);
	$i=1;
	while($row=pg_fetch_array($query)){
		$wo_status = $arr_status[$row['wo_status']];
		$wr_approve_status = $arr_status_approve[$row['wr_approve_status']];
		$wo_source = $arr_source[$row['wo_source']];
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_code']."]]></cell>");
		print("<cell><![CDATA[".$wo_source."]]></cell>");
		print("<cell><![CDATA[".$wo_status."]]></cell>");
		print("<cell><![CDATA[".$row['wo_urgency']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset_lbl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_due']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_duration']." ".$row['wo_unit_duration']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_request_byname']."]]></cell>");
		print("<cell><![CDATA[".$row['wr_approve_by']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_pic_type']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
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

function cmbgroup(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<complete>';

	$sql = "SELECT * FROM sett_assets_group ORDER BY sag_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo '<option value="'.$row['sag_code'].'">'.$row['sag_code'].' - '.$row['sag_desc'].'</option>';
	}
	echo '<option value=""></option>';
	echo '</complete>';
}

function excel(){
	global $conn;
	
	$kd = $_GET['kd'];
	$stat = $_GET['stat'];
	$p = explode("@@", $_GET['period']);

	header("Content-type: application/x-msexcel"); 
	header('Content-Disposition: attachment; filename="Asset_Card_'.$p[0].'_'.$p[1].'.xls"');
	
	
	if($stat == 'M') {
	    $where = " AND date_part('month',wo_date) = ".$p[0]." AND date_part('year',wo_date) = ".$p[1]." ";  
	} else if($stat == 'P') {
	    $where = " AND wo_date >= '".$p[0]." 00:00:00' AND wo_date <= '".$p[1]." 23:59:59' ";  
	}

	if($kd == 'all') {
	    $sqlawal = "SELECT wo_asset from (
	        select wo_asset,amm_desc,count(*) as hit from man.tbl_wo 
	        inner join man.assets_master_main on wo_asset=amm_code 
	        where wo_status in ('C') $where
	        group by wo_asset,amm_desc
	        ) as a where hit>=1  order by wo_asset";
	    $qryawal=pg_query($conn,$sqlawal);
	    while($rw=pg_fetch_array($qryawal)) {
	        $arr_asset[] = $rw[wo_asset]; 
	    }    
	} else {
	    $arr_asset = array($kd);
	}

	foreach ($arr_asset as $key => $asset_code) {
		$sql = "SELECT
		amm_code,amm_desc,amm_category,sac_desc,sag_code,sag_desc,sl_code,sl_desc,ssl_code,ssl_desc,amm_status,amm_operator
		,amm_year,amm_model,amm_serial_no,amm_type,amm_custom_field11
		from man.assets_master_main
		left outer join man.sett_assets_category on amm_category=sac_code
		left outer join man.sett_assets_group on amm_group=sag_code
		left outer join man.sett_location on amm_location=sl_code
		left outer join man.sett_sub_location on amm_sub_location=ssl_code
		left outer join man.assets_master_spesification on ams_code=amm_code
		where amm_code = '{$asset_code}'";
		$query = pg_query($conn, $sql);
		$r = pg_fetch_array($query);
		$no=1;
		$totalCost=0;
		$sql_dtl = "SELECT d.wo_code,d.wo_date,to_char(wo_date, 'dd-MM-YYYY') as tglwo,d.wo_status,d.wo_urgency,d.wo_type,d.wo_desc,d.wo_real_duration,d.wo_pic1,netcost
			from man.tbl_wo d 
			LEFT JOIN (
				SELECT wd.wo_code, sum(wd.netcost*wd.qty) AS netcost
				FROM man.tbl_wo_detail wd
				GROUP BY wd.wo_code
			) AS e ON (d.wo_code = e.wo_code)
			where wo_asset='{$asset_code}' 
			and wo_status in ('C') $where
			order by wo_date;";
		$query_dtl = pg_query($conn, $sql_dtl);
		$list_item = '<table border="1" cellpadding="4" cellspacing="2">
			<tr>
				<td style="vertical-align:top;font-size:medium;" align="center"><b>No.</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>Date</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>WO No.</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>Type</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>Description</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>Time(Mnt)</b></td>
				<td style="vertical-align:top;font-size:medium;"><b>PIC</b></td>
				<td style="vertical-align:top;font-size:medium;" align="right"><b>Cost</b></td>
			</tr>';
		while($row=pg_fetch_array($query_dtl)) { 
		    $list_item .= '<tr>
				<td style="vertical-align:top;font-size:medium;" align="center">'.$no.'</td>
				<td style="vertical-align:top;font-size:medium;">'.$row[tglwo].'</td>
				<td style="vertical-align:top;font-size:medium;" align="center">'.$row[wo_code].'</td>
				<td style="vertical-align:top;font-size:medium;">'.$row[wo_type].'</td>
				<td style="vertical-align:top;font-size:medium;">'.$row[wo_desc].'</td>
				<td style="vertical-align:top;font-size:medium;" align="center">'.$row[wo_real_duration].'</td>
				<td style="vertical-align:top;font-size:medium;">'.$row[wo_pic1].'</td>
				<td style="vertical-align:top;font-size:medium;" align="right"><b>'.number_format($row[netcost],2).'</b></td>
			</tr>';
			$no++;
			$totalCost=$totalCost+$row[netcost];
		    $sql_wo_dtl = "SELECT *, (wd.qty*wd.netcost) AS qty_total FROM man.tbl_wo_detail wd WHERE wd.wo_code = '{$row[wo_code]}' ORDER BY wd.item_code";
		    $query_wo_dtl = pg_query($conn, $sql_wo_dtl);
		    while($rd = pg_fetch_array($query_wo_dtl)) {
		        $list_item .= '<tr>
			        <td style="vertical-align:top;font-size:small;" align="center"></td>
			        <td style="vertical-align:top;font-size:small;"></td>
			        <td style="vertical-align:top;font-size:small;">'.$rd[item_code].'</td>
			        <td style="vertical-align:top;font-size:small;" colspan="2">'.$rd[item_name].'</td>
			        <td style="vertical-align:top;font-size:small;" align="right">'.$rd[qty].' '.$rd[unit].'</td>
			        <td style="vertical-align:top;font-size:small;" align="right">'.number_format($rd[netcost],2).'</td>
			        <td style="vertical-align:top;font-size:small;" align="right">'.number_format($rd[qty_total],2).'</td>
		        </tr>';
		    }
		}
    	$list_item .= '<tr>
			<td style="vertical-align:top;font-size:medium;" align="right" colspan="7"><b>Total Cost :</b></td>
			<td style="vertical-align:top;font-size:medium;" align="right"><b>'.number_format($totalCost,2).'</b></td>
		</tr>';
		$list_item .= '</table>';


		$isi = '<style>td,th{padding-left:3px;padding-right:3px;}table{border-collapse:collapse;width:100%;}</style>
		<table border="0">
		<tr>
		    <td style="text-align:center;font-weight:bold;font-size:x-large;padding-bottom:20px;" colspan="6">ASSET CARD</td>
		</tr>
		<tr>
		    <td colspan="2">Asset No.</td>
		    <td colspan="2">: '.$r[amm_code].' - '.$r[amm_year].'</td>
		    <td>Status</td>
		    <td>: '.$r[amm_status].'</td>
		</tr>
		<tr>
		    <td colspan="2">Asset Name</td>
		    <td colspan="2">: '.$r[amm_desc].'</td>
		    <td>Location</td>
		    <td>: '.$r[sl_desc].'</td>
		</tr>
		<tr>
		    <td colspan="2">Category</td>
		    <td colspan="2">: '.$r[sac_desc].'</td>
		    <td>Sub Location</td>
		    <td>: '.$r[ssl_desc].'</td>
		</tr>
		<tr>
		    <td colspan="2">Group</td>
		    <td colspan="2">: '.$r[sag_desc].'</td>
		    <td>Operator</td>
		    <td>: '.$r[amm_operator].'</td>
		</tr>
		<tr>
		    <td colspan="2" style="vertical-align:top;">Spesification</td>
		    <td style="padding-bottom:10px;" colspan="4"> 
		        <table border="1" width=100%>
		            <tr>
		                <td width=30% style="vertical-align:top;font-size:small;">Model : '.$r[amm_model].'</td>
						<td width=25% style="vertical-align:top;font-size:small;">Serial No : '.$r[amm_serial_no].'</td>
						<td width=25% style="vertical-align:top;font-size:small;">Type : '.$r[amm_type].'</td>
						<td width=20% style="vertical-align:top;font-size:small;">Capacity : '.$r[amm_custom_field11].'</td>
		            </tr>
		        </table>
		    </td>
		</tr>
		<tr>
		<td style="vertical-align:top;" colspan="6">Maintenance History</td>
		</tr>
		</table>';

		$isi .= $list_item;

		echo $isi;
	}
}

?>