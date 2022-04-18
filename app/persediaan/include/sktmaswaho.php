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

$sql = "select inmw_caba,(select gmcab_name from gen_mas_caba where gmcab_code=inmw_caba) as cab,
inmw_waho,inmw_name,inmw_addr,inmw_sale
from in_mas_waho where inmw_caba||inmw_waho='$kd'
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<cabang>".$row['inmw_caba']."</cabang>");
		print("<namacabang>".$row['cab']."</namacabang>");
		print("<kodegudang>".$row['inmw_waho']."</kodegudang>");
		print("<nama>".$row['inmw_name']."</nama>");
		print("<alamat>".$row['inmw_addr']."</alamat>");
		print("<untukjual>".$row['inmw_sale']."</untukjual>");

	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$cabang=$_POST['cabang'];
	$kodegudang=$_POST['kodegudang'];
	$alamat=$_POST['alamat'];
	$nama=$_POST['nama'];
	$untukjual=$_POST['untukjual'];
	
	$kdgd=$cabang.$kodegudang;
		
	if($stat=='ubah'){
		$sql = "update in_mas_waho
				set inmw_name='$nama',
				inmw_addr='$alamat',
				inmw_sale='$untukjual'
				where inmw_caba||inmw_waho='$kdgd'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_waho 
			(inmw_caba, inmw_waho, inmw_name, inmw_addr, inmw_sale, inmw_revi, inmw_add_user, inmw_add_date, inmw_edit_user, inmw_edit_date)
			values ('$cabang','$kodegudang','$nama','$alamat','$untukjual',
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
$sql = "select inmw_caba,inmw_caba||' - '||(select gmcab_name from gen_mas_caba where gmcab_code=inmw_caba) as cab,
inmw_waho,inmw_name,inmw_addr,case when inmw_sale='Y' then 'Ya' else 'Tidak' end as jual
from in_mas_waho";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		$kd=$row['inmw_caba'].$row['inmw_waho'];
		echo ("<row id='".$kd."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['cab']."]]></cell>");
		print("<cell><![CDATA[".$row['inmw_waho']."]]></cell>");
		print("<cell><![CDATA[".$row['inmw_name']."]]></cell>");
		print("<cell><![CDATA[".$row['inmw_addr']."]]></cell>");
		print("<cell><![CDATA[".$row['jual']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>