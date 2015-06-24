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
  // Функция создающая уменьшенную копию фотографии $big,
  // которая помещается в файл $small
  // Уменьшенная копия имеет ширину и высоту равную
  // $width и $height пикселам, соответственно. Это максимально 
  // возможные значения. Они будут пересчитаны чтобы сохранить 
  // пропорции масштабируемого изображения.
  function resizeimg($big, $small, $pos, $width, $height) 
  { 
    // Имя файла с масштабируемым изображением 
    $big = "../../photos/large/$big"; 
    // Имя файла с уменьшенной копией. 
    $small = "../../photos/$pos/$small";     
    // определим коэффициент сжатия изображения, которое будем генерить 
    $ratio = $width / $height; 
    // получим размеры исходного изображения 
    $size_img = getimagesize($big); 
    list($width_src, $height_src) = getimagesize($big); 
    // Если размеры меньше, то масштабирования не нужно 
    if (($width_src<$width) && ($height_src<$height))
    {
      copy($big, $small);
      return true; 
    }



    // создадим пустое изображение по заданным размерам 
    $dest_img = imagecreatetruecolor($width, $height);   
    $white = imagecolorallocate($dest_img, 255, 255, 255);        
    if ($size_img[2]==2)  $src_img = imagecreatefromjpeg($big);                       
    else if ($size_img[2]==1) $src_img = imagecreatefromgif($big);                       
    else if ($size_img[2]==3) $src_img = imagecreatefrompng($big); 

    // масштабируем изображение     функцией imagecopyresampled() 
    // $dest_img - уменьшенная копия 
    // $src_img - исходной изображение 
    // $width - ширина уменьшенной копии 
    // $height - высота уменьшенной копии         
    // $size_img[0] - ширина исходного изображения 
    // $size_img[1] - высота исходного изображения 
    imagecopyresampled($dest_img, 
                       $src_img, 
                       0, 
                       0, 
                       0, 
                       0, 
                       $width, 
                       $height, 
                       $width_src, 
                       $height_src);
    // сохраняем уменьшенную копию в файл 
    if ($size_img[2]==2)  imagejpeg($dest_img, $small);                       
    else if ($size_img[2]==1) imagegif($dest_img, $small);                       
    else if ($size_img[2]==3) imagepng($dest_img, $small); 

		if($pos == 'right')
		{
		$dest = imagecreatefromjpeg($small); 
		$src = imagecreatefrompng("../../photos/right/transporent_right.png");
		imagecopyresampled ($dest,$src,0,0,0,0,$width, $height, $width, $height);
		imagejpeg($dest, $small,80);
		}	
		
		if($pos == 'left')
		{
		$dest = imagecreatefromjpeg($small); 
		$src = imagecreatefrompng("../../photos/left/transporent_left.png");
		imagecopyresampled ($dest,$src,0,0,0,0,$width, $height, $width, $height);
		imagejpeg($dest, $small,80);
		}	
    // чистим память от созданных изображений 
    imagedestroy($dest_img); 
    imagedestroy($src_img); 
    return true;
  }   
?>