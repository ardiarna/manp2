<?php
require_once '../libs/init.php'; 
$id=$_GET['id'];

header("Content-type: text/xml");
echo( '<?php xml version="1.0" encoding="iso-8859-1"?>');
echo '<toolbar>';
echo created_toolbar($id,$_SESSION["user"]);
echo '</toolbar>';

function created_toolbar($id,$username){
global $app_conn;

	$sql="SELECT (SELECT gm_sty FROM gen_menu WHERE gm_ide=um_id) AS tipe, um_add AS baru, um_edit AS ubah, um_del AS dele, um_prints AS slip, um_printl AS daft, um_excel AS exce FROM usr_menu WHERE um_code='".$username."' AND um_id='".$id."';";
	$query=pg_query($app_conn, $sql);
	$row=pg_fetch_assoc($query);
	if($row['baru']=='Y'){
		echo '<item id="baru" type="button" img="fa fa-plus" imgdis="fa fa-plus" text="Tambah"/>';
	}else{
		echo '<item id="baru" type="button" img="fa fa-plus" imgdis="fa fa-plus" text="Tambah" enabled="false"/>';
	};	
	echo '<item id="sep1" type="separator"/>';
	if($row['ubah']=='Y'){
		echo '<item id="ubah" type="button" img="fa fa-edit" imgdis="fa fa-edit" text="Ubah"/>';
	}else{
		echo '<item id="ubah" type="button" img="fa fa-edit" imgdis="fa fa-edit" text="Ubah" enabled="false"/>';
	};	
	echo '<item id="sep2" type="separator"/>';
	if($row['dele']=='Y'){
		echo '<item id="dele" type="button" img="fa fa-times" imgdis="fa fa-times" text="Hapus"/>';
	}else{
		echo '<item id="dele" type="button" img="fa fa-times" imgdis="fa fa-times" text="Hapus" enabled="false"/>';
	};	
	echo '<item id="sep3" type="separator"/>';
	if($row['tipe']=='1'){
		if($row['slip']=='Y'){
			echo '<item id="slip" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak"/>';
		}else{
			echo '<item id="slip" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak" enabled="false"/>';
		};
	}else{
		if($row['slip']=='Y'){
			echo '<item id="slip" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak Slip"/>';
		}else{
			echo '<item id="slip" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak Slip" enabled="false"/>';
		};
		echo '<item id="sep4" type="separator"/>';
		if($row['daft']=='Y'){
			echo '<item id="list" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak Daftar"/>';
		}else{
			echo '<item id="list" type="button" img="fa fa-print" imgdis="fa fa-print" text="Cetak Daftar" enabled="false"/>';
		};
		echo '<item id="sep5" type="separator"/>';
		if($row['exce']=='Y'){
			echo '<item id="file" type="button" img="fa fa-file-excel-o" imgdis="fa fa-file-excel-o" text="Excel"/>';
		}else{
			echo '<item id="file" type="button" img="fa fa-file-excel-o" imgdis="fa fa-file-excel-o" text="Excel" enabled="false"/>';
		};
//		echo '<item id="sep6" type="separator"/>';
//		echo '<item id="cari" type="button" img="ic_search.gif" text="Cari"/>';
	};
}
?>