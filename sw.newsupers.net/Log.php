<?php


class Log{

	Private $mysqli;

	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}

	public function addLog($Uid, $Pid, $Seq, $Memo = '') {
$sql = <<<EOT
INSERT
INTO Log(
    `Uid`
  , `Pid`
  , `Seq`
  , `REMOTE_ADDR`
  , `REMOTE_HOST`
  , `HTTP_REFERER`
  , `HTTP_USER_AGENT`
  , `Memo`
)
VALUES (
    ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
  , ?
)

EOT;

	$stmt = $this->mysqli->prepare($sql);

$REMOTE_ADDR = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
//$REMOTE_HOST = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '';
$REMOTE_HOST = gethostbyaddr($REMOTE_ADDR);
$HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$HTTP_USER_AGENT = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

	$stmt->bind_param('ssisssss', $Uid,$Pid,$Seq,$REMOTE_ADDR,$REMOTE_HOST,$HTTP_REFERER,$HTTP_USER_AGENT,$Memo);
//	$stmt->bind_param('ssi', $Uid,$Pid,$Seq);

	$stmt->execute();
	$stmt->close();

	return true;
	}

}
?>