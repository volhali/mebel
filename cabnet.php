<?php require_once ('templates/top.php');
if ($_SESSION['id']) {
	$query = "SELECT * FROM $tbl_user WHERE id =".$_SESSION['id'] and $login = 'login' and $password = 'password';
	$cab = mysql_query($query);
	$cabin = mysql_fetch_array($cab);
	echo $cabin['login'].'<br>'.$cabin['email'];
}else{
	echo "Ошибка входа!";
}
require_once ('templates/bottom.php');?>