<?php
$db_host = "localhost";
$db_user = "user";
$db_pass = "password";
$db_name = "dbname";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die("DB connect fail");
//mysqli_query($conn, "set names euckr");
mysqli_query($conn, "set names utf8");

function dbquery($sql) {
	global $conn;
	$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)." | ".$sql);
	return $res;
}

function dbfetch($res) {
	$row = mysqli_fetch_array($res);
	return $row;
}

function dbqueryfetch($sql) {
	global $conn;
	$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)." | ".$sql);
	$row = mysqli_fetch_array($res);
	return $row;
}
?>
