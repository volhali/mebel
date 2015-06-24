<?php
  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем SoftTime FrameWork
  require_once("../../config/class.config.dmn.php");

  // Данные переменные определяют название страницы и подсказку.
  $title = 'Управление аккаунтами';
  $pageinfo = '<p class=help>Здесь можно добавить нового 
               пользователя, отредактировать или удалить 
               существующего. Вы не можете узнать пароль
               существующего пользователя, так как он 
               шифруется необратимо, однако вы можете 
               назначить ему новый пароль</p>';
  // Включаем заголовок страницы
  require_once("../utils/top.php");

  try
  {

    // Число ссылок в постраничной навигации
    $page_link = 3;
    // Число позиций на странице
    $pnumber = 10;
    // Объявляем объект постраничной навигации
    $obj = new pager_mysql($tbl_accounts,
                           "",
                           "ORDER BY name",
                           $pnumber,
                           $page_link);
?>
<table width=100% border="0" cellpadding="0" cellspacing="0">
<tr>
<td width=50% class='menu_right'>
<?
    // Добавить аккаунт
    echo "<a href=addaccount.php?page=$_GET[page]
             title='Добавить новый аккаунт'>
			 <img border=0 src='../utils/img/add.gif' align='absmiddle' />
             Добавить аккаунт</a>";
?>
</td>
<td width=50%>
</td>
</tr>
</table>
<?
    // Получаем содержимое текущей страницы
    $accounts = $obj->get_page();
    // Если имеется хотя бы одна запись - выводим
    if(!empty($accounts))
    {
      ?>
      <table width="100%" 
             class="table" 
             border="0" 
             cellpadding="0" 
             cellspacing="0">      
        <tr class="header" align="center">
          <td>Пользователь</td>
          <td>Действия</td>
        </tr>
      <?php
      for($i = 0; $i < count($accounts); $i++)
      {
        // Выводим строку таблицы
        echo "<tr>
                <td align=center>{$accounts[$i][name]}</td>
                <td align=left class='menu_right'>
                  <a href=# 
                     onClick=\"delete_position('".
                    "delaccount.php?page=$_GET[page]&".
                    "id_account={$accounts[$i][id_account]}',".
                    "'Вы действительно хотите удалить аккаунт?');\" 
                    title='Удалить пользователя'>
					<img border=0 src='../utils/img/editdelete.gif' align='absmiddle' />
					Удалить</a></td>
              </tr>";
      }
      echo "</table><br>";
    }
  
    // Выводим ссылки на другие страницы
    echo $obj;
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }

  // Включаем завершение страницы
  require_once("../utils/bottom.php");

echo "";
?>