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

  require_once("../../class/class.field.php");
  require_once("../../class/class.field.text.php");
  require_once("../../class/class.field.text.english.php");
  require_once("../../class/class.field.text.int.php");
  require_once("../../class/class.field.text.email.php");
  require_once("../../class/class.field.password.php");
  require_once("../../class/class.field.textarea.php");
  require_once("../../class/class.field.hidden.php");
  require_once("../../class/class.field.hidden.int.php");
  require_once("../../class/class.field.radio.php");
  require_once("../../class/class.field.select.php");
  require_once("../../class/class.field.checkbox.php");
  require_once("../../class/class.field.file.php");
  require_once("../../class/class.field.date.php");
  require_once("../../class/class.field.datetime.php");
  require_once("../../class/class.field.paragraph.php");
  require_once("../../class/class.field.title.php");

  require_once("../../class/class.forms.php");

  require_once("../../class/exception.member.php");
  require_once("../../class/exception.mysql.php");
  require_once("../../class/exception.object.php");

  require_once("../../class/class.pager.php");
  require_once("../../class/class.pager_abstract.php");
  require_once("../../class/class.pager_dir.php");
  require_once("../../class/class.pager_file.php");
  require_once("../../class/class.pager_file_search.php");
  require_once("../../class/class.pager_mysql.php");
?>