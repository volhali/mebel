<?php


session_start();

 require_once("../config/config.php");


// если пользователь не авторизован

if (!isset($_SESSION['user_id']))
{
	// то проверяем его куки
	// вдруг там есть логин и пароль к нашему скрипту

	if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
	{
		// если же такие имеются
		// то пробуем авторизовать пользователя по этим логину и паролю
		$login = mysql_escape_string($_COOKIE['login']);
		$password = mysql_escape_string($_COOKIE['password']);

		// и по аналогии с авторизацией через форму:

		// делаем запрос к БД
		// и ищем юзера с таким логином и паролем

		$query = "SELECT `id_account`
					FROM `system_accounts`
					WHERE `name`='{$login}' AND `pass`='{$password}'
					LIMIT 1";
		$sql = mysql_query($query) or die(mysql_error());

		// если такой пользователь нашелся
		if (mysql_num_rows($sql) == 1)
		{
			// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)

			$row = mysql_fetch_assoc($sql);
			$_SESSION['user_id'] = $row['id_account'];

			// не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
		}
	}
}



if (isset($_SESSION['user_id']))
{
	$query = "SELECT `name`
				FROM `system_accounts`
				WHERE `id_account`='{$_SESSION['user_id']}'
				LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	
	// если нету такой записи с пользователем
	// ну вдруг удалили его пока он лазил по сайту.. =)
	// то надо ему убить ID, установленный в сессии, чтобы он был гостем
	if (mysql_num_rows($sql) != 1)
	{
		header('Location: login.php?logout');
		exit;
	}
	
	header('Location: system_accounts/index.php');
	exit;
}
else
{
    header('Location: login.php');
	exit;
}


if (!isset($_SESSION['user_id']))

{
    print 'Для доступа в форму администрирования авторизируйтесь';
	include_once('login.php');

}
else
{
	print '<a href="login.php?logout">Выход</a><br />';
}

print '<p><small>* Для авторизации использовать логин: <b>md5</b> и пароль: <b>password</b></small></p>';


?>