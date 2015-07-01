<?php

  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем классы формы
  require_once("../../config/class.config.dmn.php");

  require_once("../utils/utils.resizeimg.php");

  if(empty($_POST))
  {

    // Отмечаем флажок hide
    $_REQUEST['showhide'] = true;
  }
  try
  {
    $cats = "SELECT * FROM $tbl_categories";
    $cat = mysql_query($cats);
    if (!$cat) {
      exit($cats);
    }
    $arr = array();
    while ($cc = mysql_fetch_array($cat)) {
      $arr[$cc['id']]= $cc['name'];
    }

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
     $urlpict        = new field_file("urlpict",
                                      "Изображение",false,$_FILES,'../../media/images/');
     $razdel = new field_select('razdel','Категория',$arr,$_POST['razdel']);

  
    $form = new form(array(
	                       "name" => $name, 
                           "editor1" => $editor1,
                           "hide" => $hide, 
                              "urlpict" => $urlpict,
                              "razdel"=>$razdel),
                     "Добавить",
                     "field");

    // Обработчик HTML-формы
    if(!empty($_POST))
    {
      $error = $form->check();
      if (empty($error)) {
          if ($firm->fields['showhide']->value) {
            $showhide = 'show';
          }else{
            $showhide = 'hide';
          }
        $var = $form->fields['urlpict']->get_filename();
        if ($var) {
          $picture = date('y_m_d_h_i_').$var;
          $picture_small ='S_'.$picture;
          resizeimg("../../media/images/".$picture,"../../media/images/".$picture_small,200,200);

        }else{
          $picture= "";
          $picture_small = "";
           }
           $query = "INSERT INTO $_tbl_picture VALUES (NULL,'{$form->fields['name']->value}','{$form->fields['editor1']->value}','$picture','$picture_small',NOW(),'$showhide','{$form->fields['razdel']->value}')";
          $q = mysql_query($query);
          if (!$q) {
            exit($query);
          }
        ?>
        <script>
document.location.href = 'index.php';
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
