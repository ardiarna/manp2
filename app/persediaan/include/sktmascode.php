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

$sql = "SELECT 
(select inmt_type||' - '||inmt_name from in_mas_type where inmt_type=inmi_type) as typebrg,
(select inmc_cate||' - '||inmc_name from in_mas_cate where inmc_cate=inmi_cate and inmc_type=inmi_type) as kategoribrg,
(select inmg_grou||' - '||inmg_name from in_mas_grou where inmg_grou=inmi_grou and inmg_cate=inmi_cate and inmg_type=inmi_type) as groupbrg,
inmi_type,inmi_cate,inmi_grou,inmi_item, inmi_name, inmi_desc, inmi_barc, inmi_unis, inmi_unim, inmi_unil, inmi_conm, inmi_conl, 
inmi_mini, inmi_maxi, inmi_revi, inmi_add_user, inmi_add_date, inmi_edit_user, inmi_edit_date
FROM public.in_mas_item where inmi_type||inmi_cate||inmi_grou||inmi_item='$kd'";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		print("<typebrg>".$row['inmi_type']."</typebrg>");
		print("<namatypebarang>".$row['typebrg']."</namatypebarang>");
		print("<kategoribrg>".$row['inmi_cate']."</kategoribrg>");
		print("<namakategoribarang>".$row['kategoribrg']."</namakategoribarang>");
		
		print("<groupbarang>".$row['inmi_grou']."</groupbarang>");
		print("<namagroupbarang>".$row['groupbrg']."</namagroupbarang>");
		
		print("<kodebarang>".$row['inmi_item']."</kodebarang>");
		print("<nama>".$row['inmi_name']."</nama>");
		print("<Keterangan>".$row['inmi_desc']."</Keterangan>");
		print("<barcode>".$row['inmi_barc']."</barcode>");
		print("<satbrg>".$row['inmi_unis']."</satbrg>");
		
		print("<satbrgs>".$row['inmi_unim']."</satbrgs>");
		print("<satbrgskon>".$row['inmi_conm']."</satbrgskon>");
		
		print("<satbrgb>".$row['inmi_unil']."</satbrgb>");
		print("<satbrgbkon>".$row['inmi_conl']."</satbrgbkon>");

	}
print('</data>');
}

function save(){	
global $app_conn;

	$stat=$_GET['stat'];
	$typebrg=$_POST['typebrg'];
	$kategoribrg=$_POST['kategoribrg'];
	$groupbarang=$_POST['groupbarang'];
	$kodebarang=$_POST['kodebarang'];
	
	$nama=$_POST['nama'];
	$Keterangan=$_POST['Keterangan'];
	$barcode=$_POST['barcode'];
	
	$satbrg=$_POST['satbrg'];
	$satbrgs=$_POST['satbrgs'];
	$satbrgb=$_POST['satbrgb'];
	
	$satbrgskon=$_POST['satbrgskon'];
	$satbrgbkon=$_POST['satbrgbkon'];
		
	if($stat=='ubah'){
		$sql = "update in_mas_item
				set inmi_name='$nama',
				inmi_desc='$Keterangan',
				inmi_barc='$barcode',
				inmi_unis='$satbrg',
				inmi_unim='$satbrgs',
				inmi_unil='$satbrgb',
				inmi_conm=$satbrgskon,
				inmi_conl=$satbrgbkon
				where inmi_type='$typebrg' and inmi_cate='$kategoribrg' and inmi_grou='$groupbarang' and inmi_item='$kodebarang' ";
				$ret = pg_query($app_conn,$sql);
				pg_close();
				echo "OK";
	} else {		
			$sql = " insert into in_mas_item (
			inmi_type, inmi_cate, inmi_grou, inmi_item, inmi_name, inmi_desc, inmi_barc, inmi_unis, inmi_unim, inmi_unil, inmi_conm, inmi_conl, inmi_mini, inmi_maxi, inmi_revi, inmi_add_user, inmi_add_date, inmi_edit_user, inmi_edit_date)
			values ('$typebrg','$kategoribrg','$groupbarang','$kodebarang','$nama','$Keterangan','$barcode','$satbrg','$satbrgs','$satbrgb',$satbrgskon,$satbrgbkon,0,0			
			,0,'".$_SESSION["user"]."','".date("Y-m-d H:i:s")."','".$_SESSION["user"]."','".date("Y-m-d H:i:s")."')";
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

$sql = "SELECT 
(select inmt_type||' - '||inmt_name from in_mas_type where inmt_type=inmi_type) as typebrg,
(select inmc_cate||' - '||inmc_name from in_mas_cate where inmc_cate=inmi_cate and inmc_type=inmi_type) as kategoribrg,
(select inmg_grou||' - '||inmg_name from in_mas_grou where inmg_grou=inmi_grou and inmg_cate=inmi_cate and inmg_type=inmi_type) as groupbrg,
inmi_type,inmi_cate,inmi_grou,inmi_item, inmi_name, inmi_desc, inmi_barc, inmi_unis, inmi_unim, inmi_unil, inmi_conm, inmi_conl, 
inmi_mini, inmi_maxi, inmi_revi, inmi_add_user, inmi_add_date, inmi_edit_user, inmi_edit_date
FROM public.in_mas_item;
";
//echo $sql;
$query=pg_query($app_conn,$sql);
	while($row=pg_fetch_array($query)){
		//echo $row['KodePerusahaan'];
		$id=$row['inmi_type'].$row['inmi_cate'].$row['inmi_grou'].$row['inmi_item'];
		
		echo ("<row id='".$id."'>");
		print("<cell><![CDATA[".$i."]]></cell>");
		print("<cell><![CDATA[".$row['typebrg']."]]></cell>");
		print("<cell><![CDATA[".$row['kategoribrg']."]]></cell>");
		print("<cell><![CDATA[".$row['groupbrg']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_item']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_name']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_desc']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_barc']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_unis']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_unim']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_conm']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_unil']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_conl']."]]></cell>");
		
		print("<cell><![CDATA[".$row['inmi_add_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_add_date']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_edit_user']."]]></cell>");
		print("<cell><![CDATA[".$row['inmi_edit_date']."]]></cell>");
		
		print("</row>");
		$i++;
}
echo '</rows>';
}	

?>