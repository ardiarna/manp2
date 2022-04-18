<?php
require_once '../../../libs/init.php'; 

$user=$_SESSION["user"];
//$pageName = "include/".basename($_SERVER['PHP_SELF']);
//echo $pageName;
$mode=$_GET['mode'];
switch ($mode) {
	case "view";
			view();
	break;
	case "save";
			save();
	break;
	case "load";
			load();
	break;
}

function load(){	
global $app_conn;
$kd=$_GET['kd'];

header("Content-type: text/xml");
print("<?php xml version=\"1.0\"?>");
print("<data>");
//to_char(camp_start_date,'DD-mon-yyyy')

$sql = "select * from in_mas_unit where inmu_unit='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['inmu_unit']."</kodesat>");
		print("<nama>".$row['inmu_name']."</nama>");
	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
		
	if($stat=='ubah'){
		$sql = "update in_mas_unit
				set inmu_name='$nama'
				where inmu_unit='$kodesat'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_unit (inmu_unit,inmu_name,inmu_revi,inmu_add_user,inmu_add_date,inmu_edit_user,inmu_edit_date)
			values ('$kodesat','$nama',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
			//echo $sql;
			$ret = pg_query($app_conn,$sql);
			pg_close();
			echo "OK";

	}

	//echo $sql;
//echo "OK";
}

function view(){
//include_once("koneksi.inc.php");
global $app_conn;
header("Content-type: text/xml");
//encoding may be different in your case
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';

$i=1;
$sql = "select * from in_mas_unit order by inmu_unit";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['inmu_unit']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_unit']."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_name']."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_add_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_add_date']."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_edit_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmu_edit_date']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>