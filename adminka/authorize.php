<?php

session_start();

if (!isset($_SESSION['user_id']))

	// показываем защищенные от гостей данные.
{
  exit (header("location: ../index.php"));
  
}


?>