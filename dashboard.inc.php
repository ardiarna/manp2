<?php
require_once 'libs/init.php';
$user=$_SESSION["user"];
$mode=$_GET['mode'];
switch ($mode) {
	case "reqwo";
		reqwo();
	break;
	case "loadchart";
		loadchart();
	break;
	case "dtlchart";
		dtlchart();
	break;
	case "dtlreqblnini";
		dtlreqblnini();
	break;
	case "dtlreqnotappr";
		dtlreqnotappr();
	break;
	case "dtlreqnotsch";
		dtlreqnotsch();
	break;
	case "dtlreqcancel";
		dtlreqcancel();
	break;
	case "dtlwoblnini";
		dtlwoblnini();
	break;
	case "dtlwocomp";
		dtlwocomp();
	break;
	case "dtlwonotcomp";
		dtlwonotcomp();
	break;
	case "dtlwocancel";
		dtlwocancel();
	break;
}

function reqwo() {
	global $conn;

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");
	$bln = ($_GET['bln']) ? $_GET['bln'] : date("n");

	$sql = "SELECT count(r.wr_code) AS jml
		FROM tbl_wr r 
		WHERE r.wr_approve_status = 'W';";
	$query = pg_query($conn, $sql);
	$r = pg_fetch_array($query);
	$responce->req_not_appr = $r[jml];

	$sql2 = "SELECT count(r.wr_code) AS jml
		FROM tbl_wr r
		JOIN tbl_wo w on(r.wr_code = w.wr_code)
		WHERE w.wo_status = 'O';";
	$query2 = pg_query($conn, $sql2);
	$r2 = pg_fetch_array($query2);
	$responce->req_not_sch = $r2[jml];

	$sql3 = "SELECT count(r.wr_code) AS jml
		FROM tbl_wr r 
		WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = {$bln};";
	$query3 = pg_query($conn, $sql3);
	$r3 = pg_fetch_array($query3);
	$responce->req_bln_ini = $r3[jml];

	$sql4 = "SELECT count(r.wr_code) AS jml
		FROM tbl_wr r 
		WHERE r.wr_approve_status = 'X';";
	$query4 = pg_query($conn, $sql4);
	$r4 = pg_fetch_array($query4);
	$responce->req_cancel = $r4[jml];

	$sql5 = "SELECT count(w.wo_code) AS jml
		FROM tbl_wo w 
		WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = {$bln};";
	$query5 = pg_query($conn, $sql5);
	$r5 = pg_fetch_array($query5);
	$responce->wo_bln_ini = $r5[jml];

	$sql6 = "SELECT count(w.wo_code) AS jml
		FROM tbl_wo w 
		WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = {$bln};";
	$query6 = pg_query($conn, $sql6);
	$r6 = pg_fetch_array($query6);
	$responce->wo_comp = $r6[jml];

	$sql7 = "SELECT count(w.wo_code) AS jml
		FROM tbl_wo w 
		WHERE w.wo_status = 'S' AND date_part('year', w.wo_date) = {$thn};";
	$query7 = pg_query($conn, $sql7);
	$r7 = pg_fetch_array($query7);
	$responce->wo_not_comp = $r7[jml];

	$sql8 = "SELECT count(w.wo_code) AS jml
		FROM tbl_wo w 
		WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn};";
	$query8 = pg_query($conn, $sql8);
	$r8 = pg_fetch_array($query8);
	$responce->wo_cancel = $r8[jml];

	$sql9 = "SELECT *
	FROM (
		SELECT 0 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 1
		UNION ALL
		SELECT 1 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 2
		UNION ALL
		SELECT 2 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 3
		UNION ALL
		SELECT 3 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 4
		UNION ALL
		SELECT 4 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 5
		UNION ALL
		SELECT 5 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 6
		UNION ALL
		SELECT 6 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 7
		UNION ALL
		SELECT 7 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 8
		UNION ALL
		SELECT 8 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 9
		UNION ALL
		SELECT 9 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 10
		UNION ALL
		SELECT 10 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 11
		UNION ALL
		SELECT 11 AS bulan, COUNT(r.wr_code) AS jml FROM tbl_wr r WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = 12
	) AS z";
	$query9 = pg_query($conn, $sql9);
	while($r9 = pg_fetch_array($query9)) {
		$arr_req["$r9[bulan]"] = intval($r9[jml]);
	}
	$responce->chart_req[0]['name'] = 'Request';
	$responce->chart_req[0]['color'] = 'red';
	$responce->chart_req[0]['data'] = $arr_req;

	$sql10 = "SELECT *
	FROM (
		SELECT 0 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 1
		UNION ALL
		SELECT 1 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 2
		UNION ALL
		SELECT 2 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 3
		UNION ALL
		SELECT 3 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 4
		UNION ALL
		SELECT 4 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 5
		UNION ALL
		SELECT 5 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 6
		UNION ALL
		SELECT 6 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 7
		UNION ALL
		SELECT 7 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 8
		UNION ALL
		SELECT 8 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 9
		UNION ALL
		SELECT 9 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 10
		UNION ALL
		SELECT 10 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 11
		UNION ALL
		SELECT 11 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 12
	) AS z";
	$query10 = pg_query($conn, $sql10);
	while($r10 = pg_fetch_array($query10)) {
		$arr_wo_all["$r10[bulan]"] = intval($r10[jml]);
	}

	$sql11 = "SELECT *
	FROM (
		SELECT 0 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 1
		UNION ALL
		SELECT 1 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 2
		UNION ALL
		SELECT 2 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 3
		UNION ALL
		SELECT 3 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 4
		UNION ALL
		SELECT 4 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 5
		UNION ALL
		SELECT 5 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 6
		UNION ALL
		SELECT 6 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 7
		UNION ALL
		SELECT 7 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 8
		UNION ALL
		SELECT 8 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 9
		UNION ALL
		SELECT 9 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 10
		UNION ALL
		SELECT 10 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 11
		UNION ALL
		SELECT 11 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 12
	) AS z";
	$query11 = pg_query($conn, $sql11);
	while($r11 = pg_fetch_array($query11)) {
		$arr_wo_comp["$r11[bulan]"] = intval($r11[jml]);
	}

	$sql12 = "SELECT *
	FROM (
		SELECT 0 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 1
		UNION ALL
		SELECT 1 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 2
		UNION ALL
		SELECT 2 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 3
		UNION ALL
		SELECT 3 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 4
		UNION ALL
		SELECT 4 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 5
		UNION ALL
		SELECT 5 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 6
		UNION ALL
		SELECT 6 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 7
		UNION ALL
		SELECT 7 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 8
		UNION ALL
		SELECT 8 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 9
		UNION ALL
		SELECT 9 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 10
		UNION ALL
		SELECT 10 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 11
		UNION ALL
		SELECT 11 AS bulan, COUNT(w.wo_code) AS jml FROM tbl_wo w WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = 12
	) AS z";
	$query12 = pg_query($conn, $sql12);
	while($r12 = pg_fetch_array($query12)) {
		$arr_wo_canc["$r12[bulan]"] = intval($r12[jml]);
	}

	$responce->chart_wo[0]['name'] = 'Work Order';
	$responce->chart_wo[0]['color'] = '#2196F3';
	$responce->chart_wo[0]['data'] = $arr_wo_all;
	$responce->chart_wo[1]['name'] = 'Work Order Completed';
	$responce->chart_wo[1]['color'] = '#31de5f';
	$responce->chart_wo[1]['data'] = $arr_wo_comp;
	$responce->chart_wo[2]['name'] = 'Work Order Cancel';
	$responce->chart_wo[2]['color'] = 'red';
	$responce->chart_wo[2]['data'] = $arr_wo_canc;

	echo json_encode($responce);
}

function dtlreqblnini() {
	global $conn;

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");
	$bln = ($_GET['bln']) ? $_GET['bln'] : date("n");

	$sql = "SELECT r.wr_code, to_char(r.wr_date, 'DD-MM-YYYY') AS wr_date, r.wr_urgency, r.wr_desc, to_char(r.wr_due, 'DD-MM-YYYY') AS wr_due, r.wr_request_by, r.wr_to_department
		FROM tbl_wr r 
		WHERE date_part('year', r.wr_date) = {$thn} AND date_part('month', r.wr_date) = {$bln} ORDER BY r.wr_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>Request#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Due Date</th><th>Request By</th><th>To Department</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wr_code].'</td><td>'.$r[wr_date].'</td><td>'.$r[wr_urgency].'</td><td>'.$r[wr_desc].'</td><td>'.$r[wr_due].'</td><td>'.$r[wr_request_by].'</td><td>'.$r[wr_to_department].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlreqnotappr() {
	global $conn;
	$sql = "SELECT r.wr_code, to_char(r.wr_date, 'DD-MM-YYYY') AS wr_date, r.wr_urgency, r.wr_desc, to_char(r.wr_due, 'DD-MM-YYYY') AS wr_due, r.wr_request_by, r.wr_to_department
		FROM tbl_wr r 
		WHERE r.wr_approve_status = 'W' ORDER BY r.wr_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>Request#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Due Date</th><th>Request By</th><th>To Department</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wr_code].'</td><td>'.$r[wr_date].'</td><td>'.$r[wr_urgency].'</td><td>'.$r[wr_desc].'</td><td>'.$r[wr_due].'</td><td>'.$r[wr_request_by].'</td><td>'.$r[wr_to_department].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlreqnotsch() {
	global $conn;
	$sql = "SELECT r.wr_code, to_char(r.wr_date, 'DD-MM-YYYY') AS wr_date, r.wr_urgency, r.wr_desc, to_char(r.wr_due, 'DD-MM-YYYY') AS wr_due, r.wr_request_by, r.wr_to_department, w.wo_code, to_char(w.wo_date, 'DD-MM-YYYY') AS wo_date
		FROM tbl_wr r
		JOIN tbl_wo w on(r.wr_code = w.wr_code)
		WHERE w.wo_status = 'O' ORDER BY r.wr_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>Request#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Due Date</th><th>Request By</th><th>To Department</th><th>WO#</th><th>WO Date</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wr_code].'</td><td>'.$r[wr_date].'</td><td>'.$r[wr_urgency].'</td><td>'.$r[wr_desc].'</td><td>'.$r[wr_due].'</td><td>'.$r[wr_request_by].'</td><td>'.$r[wr_to_department].'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlreqcancel() {
	global $conn;
	$sql = "SELECT r.wr_code, to_char(r.wr_date, 'DD-MM-YYYY') AS wr_date, r.wr_urgency, r.wr_desc, to_char(r.wr_due, 'DD-MM-YYYY') AS wr_due, r.wr_request_by, r.wr_to_department
		FROM tbl_wr r 
		WHERE r.wr_approve_status = 'X' ORDER BY r.wr_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>Request#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Due Date</th><th>Request By</th><th>To Department</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wr_code].'</td><td>'.$r[wr_date].'</td><td>'.$r[wr_urgency].'</td><td>'.$r[wr_desc].'</td><td>'.$r[wr_due].'</td><td>'.$r[wr_request_by].'</td><td>'.$r[wr_to_department].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlwoblnini() {
	global $conn;
	$arr_status = array("O" => "Open", "S" => "Scheduled", "C" => "Completed", "X" => "Cancel");
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");
	$bln = ($_GET['bln']) ? $_GET['bln'] : date("n");

	$sql = "SELECT w.wo_code, to_char(w.wo_date, 'DD-MM-YYYY') AS wo_date, w.wo_urgency, w.wo_desc, w.wo_source, w.wo_status
		FROM tbl_wo w 
		WHERE date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = {$bln} ORDER BY w.wo_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>WO#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Source</th><th>Status</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[wo_urgency].'</td><td>'.$r[wo_desc].'</td><td>'.$arr_source[$r[wo_source]].'</td><td>'.$arr_status[$r[wo_status]].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlwocomp() {
	global $conn;
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");
	$bln = ($_GET['bln']) ? $_GET['bln'] : date("n");

	$sql = "SELECT w.wo_code, to_char(w.wo_date, 'DD-MM-YYYY') AS wo_date, w.wo_urgency, w.wo_desc, w.wo_source
		FROM tbl_wo w 
		WHERE w.wo_status = 'C' AND date_part('year', w.wo_date) = {$thn} AND date_part('month', w.wo_date) = {$bln} ORDER BY w.wo_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>WO#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Source</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[wo_urgency].'</td><td>'.$r[wo_desc].'</td><td>'.$arr_source[$r[wo_source]].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlwonotcomp() {
	global $conn;
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");

	$sql = "SELECT w.wo_code, to_char(w.wo_date, 'DD-MM-YYYY') AS wo_date, w.wo_urgency, w.wo_desc, w.wo_source
		FROM tbl_wo w 
		WHERE w.wo_status = 'S' AND date_part('year', w.wo_date) = {$thn} ORDER BY w.wo_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>WO#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Source</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[wo_urgency].'</td><td>'.$r[wo_desc].'</td><td>'.$arr_source[$r[wo_source]].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function dtlwocancel() {
	global $conn;
	$arr_source = array("WR" => "Request", "MT" => "Maintenace", "SM" => "Maintenace Schedule");

	$thn = ($_GET['thn']) ? $_GET['thn'] : date("Y");

	$sql = "SELECT w.wo_code, to_char(w.wo_date, 'DD-MM-YYYY') AS wo_date, w.wo_urgency, w.wo_desc, w.wo_source
		FROM tbl_wo w 
		WHERE w.wo_status = 'X' AND date_part('year', w.wo_date) = {$thn} ORDER BY w.wo_code DESC;";
	$query = pg_query($conn, $sql);
	$i = 1;
	$hasil = '<table class="striped"><tr><th>NO</th><th>WO#</th><th>Date</th><th>Urgency</th><th>Description</th><th>Source</th></tr>';
	while($r = pg_fetch_array($query)) {
		$hasil .= '<tr><td>'.$i.'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[wo_urgency].'</td><td>'.$r[wo_desc].'</td><td>'.$arr_source[$r[wo_source]].'</td></tr>';
		$i++;
	}
	$hasil .= '</table>';

	$responce->hasil = $hasil;

	echo json_encode($responce);
}

function loadchart() {
	global $conn;
	$jns = $_GET['jns'];
	$thn = $_GET['thn'];
	$bln = $_GET['bln'];
	if($jns == 'Y') {
		$whsatu = " AND date_part('year', w.wo_real_scheduled_end) = {$thn} ";
		$whdua = " AND date_part('year', d.tanggal) = {$thn} ";
	} else if($jns == 'M') {
		$whsatu = " AND date_part('year', w.wo_real_scheduled_end) = {$thn} AND date_part('month', w.wo_real_scheduled_end) = {$bln} ";
		$whdua = " AND date_part('year', d.tanggal) = {$thn} AND date_part('month', d.tanggal) = {$bln} ";
	}

	$sql = "SELECT nama_asset, SUM(downtime) AS downtime
		FROM (
			SELECT a.amm_desc AS nama_asset,
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu
			UNION ALL
			SELECT a.amm_desc, d.dt_value
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua
		) AS z
		GROUP BY nama_asset
		ORDER BY downtime DESC LIMIT 5";
	$query = pg_query($conn, $sql);
	$i = 0;
	while($r = pg_fetch_array($query)) {
		$responce->data_a[$i]['name'] = $r[nama_asset];
		$responce->data_a[$i]['data'][0] = intval($r[downtime]);
		$i++;
	}

	$sql2 = "SELECT mtc_type, SUM(downtime) AS downtime
		FROM (
			SELECT w.wo_type||' - '||m.smt_description AS mtc_type,
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu
			UNION ALL
			SELECT 'Production' AS mtc_type, d.dt_value
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua
		) AS z
		GROUP BY mtc_type
		ORDER BY downtime DESC LIMIT 5";
	$query2 = pg_query($conn, $sql2);
	$i = 0;
	while($r2 = pg_fetch_array($query2)) {
		$responce->data_b[$i]['name'] = $r2[mtc_type];
		$responce->data_b[$i]['data'][0] = intval($r2[downtime]);
		$i++;
	}

	$sql3 = "SELECT nama_asset, SUM(downtime) AS downtime
		FROM (
			SELECT a.amm_desc AS nama_asset,
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu
		) AS z
		GROUP BY nama_asset
		ORDER BY downtime DESC LIMIT 5";
	$query3 = pg_query($conn, $sql3);
	$i = 0;
	while($r3 = pg_fetch_array($query3)) {
		$responce->data_c[$i]['name'] = $r3[nama_asset];
		$responce->data_c[$i]['data'][0] = intval($r3[downtime]);
		$i++;
	}

	$sql4 = "SELECT nama_asset, SUM(downtime) AS downtime
		FROM (
			SELECT a.amm_desc AS nama_asset, d.dt_value As downtime
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua
		) AS z
		GROUP BY nama_asset
		ORDER BY downtime DESC LIMIT 5";
	$query4 = pg_query($conn, $sql4);
	$i = 0;
	while($r4 = pg_fetch_array($query4)) {
		$responce->data_d[$i]['name'] = $r4[nama_asset];
		$responce->data_d[$i]['data'][0] = intval($r4[downtime]);
		$i++;
	}

	$sql5 = "SELECT w.wo_asset, a.amm_desc AS nama_asset, sum(wd.netcost*wd.qty) AS netcost
		FROM tbl_wo_detail wd
		JOIN tbl_wo w ON(wd.wo_code = w.wo_code)
		JOIN assets_master_main a ON(w.wo_asset = a.amm_code)
		WHERE 1 = 1 $whsatu
		GROUP BY w.wo_asset, a.amm_desc
		HAVING sum(wd.netcost) > 0
		ORDER BY netcost DESC LIMIT 5";
	$query5 = pg_query($conn, $sql5);
	$responce->sql5 = $sql5;
	$i = 0;
	while($r5 = pg_fetch_array($query5)) {
		$responce->data_e[$i]['name'] = $r5[nama_asset];
		$responce->data_e[$i]['data'][0] = intval($r5[netcost]);
		$i++;
	}

	echo json_encode($responce);
}

function dtlchart() {
	global $conn;
	$jns = $_GET['jns'];
	$thn = $_GET['thn'];
	$bln = $_GET['bln'];
	$ktn = $_GET['ktn'];
	$val = $_GET['val'];
	if($jns == 'Y') {
		$whsatu = " AND date_part('year', w.wo_real_scheduled_end) = {$thn} ";
		$whdua = " AND date_part('year', d.tanggal) = {$thn} ";
	} else if($jns == 'M') {
		$whsatu = " AND date_part('year', w.wo_real_scheduled_end) = {$thn} AND date_part('month', w.wo_real_scheduled_end) = {$bln} ";
		$whdua = " AND date_part('year', d.tanggal) = {$thn} AND date_part('month', d.tanggal) = {$bln} ";
	}

	if($ktn == 'kontener1') {
		$sql = "SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, a.amm_desc AS nama_asset, 
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime, 
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_desc, w.wo_type||' - '||m.smt_description AS mtc_type
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			LEFT JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu AND a.amm_desc = '{$val}'
			UNION ALL
			SELECT d.tanggal::text, a.amm_desc, d.dt_value, d.dt_code, '' AS wo_date, dt_desc, 'Production' AS mtc_type
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua AND a.amm_desc = '{$val}'
			ORDER BY tgl";	
	} else if($ktn == 'kontener2') {
		if($val == 'Production') {
			$sql = "SELECT d.tanggal::text AS tgl, a.amm_desc AS nama_asset, d.dt_value AS downtime, d.dt_code AS wo_code, '' AS wo_date, dt_desc AS wo_desc, 'Production' AS mtc_type
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua
			ORDER BY tgl";
		} else {
			$sql = "SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, a.amm_desc AS nama_asset, 
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime, 
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_desc, w.wo_type||' - '||m.smt_description AS mtc_type
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu AND (w.wo_type||' - '||m.smt_description) = '{$val}'
			ORDER BY tgl";
		}
	} else if($ktn == 'kontener3') {
		$sql = "SELECT to_char(w.wo_real_scheduled_end, 'YYYY-MM-DD') AS tgl, a.amm_desc AS nama_asset, 
			CASE 
				WHEN w.wo_real_unit_duration = 'Hours' THEN (w.wo_real_duration*60)
				WHEN w.wo_real_unit_duration = 'Days' THEN (w.wo_real_duration*24*60) 
				ELSE w.wo_real_duration END AS downtime, 
			w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_desc, w.wo_type||' - '||m.smt_description AS mtc_type
			FROM tbl_wo w
			JOIN assets_master_main a ON (w.wo_asset = a.amm_code)
			LEFT JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
			WHERE w.wo_status = 'C' and w.wo_isdowntime = 'Y' $whsatu AND a.amm_desc = '{$val}'
			ORDER BY tgl";
	} else if($ktn == 'kontener4') {
		$sql = "SELECT d.tanggal::text AS tgl, a.amm_desc AS nama_asset, d.dt_value AS downtime, d.dt_code AS wo_code, '' AS wo_date, dt_desc AS wo_desc, 'Production' AS mtc_type
			FROM tbl_downtime d
			JOIN assets_master_main a ON (d.amm_code = a.amm_code)
			WHERE 1 = 1 $whdua AND a.amm_desc = '{$val}'
			ORDER BY tgl";
	} else if($ktn == 'kontener5') {
		$sql = "SELECT nama_asset, wo_code, wo_date, mtc_type, wo_desc, sum(cost) AS cost
			FROM (
				SELECT a.amm_desc AS nama_asset, w.wo_code, to_char(w.wo_date, 'YYYY-MM-DD') AS wo_date, w.wo_type||' - '||m.smt_description AS mtc_type, w.wo_desc, wd.netcost*wd.qty as cost
				FROM tbl_wo_detail wd
				JOIN tbl_wo w ON(wd.wo_code = w.wo_code)
				JOIN assets_master_main a ON(w.wo_asset = a.amm_code)
				LEFT JOIN sett_maintenance_type m ON (w.wo_type = m.smt_work_type AND w.wo_type_code = m.smt_code)
				WHERE 1 = 1 $whsatu AND a.amm_desc = '{$val}'
			) AS z
			GROUP BY nama_asset, wo_code, wo_date, mtc_type, wo_desc
			ORDER BY wo_code";
	}
	
	$query = pg_query($conn, $sql);
	$i = 1;
	if($ktn == 'kontener5') {
		$total = 0;
		$hasil = '<table class="striped"><tr><th>NO</th><th>Asset Name</th><th>Cost</th><th>WO#</th><th>WO Date</th><th>Maintenance Type</th><th>Description</th></tr>';
		while($r = pg_fetch_array($query)) {
			$hasil .= '<tr><td>'.$i.'</td><td>'.$r[nama_asset].'</td><td class="right-align">'.number_format($r[cost], 0).'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[mtc_type].'</td><td>'.$r[wo_desc].'</td></tr>';
			$total += $r[cost];
			$i++;
		}
		$hasil .= '<tr><td colspan="2">TOTAL : </td><td class="right-align">'.number_format($total, 0).'</td><td colspan="4"></td></tr>';
	} else {
		$hasil = '<table class="striped"><tr><th>NO</th><th>Date</th><th>Asset Name</th><th>Downtime</th><th>WO#</th><th>WO Date</th><th>Maintenance Type</th><th>Description</th></tr>';
		$total_downtime = 0;
		while($r = pg_fetch_array($query)) {
			$hasil .= '<tr><td>'.$i.'</td><td>'.$r[tgl].'</td><td>'.$r[nama_asset].'</td><td class="right-align">'.number_format($r[downtime]).'</td><td>'.$r[wo_code].'</td><td>'.$r[wo_date].'</td><td>'.$r[mtc_type].'</td><td>'.$r[wo_desc].'</td></tr>';
			$total_downtime += $r[downtime];
			$i++;
		}
		$hasil .= '<tr><td colspan="2" class="right-align">TOTAL : </td><td colspan="2" class="right-align">'.number_format($total_downtime).'</td><td colspan="4"></td></tr>';
	}
		
	$hasil .= '</table>';

	$responce->hasil = $hasil;
	
	echo json_encode($responce);
	// echo $sql;
}

?>