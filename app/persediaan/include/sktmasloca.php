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

$sql = "select inml_caba,
inml_caba,(select gmcab_name from gen_mas_caba where gmcab_code=inml_caba) as cab,
inml_waho,(select inmw_name from in_mas_waho where inmw_caba=inml_caba and inmw_waho=inml_waho) as gudang,
inml_loca,inml_name
from in_mas_loca where inml_caba||inml_waho||inml_loca='$kd'";

//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<cabang>".$row['inml_caba']."</cabang>");
		print("<namacabang>".$row['cab']."</namacabang>");
		print("<gudang>".$row['inml_waho']."</gudang>");
		print("<namagudang>".$row['gudang']."</namagudang>");
		print("<kodelokasi>".$row['inml_loca']."</kodelokasi>");
		print("<nama>".$row['inml_name']."</nama>");
	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$cabang=$_POST['cabang'];
	$gudang=$_POST['gudang'];
	$kodelokasi=$_POST['kodelokasi'];
	$nama=$_POST['nama'];
	
	$kdgd=$cabang.$gudang.$kodelokasi;
		
	if($stat=='ubah'){
		$sql = "update in_mas_loca
				set inml_name='$nama'
				where inml_caba||inml_waho||inml_loca='$kdgd'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_loca 
			(inml_caba, inml_waho, inml_loca, inml_name, inml_revi, inml_add_user, inml_add_date, inml_edit_user, inml_edit_date)
			values ('$cabang','$gudang','$kodelokasi','$nama',
			0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select inml_caba,inml_waho,
inml_caba||' - '||(select gmcab_name from gen_mas_caba where gmcab_code=inml_caba) as cab,
inml_waho||' - '||(select inmw_name from in_mas_waho where inmw_caba=inml_caba and inmw_waho=inml_waho) as gudang,
inml_loca,inml_name
from in_mas_loca";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		$kd=$row['inml_caba'].$row['inml_waho'].$row['inml_loca'];
		echo ("<row id='".$kd."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['cab']."]]></cell>");
		print("<cell><![CDATA[".$row['gudang']."]]></cell>");
		print("<cell><![CDATA[".$row['inml_loca']."]]></cell>");
		print("<cell><![CDATA[".$row['inml_name']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>