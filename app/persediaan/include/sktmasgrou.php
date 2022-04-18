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

$sql = "select *,
(select inmt_type||' - '||inmt_name from in_mas_type where inmt_type=inmg_type) as typebrg,
(select inmc_cate||' - '||inmc_name from in_mas_cate where inmc_cate=inmg_cate and inmc_type=inmg_type) as kategoribrg
from in_mas_grou where inmg_type||inmg_cate||inmg_grou='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<typebrg>".$row['inmg_type']."</typebrg>");
		print("<namatypebarang>".$row['typebrg']."</namatypebarang>");
		print("<kategoribrg>".$row['inmg_cate']."</kategoribrg>");
		print("<namakategoribarang>".$row['kategoribrg']."</namakategoribarang>");
		print("<kodekelompok>".$row['inmg_grou']."</kodekelompok>");
		print("<nama>".$row['inmg_name']."</nama>");

	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$typebrg=$_POST['typebrg'];
	$kategoribrg=$_POST['kategoribrg'];
	$kodekelompok=$_POST['kodekelompok'];
	$nama=$_POST['nama'];
		
	if($stat=='ubah'){
		$sql = "update in_mas_grou
				set inmg_name='$nama'
				where inmg_type='$typebrg' and inmg_cate='$kategoribrg' and inmg_grou='$kodekelompok'";
				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_grou (
			inmg_type, inmg_cate, inmg_grou, inmg_name, inmg_revi, inmg_add_user, inmg_add_date, inmg_edit_user, inmg_edit_date)
			values ('$typebrg','$kategoribrg','$kodekelompok','$nama',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select 
(select inmt_type||' - '||inmt_name from in_mas_type where inmt_type=inmg_type) as typebrg,
(select inmc_cate||' - '||inmc_name from in_mas_cate where inmc_type||inmc_cate=inmg_type||inmg_cate) as kategoribrg,
inmg_type,inmg_cate,inmg_grou,inmg_name,inmg_add_user,inmg_add_date,inmg_edit_user,inmg_edit_date
from in_mas_grou
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		$id=$row['inmg_type'].$row['inmg_cate'].$row['inmg_grou'];
		echo ("<row id='".$id."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['typebrg']."]]></cell>");
		print("<cell><![CDATA[".$row['kategoribrg']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_grou']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_name']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_add_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_add_date']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_edit_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmg_edit_date']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>