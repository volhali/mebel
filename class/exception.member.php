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
  // Обращение к несуществующему члену
  ////////////////////////////////////////////////////////////

  class ExceptionMember extends Exception
  {
    // Имя не существующего члена
    protected $key;

    public function __construct($key, $message)
    {
      $this->key = $key;

      // Вызываем конструктор базового класса
      parent::__construct($message);
    }

    public function getKey()
    {
      return $this->key;
    }
  }
?>
