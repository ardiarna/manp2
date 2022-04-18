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

$sql = "select * from gen_mas_truk where gmt_code='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<kodekendaraan>".$row['gmt_code']."</kodekendaraan>");
		print("<nama>".$row['gmt_name']."</nama>");
		print("<supir>".$row['gmt_driv']."</supir>");
	}
print('</data>');
}

function save(){	
global $app_conn;
$data = json_decode(file_get_contents("php://input"),true);
$stat=$_GET['stat'];

$kodekendaraan=$data['kodekendaraan'];
$nama=$data['nama'];
$supir=$data['supir'];
$cabang=$data['cabang'];

if($stat=='ubah'){
		$sql ="update gen_mas_truk
				set gmt_name='$nama'
				,gmt_driv='$supir'
				where gmt_code='$kodekendaraan';";

		$sql.="delete from gen_mas_truk_caba where gmtc_code='$kodekendaraan';";
//				$ret = pg_query($app_conn,$sql);
//				pg_close();
//				echo "OK";
	} else {		
			$sql = " insert into gen_mas_truk 
			(gmt_code, gmt_name, gmt_divi, gmt_driv, gmt_revi, gmt_add_user, gmt_add_date, gmt_edit_user, gmt_edit_date)
			values ('$kodekendaraan','$nama','','$supir',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
			//echo $sql;
//			$ret = pg_query($app_conn,$sql);
//			pg_close();
//			echo "OK";
}

for( $i=0; $i < sizeof($cabang); $i++ ) {
 // echo $cabang[$i]['namacabang'];
	$sql.="insert into gen_mas_truk_caba values ('$kodekendaraan','".$cabang[$i]['kodecabang']."');";
}
//echo $sql;

$ret = pg_query($app_conn,$sql);
	pg_close();
	echo "OK";

/*
	$stat=$_GET['stat'];
	$kodekendaraan=$_POST['kodekendaraan'];
	$nama=$_POST['nama'];
	$supir=$_POST['supir'];
			
	if($stat=='ubah'){
		$sql = "update gen_mas_truk
				set gmt_name='$nama'
				,gmt_driv='$supir'
				where gmt_code='$kodekendaraan'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into gen_mas_truk 
			(gmt_code, gmt_name, gmt_divi, gmt_driv, gmt_revi, gmt_add_user, gmt_add_date, gmt_edit_user, gmt_edit_date)
			values ('$kodekendaraan','$nama','','$supir',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
			//echo $sql;
			$ret = pg_query($app_conn,$sql);
			pg_close();
			echo "OK";

	}

	//echo $sql;
*/
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
$sql = "select *from gen_mas_truk order by gmt_code";

$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['gmt_code']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_code']."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_name']."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_driv']."]]></cell>");
		print("<cell><![CDATA[".cabang($row['gmt_code'])."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_add_user']."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_add_date']."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_edit_user']."]]></cell>");
		print("<cell><![CDATA[".$row['gmt_edit_date']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

function cabang($kd){
global $app_conn;

$sql = "select array(select gmcab_name from gen_mas_truk_caba inner join gen_mas_caba on gmtc_caba=gmcab_code
where gmtc_code='$kd'
group by gmcab_name) as caba";
//echo $sql;
//exit;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
	//return str_replace('"}','',(str_replace('{"','',$row[0])));
	return str_replace('}','',(str_replace('{','',$row[0])));
	}
}

?>