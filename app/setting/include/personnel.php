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

$sql = "select * from sett_employee where se_code='$kd'";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodesat>".$row['se_code']."</kodesat>");
		print("<nama>".$row['se_name']."</nama>");
		print("<position>".$row['se_position']."</position>");
		print("<deparment>".$row['se_department']."</deparment>");
	}
print('</data>');
}

function save(){	
global $conn;

	$stat=$_GET['stat'];
	$kodesat=$_POST['kodesat'];
	$nama=$_POST['nama'];
	$position=$_POST['position'];
	$deparment=$_POST['deparment'];
		
	if($stat=='ubah'){
		$sql = "update sett_employee
				set se_name='$nama'
				,se_position='$position'
				,se_department='$deparment'
				where se_code='$kodesat'";

				$ret = pg_query($conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into sett_employee (se_code,se_name,se_department,se_position,se_add_user,se_add_date,se_edit_user,se_edit_date)
			values ('$kodesat','$nama','$deparment','$position','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select *,
case when se_position='O' then 'Operator'
when se_position='S' then 'Staff'
when se_position='K' then 'Kasubsi'
when se_position='B' then 'Kabag'
when se_position='M' then 'Manager'
end as posisi
from sett_employee order by se_code";
//echo $sql;
$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['se_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['se_code']."]]></cell>");
		print("<cell><![CDATA[".$row['se_name']."]]></cell>");
		print("<cell><![CDATA[".$row['se_department']."]]></cell>");
		print("<cell><![CDATA[".$row['posisi']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "DELETE FROM sett_employee WHERE se_code = '{$kd}';";
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