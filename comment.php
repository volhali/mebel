<? require_once ('templates/top.php');
if ($_GET['id']) {
	if ($_GET['id'] = (int)$_GET['id'])
	$query = "SELECT * FROM $tbl_maintext WHERE id =".$_GET['id'];
	$quer = mysql_query($query);
	$text = mysql_fetch_array($quer);
	
              echo "<p><strong>".$text['name']."</strong></p>";
              echo "<p>".$text['body'];
   if ($_SESSION['id']) {
  	$text = new field_textarea ('text','Текст',true,$_POST['text']);
  	$form = new form(array('text'=>$text),'Добавить','field');
	$form -> print_form($form);
  }else{
	echo "Пользователь не авторизирован!<a href = 'auth.php'>Авторизируетесь</a>";
}
}
if ($_POST) {
	$zapr = "INSERT INTO $tbl_comment values(null,'{$form->fields['text']->value}')";
$zzr= mysql_query($zapr);
if (!$zzr) {
	exit($zapr);}
}

 require_once ('templates/bottom.php');
 ?>