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

$sql = "select * from sett_assets_category where sac_code='$kd'";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['sac_code']."</kodesat>");
		print("<nama>".$row['sac_desc']."</nama>");

	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
		
	if($stat=='ubah'){
		$sql = "update sett_assets_category
				set sac_desc='$nama'
				where sac_code='$kodesat'";

				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into sett_assets_category (sac_code,sac_desc,sac_add_user,sac_add_date,sac_edit_user,sac_edit_date)
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
$sql = "select * from sett_assets_category order by sac_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['sac_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['sac_code']."]]></cell>");
		print("<cell><![CDATA[".$row['sac_desc']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "DELETE FROM sett_assets_category WHERE sac_code = '{$kd}';";
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