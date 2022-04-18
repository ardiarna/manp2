<?php
require_once '../../../libs/init.php'; 
$user=$_SESSION["user"];

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
	$kd = $_GET['kd'];

	header("Content-type: text/xml");
	print("<?php xml version=\"1.0\"?>");
	print("<data>");

	$sql = "SELECT * from app_user where user_id = '$kd'";
	$query=pg_query($conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<user_id>".$row['user_id']."</user_id>");
		print("<user_name>".$row['user_name']."</user_name>");
		print("<first_name>".$row['first_name']."</first_name>");
		print("<last_name>".$row['last_name']."</last_name>");
		print("<password>".$row['password']."</password>");
	}
	print('</data>');
}

function save(){	
	global $conn;
	$stat = $_GET['stat'];
	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$password = $_POST['password'];
	if($stat=='ubah'){
		$sql = "UPDATE app_user set user_name = '$user_name', first_name = '$first_name', last_name = '$last_name', password = '$password' where user_id = '$user_id'";			
	} else {		
		$sql = "INSERT into app_user(user_name, first_name, last_name, password, user_create, date_create, user_modify, date_modify) values('$user_name', '$first_name', '$last_name', '$password', '".$_SESSION["user"]."', '".date("Y-m-d H:i:s")."', '".$_SESSION["user"]."', '".date("Y-m-d H:i:s")."')";
	}
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function view(){
	global $conn;
	header("Content-type: text/xml");
	echo('<?php xml version="1.0" encoding="utf-8"?>'); 
	echo '<rows >';

	$i=1;
	$sql = "SELECT * from app_user order by user_id";
	$query=pg_query($conn,$sql);
		while($row=pg_fetch_array($query)){
			//echo $row['KodePerusahaan'];
			echo ("<row id='".$row['user_id']."'>");
			print("<cell><![CDATA[".$i."]]></cell>");
			print("<cell><![CDATA[".$row['user_name']."]]></cell>");
			print("<cell><![CDATA[".$row['first_name']."]]></cell>");
			print("<cell><![CDATA[".$row['last_name']."]]></cell>");
			print("</row>");
			$i++;
	}
	echo '</rows>';
}

function delete() {	
	global $conn;

	$kd = $_POST['kd'];
	$sql = "DELETE FROM app_user WHERE user_id = '{$kd}';";
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