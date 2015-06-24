<?php 
		$db_location = 'localhost';
       $dbname = 'nomenklatura';
       $dbuser = 'root';
       $dbpassword = '';
       $tbl_maintext = 'maintexts';
       $dbx = mysql_connect($db_location,$dbuser,$dbpassword);//подключение к БД
       if (!$dbx) {
       	exit ("Error of db connection");
       }
       $dbuse = mysql_select_db($dbname,$dbx);//установка подключения 
       if (!$dbuse) {
       exit('Ошибка выбора БД');
       }
       @mysql_query("SET NAMES 'utf-8'");//функция не критична для выполнения,устанавливаем кодировку.
//k50ijseries пароль
       //volhali логин


