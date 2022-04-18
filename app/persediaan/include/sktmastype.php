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

$sql = "select * from in_mas_type where inmt_type='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['inmt_type']."</kodesat>");
		print("<nama>".$row['inmt_name']."</nama>");
		print("<jnsbrg>".$row['inmt_char']."</jnsbrg>");
	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
	$jnsbrg=$_POST['jnsbrg'];
		
	if($stat=='ubah'){
		$sql = "update in_mas_type
				set inmt_name='$nama'
				,inmt_char='$jnsbrg'
				where inmt_type='$kodesat'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_type (inmt_type,inmt_name,inmt_char,inmt_revi,inmt_add_user,inmt_add_date,inmt_edit_user,inmt_edit_date)
			values ('$kodesat','$nama','$jnsbrg',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select *,case when inmt_char='I' then 'Inventory' else 'Non Inventory' end as typebrg from in_mas_type order by inmt_type";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['inmt_type']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_type']."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_name']."]]></cell>");
		print("<cell><![CDATA[".$row['typebrg']."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_add_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_add_date']."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_edit_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmt_edit_date']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>