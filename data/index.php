<?php 
session_name('app_caotico');
if (!isset($_SESSION)) {
	session_start();
}
	header("Location:".$_SERVER['HTTP_HOST']);
?>