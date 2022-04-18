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

$sql = "select gmref_caba,(select gmcab_name from gen_mas_caba where gmcab_code=gmref_caba) as cab
,gmref_type,(SELECT ghc_text from gen_hard_code where ghc_code='FORMPO' and ghc_value=gmref_type) as typepo
,gmref_code,gmref_name,gmref_mode
from gen_mas_reff where gmref_modu='PO' and gmref_code='$kd'
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<cabang>".$row['gmref_caba']."</cabang>");
		print("<namacabang>".$row['cab']."</namacabang>");
		print("<typereff>".$row['gmref_type']."</typereff>");
		print("<kodereff>".$row['gmref_code']."</kodereff>");
		print("<nama>".$row['gmref_name']."</nama>");
		print("<penomoran>".$row['gmref_mode']."</penomoran>");

	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$cabang=$_POST['cabang'];
	$typereff=$_POST['typereff'];
	$kodereff=$_POST['kodereff'];
	$nama=$_POST['nama'];
	$penomoran=$_POST['penomoran'];

		
	if($stat=='ubah'){
		$sql = "update gen_mas_reff
				set gmref_name='$nama'
				where gmref_code='$kodereff'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into gen_mas_reff (gmref_modu,gmref_caba,gmref_type,gmref_code,gmref_name,gmref_mode,gmref_revi,gmref_add_user,gmref_add_date,gmref_edit_user,gmref_edit_date)
			values ('PO','$cabang','$typereff','$kodereff','$nama','$penomoran',
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
$sql = "select inprh_reff, inprh_docu, inprh_date, inprh_po_no, 
(select pcms_name from pc_mas_supplier where pcms_code=inprh_vend_code) as supp, 
inprh_delv_no, inprh_delv_date
from in_po_rec_head
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['inprh_docu']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['inprh_docu']."]]></cell>");
		print("<cell><![CDATA[".$row['inprh_date']."]]></cell>");
		print("<cell><![CDATA[".$row['inprh_po_no']."]]></cell>");
		print("<cell><![CDATA[".$row['supp']."]]></cell>");
		print("<cell><![CDATA[".$row['inprh_delv_no']."]]></cell>");
		print("<cell><![CDATA[".$row['inprh_delv_date']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>