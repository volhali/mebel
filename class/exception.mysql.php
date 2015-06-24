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

  ////////////////////////////////////////////////////////////
  // Ошибки обращения к СУБД MySQL
  ////////////////////////////////////////////////////////////

  class ExceptionMySQL extends Exception
  {
    // Сообщение об ошибке
    protected $mysql_error;
    // SQL-запрос
    protected $sql_query;

    public function __construct($mysql_error, $sql_query, $message)
    {
      $this->mysql_error = $mysql_error;
      $this->sql_query = $sql_query;

      // Вызываем конструктор базового класса
      parent::__construct($message);
    }

    public function getMySQLError()
    {
      return $this->mysql_error;
    }
    public function getSQLQuery()
    {
      return $this->sql_query;
    }
  }
?>
