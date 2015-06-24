<?php

  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем SoftTime FrameWork
  require_once("../../config/class.config.dmn.php");

  // Проверяем GET-параметр, предотвращая SQL-инъекцию
  $_GET['id_account'] = intval($_GET['id_account']);
  if ($_GET['page'])
  {
  $_GET['page'] = intval($_GET['page']);
  }
  else
  {
  $_GET['page'] = 0;
  }

  try
  {
    // Проверяем не удаляется ли последний аккаунт -
    // если он будет удалён в систему нельзя будет войти
    $query = "SELECT COUNT(*) FROM $tbl_accounts";
    $acc = mysql_query($query);
    if(!$acc)
    {
      throw new ExceptionMySQL(mysql_error(), 
                               $query,
                              "Ошибка удаления
                               пользователя");
    }
    if(mysql_result($acc, 0) > 1)
    {
      $query = "DELETE FROM $tbl_accounts 
                WHERE id_account=".$_GET['id_account'];
      if(mysql_query($query))
      {
        ?>
		<script>
		 document.location.href="index.php?page=<?php echo $_GET['page'] ?>";
		</script>
		<?php
      }
      else
      {
        throw new ExceptionMySQL(mysql_error(), 
                                 $query,
                                "Ошибка удаления
                                 пользователя");
      }
    }
    else
    {
      throw new Exception("Нельзя удалить 
                           единственный аккаунт");
    }
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }
  catch(Exception $exc)
  {
    require("../utils/exception.php"); 
  }
?>