<?php require_once ('templates/top.php');
require_once ('utils/utils.users.php');
$login = new field_text('login','Логин',true,$_POST['login']);
$password = new field_password('password','Пароль',true,$_POST['password']);
$form = new form(array('login'=>$login,'password'=>$password),Авторизация,'field');
$form -> print_form($form);

 if ($_POST) {
    if (!$error) {
      enter($form->fields['login']->value,$form->fields['password']->value);?>
      <script>document.location.href='cabnet.php'</script>
  <?  } 
    if ($error) {
      foreach ($error as $err) {
       echo "<span style = 'color:red'>";
       echo $err;
       echo "</span>";

      }
    }

  } 

?>
<?php require_once ('templates/bottom.php');?>