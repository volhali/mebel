<?session_start();
require_once('config/config.php'); 
require_once('config/class.config.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Простой шаблон сайта</title>
<meta name="description" content="Готовый шаблон сайта на CSS, HTML, Java. Блочная верстка. Комментарии в коде. Скачать бесплатно" />
<meta name="keywords" content="простой, шаблон, css, html, java, скачать" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/easySlider1.5.js"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function () {
    $("#slider").easySlider({
        controlsBefore: '<p id="controls">',
        controlsAfter: '</p>',
        auto: true,
        continuous: true
    });
});
</script>
</head>
<body>
<!-- Начало страницы -->
<div class="main">
  <div class="header">
    <div class="block_header">
<!-- Логотип -->
      <div class="logo"><a href="main.html"><img src="images/538914.jpeg" width="200" height="130" border="0" alt="" /></a></div>
<!-- Поиск -->
      <div><?if ($_SESSION['id']) {?>
        <a href = 'logout.php'>Выход</a>
     <?}else{?>
      <a href = 'reg.php'> Регистрация</a>
      <a href = 'auth.php'>Авторизация</a>
      <?}?>
     
      </div>
             <div class="search">
      <form id="form1" name="form1" method="post" action="#">
		<br /><font color="#FFFFFF"><b>Поиск по сайту</b></font><br /> 
           <label><span>
            <input name="q" type="text" class="keywords" id="textfield" maxlength="50" value="" />
            </span>
            <input name="b" type="image" src="images/search.gif" class="button" />
          </label>
        </form>
      </div>
<!-- Конец поиск -->
<!-- Меню -->
      <div class="menu">
        <ul>
          <li><a href="index.php?url=index" <?=($_GET['url']=='index')? "class = 'active'":"";?>><span>Главная</span></a></li>
          <li><a href="index.php?url=about" <?=($_GET['url']=='about')? "class = 'active'":"";?>><span>Наш проект</span></a></li>
          <li><a href="index.php?url=services"<?=($_GET['url']=='services')? "class = 'active'":"";?>><span>Наши услуги</span></a></li>
          <li><a href="#"><span>Портфолио</span></a></li>
          <li><a href="index.php?url=contacts"<?=($_GET['url']=='contacts')? "class = 'active'":"";?>><span>Контакты</span></a></li>
        </ul>
      </div>
      <div class="clr"></div>                                                                                                                                                                                                                                                                    <a href="http://sitey.ru" target="_blank"><img border="0" src="htp://sitey.ru/template_logo/prosto_logo1x1.jpg" width="5" height="5" align="right"></a>
    </div>
  </div>
<!-- Под меню -->
<div class="slider_top">
    <div class="header_text2"> <a href="#"><img src="images/prosmotr.png" alt="" border="0" /></a>
      <h2>О компании </h2>
      <div class="clr"></div>
    </div>
  </div>
  <div class="top_bg2">
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <div class="body">
    <div class="body_resize">
      <div class="left">