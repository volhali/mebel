<?php require_once ('templates/top.php');?>
<h2>Регистрация</h2>
<?$login = new field_text('login','Логин',true,$_POST['login']);
$password = new field_password('password','Пароль',true,$_POST['password']);
$password2 = new field_password('password2','Повтор пароля',true,$_POST['password2']);
$email = new field_text_email ('email','E-mail',true,$_POST['email']);

$form = new form(array('login'=>$login,'password'=>$password,'password2' => $password2,'email'=>$email),Регистрация,'field');
if ($_POST) {
	$error = $form->check();
	if ($form->fields['password']->value != $form->fields['password2']->value ) {
		$error[] = 'Пароли не совпадают';
	}
	if (empty($error)) {
		$query = "INSERT INTO $tbl_user VALUES (null,
			'{$form->fields['login'] -> value}',
			'{$form->fields['password'] -> value}',
			'unblock',
			NOW(),NOW(),'{$form->fields['email'] ->value}')";
$cut = mysql_query($query);
if (!$cut) {
	exit($query);
}

?>
<script>
document.location.href= 'auth.php';</script>
	<?}
	if (!empty($error)) {
		foreach ($error as $err) {
			echo "<span style = 'color:red'>";
			echo $err;
			echo "</span><br/>";
		}
	}
}
$form -> print_form($form);
?>
<?php require_once ('templates/bottom.php');?>