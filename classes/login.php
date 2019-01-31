<!DOCTYPE html>

<?php
	$db = DB::getInstance();
	$db->get('users', array('username', '=', Input::get("username")));
	if ($db->count())
	{
		if ($db->first()->username->'password' == Input::get('password'))
			echo 'success';
		else
			echo 'wrong password';
	}
	else
		echo 'Username does not exist';
?>

<html>
	<div>
		<form action="" method="post">
			<div>
				<label for 'username'>Username:</label>
				<input type='text' name='username' value=Input::get('username') id='username' />
			</div>
			<div>
				<label for 'password'>Password:</label>
				<input type='password' name='password' id='username' />
			</div>
		</form>
	</div>
</html>
