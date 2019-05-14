<?php

$error = '';
if ((fileperms(ROOT) & 0002)) {
	$error .= "error: chmod project other permissions to be unwritable.<br />";
}
if (!file_exists(ROOT . 'tmp')) {
	$error .= "error: project/tmp doesn't exists. please run again install (remove config.php).<br />";
} else {
	$uid = fileowner(ROOT . 'tmp');
	$gid = filegroup(ROOT . 'tmp');
	$uidInfo = posix_getpwuid($uid);
	$gidInfo = posix_getgrgid($gid);
	if ($uidInfo['name'] !== 'www-data'
		&& $gidInfo['name'] !== 'www-data'
		&& !(fileperms(ROOT) . 'tmp' & 0002)) {
		$error .= "error: chmod project/tmp other permissions to be writable for file upload.<br />";
	}
}
require 'template/error.php';

function loadPostedFile(array $postedFiles, array $fileExtention, $fileMaxSizeB, $uploadFolder) {
	$errorMsg = '';
	$tmpFilesNames = [];
	$tmpFilesExts = [];
	$rslFilesNames = [];
	$maxSizeKb = $fileMaxSizeB / 1024.0;

	foreach ($postedFiles as $fileId => $file) {
		if (filesize(($file['tmp_name'])) > $fileMaxSizeB) {
			$errorMsg .= "Error: $fileId file too big. (max: $maxSizeMb Kb)" . PHP_EOL;
		}
		$ext = substr(strrchr($file['name'], '.'), 1);
		if (!$file['error'] && ($ext === false || !in_array($ext, $fileExtention))) {
			$errorMsg .= "Error: $fileId file bad extention." . PHP_EOL;
		}
		else if ($file['error']) {
			$errorMsg .= "Error: $fileId loding failed." . PHP_EOL;
		}
		if ($errorMsg === '') {
			$tmpFilesNames[$fileId] = $file['tmp_name'];
			$tmpFilesExts[$fileId] = $ext;
		}
	}
	if ($errorMsg !== '')
		throw new Exception($errorMsg);
	foreach ($tmpFilesNames as $fileId => $file) {
		$fileName = $uploadFolder . '/' . basename($file) . '.' . $tmpFilesExts[$fileId];
		if (move_uploaded_file($file, $fileName))
			$rslFilesNames{$fileId} = $fileName;
	}
	return ($rslFilesNames);
}

function loadToXml($files) {
	$xmlFiles = [];
	foreach ($files as $fId => $file) {
		$xmlFiles[$fId] = new FileSheet($file);
		unlink($file);
	}
	return $xmlFiles;
}

function loadMatchFromDb() {
	$match = [];
	$config = require ROOT . 'config.php';
	$db = new Database($config['db_name'], $config['db_user'], $config['db_passwd'], $config['db_host']);
	$rawMatch = $db->query("SELECT french_id, chinese_ids FROM `match`", NULL);
	foreach ($rawMatch as $m) {
		$match[$m[0]] = unserialize($m[1]);
	}
	return $match;
}

function hashByCase($xmlFiles) {
	$rsl = [];
	$groupByCaseFR = $xmlFiles['FR']->getRowGroupedByCol(0);
	$groupByCaseCN = $xmlFiles['CN']->getRowGroupedByCol(1);

	foreach ($groupByCaseFR as $case => $refsFR) {
		foreach ($refsFR as $ref) {
			if (isset($rsl[$case]['FR']) && array_key_exists($ref[1], $rsl[$case]['FR']))
				$rsl[$case]['FR'][$ref[1]] += $ref[3];
			else
				$rsl[$case]['FR'][$ref[1]] = $ref[3];
			if (array_key_exists($case, $groupByCaseCN)) {
				foreach ($groupByCaseCN[$case] as $ref) {
					if (isset($rsl[$case]['CN']) && array_key_exists($ref[4], $rsl[$case]['CN']))
						$rsl[$case]['CN'][$ref[4]] += $ref[6];
					else
						$rsl[$case]['CN'][$ref[4]] = $ref[6];
				}
				unset($groupByCaseCN[$case]);
			}
		}
	}
	foreach ($groupByCaseCN as $case => $refsCN) {
		foreach ($refsCN as $ref) {
			if (isset($rsl[$case]['CN']) && array_key_exists($ref[4], $rsl[$case]['CN']))
				$rsl[$case]['CN'][$ref[4]] += $ref[6];
			else
				$rsl[$case]['CN'][$ref[4]] = $ref[6];
			$rsl[$case]['FR']['No FR case'] = 0;
		}
	}
	return $rsl;
}

function diffCase($hashByCase, $match) {
	$rsl = [];

	foreach ($hashByCase as $case => $data) {
		$FR_data = $data['FR'];
		$CN_data = $data['CN'];
		foreach ($FR_data as $FR_ref => $FR_quantity) {
			$CN_matchedRefs = [];
			$rsl[$case]['diff'] = 0;
			$rsl[$case]['FR'][$FR_ref] = $FR_quantity;
			if (array_key_exists($FR_ref, $match))
				$CN_matchedRefs = $match[$FR_ref];
			foreach ($CN_matchedRefs as $needed_CN_ref) {
				if (array_key_exists($needed_CN_ref, $CN_data)) {
					$rsl[$case]['CN'][$needed_CN_ref] = $CN_data[$needed_CN_ref] - $FR_data[$FR_ref];
					unset($CN_data[$needed_CN_ref]);
				} else {
					$rsl[$case]['CN'][$needed_CN_ref] = 'Not Found';
				}
			}
			foreach ($CN_data as $CN_ref => $CN_quantity) {
				$rsl[$case]['CN'][$CN_ref] = $CN_quantity;
			}
		}
	}
	foreach ($rsl as $case => $data) {
		foreach ($data['FR'] as $ref => $quantity) {
			if (!$quantity)
				$rsl[$case]['diff'] = 1;
		}
		foreach ($data['CN'] as $ref => $quantity) {
			if ($quantity <> 0)
				$rsl[$case]['diff'] = 1;
		}
	}
	return $rsl;
}

$content['maxFileSize'] = 1024 * 1024;
ini_set('upload_max_filesize', $content['maxFileSize']);
ini_set('max_file_uploads', 2);

require 'template/upload.php';
include_once 'libs/FileSheet.php';
include_once 'libs/Database.php';

if (isset($_POST['upload'])) {
	clearstatcache();
	$extentions = array ('xls');
	if (isset($_FILES)) {
		try {
			$files = loadPostedFile($_FILES, array('xls'), $content['maxFileSize'], 'tmp');
			$files = loadToXml($files);
			$match = loadMatchFromDb();
			$hashByCase = hashByCase($files);
			$diff = diffCase($hashByCase, $match);

			$content['match'] = $match;
			$content['diff'] = $diff;
		} catch (Exception $e) {
			$error = '<pre>' . $e->getMessage() . '</pre>' . PHP_EOL;
			require 'template/error.php';
		}
	}
	require 'template/diff.php';
}
