<?php
require_once '../../../libs/init.php'; 

$user = $_SESSION["user"];
$mode = $_GET['mode'];
switch ($mode) {
	case "urai":
		urai();
	break;
	case "add":
		simpan("add");
	break;
	case "edit":
		simpan("edit");
	break;
	case "hapus":
		hapus();
	break;
	case "loadceklist";
		loadceklist();
	break;
	case "cboasset";
		cboasset();
	break;
	case "cboceklist";
		cboceklist();
	break;
	case "detailtabel":
		detailtabel($_POST['stat']);
	break;
}

function urai(){
	global $conn;
	$page = $_POST['page']; 
	$rows = $_POST['rows']; 
	$sidx = $_POST['sidx']; 
	$sord = $_POST['sord'];
	$tanggal = explode('@', $_GET['tanggal']);
	$tglfrom = cgx_dmy2ymd($tanggal[0]);
	$tglto = cgx_dmy2ymd($tanggal[1]);
	$whsatu = dptKondisiWhere($_POST['_search'],$_POST['filters'],$_POST['searchField'],$_POST['searchOper'],$_POST['searchString']);
	$whdua = "";
	if($_POST['asset_code']) {
		$whdua .= " and asset_code = '".$_POST['asset_code']."'";
	}
	if($_POST['tanggal']) {
		$whdua .= " and to_char(tanggal, 'YYYY-MM-DD')  = '".$_POST['tanggal']."'";
	}
	if($_POST['asset_name']) {
		$whdua .= " and upper(amm_desc) like '%".strtoupper($_POST['asset_name'])."%'";
	}
	if(!$sidx) $sidx = 1;
	$sql = "SELECT count(*) AS count FROM tbl_ceklist
		JOIN assets_master_main ON(tbl_ceklist.asset_code = assets_master_main.amm_code) 
		WHERE tanggal >= '{$tglfrom}' and tanggal <= '{$tglto}' $whsatu $whdua";
	$query = pg_query($conn, $sql);
	$r = pg_fetch_array($query);
	$count = $r['count'];
	if($count > 0) { 
		if($rows == -1){
			$total_pages = 1;
			$limit = "";
		} else {
			$total_pages = ceil($count / $rows);
			$start = $rows * $page - $rows;
			$limit = "limit ".$rows." offset ".$start;
		}
		$sql = "SELECT * FROM tbl_ceklist
			JOIN assets_master_main ON(tbl_ceklist.asset_code = assets_master_main.amm_code) 
			WHERE tanggal >= '{$tglfrom}' and tanggal <= '{$tglto}' $whsatu $whdua
			ORDER BY $sidx $sord $limit";
		$query = pg_query($conn, $sql);
		$i = 0;
	} else { 
		$total_pages = 1; 
	}
	if($page > $total_pages) $page = $total_pages; 
	$responce->page = $page; 
	$responce->total = $total_pages; 
	$responce->records = $count; 
	if($count > 0) {
		while($ro = pg_fetch_array($query)) {
			$id = $ro['asset_code']."@@".$ro['tanggal'];
			$btnView = '<button class="btn btn-default btn-xs" onClick="lihatData(\''.$id.'\')"><span class="glyphicon glyphicon-zoom-in"></span></button> ';
			$btnEdit = '<button class="btn btn-default btn-xs" onClick="editData(\''.$id.'\')"><span class="glyphicon glyphicon-pencil"></span></button> ';
			$btnDel = '<button class="btn btn-default btn-xs" onClick="hapusData(\''.$id.'\')"><span class="glyphicon glyphicon-trash"></span></button> ';
			$ro['kontrol'] = $btnView.$btnEdit.$btnDel;
			$responce->rows[$i]['id']=$id; 
			$responce->rows[$i]['cell']=array($ro['asset_code'],$ro['amm_desc'],$ro['tanggal'],$ro['kontrol']);
			$i++;
		}
	}
	$responce->sql = $sql;
	echo json_encode($responce);
}

function simpan($stat){
	global $conn;
	$r = $_REQUEST;
	$r[tanggal] = cgx_dmy2ymd($r[tanggal]);
	$r[user] = $_SESSION["user"];
	$r[hari_ini] = date("Y-m-d H:i:s");
	if($stat == "add") {
		$sql = "INSERT INTO tbl_ceklist(asset_code, tanggal, ceklist_code, user_create, date_create) VALUES('{$r[asset_code]}', '{$r[tanggal]}', '{$r[ceklist_code]}', '{$r[user]}', '{$r[hari_ini]}'); "; 
	} else if($stat == 'edit') {
		$r[tanggal_lm] = cgx_dmy2ymd($r[tanggal_lm]);
		$r[asset_code] = $r[asset_code_lm];
		$sql = "UPDATE tbl_ceklist SET tanggal = '{$r[tanggal]}', user_modify = '{$r[user]}', date_modify = '{$r[hari_ini]}' WHERE asset_code = '{$r[asset_code_lm]}' AND tanggal = '{$r[tanggal_lm]}'; DELETE FROM tbl_ceklist_detail WHERE asset_code = '{$r[asset_code_lm]}' AND tanggal = '{$r[tanggal_lm]}'; ";
	}
	foreach ($r[cd_code] as $i => $cd_code) {
		$value_lbl = "cd_value_".$cd_code;
		$note_lbl = "cd_note_".$cd_code;
		$uty_lbl = "cd_uty_".$cd_code;
		$cd_value = $_POST[$value_lbl];
		$cd_note = $_POST[$note_lbl];
		$cd_uty = $_POST[$uty_lbl];
		$sql .= "INSERT INTO tbl_ceklist_detail (asset_code, tanggal, cd_code, cd_value, cd_note, cd_uty) values('{$r[asset_code]}', '{$r[tanggal]}', '{$cd_code}', '{$cd_value}', '{$cd_note}', '{$cd_uty}'); ";
	}
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function hapus(){
	global $conn;
	$kode = explode('@@', $_POST['kode']);
	$sql = "DELETE FROM tbl_ceklist WHERE asset_code = '{$kode[0]}' AND tanggal = '{$kode[1]}'; DELETE FROM tbl_ceklist_detail WHERE asset_code = '{$kode[0]}' AND tanggal = '{$kode[1]}';";
	$res = pg_query($conn, $sql);
	if($res){
		$ret = "OK";
	}else{
		$ret = pg_errormessage($conn);
	}
	pg_close();
	echo $ret;
}

function cboasset(){
	global $conn;
	$sql = "SELECT amm_code, amm_code||' - '||amm_desc AS lbl FROM assets_master_main WHERE amm_status = 'Active' ORDER BY amm_desc";
	$query = pg_query($conn, $sql);
	$hasil = '<option value=""></option>';
	while($r = pg_fetch_array($query)) {
		$hasil .='<option value="'.$r[amm_code].'">'.$r[lbl].'</option>';
	}
	echo $hasil;
}

function cboceklist(){
	global $conn;
	$asset_code = $_POST['asset_code'];
	$sql = "SELECT a.ceklist_code, a.ceklist_code||' - '||b.ceklist_name AS lbl 
		FROM sett_ceklist_asset a
		JOIN sett_ceklist b ON(a.ceklist_code = b.ceklist_code) 
		WHERE a.asset_code = '{$asset_code}' ORDER BY b.ceklist_name";
	$query = pg_query($conn, $sql);
	while($r = pg_fetch_array($query)) {
		$hasil .='<option value="'.$r[ceklist_code].'">'.$r[lbl].'</option>';
	}
	echo $hasil;
}

function display_node($ceklist_code, $parent, $padding) {
	global $conn;
	if($parent == '0') {
		$no = 0;
		$no2 = '';
	} else {
		$no = '';
		$no2 = '-';
	}
	
	$sql = "SELECT a.ceklist_code, a.cd_code, a.cd_name, a.cd_parent, a.cd_sort, b.am_count, c.max_sort
		FROM sett_ceklist_detail a 
		LEFT JOIN (SELECT cd_parent, count(*) AS am_count FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) b ON(a.cd_code = b.cd_parent)
		LEFT JOIN (SELECT cd_parent, max(cd_sort) AS max_sort FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) c ON(a.cd_parent = c.cd_parent)
		WHERE a.ceklist_code = '{$ceklist_code}' AND a.cd_parent = {$parent} ORDER BY a.cd_sort";
	$query=pg_query($conn,$sql);
	while($r=pg_fetch_array($query)) {
		if($parent == '0') {
			$no++;
		}
		$out .= '<tr>';
		$out .= '<td class="text-center" style="max-width:200px;">'.$no.'</td>';
		$out .= '<td style="padding-left:'.$padding.'px;"><input type="hidden" name="cd_code[]" value="'.$r[cd_code].'">'.$no2.' '.$r[cd_name].'</td>';
		$out .= '<td class="text-center"><input type="text" class="form-control input-sm" name="cd_uty_'.$r[cd_code].'"></td>';
		$out .= '<td class="text-center"><label class="radio-inline" style="margin-left: 40px;"><input type="radio" name="cd_value_'.$r[cd_code].'" value="V"> V </label><label class="radio-inline" style="margin-left: 40px;"><input type="radio" name="cd_value_'.$r[cd_code].'" value="X"> X </label></td>';
		$out .= '<td class="text-center"><input type="text" class="form-control input-sm" name="cd_note_'.$r[cd_code].'"></td>';
		$out .= '</tr>';
		if ($r[am_count] > 0) {
			$out .= display_node($r[ceklist_code], $r[cd_code], $padding+15);
        }
	}
	return $out;
}

function display_node_nilai($ceklist_code, $parent, $padding, $asset_code, $tanggal) {
	global $conn;
	if($parent == '0') {
		$no = 0;
		$no2 = '';
	} else {
		$no = '';
		$no2 = '-';
	}
	
	$sql = "SELECT a.ceklist_code, a.cd_code, a.cd_name, a.cd_parent, a.cd_sort, b.am_count, c.max_sort
		FROM sett_ceklist_detail a 
		LEFT JOIN (SELECT cd_parent, count(*) AS am_count FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) b ON(a.cd_code = b.cd_parent)
		LEFT JOIN (SELECT cd_parent, max(cd_sort) AS max_sort FROM sett_ceklist_detail WHERE ceklist_code = '{$ceklist_code}' GROUP BY cd_parent) c ON(a.cd_parent = c.cd_parent)
		WHERE a.ceklist_code = '{$ceklist_code}' AND a.cd_parent = {$parent} ORDER BY a.cd_sort";
	$query = pg_query($conn,$sql);
	while($r = pg_fetch_array($query)) {
		$sql2 = "SELECT cd_value, cd_note, cd_uty FROM tbl_ceklist_detail WHERE asset_code = '{$asset_code}' AND tanggal = '{$tanggal}' AND cd_code = '{$r[cd_code]}';";
		$query2 = pg_query($conn, $sql2);
		$r2 = pg_fetch_array($query2);
		if($r2[cd_value] == 'V' ) {
			$checked_v = 'checked';
			$checked_x = '';
		} else if($r2[cd_value] == 'X' ) {
			$checked_x = 'checked';
			$checked_v = '';
		} else {
			$checked_v = '';
			$checked_x = '';
		}
		if($parent == '0') {
			$no++;
		}
		$out .= '<tr>';
		$out .= '<td class="text-center" style="max-width:200px;">'.$no.'</td>';
		$out .= '<td style="padding-left:'.$padding.'px;"><input type="hidden" name="cd_code[]" value="'.$r[cd_code].'">'.$no2.' '.$r[cd_name].'</td>';
		$out .= '<td class="text-center"><input type="text" class="form-control input-sm" name="cd_uty_'.$r[cd_code].'" value="'.$r2[cd_uty].'"></td>';
		$out .= '<td class="text-center"><label class="radio-inline" style="margin-left: 40px;"><input type="radio" name="cd_value_'.$r[cd_code].'" value="V" '.$checked_v.'> V </label><label class="radio-inline" style="margin-left: 40px;"><input type="radio" name="cd_value_'.$r[cd_code].'" value="X" '.$checked_x.'> X </label></td>';
		$out .= '<td class="text-center"><input type="text" class="form-control input-sm" name="cd_note_'.$r[cd_code].'" value="'.$r2[cd_note].'"></td>';
		$out .= '</tr>';
		if ($r[am_count] > 0) {
			$out .= display_node_nilai($r[ceklist_code], $r[cd_code], $padding+15, $asset_code, $tanggal);
        }
	}
	return $out;
}

function loadceklist(){
	global $conn;

	$ceklist_code = $_POST['ceklist_code'];

	$out = '<table class="table table-bordered table-striped table-condensed">';
	$out .= '<tr>';
	$out .= '<th>NO</th>';
	$out .= '<th>NAMA</th>';
	$out .= '<th>KHUSUS UTILITY</th>';
	$out .= '<th>STATUS</th>';
	$out .= '<th>KETERANGAN</th>';
	$out .= '</tr>';	
	$out .= display_node($ceklist_code, 0, 10);
	$out .= "</table>";

	echo $out;
}

function detailtabel($stat) {
	global $conn;
	$kode = explode('@@', $_POST['kode']);
	$sql = "SELECT * FROM tbl_ceklist WHERE asset_code = '{$kode[0]}' AND tanggal = '{$kode[1]}';";
	$query=pg_query($conn,$sql);
	$r = pg_fetch_array($query);

	$out = '<table class="table table-bordered table-striped table-condensed">';
	$out .= '<tr>';
	$out .= '<th>NO</th>';
	$out .= '<th>NAMA</th>';
	$out .= '<th>KHUSUS UTILITY</th>';
	$out .= '<th>STATUS</th>';
	$out .= '<th>KETERANGAN</th>';
	$out .= '</tr>';
	$out .= display_node_nilai($r[ceklist_code], 0, 10, $r[asset_code], $r[tanggal]);
	$out .= "</table>";

	$responce->asset_code = $r[asset_code];
	$responce->tanggal = cgx_dmy2ymd($r[tanggal]);
	$responce->ceklist_code = $r[ceklist_code];
	$responce->detailtabel = $out;
	echo json_encode($responce);
}

?>