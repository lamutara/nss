<?php

require_once('Conf.php');

//COOKIE
if(isset($_COOKIE['TimeLastView'])) {
	$TIME_LAST_VIEW = $_COOKIE['TimeLastView'];
}
//setcookie('TimeLastView', date('Y-m-d H:i:s', time()), time()+60*60*30);
if (!empty($_POST['post']) and !empty($_POST['text'])){
	;
} else {
	setcookie('TimeLastView', time(), time()+60*60*30);
}

if (!$GO) {
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body bgcolor="#004040" text="#FFFFFF">';
	echo 'メンテナンス中です';
	echo '</body></html>';
	die();
}

if (empty($_REQUEST['at'])) {
	$AT = 'なごみ';
	$PAGE_URL_QUERY_ARRAY['at'] = 'なごみ';
} else {
		if ($_REQUEST['at'] == 'なごみ') {
			//$PAGE_URL_QUERY_ARRAY['root'] = 1;
		} else {
			//$PAGE_URL_QUERY_ARRAY['root'] = 0;
		}
	$AT = $_REQUEST['at'];
}
if (!empty($AT)) {
	preg_match_all($TAG_PATTERN, "#".$AT, $out, PREG_PATTERN_ORDER);
	if (!empty($out[1][0])) {
		$AT = $out[1][0];
	} else {
		$AT = 'なごみ';
	}
}
if (!empty($AT)) {
	$PAGE_URL_QUERY_ARRAY['at'] = $AT;
}

if (!empty($PAGE_URL_QUERY_ARRAY['at'])) {
	if ($PAGE_URL_QUERY_ARRAY['at'] == 'なごみ') {
		$PAGE_TITLE = "あやしいわーるど＠";
	} else {
		$PAGE_TITLE = "＠";
	}
	$PAGE_TITLE = $PAGE_TITLE.htmlspecialchars($PAGE_URL_QUERY_ARRAY['at']);
}



$viewNm = '';
if (!empty($_POST['view'])) {
	$viewNm = $_POST['view'];
} elseif (!empty($_GET['view'])) {
	$viewNm = $_GET['view'];
}
if (!empty($viewNm)) {
	$PAGE_URL_QUERY_ARRAY['view'] = $viewNm;
}

if (!empty($_POST['tag'])) {
	$TAG = $_POST['tag'];
} elseif (!empty($_GET['tag'])) {
	$TAG = $_GET['tag'];
}

if (!empty($TAG)) {
	preg_match_all($TAG_PATTERN, "#".$TAG, $out, PREG_PATTERN_ORDER);
	if (!empty($out[1][0])) {
		$TAG = $out[1][0];
	} else {
		$TAG = '';
	}
}

// if (empty($TAG)) {
// 	if (empty($_REQUEST['root'])) {
// 		$TAG = 'なごみ';
// 	}
// } else if($TAG == 'root') {
// 	$TAG = '';
// }

if (!empty($TAG)) {
	$PAGE_URL_QUERY_ARRAY['tag'] = $TAG;
}

if (!empty($_POST['dtTo'])) {
	$DT_TO = $_POST['dtTo'];
}
if (!empty($_POST['dtFr'])) {
	$DT_FR = $_POST['dtFr'];
}

if (!empty($PAGE_URL_QUERY_ARRAY)) {
	$arr = $PAGE_URL_QUERY_ARRAY;
	$PAGE_URL_WITH_QUERY = $PAGE_URL.'?'.http_build_query($arr);

	$arr = $PAGE_URL_QUERY_ARRAY;
	if(isset($arr['tag'])) {
		unset($arr['tag']);
	}
	$PAGE_URL_WITH_QUERY_WO_TAG = $PAGE_URL.'?'.http_build_query($arr);
}



$HTML_INPUT_HIDDEN = <<<EOT
<input type="hidden" name="at" value="{$AT}">
<input type="hidden" name="tag" value="{$TAG}">
<input type="hidden" name="view" value="{$viewNm}">
EOT;

if (!empty($_GET['res']) and !empty($_GET['seq'])) {
	$HTML_INPUT_HIDDEN .= "<input type=\"hidden\" name=\"seqres\" value=\"{$_GET['seq']}\">";
}
if (!empty($PAGE_URL_QUERY_ARRAY['root'])) {
	$HTML_INPUT_HIDDEN .= '<input type="hidden" name="root" value="1">';
}


$mysqli = new mysqli($MMYSQLI_HOST, $MMYSQLI_USERNAME, $MMYSQLI_PASSWD, $MMYSQLI_DBNAME);

if ($mysqli->connect_error) {
	echo $mysqli->connect_error;
	exit();
} else {
	$mysqli->set_charset("utf8");
}

require_once('Exec.php');

$exec = new Exec($mysqli);
$exec->execAtFirst();

if ($viewNm == '') {
	require_once('View.php');
} elseif ($viewNm == 'tree') {
	require_once('View.php');
} elseif ($viewNm == 'min') {
	require_once('View_min.php');
} elseif ($viewNm == 'kuzuha') {
	require_once('View.php');
} elseif ($viewNm == 'none') {
	;
} else {
	require_once('View.php');
}


?>

