<? function enter ($name,$password){
	global $tbl_user;
	$query = "SELECT * FROM USERS WHERE login = '$name' AND password = '$password' and blockinblock = 'unblock' LIMIT 1";

	$us =mysql_query($query);
	if (!$us) {
		exit($query);
	}
		if (mysql_num_rows($us)){
			$user = mysql_fetch_array($us);
			$_SESSION['id']= $user['id'];
			$query = "UPDATE $tbl_user SET lastvisit = NOW() WHERE id = ".$user['id'];
			$cat =mysql_query($query);
			if (!$cat) {
				exit($query);
			}
		return true;
	}else{
		return false;
	}

}
