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

$sql = "select * from jimun_api_todo where gbn='4' and procok='0' and reservfg<>'1' order by no limit 0,20";
$res = dbquery($sql);
$j=0;
while($row = dbfetch($res)) {
	$now = date('Y-m-d H:i:s');
	if($row['reservfg'] == "1" && $row['reservdt'] > $now) {
		continue;
	}

	$url="http://192.168.5.5:8795/v2/users/".$row['jimunid'];
	$cookie="cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($ch);
	curl_close($ch);

	$arr = json_decode($result, true);

	if($arr['status_code'] == "SUCCESSFUL") {
		//결과 회신
		$sql2 = "update jimun_api_todo set procok='1', procdt=now() where no='$row[no]'";
		dbquery($sql2);
	} else if($arr['status_code'] == "ACB_ERROR_CODE.201") {
		//결과 회신
		$sql2 = "update jimun_api_todo set procok='-1', procdt=now(), memo='ACB_ERROR_CODE.201 [User not found]' where no='$row[no]'";
		dbquery($sql2);
	}
}

$sql = "select * from jimun_api_todo where gbn='4' and procok='0' and reservfg='1' order by reservdt, no limit 0,20";
$res = dbquery($sql);
$j=0;
while($row = dbfetch($res)) {
	$now = date('Y-m-d H:i:s');
	if($row['reservfg'] == "1" && $row['reservdt'] > $now) {
		continue;
	}

	$url="http://192.168.5.5:8795/v2/users/".$row['jimunid'];
	$cookie="cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($ch);
	curl_close($ch);

	$arr = json_decode($result, true);

	if($arr['status_code'] == "SUCCESSFUL") {
		//결과 회신
		$sql2 = "update jimun_api_todo set procok='1', procdt=now() where no='$row[no]'";
		dbquery($sql2);
	} else if($arr['status_code'] == "ACB_ERROR_CODE.201") {
		//결과 회신
		$sql2 = "update jimun_api_todo set procok='-1', procdt=now(), memo='ACB_ERROR_CODE.201 [User not found]' where no='$row[no]'";
		dbquery($sql2);
	}
}

?>
