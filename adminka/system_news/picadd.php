	<script type="text/javascript" src="../../texteditor/ckeditor.js"></script>
	<script src="../../texteditor/_samples/sample.js" type="text/javascript"></script>
	<link href="../../texteditor/_samples/sample.css" rel="stylesheet" type="text/css" />
<?php

  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем классы формы
  require_once("../../config/class.config.dmn.php");

  try
  {
    $urlpict   = new field_file("urlpict",
                            "Фото",
                             false,
                             $_FILES,
                            "../../files/photo/");
  
    $form = new form(array(
                           "urlpict" => $urlpict), 
                     "Добавить",
                     "field");

    // Обработчик HTML-формы
    if(!empty($_FILES))
    {
      // Проверяем корректность заполнения HTML-формы
      // и обрабатываем текстовые поля
      $error = $form->check();
      if(empty($error))
      {
        // Изображение
        $var = $form->fields['urlpict']->get_filename();
			if(!empty($var))
			{
			  $picture = date("y_m_d_h_i_").$var;
			}
			else
			{
			  $picture = "";
			}
        // Формируем SQL-запрос на добавление
        // новостного сообщения
        $query = "INSERT INTO $tbl_picture
                  VALUES (NULL,
                          '$picture',
                          NOW())";
        if(!mysql_query($query))
        {
          throw new ExceptionMySQL(mysql_error(), 
                                   $query,
                                  "Ошибка добавления новостного
                                   сообщения");
        }
        // Осуществляем перенаправление
        // на главную страницу администрирования
        ?>
		<script>
		 document.location.href="newsedit.php?id_news=<?php echo $_GET['id_news'] ?>&page=<?php echo $_GET['page'] ?>";
		</script>
		<?
      }
    }
    // Начало страницы
    $title     = 'Добавление новостного сообщения';
    $pageinfo  = '<p class=help></p>';
    // Включаем заголовок страницы
    require_once("../utils/top.php");
?>
<div align=left>
<FORM>
<INPUT class="button" TYPE="button" VALUE="На предыдущую страницу" 
onClick="history.back()">
</FORM> 
</div>
<?
    // Выводим сообщения об ошибках, если они имеются
    if(!empty($error))
    {
      foreach($error as $err)
      {
        echo "<span style=\"color:red\">$err</span><br>";
      }
    }
?>
<div class="table_user">
<?
    // Выводим HTML-форму 
    $form->print_form();
?>
</div>
<?
  }
  catch(ExceptionObject $exc) 
  {
    require("../utils/exception_object.php"); 
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }
  catch(ExceptionMember $exc)
  {
    require("../utils/exception_member.php"); 
  }

  // Включаем завершение страницы
  require_once("../utils/bottom.php");
?>
