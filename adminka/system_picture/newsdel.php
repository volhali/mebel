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

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем SoftTime FrameWork
  require_once("../../config/class.config.dmn.php");

  // Проверяем параметр id_news, предотвращая SQL-инъекцию
  $_GET['id_news'] = intval($_GET['id_news']);

  try
  {
    // Если новостное сообщение содержит 
    // изображение - удаляем его
    $query = "SELECT * FROM $tbl_news 
              WHERE id_news=$_GET[id_news]";
    $new = mysql_query($query);
    if(!$new)
    {
      throw new ExceptionMySQL(mysql_error(), 
                               $query,
                              "Ошибка удаления
                               новостного блока");
    }
    if(mysql_num_rows($new) > 0)
    {
      $news = mysql_fetch_array($new);
      if(file_exists("../../img/".$news['urlpict']))
      {
        @unlink("../../img/".$news['urlpict']);
      }
    }
    // Формируем и выполянем SQL-запрос 
    // на удаление новостного блока из базы данных
    $query = "DELETE FROM $tbl_news 
              WHERE id_news=$_GET[id_news]
              LIMIT 1";
    if(mysql_query($query))
    {
      header("Location: index.php?page=$_GET[page]");
    }
    else
    {
      throw new ExceptionMySQL(mysql_error(), 
                               $query,
                              "Ошибка удаления
                               новостного блока");
    }
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }
?>
