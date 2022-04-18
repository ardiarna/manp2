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

$sql = "select smt_work_type||smt_code as kode,smt_work_type,smt_code,smt_description from sett_maintenance_type 
		where smt_work_type||smt_code='$kd'";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['smt_code']."</kodesat>");
		print("<nama>".htmlentities($row['smt_description'])."</nama>");
		print("<worktype>".$row['smt_work_type']."</worktype>");
	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
	$worktype=$_POST['worktype'];
	$kode=$worktype.$kodesat;
		
	if($stat=='ubah'){
		$sql = "update sett_maintenance_type
				set smt_description='$nama'
				where smt_work_type||smt_code='$kode'";

				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into sett_maintenance_type (smt_work_type,smt_code,smt_description,smt_add_user,smt_add_date,smt_edit_user,smt_edit_date)
			values ('$worktype','$kodesat','$nama','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select smt_work_type||smt_code as kode,smt_work_type,smt_code,smt_description from sett_maintenance_type order by smt_work_type,smt_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['kode']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['smt_work_type']."]]></cell>");
		print("<cell><![CDATA[".$row['smt_code']."]]></cell>");
		print("<cell><![CDATA[".$row['smt_description']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$smt_code = $_POST['smt_code'];
	$smt_work_type = $_POST['smt_work_type'];
	$sql = "DELETE FROM sett_maintenance_type WHERE smt_work_type = '{$smt_work_type}' AND smt_code = '{$smt_code}';";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = $sql;
	}
	pg_close();
	echo $ret;
}	

?>