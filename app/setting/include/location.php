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

$sql = "select * from sett_location where sl_code='$kd'";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['sl_code']."</kodesat>");
		print("<nama>".$row['sl_desc']."</nama>");

	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
		
	if($stat=='ubah'){
		$sql = "update sett_location
				set sl_desc='$nama'
				where sl_code='$kodesat'";

				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into sett_location (sl_code,sl_desc,sl_add_user,sl_add_date,sl_edit_user,sl_edit_date)
			values ('$kodesat','$nama','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select * from sett_location order by sl_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['sl_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sl_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sl_desc']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "DELETE FROM sett_location WHERE sl_code = '{$kd}';";
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