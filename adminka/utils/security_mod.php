<?php
  ////////////////////////////////////////////////////////////
  // 2005-2008 (C) Кузнецов М.В., Симдянов И.В.
  // PHP. Практика создания Web-сайтов
  // IT-студия SoftTime 
  // http://www.softtime.ru   - портал по Web-программированию
  // http://www.softtime.biz  - коммерческие услуги
  // http://www.softtime.mobi - мобильные проекты
  // http://www.softtime.org  - некоммерческие проекты
  ////////////////////////////////////////////////////////////
  // Выставляем уровень обработки ошибок 
  // (http://www.softtime.ru/info/articlephp.php?id_article=23)
  error_reporting(E_ALL & ~E_NOTICE);

  // Если пользователь не авторизовался - авторизуемся
  if(!isset($_SERVER['PHP_AUTH_USER']) || (!empty($_GET['logout']) && $_SERVER['PHP_AUTH_USER'] == $_GET['logout'])) 
  { 
    Header("WWW-Authenticate: Basic realm=\"Control Page\""); 
    Header("HTTP/1.0 401 Unauthorized"); 
    exit(); 
  } 
  else 
  { 
    // Утюжим переменные $_SERVER['PHP_AUTH_USER'] и $_SERVER['PHP_AUTH_PW'],
    // чтобы мышь не проскочила
    if (!get_magic_quotes_gpc())
    {
      $_SERVER['PHP_AUTH_USER'] = mysql_escape_string($_SERVER['PHP_AUTH_USER']);
      $_SERVER['PHP_AUTH_PW']   = mysql_escape_string($_SERVER['PHP_AUTH_PW']);
    }
    
    $query = "SELECT * FROM $tbl_accounts
              WHERE name = '".$_SERVER['PHP_AUTH_USER']."'";
    $lst = @mysql_query($query); 
    // Если ошибка в SQL-запросе - выдаём окно
    if(!$lst)
    {
      Header("WWW-Authenticate: Basic realm=\"Control Page\""); 
      Header("HTTP/1.0 401 Unauthorized"); 
      exit(); 
    }
    // Если такого пользователя нет - выдаём окно
    if(mysql_num_rows($lst) == 0)
    {
      Header("WWW-Authenticate: Basic realm=\"Control Page\""); 
      Header("HTTP/1.0 401 Unauthorized"); 
      exit(); 
    }
    // Если все проверки пройдены, сравниваем хэши паролей
    $account = @mysql_fetch_array($lst);
    if(md5($_SERVER['PHP_AUTH_PW']) != $account['pass'])
    {
      Header("WWW-Authenticate: Basic realm=\"Control Page\""); 
      Header("HTTP/1.0 401 Unauthorized"); 
      exit(); 
    }
  }
?>