<?php

$GO = true;
$DEBUG = false;

$PAGE_TITLE = "あやしいわーるど＠上部構造";
$PAGE_TITLE_AT = "＠上部構造";
$PAGE_URL_ABS = "http://sw.newsupers.net/";
//$PAGE_URL = $PAGE_URL_ABS;
$PAGE_URL = "./";
$PAGE_URL_QUERY_ARRAY = array();
$PAGE_URL_WITH_QUERY = $PAGE_URL;
$PAGE_URL_WITH_QUERY_WO_TAG = $PAGE_URL;
$PAGE_SCRIPT_NM = $PAGE_TITLE_AT;
$PAGE_SCRIPT_VER = "Ver20170226";
$PAGE_SCRIPT_URL = $PAGE_URL;
$HTML_INPUT_HIDDEN = '';

$NO_EXEC = false;
$DT_TO = 0;
$DT_FR = 0;
$AT = '';
$TAG = '';
$TAG_PATTERN = <<<EOT
/
(?:^|[^0-9A-Za-z_〃々ぁ-ゖ゛-ゞァ-ヺーヽヾ一-龥０-９Ａ-Ｚａ-ｚｦ-ﾟ]+)
[#＃]
(
		[0-9A-Za-z_〃々ぁ-ゖ゛-ゞァ-ヺーヽヾ一-龥０-９Ａ-Ｚａ-ｚｦ-ﾟ]*
		[A-Za-z〃々ぁ-ゖ゛-ゞァ-ヺーヽヾ一-龥Ａ-Ｚａ-ｚｦ-ﾟ]+
		[0-9A-Za-z_〃々ぁ-ゖ゛-ゞァ-ヺーヽヾ一-龥０-９Ａ-Ｚａ-ｚｦ-ﾟ]*
)
(?![#＃0-9A-Za-z_〃々ぁ-ゖ゛-ゞァ-ヺーヽヾ一-龥０-９Ａ-Ｚａ-ｚｦ-ﾟ]+)
/xu
EOT;

//mysqli
$MMYSQLI_HOST = 'localhost';
$MMYSQLI_USERNAME = 'nss';
$MMYSQLI_PASSWD = '********';
$MMYSQLI_DBNAME = 'nss';





?>