<?php
require_once '../libs/init.php'; 

$mode=$_GET['mode'];
switch ($mode) {
	case "dtlocation";
			dtlocation();
	break;
	case "dtcategory";
			dtcategory();
	break;
	case "dtmanufacture";
			dtmanufacture();
	break;
	case "dtsublocation";
			dtsublocation();
	break;
	case "dtgroup";
			dtgroup();
	break;	
	case "dtoperator";
			dtoperator();
	break;	
	case "dtparent";
			dtparent();
	break;
	case "dtasset";
			dtasset();
	break;
	case "dtworktype";
			dtworktype();
	break;
	case "dtsparepart";
		dtsparepart();
	break;
	case "dtmaintenance";
		dtmaintenance();
	break;
}
function dtparent(){
global $conn;
$kd=$_GET['kd'];
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "SELECT amm_code, amm_desc FROM assets_master_main where amm_group='$kd';";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	

function dtoperator(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "SELECT se_code, se_name, se_department, se_position,
case when se_position='O' then 'Operartor'
when se_position='S' then 'Staff'
when se_position='K' then 'Kasubsie'
when se_position='B' then 'Kabag'
when se_position='M' then 'Manager' end jabatan
FROM sett_employee;";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['se_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['se_code']."]]></cell>");
		print("<cell><![CDATA[".$row['se_name']."]]></cell>");
		print("<cell><![CDATA[".$row['se_department']."]]></cell>");
		print("<cell><![CDATA[".$row['jabatan']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	

function dtgroup(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "select sag_code, sag_desc from sett_assets_group  order by sag_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['sag_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sag_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sag_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	



function dtsublocation(){
global $conn;
$kd=$_GET['kd'];
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "select ssl_code, ssl_desc from sett_sub_location 
where ssl_location_code='$kd' order by ssl_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['ssl_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_code']."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	



function dtmanufacture(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "select sm_code, sm_desc from sett_manufacture order by sm_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['sm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sm_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	

function dtcategory(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "select sac_code,sac_desc from sett_assets_category order by sac_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['sac_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sac_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sac_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}	

function cbocab(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<complete>';
$sql = "select * from gen_mas_caba order by gmcab_code";
print("<option value='' ></option>");
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){		
		print("<option value='".$row['gmcab_code']."'><![CDATA[".$row['gmcab_name']."]]></option>");
}
echo '</complete>';
}	

function dtlocation(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "select sl_code,sl_desc from sett_location order by sl_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){

		echo ("<row id='".$row['sl_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sl_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sl_desc']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}

function dtasset(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "SELECT a.amm_code, a.amm_desc, a.amm_status, a.amm_location, b.sac_desc, c.sl_desc
	FROM assets_master_main a
	LEFT JOIN sett_assets_category b on(a.amm_category = b.sac_code)
	LEFT JOIN sett_location c on(a.amm_location = c.sl_code)
	ORDER BY a.amm_code";
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['amm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_location']."]]></cell>");
		print("<cell><![CDATA[".$row['sl_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['sac_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amm_status']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}

function dtworktype(){
global $conn;
header("Content-type: text/xml");
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';
$i=1;
$sql = "SELECT a.smt_work_type, a.smt_code, a.smt_description from sett_maintenance_type a order by a.smt_work_type, a.smt_code";
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['smt_work_type']."]]></cell>");
		print("<cell><![CDATA[".$row['smt_code']."]]></cell>");
		print("<cell><![CDATA[".$row['smt_work_type']." - ".$row['smt_description']."]]></cell>");
		print("<cell><![CDATA[".$row['smt_description']."]]></cell>");
		print("</row>");
		$i++;
	}
echo '</rows>';
}

function dtsparepart(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';
	
	$kd = $_REQUEST['kd'];

	$sql = "SELECT * FROM assets_master_sparepart WHERE amsp_code = '{$kd}'";
	$query=pg_query($conn, $sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amsp_sparepart_code']."'>");
		print("<cell><![CDATA[".$row['amsp_sparepart_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amsp_sparepart_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['amsp_unit']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}

function dtmaintenance(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$kd = $_GET['kd'];
	$sql = "SELECT * FROM assets_master_maintenance WHERE amm_code = '{$kd}' ORDER BY amms_code";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		echo ("<row id='".$row['amms_code']."'>");
		print("<cell><![CDATA[".$row['amms_code']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_description']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_part']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_intv_cycle']."]]></cell>");
		print("<cell><![CDATA[".$row['amms_type_cycle']."]]></cell>");
		print("</row>");
	}
	echo '</rows>';
}	

?>