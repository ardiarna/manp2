<?php

$root_path = dirname(__FILE__);
require_once ("konfigurasi.php");

session_start();

$conn = pg_connect( "host=$app_host port=$app_port dbname=$app_dbname user=$app_user password=$app_pass");
if(!$conn){
	print("Connection FAILED");
	exit;
}

function dbselect($sql = null){
	global $conn,$app_dbtype;
	if($app_dbtype=="postgres"){
		$res = pg_query($conn,$sql);
		if(pg_num_rows($res)==0){
			$ret = null;
		}else{
			$ret = pg_fetch_array($res); // pg_free_result($res); // pg_close($srv_conn);	
		}
	}else if($app_dbtype=="mysql"){
		$res = mysql_query($sql,$conn);
		if(mysql_num_rows($res)==0){
			$ret = null;
		}else{
			if(($d1 = mysql_fetch_array($res)) != FALSE) {
				$ret = $d1;
			} else {
				$ret = NULL;
			}
		}
		mysql_free_result($res);
	}
	return $ret;
}

function dbsave($sql = null){
	global $conn,$app_dbtype;
	if($app_dbtype=="postgres"){
		$res = pg_query($conn,$sql);
		if($res){
			$ret = "OK";
		}else{
			$ret = $sql.pg_errormessage($conn);
		}
		// pg_free_result($res); // pg_close($conn);
	}else if($app_dbtype=="mysql"){
		$res = mysql_query($sql,$conn);
		if($res){
			$ret = "OK";
		}else{
			$ret = $sql.mysql_error($conn);
		}
		mysql_free_result($res);	
	}
	return $ret;	
}


function createMyExcel($vfilename,$vquery,$vcolumn,$vcoltitle){
	require_once("PHPExcel.php");
	$icell = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$icolumn = explode(",", $vcolumn);
	$icoltitle = explode(",", $vcoltitle);
	// Create new PHPExcel object
	$oexcel = new PHPExcel();
	// Create style for column title
	$coltitleSy = new PHPExcel_Style();
	$coltitleSy->applyFromArray(
		array('fill' 	=> array(
			'type'    	=> PHPExcel_Style_Fill::FILL_SOLID,
			'color'		=> array('argb' => '000000')),
			'font'		=> array(
				'bold' 	=> true,
				'color' => array('rgb' => 'FFFFFF')
			),
	        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		)
	);
	// Set document properties
	$oexcel->getProperties()->setCreator("Ardi Fianto")
							->setLastModifiedBy("Ardi Fianto");
	$si = $oexcel->setActiveSheetIndex(0);
	$baris = 1;
	$row = dbselect_all($vquery);
	// Set Title of Column
	for ($i=0; $i<count($icolumn); $i++) {
		if($icoltitle[$i]){
			$si->setCellValue($icell[$i].$baris,$icoltitle[$i]);	
		}else{
			$si->setCellValue($icell[$i].$baris,$icolumn[$i]);
		}	
	}
	// Set style of colum title 
	$si->setSharedStyle($coltitleSy, $icell[0].$baris.':'.$icell[--$i].$baris);
	
	//Set contain colums and rows
	foreach($row as $r){
		$baris++;
		for ($i=0; $i<count($icolumn); $i++) {
			$si->setCellValue($icell[$i].$baris,$r[$icolumn[$i]]);
		}
	}
	// Rename worksheet
	$si->setTitle('Sheet1');
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$oexcel->setActiveSheetIndex(0);
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename='.$vfilename.'.xlsx');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($oexcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}

function dptKondisiWhere($_search,$filters,$searchField,$searchOper,$searchString){
    $qwery = "";
	if($_search == "true"){
		$ops = array(
			'eq'=>"=",
			'ne'=>"<>",
			'lt'=>"<",
			'le'=>"<=",
			'gt'=>">",
			'ge'=>">=",
			'bw'=>"LIKE",
			'bn'=>"NOT LIKE",
			'in'=>"IN",
			'ni'=>"NOT IN",
			'ew'=>"LIKE",
			'en'=>"NOT LIKE",
			'cn'=>"LIKE" ,
			'nc'=>"NOT LIKE",
			'nu'=>"IS NULL",
			'nn'=>"IS NOT NULL" 
		);
		if($filters){
	        $jsona = json_decode($filters,true);
	        if(is_array($jsona)){
				$groupOp = $jsona['groupOp'];
				$rules = $jsona['rules'];
	            $i = 0;
	            foreach($rules as $key => $val) {
	                $i++;
	                $field = $val['field'];
	                $op = $val['op'];
	                $data = $val['data'];
					$data = toValueSql($op,$data);
					if($i == 1) $qwery = " AND ";
					else $qwery .= " ".$groupOp." ";
					$qwery .= $field." ".$ops[$op]." ".$data;
	            }
	        }
	    }else if($searchString){
	    	$searchString = toValueSql($searchOper,$searchString);
			$qwery = " AND ".$searchField." ".$ops[$searchOper]." ".$searchString;	
	    }
	}
    return $qwery;
}

function toValueSql ($oper, $val) {
	if($oper=='bw' || $oper=='bn') return "'" . addslashes($val) . "%'";
	else if ($oper=='ew' || $oper=='en') return "'%" . addslashes($val) . "'";
	else if ($oper=='cn' || $oper=='nc') return "'%" . addslashes($val) . "%'";
	else if ($oper=='in' || $oper=='ni') return "(" . $val . ")";
	else if ($oper=='nu' || $oper=='nn') return "";
	else return "'" . addslashes($val) . "'";
}

function cgx_emptydate($date) {
    return empty($date) || $date == '0000-00-00';
}

function cgx_dmy2ymd($dmy) {
    $arr = explode("-", $dmy);
    $out = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
    $out = cgx_emptydate($out) || $out == '--' ? '0000-00-00' : $out;
    return $out;
}

function cgx_mmm2m($mmm) {
	$mmm = strtolower($mmm);
	switch ($mmm) {
		case 'jan':
			$out = '1';
			break;
		case 'feb':
			$out = '2';
			break;
		case 'mar':
			$out = '3';
			break;
		case 'apr':
			$out = '4';
			break;
		case 'may':
			$out = '5';
			break;
		case 'jun':
			$out = '6';
			break;
		case 'jul':
			$out = '7';
			break;
		case 'aug':
			$out = '8';
			break;
		case 'sep':
			$out = '9';
			break;
		case 'oct':
			$out = '10';
			break;
		case 'nov':
			$out = '11';
			break;
		case 'dec':
			$out = '12';
			break;
	}
	return $out;
}

function cgx_angka($angka){
	if (is_numeric($angka)) {
		$out = $angka;
	} else {
		$out = 0;
	}
	return $out;
}

function cgx_null($par) {
	if($par) {
		$out = $par;
	} else {
		$out = 'NULL';
	}
	return $out;
}

function login($id, $password) {
    global $app_plan_id, $app_id;
    $sql = "SELECT u.* from app_user u where u.user_name ='{$id}' and u.password='{$password}'"; 
    $r = dbselect($sql);
    if($r){
    	$_SESSION['authenticated'] = 1;
        $_SESSION['user'] = $r['user_name'];
        $_SESSION['full_name'] = $r['first_name']." ".$r['last_name'];
        buat_wo_otomatis();
        return TRUE;
    }else{
        return FALSE;
    }
}

function logout(){
	unset($_SESSION['authenticated']);
	unset($_SESSION['user']);    
}

function authenticated() {
    return $_SESSION['authenticated'] == 1;
}

function cbo_plant($nilai = "TIDAKADA"){
	global $app_plan_id;
	$sql = "SELECT plan_kode, plan_nama from plan order by plan_kode";
	$qry = dbselect_plan_all($app_plan_id, $sql);
	$out .= "<option></option>";
	if(is_array($qry)) {
		foreach($qry as $r){
			if($r[plan_kode] == $nilai){
				$out .= "<option value='{$r[plan_kode]}' selected>$r[plan_nama]</option>";
			} else {
				$out .= "<option value='{$r[plan_kode]}'>$r[plan_nama]</option>";
			}	
		}	
	}
	return $out;
}

function Romawi($angka){
    $hsl = "";
    if($angka<1||$angka>3999){
        $hsl = "Batas Angka 1 s/d 3999";
    }else{
         while($angka>=1000){
             $hsl .= "M";
             $angka -= 1000;
         }
         if($angka>=500){
             if($angka>500){
                 if($angka>=900){
                     $hsl .= "M";
                     $angka-=900;
                 }else{
                     $hsl .= "D";
                     $angka-=500;
                 }
             }
         }
         while($angka>=100){
             if($angka>=400){
                 $hsl .= "CD";
                 $angka-=400;
             }else{
                 $angka-=100;
             }
         }
         if($angka>=50){
             if($angka>=90){
                 $hsl .= "XC";
                  $angka-=90;
             }else{
                $hsl .= "L";
                $angka-=50;
             }
         }
         while($angka>=10){
             if($angka>=40){
                $hsl .= "XL";
                $angka-=40;
             }else{
                $hsl .= "X";
                $angka-=10;
             }
         }
         if($angka>=5){
             if($angka==9){
                 $hsl .= "IX";
                 $angka-=9;
             }else{
                $hsl .= "V";
                $angka-=5;
             }
         }
         while($angka>=1){
             if($angka==4){
                $hsl .= "IV";
                $angka-=4;
             }else{
                $hsl .= "I";
                $angka-=1;
             }
         }
    }
    return ($hsl);
}

function nl2br2($string) {
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
	return $string;
}

function buat_wo_otomatis() {
	global $conn;
	
	$hari_ini_jam = date("Y-m-d 00:00:00");
	$hari_wo = date("Y-m-d", strtotime("+2 week"));
	$tglfrom = date("Y-m-d 00:00:00", strtotime("+2 week"));
	$tglto = date("Y-m-d 23:59:59", strtotime("+2 week"));
	
	$sql0 = "SELECT a.*, b.amm_location, b.amm_sub_location
		from assets_master_maintenance a
		join assets_master_main b on(a.amm_code = b.amm_code)
		where a.amms_next_wo >= '{$tglfrom}' and a.amms_next_wo <= '{$tglto}'
		and lower(a.amms_description) not like '%cek%' and lower(a.amms_description) not like '%kebersihan%' and lower(a.amms_description) not like '%setting%'
		order by a.amm_code, a.amms_code";
	$query0=pg_query($conn, $sql0);
	while($r=pg_fetch_array($query0)) {
		if($r[amm_location] == '02') {
			$sub_plant = '2B';
		} else if($r[amm_location] == '03') {
			$sub_plant = '2C';
		} else {
			$sub_plant = '2A';
		}
		$formatwokode = "WO-".date("y")."-".date("m");
		$thnbln = date("Y-m");
		$sql = "SELECT max(wo_code) as wo_code_max from tbl_wo where to_char(wo_date, 'YYYY-MM') = '{$thnbln}'";
		$query = pg_query($conn, $sql);
		$mx = pg_fetch_array($query);
		if($mx['wo_code_max'] == ''){
			$mx['wo_code_max'] = 0;
		} else {
			$mx['wo_code_max'] = substr($mx['wo_code_max'],-4);
		}
		$urutbaru = $mx['wo_code_max']+1;
		$wo_code = $formatwokode."-".str_pad($urutbaru,4,"0",STR_PAD_LEFT);
		$sql_i = "INSERT INTO tbl_wo (wo_code, wo_date, wo_source, wo_status, wo_urgency, wo_due, wo_desc, wo_type, wo_type_code, wo_asset, wo_location, wo_maintenance, wo_scheduled, wo_duration, wo_unit_duration, wo_instruction, wo_pic_type, wo_pic1, wo_pic2, wo_pic3, sub_plant, user_create, date_create) VALUES ('{$wo_code}', '{$hari_ini_jam}', 'SM', 'S', 'Normal', '{$hari_wo}', '{$r[amms_description]} {$r[amms_part]}', 'Preventive', '01', '{$r[amm_code]}', '{$r[amm_location]}', '{$r[amms_code]}', '{$r[amms_next_wo]}', 0, '', '-', 'I', '', '', '', '{$sub_plant}', 'By Sistem', '{$hari_ini_jam}'); ";
		$sql2 = "SELECT a.*
			from assets_master_maintenance_detail a
			where amm_code = '{$r[amm_code]}' and amms_code = '{$r[amms_code]}'";
		$query2=pg_query($conn, $sql2);
		while($r2=pg_fetch_array($query2)) {
			$sql_i .= "INSERT INTO tbl_wo_detail (wo_code, item_code, item_name, unit, qty) VALUES ('{$wo_code}', '{$r2[item_code]}', '{$r2[item_name]}', '{$r2[unit]}', '{$r2[qty]}'); ";	
		}
		$res = pg_query($conn, $sql_i);
		if($res){
			$intv_cycle = $r[amms_intv_cycle]." ".$r[amms_type_cycle];
			$sql_u = "UPDATE assets_master_maintenance SET amms_next_wo = ('{$r[amms_next_wo]}'::date + INTERVAL '{$intv_cycle}') WHERE amm_code = '{$r[amm_code]}' AND amms_code = '{$r[amms_code]}';";
			$res = pg_query($conn, $sql_u);
		}
	}	
}

?>
