<?php
require_once '../libs/init.php'; 
header('Content-type: text/xml');
echo('<?phpxml version="1.0" encoding="UTF-8"?>');
echo('<menu id="mnuheader">');
echo created_menu("mainmenu",$_SESSION["user"]);
//echo '<item id="asdsadas" type="separator"></item>';
echo '</menu>';

function created_menu($parent_id,$username){
	global $app_conn;
	$sql="SELECT gm_ide AS kode, CASE WHEN (SELECT um_code FROM usr_menu WHERE um_id=gm_ide AND um_code='".$username."') 
	IS NULL THEN 'N' ELSE (SELECT um_code FROM usr_menu WHERE um_id=gm_ide AND um_code='".$username."') 
	END AS akses, (SELECT gmod_access FROM gen_modu WHERE gmod_code=gm_mdl) AS modul, gm_ina AS nama, gm_typ AS tipe, 
	CASE WHEN gm_typ='F' THEN CONCAT('app/',gm_frm,'.php') ELSE 'NoForm' END AS form FROM gen_menu 
	WHERE gm_par='".$parent_id."' ORDER BY gm_num ASC;";
	$query = pg_query($app_conn, $sql);
	//$query = dbselect($sql);
		while($row = pg_fetch_assoc($query)){
//	foreach($query as $row) {
			if($row['tipe']=='F'){
				//if($_SESSION["masa"]=="N"){
				//	echo '<item id="'.$row["kode"].'" text="'. $row["nama"].'" enabled="false">';
				//}else{
					if($row['modul']=='Y' && $row['akses']!='N'){
						echo '<item id="'.$row['kode'].'" text="'. $row['nama'].'"><userdata name="href">'.$row['form'].'</userdata>';
					}else{
						echo '<item id="'.$row['kode'].'" text="'. $row['nama'].'" enabled="false">';
					};
				//};
			};
			if($row['tipe']=='T'){
				echo '<item id="'.$row['kode'].'" text="'. $row['nama'].'">';
			};
			if($row['tipe']=='S'){
				echo '<item id="'.$row['kode'].'" type="separator">';
			};
			created_menu($row['kode'],$username);
			echo '</item>';
		};

};
?>