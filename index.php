<?php require_once ('templates/top.php');
if ($_GET['url'] ){
  $file = $_GET['url'];
}else {
  $file = 'index';
}
$query = "SELECT * FROM $tbl_maintext WHERE url = '$file'";
$addr = mysql_query($query);
if (!$addr) {
 exit($query);
}
$text = mysql_fetch_array($addr)//выводит ассоциативный массив

?>

        <h2><?=$text['name'];?></h2>
        <?=$text['body'];?>


<?php require_once ('templates/bottom.php');?>


