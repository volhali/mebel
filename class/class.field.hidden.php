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
  // Скрытое поле hidden
  ////////////////////////////////////////////////////////////

  class field_hidden extends field
  {
    // Конструктор класса
    function __construct($name, 
                   $id_required = false, 
                   $value = "")
    {
      // Вызываем конструктор базового класса field для 
      // инициализации его данных
      parent::__construct($name, 
                   "hidden", 
                   "-", 
                   $id_required, 
                   $value,
                   $parameters, 
                   "",
                   "");
    }
    
    // Метод, для возврата имени названия поля
    // и самого тэга элемента управления
    function get_html()
    {
      $tag = "<input type=\"".$this->type."\" 
                     name=\"".$this->name."\" 
                     value=\"".htmlspecialchars($this->value, ENT_QUOTES)."\">\n";
      return array("", $tag);
    }
    // Метод, проверяющий корректность переданных данных
    function check()
    {
      // Обезопасить текст перед внесением в базу данных
      if (!get_magic_quotes_gpc())
      {
        $this->value = mysql_escape_string($this->value);
      }
      if($this->is_required)
      {
        if(empty($this->value)) return "Скрытое поле не заполнено";
      }

      return "";
    }
  }
?>