<?php
$site['title'] = 'Activate user';

$userId = $_GET['id'];
if ($currentUser['isAdmin'] == '1') {
	if ($userId != '') {
		$sql = 'UPDATE users SET enabled = 1 WHERE id = ' . mysql_real_escape_string($userId);
		$db->query($sql);
		$sql = 'SELECT * FROM users WHERE id = ' . mysql_real_escape_string($userId);;
		$user = mysql_fetch_assoc($db->query($sql));
		if ($user['email'] != '') {
			$message = createMessage('You have succesfully activated the following account: ' . $user['email'] . '!', 'confirm');
		} else {
			$errors[] = 'There is no user that matches this id: ' . $userId;
			$message = createMessage(implode('<br />', $errors));
		}
		echo $message;
	} else {
		include(dirname(__FILE__) . '/../common/error404.php');
	}
} else {
	include(dirname(__FILE__) . '/../common/error401.php');
}

?>