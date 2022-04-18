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

$sql = "SELECT ssl_location_code,
(select sl_desc from sett_location where sl_code=ssl_location_code) as locname,
ssl_code, ssl_desc from sett_sub_location where ssl_location_code||ssl_code='$kd'";

$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<codeloc>".$row['ssl_location_code']."</codeloc>");
		print("<descloc>".$row['locname']."</descloc>");
		print("<codesubloc>".$row['ssl_code']."</codesubloc>");
		print("<descsubloc>".htmlentities($row['ssl_desc'])."</descsubloc>");
		print("<codeloc_lm>".$row['ssl_location_code']."</codeloc_lm>");
		print("<codesubloc_lm>".$row['ssl_code']."</codesubloc_lm>");
	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$codeloc=$_POST['codeloc'];
	$codesubloc=$_POST['codesubloc'];
	$descsubloc=$_POST['descsubloc'];
	$codeloc_lm=$_POST['codeloc_lm'];
	$codesubloc_lm=$_POST['codesubloc_lm'];
		
	if($stat=='ubah'){
		$sql = "UPDATE sett_sub_location
				set ssl_location_code = '$codeloc', ssl_code = '$codesubloc', ssl_desc = '$descsubloc' 
				where ssl_location_code = '$codeloc_lm' AND ssl_code = '$codesubloc_lm'";
				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " INSERT INTO sett_sub_location (
			ssl_location_code, ssl_code, ssl_desc, ssl_add_user, ssl_add_date, ssl_edit_user, ssl_edit_date)
			values ('$codeloc','$codesubloc','$descsubloc','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "SELECT ssl_location_code,
(select sl_code||' - '||sl_desc from sett_location where sl_code=ssl_location_code) as locname,
ssl_code, ssl_desc from sett_sub_location ORDER BY ssl_location_code, ssl_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		$kdsubloc=$row['ssl_location_code'].$row['ssl_code'];
		echo ("<row id='".$kdsubloc."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['locname']."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_code']."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['ssl_location_code']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$ssl_code = $_POST['ssl_code'];
	$ssl_location_code = $_POST['ssl_location_code'];
	$sql = "DELETE FROM sett_sub_location WHERE ssl_location_code = '{$ssl_location_code}' AND ssl_code = '{$ssl_code}';";
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