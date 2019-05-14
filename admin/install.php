<?php

include_once LIBS . 'Database.php';

$db_host = '';
$db_name = '';
$db_user_name = '';
$db_user_passwd = '';

if (isset($_POST['install_done'])) {
	$db;

	if (isset($_POST['db_host']))
		$db_host = $_POST['db_host'];
	if (isset($_POST['db_name']))
		$db_name = $_POST['db_name'];
	if (isset($_POST['db_user_name']))
		$db_user_name = $_POST['db_user_name'];
	if (isset($_POST['db_user_passwd']))
		$db_user_passwd = $_POST['db_user_passwd'];

	try {
		$db = new Database($db_name, $db_user_name, $db_user_passwd, $db_host);
		unset($db);
		$config = "<?php
			return array(
				'db_host'	=>	'$db_host',
				'db_name'	=>	'$db_name',
				'db_user'	=>	'$db_user_name',
				'db_passwd'	=>	'$db_user_passwd'
			);";
		file_put_contents(ROOT . 'config.php', $config);
		require ROOT . 'admin/update_match.php';
		header('Location :' . ROOT); 
		$msg = 'install success !<br />please reload page.<br />';
		require 'template/success.php';
	} catch (PDOException $e) {
		$error = $e->getMessage(). '.<br />';
		require 'template/error.php';
	}
}

if (!(fileperms(ROOT) & 0002)) {
	$error = "error: chmod project other permissions to be writable for the installation.<br />";
	require 'template/error.php';
} else {
	if (file_exists(ROOT . 'tmp'))
		rmdir(ROOT . 'tmp');
	if (!mkdir(ROOT . 'tmp', 0750)) {
		$error = 'error: failled to create tmp directory.<br />';
		require 'template/error.php';
	}
	require 'template/install.php';
}
