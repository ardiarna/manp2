<?php
require_once '../../../libs/init.php';
require_once '../../../libs/konfigurasiarmasi.php';


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
	case "loadetail";
		loadetail();
	break;
	case "save";
		save();
	break;
	case "delete";
		delete();
	break;
	case "listwo";
		listwo();
	break;
	case "listdep";
		listdep();
	break;
	case "loadsparepart";
		loadsparepart();
	break;
	case "cetakmemo";
		cetakmemo();
	break;
	case "cekeditable";
		cekeditable();
	break;
	case "cekhapus";
		cekhapus();
	break;
}

function view(){
	global $conn, $app_plan_id, $armasi_conn;
	$tglfrom = $_GET['from_date'];
	$tglto = $_GET['to_date'];
	
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i = 1;
	$sql = "SELECT b.*
		FROM tbl_spkmr b
		WHERE b.tgl >= '{$tglfrom}' AND b.tgl <= '{$tglto}'";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		$sql_armasi = "SELECT b.*, d.departemen_nama
		FROM tbl_spkmr b 
		JOIN departemen d ON (b.departemen_kode = d.departemen_kode)
		WHERE b.no_mr = '".$row['no_mr']."'";
		$query_armasi = pg_query($armasi_conn, $sql_armasi);
		$ra = pg_fetch_array($query_armasi);

		$sql_armasi_b = "SELECT DISTINCT(spk) AS spk FROM tbl_dtl_spkmr WHERE no_mr = '".$row['no_mr']."'";
		$query_armasi_b = pg_query($armasi_conn, $sql_armasi_b);
		$rb = pg_fetch_array($query_armasi_b);
		$spk = ($rb['spk'] == "-") ? "" : $rb['spk'];
		$approve_by1 = ($ra['$r[approval_spk1'] == 't') ? $ra['approve_by1'] : "";
		$status = ($ra['approval_spk'] == '' or $ra['approval_spk'] == 'f') ? "Not Approved" : "Approve"; 
		echo ("<row id='".$row['no_mr']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$ra['tgl']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['no_mr']."]]></cell>");
		print("<cell><![CDATA[".$ra['usefor']."]]></cell>");
		print("<cell><![CDATA[".$ra['departemen_nama']."]]></cell>");
		print("<cell><![CDATA[".$ra['keterangan']."]]></cell>");
		print("<cell><![CDATA[".$ra['keterangan1']."]]></cell>");
		print("<cell><![CDATA[".$ra['kode_produksi']."]]></cell>");
		print("<cell><![CDATA[".$spk."]]></cell>");
		print("<cell><![CDATA[".$approve_by1."]]></cell>");
		print("<cell><![CDATA[".$status."]]></cell>");
		print("<cell><![CDATA[".$ra['tgl_approval_spk']."]]></cell>");
		print("<cell><![CDATA[".substr($ra['approval_time'],0,5)."]]></cell>");
		print("<cell><![CDATA[".$ra['approve_by']."]]></cell>");
		print("<cell><![CDATA[".$ra['keterangan_spk']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function load(){	
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$no_mr = $_GET['no_mr'];
	$sql = "SELECT a.*, b.wo_date, b.wo_asset, c.amm_desc, b.wo_desc, b.wo_scheduled
		FROM tbl_spkmr a
		JOIN tbl_wo b ON(a.wo_code = b.wo_code)
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		WHERE a.no_mr = '{$no_mr}'";
	$query = pg_query($conn, $sql);
	$row = pg_fetch_array($query);
	print("<keterangan>".$row['no_mr']."</keterangan>");
	print("<no_mr>".$row['no_mr']."</no_mr>");
	print("<tgl>".$row['tgl']."</tgl>");
	print("<usefor>".$row['usefor']."</usefor>");
	print("<keterangan1>".$row['keterangan1']."</keterangan1>");
	print("<kode_produksi>".$row['kode_produksi']."</kode_produksi>");
	print("<wo_code>".$row['wo_code']."</wo_code>");
	print("<wo_date>".$row['wo_date']."</wo_date>");
	print("<wo_scheduled>".$row['wo_scheduled']."</wo_scheduled>");
	print("<asset_code>".$row['wo_asset']."</asset_code>");
	print("<asset_name>".$row['amm_desc']."</asset_name>");
	print("<wo_desc>".$row['wo_desc']."</wo_desc>");
	print("<departemen_kode>".$row['departemen_kode']."</departemen_kode>");
	print("<departemen_nama>".$row['departemen_nama']."</departemen_nama>");
	print('</data>');
}

function loadetail(){
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';	
	
	$no_mr = $_GET['no_mr'];
	$sql = "SELECT * FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}'";
		
	$query = pg_query($conn, $sql);
	$i = 1;
	while($row = pg_fetch_array($query)){
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[".$row['keterangan']."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("<cell><![CDATA[".$row['jenis']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function cekeditable() {
	global $conn, $app_plan_id, $armasi_conn;
	$no_mr = $_POST['no_mr'];
	
	$sql = "SELECT spk FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}' AND spk IS NOT NULL;";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	if($r['spk'] <> '' && $r['spk'] <> '-') {
		$ret = "Maaf MRSPK sudah dibuat SPK, tidak bisa diedit";
	} else{
		$ret = "OK";
	}
	echo $ret;
}

function save() {	
	global $conn, $app_plan_id, $armasi_conn;
	$stat = $_GET['stat'];
	$tgl = $_POST['tgl'];
	$usefor = $_POST['usefor'];
	$keterangan = $_POST['keterangan'];
	$keterangan1 = $_POST['keterangan1'];
	$kode_produksi = $_POST['kode_produksi'];
	$wo_code = $_POST['wo_code'];
	$wo_desc = $_POST['wo_desc'];
	$wo_scheduled = $_POST['wo_scheduled'];
	$asset_code = $_POST['asset_code'];
	$asset_name = $_POST['asset_name'];
	$departemen_kode = $_POST['departemen_kode'];
	$departemen_nama = $_POST['departemen_nama'];
	$arr_item_code = json_decode($_POST['sparepartlist'], false);
	$user = $_SESSION["user"];
	$hari_ini = date("Y-m-d");
	if($stat=='ubah'){
		$no_mr = $_POST['no_mr'];
		$sql_armasi = "UPDATE tbl_spkmr SET departemen_kode = '{$departemen_kode}', tgl = '{$tgl}', usefor = '{$usefor}', keterangan1 = '{$keterangan1}', kode_produksi = '{$kode_produksi}' WHERE no_mr = '{$no_mr}'; DELETE FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}'; ";
	} else {
		if($keterangan == 'MSPK') {
			$mr_jenis = 'MSPK';
		} else if($keterangan == 'HR') {
			$mr_jenis = 'MSPU';
		} else if($keterangan == 'PROJECT') {
			$mr_jenis = 'MSPP';
		} else if($keterangan == 'MSPO') {
			$mr_jenis = 'MSPO';
		}
		$thn = date("Y");
		$formatmrkode = $mr_jenis."/".$app_plan_id."/".date("y")."/";
		$sql = "SELECT max(no_mr) AS no_mr_max FROM tbl_spkmr WHERE no_mr like '%$formatmrkode%' AND date_part('year',tgl) = {$thn}";
		$query = pg_query($armasi_conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['no_mr_max'] == ''){
			$mx['no_mr_max'] = 0;
		} else {
			$mx['no_mr_max'] = substr($mx['no_mr_max'],-5);
		}
		$urutbaru = $mx['no_mr_max']+1;
		$no_mr = $formatmrkode.str_pad($urutbaru,5,"0",STR_PAD_LEFT);
		$sql_armasi = "INSERT INTO tbl_spkmr (no_mr, departemen_kode, tgl, usefor, keterangan, spk, keterangan1, kode_produksi, wo_kode, wo_desc, asset_kode, asset_nama, wo_scheduled) VALUES ('{$no_mr}', '{$departemen_kode}', '{$tgl}', '{$usefor}', '{$keterangan}', '-', '{$keterangan1}', '{$kode_produksi}', '{$wo_code}', '{$wo_desc}', '{$asset_code}', '{$asset_name}', '{$wo_scheduled}'); ";
	}
	$i = 1;
	foreach ($arr_item_code as $r) {
		$no_mr_dtl = $no_mr."/".$i;
		$sql_armasi .= "INSERT INTO tbl_dtl_spkmr (no_mr, no_mr_dtl, keterangan, qty, spk, jenis) VALUES ('{$no_mr}', '{$no_mr_dtl}', '{$r->keterangan}', '{$r->qty}', '-', '{$r->jenis}'); ";
		$i++;
	}
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		if($stat=='ubah'){
			$sql_u = "UPDATE tbl_spkmr SET departemen_kode = '{$departemen_kode}', tgl = '{$tgl}', usefor = '{$usefor}', keterangan1 = '{$keterangan1}', kode_produksi = '{$kode_produksi}' WHERE no_mr = '{$no_mr}'; DELETE FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}'; ";
			
		} else {
			$sql_u = "INSERT INTO tbl_spkmr (no_mr, departemen_kode, tgl, usefor, keterangan, spk, keterangan1, kode_produksi, wo_code, departemen_nama) VALUES ('{$no_mr}', '{$departemen_kode}', '{$tgl}', '{$usefor}', '{$keterangan}', '-', '{$keterangan1}', '{$kode_produksi}', '{$wo_code}', '{$departemen_nama}'); ";
		}
		$i = 1;
		foreach ($arr_item_code as $r) {
			$no_mr_dtl = $no_mr."/".$i;
			$sql_u .= "INSERT INTO tbl_dtl_spkmr (no_mr, no_mr_dtl, keterangan, qty, spk, jenis) VALUES ('{$no_mr}', '{$no_mr_dtl}', '{$r->keterangan}', '{$r->qty}', '-', '{$r->jenis}'); ";
			$i++;
		}
		$res = pg_query($conn, $sql_u);
		if($res){
			$ret = "OK";
		} else {
			$ret = pg_errormessage($conn)."MTC:".$sql_u;
		}
	}else{
		$ret = pg_errormessage($armasi_conn)."ARMASI".$sql_armasi;
	}
	pg_close();
	echo $ret;
}

function cekhapus() {
	global $conn, $app_plan_id, $armasi_conn;
	$no_mr = $_POST['no_mr'];
	
	$sql = "SELECT spk FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}' AND spk IS NOT NULL;";
	$query = pg_query($armasi_conn, $sql);
	$r = pg_fetch_array($query);
	if($r['spk'] <> '' && $r['spk'] <> '-') {
		$ret = "Maaf MRSPK sudah dibuat SPK, tidak bisa didelete";
	} else{
		$ret = "OK";
	}
	echo $ret;
}

function delete(){	
	global $conn, $app_plan_id, $armasi_conn;
	
	$no_mr = $_POST['no_mr'];
	
	$sql_armasi = "DELETE FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}'; DELETE FROM tbl_spkmr WHERE no_mr = '{$no_mr}';";
	$res_armasi = pg_query($armasi_conn, $sql_armasi);
	if($res_armasi){
		$sql_u = "DELETE FROM tbl_dtl_spkmr WHERE no_mr = '{$no_mr}'; DELETE FROM tbl_spkmr WHERE no_mr = '{$no_mr}';";
		$res = pg_query($conn, $sql_u);
		if($res){
			$ret = "OK";
		} else {
			$ret = pg_errormessage($conn);
		}
	} else {
		$ret = pg_errormessage($armasi_conn);	
	}
	pg_close();
	echo $ret;
}

function listwo(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	$i=1;
	$sql = "SELECT b.wo_code, b.wo_date, b.wo_desc, b.wo_asset, c.amm_desc, b.wo_pic1, b.wo_scheduled
		FROM tbl_wo b
		LEFT JOIN assets_master_main c ON(b.wo_asset = c.amm_code)
		WHERE b.wo_status IN('O','S') AND b.wo_code NOT IN (SELECT wo_code FROM tbl_spkmr)
		ORDER BY b.wo_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['wo_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['wo_code']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_date']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_asset']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_pic1']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function listdep(){
	global $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	$i=1;
	$sql = "SELECT departemen_kode, departemen_nama FROM departemen WHERE plan_kode = '{$app_plan_id}' AND departemen_nama <> 'AFILIASI' AND inactive = true ORDER BY departemen_nama";
	$query=pg_query($armasi_conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['departemen_kode']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['departemen_nama']."]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function loadsparepart(){
	global $conn, $app_plan_id, $armasi_conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$wo_code = $_GET['kd'];
	$sql = "SELECT wd.*, w.wo_desc, TO_CHAR(w.wo_scheduled, 'YYYY-MM-DD') AS wo_scheduled 
		FROM tbl_wo_detail wd
		JOIN tbl_wo w ON (wd.wo_code = w.wo_code) 
		WHERE wd.wo_code = '{$wo_code}' ORDER BY wd.item_name";
	$query = pg_query($conn,$sql);
	$i = 1;
	while($row = pg_fetch_array($query)){
		$sqlstok = "SELECT SUM(qty) AS ada FROM tbl_mutasi_dead_stock WHERE item_kode = '".$row['item_code']."'";
		$querystok = pg_query($armasi_conn, $sqlstok);
		$rstok = pg_fetch_array($querystok);
		$stockDS = $rstok['ada'] > 0 ? "Dead Stock" : "Stock";
		echo ("<row id='".$i."'>");
		print("<cell><![CDATA[1]]></cell>");
		print("<cell><![CDATA[".$row['item_code']."]]></cell>");
		print("<cell><![CDATA[".$row['item_name']."]]></cell>");
		print("<cell><![CDATA[".$row['unit']."]]></cell>");
		print("<cell><![CDATA[".$stockDS."]]></cell>");
		print("<cell><![CDATA[".$row['qty']."]]></cell>");
		print("<cell><![CDATA[]]></cell>");
		print("<cell><![CDATA[".$row['wo_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['wo_scheduled']."]]></cell>");
		print("<cell><![CDATA[]]></cell>");
		print("<cell><![CDATA[]]></cell>");
		print("</row>");
		$i++;
	}
	echo '</rows>';
}

function cetakmemo() {
	global $app_plan_id, $armasi_conn;

    echo "<script>";
	echo "function printContent(){";
	echo "var restorepage = document.body.innerHTML;";
	echo "var printcontent = document.getElementById('div1').innerHTML;";
	echo "document.body.innerHTML = printcontent;";
	echo "window.print();";
	echo "document.body.innerHTML = restorepage;";
	echo "}";
	echo "</script>";
	echo "<button onclick='printContent()'>PRINT</button>";
	echo "<div id='div1' style='margin-left: 3px; margin-right: 3px; margin-top: 3px; margin-bottom: 3px;'>";

	$no_mr = $_GET['no_mr'];
	$sql1="select * from tbl_spkmr where no_mr='$no_mr'";
	$result1=pg_query($armasi_conn, $sql1);
	$r=pg_fetch_array($result1);
	$request_kode=$r[request_kode];
	$departemen=$r[departemen_kode];
	$requester=$r[usefor];
	$tgl=$r[tgl];		
	$arr = explode("-",$r[tgl]);
	$hari = $arr[2];
	$bulan= $arr[1];
	$tahun= $arr[0];
	$txtout="<tr>
		<td align=center valign=top nowrap>";
	
        $sql="select * from plan where plan_kode='$app_plan_id'";
        $res=pg_query($armasi_conn, $sql);
        $r=pg_fetch_array($res);
        	$txtout.="<br><table width=90% border=0 align=center cellpadding=0 cellspacing=0>
		<tr>
		<td rowspan=3 valign=top><font size=1 face=Verdana, Arial, Helvetica, sans-serif>
		ARWANA - PLANT $r[plan_kode]<br>
		$r[plan_address]<br>
		Telp. $r[plan_phone] Fax. $r[plan_fax]
		</font></td>
		<td valign=top width=200 colspan=3><font size=1 face=Verdana, Arial, Helvetica, sans-serif>".cari_iso("Purchase Request",$app_plan_id)."</td>
		</tr>
		<tr>
		<td width=100><font size=1 face=Verdana, Arial, Helvetica, sans-serif>ORIGINAL</font></td>
		<td width=10><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:</font></td>
		<td width=125><font size=1 face=Verdana, Arial, Helvetica, sans-serif>PURCHASING DEPT.</font></td>
		</tr>
		<tr>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>RED</font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:</font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>RECEIVING</font></td>
		</tr>
		<tr>
		<td align=center colspan=4><strong><br>MEMO REQUEST SPK</strong></td>
		</tr>
		</table>";

	$txtout.="<table width=90% border=0 align=center cellpadding=0 cellspacing=0>
		<tr>
		<td><table width=100%  border=0 align=center cellpadding=0 cellspacing=1>
		<tr>
		<td height=18 colspan=7><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>&nbsp;$judul</storng></font></td>
		</tr>
		<tr bgcolor=#FFFFFF>
		<td colspan=2><font size=1 face=Verdana, Arial, Helvetica, sans-serif>MR. No.</font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:&nbsp;<strong>$no_mr</strong></font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>Requested by</font></td>
		<td colspan=3><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:&nbsp;<strong>$requester </strong></font></td>
		</tr>";
	$sql="select * from departemen where departemen_kode='$departemen'";
	$result=pg_query($armasi_conn, $sql);
	$r1=pg_fetch_array($result);
	$txtout.="<tr>
		<td colspan=2><font size=1 face=Verdana, Arial, Helvetica, sans-serif>Department</font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:&nbsp;<strong>$r1[departemen_nama]</strong></font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>Date</font></td>

		<td colspan=3><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:&nbsp;<strong>$hari-$bulan-$tahun</strong></font></td>
		</tr>
		<tr bgcolor=#FF6633>
		";
        
        $sql2="select * from tbl_spkmr where no_mr='$no_mr'";
		$result2=pg_query($armasi_conn, $sql2);
		$r2=pg_fetch_array($result2);
		$wo_kode = $r2[wo_kode] ? $r2[wo_kode]." / ".$r2[asset_nama] : "";
        $txtout.="<tr>
		<td colspan=2><font size=1 face=Verdana, Arial, Helvetica, sans-serif>Jenis Pekerjaan</font></td>
		<td><font size=1 face=Verdana, Arial, Helvetica, sans-serif>:&nbsp;<strong>$r2[keterangan]</strong></font></td>
		<td colspan=4><font size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>$wo_kode</strong></font></td>
		</tr></table>";
                
	$txtout.="
		<tr><td height=8 valign=top><img src=../gambar/0.gif width=1 height=8></td></tr>
		<table width=90%  border=0 align=center cellpadding=0 cellspacing=0>
		<tr>
		<td bgcolor=#000000><table width=100%  border=0 align=center cellpadding=0 cellspacing=1>
		<tr bgcolor=#FFFFFF> 
		<td height=18 width=25><div align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>
		<strong>No.</strong></font></div></td>
        <td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Keterangan</strong></font></td>
        <td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Qty</strong></font></td>
        <td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Satuan</strong></font></td>
        ";
		$sql="select * from tbl_dtl_spkmr where no_mr='$no_mr'";
	
        $result=pg_query($armasi_conn, $sql);
		$i=1;
		$count=1;
		while($r=pg_fetch_array($result)){
		$txtout.="<tr bgcolor=#FFFFFF><td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>$count.</font></td> 
        <td ><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>$r[keterangan]</font></td>
        <td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>$r[qty]</font></td>
        <td align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>$r[jenis]</font></td></tr>";
		$count++;
		}		
		$txtout.="</font></div></td>
			</tr>";
		$txtout.="</tr>
			</tr>";
	$txtout.="<tr bgcolor=#FFFFFF>
		<td bgcolor=#FFFFFF colspan=2>
		<div align=center><font size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Additional Notes :</strong></font></div></td>
		<td height=50>&nbsp;</td><td height=50>&nbsp;</td></tr>";
	$txtout.="
		</table>
		<tr>
		<td><br>
		<table border=0 align=right cellpadding=0 cellspacing=1 bgcolor=#000000>
		<tr bgcolor=#FFFFFF height=20>
		<td width=150><div align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Prepared by</div></td>
		<td width=150><div align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Checked by</div></td>
		<td width=150><div align=center><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif><strong>Approved by</div></td>
		</tr>
		<tr bgcolor=#FFFFFF heigth=20>
		<td align=center valign=bottom height=60><font color=#000000 size=1 face=Verdana, Arial, Helvetica, sans-serif>$requester</font></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
		</table>
		</td>
		</tr>
		</table></div>";

	echo $txtout;
	
}

function cari_iso($jenis_dokumen,$plan_kode){
	global $app_plan_id, $armasi_conn;

    $sql="select no_iso from tbl_iso where jenis_dokumen='$jenis_dokumen' and plan_kode='$plan_kode'";
    $res=pg_query($armasi_conn, $sql);
    $r=pg_fetch_array($res);
    $iso="No.F.$r[no_iso]<br>Hal. 1/1";
    return $iso;
}

?>