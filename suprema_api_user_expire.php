<?php
unset($env);
$env['prefix'] = '/home/htdocs';
include($env['prefix']."/inc/func.php");

$url="http://192.168.5.5:8795/v2/login";
$data = array("name"=>"myapi", "user_id"=>"admin", "password"=>"admin_password");
$cookie="cookie.txt";
$postdata = json_encode($data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_REFERER, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$result = curl_exec($ch);
curl_close($ch);

$arr = json_decode($result, true);

$sql = "select * from jimun_api_todo where gbn='3' and procok='0' order by no limit 0,10";
$res = dbquery($sql);
while($row = dbfetch($res)) {
	$now = date('Y-m-d H:i:s');
	if($row['reservfg'] == "1" && $row['reservdt'] > $now) continue;

	$row['name'] = iconv("EUC-KR", "UTF-8", $row['name']);

	$url="http://192.168.5.5:8795/v2/users/".$row['jimunid'];
	$cookie="cookie.txt";

	//"status": "IN"(비활성) / "AC"(활성)
	$frdt = date('Y-m-d')."T07:00:00.00Z";
	$todt = date('Y-m-d')."T".date('H:i:s').".00Z";

	$_gz = explode(",", $row['gatezone']);
	$_cnt = count($_gz);

	unset($str);
	for($i=0;$i<$_cnt;$i++) {
		$_id = get_group_inout($_gz[$i]);
		$str[] = array("id"=>$_id, "included_by_user_group"=>"BOTH");
	}
	$_access_groups = $str;
	$_id2 = get_group_user($row['usrgrp']);
	$_user_group = array("id"=>$_id2);
	$_postdata = array("access_groups"=>$_access_groups, "user_group"=>$_user_group, "name"=>$row['name'], "start_datetime"=>$frdt, "expiry_datetime"=>$todt,
		"security_level"=>"DEFAULT", "status"=>"AC");

	$postdata = json_encode($_postdata);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($ch);
	curl_close($ch);

	$arr = json_decode($result, true);

	if($arr['status_code'] == "SUCCESSFUL") {
		//결과 회신
		$sql2 = "update jimun_api_todo set procok='1', procdt=now() where no='$row[no]'";
		dbquery($sql2);
	}
}

?>
