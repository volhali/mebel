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

  // Скрываем новость
  try
  {
    $query = "UPDATE $tbl_news SET hide='hide' 
              WHERE id_news=".$_GET['id_news'];
    if(mysql_query($query))
    {
        ?>
		<script>
		 document.location.href="index.php?page=<?php echo $_GET['page'] ?>";
		</script>
		<?php
      header("Location: index.php?page=$_GET[page]");
    }
    else
    {
      throw new ExceptionMySQL(mysql_error(), 
                               $query,
                              "Ошибка при обращении 
                               к блоку новостей");
    }
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }
?>