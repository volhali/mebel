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
  // Текстовое поле для английского текста
  ////////////////////////////////////////////////////////////

  class field_text_english extends field_text
  {
    // Метод, проверяющий корректность переданных данных
    function check()
    {
      // Обезопасить текст перед внесением в базу данных
      if (!get_magic_quotes_gpc())
      {
        $this->value = mysql_escape_string($this->value);
      }
      if($this->is_required) $pattern = "|^[a-z]+$|i";
      else $pattern = "|^[a-z]*$|i";

      // Проверяем поле value на английский символ
      if(!preg_match($pattern, $this->value))
      {
        return "Поле \"{$this->caption}\" должно содержать только символы латинского алфавита";
      }

      return "";
    }
  }
?>