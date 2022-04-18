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

$sql = "select inmc_type,inmc_cate as cate,(select inmt_name from in_mas_type where inmt_type=inmc_type) as typebrg,
inmc_cate,inmc_name,inmc_sale,inmc_sdis,inmc_sret,inmc_purc,inmc_pdis,inmc_pret,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sale) as accsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sdis) as accdiscsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sret) as accretsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_purc) as accpo,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_pdis) as accdiscpo,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_pret) as accpret
from in_mas_cate where inmc_type||inmc_cate='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<typebrg>".$row['inmc_type']."</typebrg>");
		print("<namatypebarang>".$row['typebrg']."</namatypebarang>");
		print("<kodekategori>".$row['inmc_cate']."</kodekategori>");
		print("<nama>".htmlentities($row['inmc_name'])."</nama>");
		
		print("<accjual>".htmlentities($row['inmc_sale'])."</accjual>");
		print("<namaaccjual>".htmlentities($row['accsales'])."</namaaccjual>");
		
		print("<accpotjual>".htmlentities($row['inmc_sdis'])."</accpotjual>");
		print("<namaaccpotjual>".htmlentities($row['accdiscsales'])."</namaaccpotjual>");
		
		print("<accretjual>".htmlentities($row['inmc_sret'])."</accretjual>");
		print("<namaaccretjual>".htmlentities($row['accretsales'])."</namaaccretjual>");
		
		print("<accbeli>".htmlentities($row['inmc_purc'])."</accbeli>");
		print("<namaaccbeli>".htmlentities($row['accpo'])."</namaaccbeli>");
		
		print("<accpotbeli>".htmlentities($row['inmc_pdis'])."</accpotbeli>");
		print("<namaaccpotbeli>".htmlentities($row['accdiscpo'])."</namaaccpotbeli>");
		
		print("<accretbeli>".htmlentities($row['inmc_pret'])."</accretbeli>");
		print("<namaaccretbeli>".htmlentities($row['accpret'])."</namaaccretbeli>");
		
	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$typebrg=$_POST['typebrg'];
	$kodekategori=$_POST['kodekategori'];
	$nama=$_POST['nama'];

	$accjual=$_POST['accjual'];
	$accpotjual=$_POST['accpotjual'];
	$accretjual=$_POST['accretjual'];

	$accbeli=$_POST['accbeli'];
	$accpotbeli=$_POST['accpotbeli'];
	$accretbeli=$_POST['accretbeli'];
		
	$kodetypecate=$typebrg.$kodekategori;
		
	if($stat=='ubah'){
		$sql = "update in_mas_cate
				set inmc_name='$nama',
				inmc_sale='$accjual',
				inmc_sdis='$accpotjual',
				inmc_sret='$accretjual',
				inmc_purc='$accbeli',
				inmc_pdis='$accpotbeli',			
				inmc_pret='$accretbeli'
				where inmc_type||inmc_cate='$kodetypecate'";

				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_cate (
			inmc_type, inmc_cate, inmc_name, inmc_sale, inmc_sdis, inmc_sret, inmc_purc, inmc_pdis, inmc_pret, inmc_revi, inmc_add_user, inmc_add_date, inmc_edit_user, inmc_edit_date)
			values ('$typebrg','$kodekategori','$nama','$accjual','$accpotjual','$accretjual','$accbeli','$accpotbeli','$accretbeli',0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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
$sql = "select inmc_type||inmc_cate as cate,(select inmt_type||' - '||inmt_name from in_mas_type where inmt_type=inmc_type) as typebrg,
inmc_cate,inmc_name,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sale) as accsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sdis) as accdiscsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_sret) as accretsales,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_purc) as accpo,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_pdis) as accdiscpo,
(select acmc_code||' - '||acmc_name from ac_mas_code where acmc_code=inmc_pret) as accpret
from in_mas_cate
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		echo ("<row id='".$row['cate']."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['typebrg']."]]></cell>");
		print("<cell><![CDATA[".$row['inmc_cate']."]]></cell>");
		print("<cell><![CDATA[".$row['inmc_name']."]]></cell>");
		print("<cell><![CDATA[".$row['accsales']."]]></cell>");
		print("<cell><![CDATA[".$row['accdiscsales']."]]></cell>");
		print("<cell><![CDATA[".$row['accretsales']."]]></cell>");
		print("<cell><![CDATA[".$row['accpo']."]]></cell>");
		print("<cell><![CDATA[".$row['accdiscpo']."]]></cell>");
		print("<cell><![CDATA[".$row['accpret']."]]></cell>");
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>