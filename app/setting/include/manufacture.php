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
	case "delete";
			delete();
	break;
}

function load(){	
global $conn;
$kd=$_GET['kd'];

header("Content-type: text/xml");
print("<?php xml version=\"1.0\"?>");
print("<data>");
//to_char(camp_start_date,'DD-mon-yyyy')

$sql = "select * from sett_manufacture where sm_code='$kd'";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['sm_code']."</kodesat>");
		print("<nama>".$row['sm_desc']."</nama>");
		print("<negara>".$row['sm_country']."</negara>");

	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
	$negara=$_POST['negara'];
		
	if($stat=='ubah'){
		$sql = "update sett_manufacture
				set sm_desc='$nama'
				,sm_country='$negara'
				where sm_code='$kodesat'";

				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into sett_manufacture (sm_code,sm_desc,sm_country,sm_add_user,sm_add_date,sm_edit_user,sm_edit_date)
			values ('$kodesat','$nama','$negara','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
			//echo $sql;
			$ret = pg_query($conn,$sql);
			pg_close();
			echo "OK";
	}

	//echo $sql;
//echo "OK";
}

function view(){
//include_once("koneksi.inc.php");
global $conn;
header("Content-type: text/xml");
//encoding may be different in your case
echo('<?php xml version="1.0" encoding="utf-8"?>'); 
echo '<rows >';

$i=1;
$sql = "select * from sett_manufacture order by sm_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['sm_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sm_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sm_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['sm_country']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "DELETE FROM sett_manufacture WHERE sm_code = '{$kd}';";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}	

?>