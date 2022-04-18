<?php

require_once '../init.php'; 
include("mpdf.php");
$mpdf = new mPDF('','A4');
$kd = $_GET['kd'];
$stat = $_GET['stat'];
$p = explode("@@", $_GET['period']);

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
$sql_dtl = "SELECT wo_code,wo_date,to_char(wo_date, 'dd-MM-YYYY') as tglwo,wo_status,wo_urgency,wo_type,wo_desc,wo_real_duration,wo_pic1
from man.tbl_wo where wo_status in ('C') and wo_asset='{$asset_code}' order by wo_date";

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
//<td style="vertical-align:top;font-size:x-small;" align="left" colspan="2">'.date("Y.m.d h:i").'</td>
    $list_item .= '<tr>
	<td style="vertical-align:top;font-size:medium;" align="right" colspan="7"><b>Total Cost :</b></td>
	<td style="vertical-align:top;font-size:medium;" align="right"><b>'.number_format($totalCost,2).'</b></td>
	</tr>';
$list_item .= '</table>';


$isi = '<style>td,th{padding-left:3px;padding-right:3px;}table{border-collapse:collapse;width:100%;}</style>
<table border="0">
<tr>
    <td style="text-align:right;" colspan="6">No.F.903.ME.34</td>
</tr>
<tr>
    <td style="text-align:center;font-weight:bold;font-size:x-large;padding-bottom:20px;" colspan="6">ASSET CARD</td>
</tr>
<tr>
    <td style="width:115px;">Asset No.</td>
    <td style="width:1px">:</td>
    <td>'.$r[amm_code].' - '.$r[amm_year].'</td>
    <td style="width:110px">Status</td>
    <td style="width:1px">:</td>
    <td>'.$r[amm_status].'</td>
</tr>
<tr>
    <td>Asset Name</td>
    <td>:</td>
    <td>'.$r[amm_desc].'</td>
    <td>Location</td>
    <td>:</td>
    <td>'.$r[sl_desc].'</td>
</tr>
<tr>
    <td>Category</td>
    <td>:</td>
    <td>'.$r[sac_desc].'</td>
    <td>Sub Location</td>
    <td>:</td>
    <td>'.$r[ssl_desc].'</td>
</tr>
<tr>
    <td>Group</td>
    <td>:</td>
    <td>'.$r[sag_desc].'</td>
    <td>Operator</td>
    <td>:</td>
    <td>'.$r[amm_operator].'</td>
</tr>
<tr>
    <td style="vertical-align:top;">Spesification</td>
    <td style="vertical-align:top;">:</td>
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
<tr>  
    <td style="padding-bottom:10px;" colspan="6">
        <table border="0">
            <tr>
                <td style="vertical-align:top;">'.$list_item.'</td>
            </tr>
        </table>
    </td>
</tr>

</table>';

$footer = "<table name='footer' width='100%' border='0'>
           <tr>
			 <td style='font-size: 10px; padding-bottom: 10px;' align=\"left\">{DATE j-m-Y H:i}</td>
			 <td style='font-size: 10px; padding-bottom: 10px;' align=\"right\">{PAGENO}</td>
           </tr>
         </table>";


$mpdf->SetFooter($footer);
//$mpdf->AddPage('L');
$mpdf->AddPage('L','','','','',10,10,10,10,10,10);
$mpdf->WriteHTML($isi);

}

$mpdf->Output($kd.'.pdf', 'I');

?>