<?php

  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем классы формы
  require_once("../../config/class.config.dmn.php");

  if(empty($_POST))
  {
    // Отмечаем флажок hide
    $_REQUEST['hide'] = true;
  }
  try
  {
    $name        = new field_text("name",
                                  "Название",
                                  true,
                                  $_POST['name']);
	$editor1        = new field_textarea("editor1",
                                  "Содержание",
                                  true,
                                  $_POST['editor1']);
    $hide        = new field_checkbox("hide",
                                      "Отображать",
                                      $_REQUEST['hide']);
  
    $form = new form(array(
	                       "name" => $name, 
                           "editor1" => $editor1,
                           "hide" => $hide), 
                     "Добавить",
                     "field");

    // Обработчик HTML-формы
    if(!empty($_POST))
    {
      // Проверяем корректность заполнения HTML-формы
      // и обрабатываем текстовые поля
      $error = $form->check();
	  if($form->fields['urltext']->value == "-")
	  {
	  $error[] = "Вы не выбрали раздел";
	  }
      if(empty($error))
      {
        // Скрытая или открытая директория
        if($form->fields['hide']->value) $showhide = "show";
        else $showhide = "hide";
        // Формируем SQL-запрос на добавление
        // новостного сообщения
        $query = "INSERT INTO $tbl_news
                  VALUES (NULL,
                          '{$form->fields[name]->value}',
                          '{$form->fields[editor1]->value}',
                          NOW(),
                          'news',
                          'ru',
                          '',
						  '$showhide')";
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
		 document.location.href="index.php";
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
