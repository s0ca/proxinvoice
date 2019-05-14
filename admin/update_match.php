<?php

include_once "libs/FileSheet.php";
include_once "libs/Database.php";

$match = new FileSheet('match.xls');

$grouped = $match->getRowGroupedByCol(0);

$notSerializedResult = [];
foreach ($grouped as $key => $content) {
	foreach ($content as $ref) {
		$notSerializedResult[$key][] = $ref[1]; 
	}
}

$serializedResult = [];
foreach ($notSerializedResult as $key => $match) {
	$serializedResult[$key] = serialize($match);
}

$config = require ROOT . 'config.php';
$db = new Database($config['db_name'], $config['db_user'], $config['db_passwd'], $config['db_host']);

$db->create_insert_drop("DROP TABLE IF EXISTS `match`");
$db->create_insert_drop("CREATE TABLE `match` (`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT, `french_id` int NOT NULL, `chinese_ids` varchar(65535)) DEFAULT CHARSET=utf8;");

foreach ($serializedResult as $key => $match) {
	$db->create_insert_drop("INSERT INTO `match` (french_id, chinese_ids) VALUES (?, ?);", array($key, $match));
}

unset($db);
