<?php

require_once '../init.php'; 
include("mpdf.php");

$kd = $_GET['kd'];
$sql = "SELECT d.wo_code, d.wo_date, d.wr_code, a.wr_date, a.wr_request_by, c.se_name AS wr_request_byname, d.wo_urgency, d.wo_type, d.wo_type_code, a.wr_due, d.wo_desc, d.wo_asset, d.wo_location, b.amm_desc AS wo_assetname, d.wo_duration, d.wo_unit_duration, d.wo_scheduled, d.wo_instruction, d.wo_pic_type, d.wo_pic1, d.wo_pic2, d.wo_pic3, d.wo_due, d.wo_real_duration, d.wo_real_scheduled_start, d.wo_real_scheduled_end, e.smt_description, f.sl_desc
    FROM tbl_wo d
    LEFT JOIN tbl_wr a ON (d.wr_code = a.wr_code)
    LEFT JOIN assets_master_main b ON (d.wo_asset = b.amm_code)
    LEFT JOIN sett_employee c ON (a.wr_request_by = c.se_code)
    LEFT JOIN sett_maintenance_type e ON (d.wo_type = e.smt_work_type AND d.wo_type_code = e.smt_code) 
    LEFT JOIN sett_location f ON (d.wo_location = f.sl_code)
    WHERE d.wo_code = '{$kd}'";
$query = pg_query($conn, $sql);
$r = pg_fetch_array($query);

$sql_dtl = "SELECT * FROM tbl_wo_detail WHERE wo_code = '{$kd}'";
$query_dtl = pg_query($conn, $sql_dtl);
$list_item = '<table>';
while($row=pg_fetch_array($query_dtl)) { 
    $list_item .= '<tr><td style="vertical-align:top;">- '.$row[item_code].'</td><td style="vertical-align:top;">'.$row[item_name].'</td><td style="vertical-align:top;">'.$row[qty].' '.$row[unit].'</td></tr>';
}
$list_item .= '</table>';

$isi = '<style>td,th{padding-left:3px;padding-right:3px;}table{border-collapse:collapse;width:100%;}</style>
<table>
<tr>
    <td style="text-align:center;font-weight:bold;font-size:x-large;padding-bottom:20px;" colspan="6">WORK ORDER</td>
</tr>
<tr>
    <td style="width:115px">WO. No.</td>
    <td style="width:1px">:</td>
    <td>'.$r[wo_code].'</td>
    <td style="width:110px">Urgency</td>
    <td style="width:1px">:</td>
    <td>'.$r[wo_urgency].'</td>
</tr>
<tr>
    <td>Date</td>
    <td>:</td>
    <td>'.$r[wo_date].'</td>
    <td>WO. Type</td>
    <td>:</td>
    <td>'.$r[wo_type].' - '.$r[smt_description].'</td>
</tr>
<tr>
    <td>Request By</td>
    <td>:</td>
    <td>'.$r[wr_request_byname].'</td>
    <td>Due Date</td>
    <td>:</td>
    <td>'.$r[wo_due].'</td>
</tr>
<tr>
    <td>Request Date</td>
    <td>:</td>
    <td>'.$r[wr_date].'</td>
    <td>Est. Duration</td>
    <td>:</td>
    <td>'.$r[wo_duration].' '.$r[wo_unit_duration].'</td>
</tr>
<tr>
    <td>Location</td>
    <td>:</td>
    <td>'.$r[sl_desc].'</td>
    <td>Scheduled Est.</td>
    <td>:</td>
    <td>'.$r[wo_scheduled].'</td>
</tr>
<tr>
    <td>Asset</td>
    <td>:</td>
    <td colspan="4">'.$r[wo_assetname].'</td>
</tr>
<tr>
    <td>PIC</td>
    <td>:</td>
    <td colspan="4">'.$r[wo_pic1].' - '.$r[wo_pic2].' - '.$r[wo_pic3].'</td>
</tr>
<tr>
    <td style="vertical-align:top;">Description</td>
    <td style="vertical-align:top;">:</td>
    <td style="padding-bottom:10px;" colspan="4">
        <table border="1">
            <tr>
                <td td style="vertical-align:top;height:50px;">'.$r[wo_desc].'</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="vertical-align:top;">Sparepart</td>
    <td style="vertical-align:top;">:</td>
    <td style="padding-bottom:10px;" colspan="4">
        <table border="1">
            <tr>
                <td td style="vertical-align:top;">'.$list_item.'</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="vertical-align:top;">Instruction</td>
    <td style="vertical-align:top;">:</td>
    <td style="padding-bottom:30px;" colspan="4">
        <table border="1">
            <tr>
                <td td style="vertical-align:top;height:100px;">'.nl2br2($r[wo_instruction]).'</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="vertical-align:top;">Duration Real</td>
    <td style="vertical-align:top;">:</td>
    <td style="padding-bottom:10px;" colspan="4">
        <table border="1">
            <tr>
                <td td style="vertical-align:top;">'.$r[wo_real_duration].' '.$r[wo_unit_duration].'</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="vertical-align:top;">Scheduled Real</td>
    <td style="vertical-align:top;">:</td>
    <td style="padding-bottom:10px;" colspan="4">
        <table border="1">
            <tr>
                <td td style="vertical-align:top;">'.$r[wo_real_scheduled_start].' s/d '.$r[wo_real_scheduled_end].'</td>
            </tr>
        </table>
    </td>
</tr>
</table>';

$mpdf = new mPDF('','A4');
$mpdf->WriteHTML($isi);
$mpdf->Output($kd.'.pdf', 'I');

?>