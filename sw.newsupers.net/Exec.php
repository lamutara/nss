<?php

require_once('Log.php');

class Exec
{
	Private $mysqli;
	Private $log;

	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
		$this->log = new Log($mysqli);
	}

	public function execAtFirst(){
		global $PAGE_URL_WITH_QUERY;

		require_once('Check.php');

		$ck = new Check();

		if (!empty($_POST['confirm']) or !empty($_GET['confirm'])) {
			session_start();

			if (!isset($_SESSION['checkCnt'])) {
				$_SESSION['checkCnt'] = 0;
			} else {
				$_SESSION['checkCnt']++;
			}
			if (!empty($_SESSION["check_ans"]) and !empty($_POST['check'])) {
				if ($_SESSION["Pid"] == 'Post') {
					$text = $_SESSION['text'];
					$seq = $_SESSION['seq'];
					session_destroy();
					$this->execPost($text, $seq);
				} elseif ($_SESSION["Pid"] == 'flushAll') {

					session_destroy();

					//check
					if ($ck->CheckflushAll()) {
						$this->execFlushAll();
					}
				} elseif ($_SESSION["Pid"] == 'flushOne') {

					session_destroy();
					$seq = $_SESSION['seq'];

					//check
					if ($ck->CheckflushOne($seq)) {
						$this->execFlushOne($seq);
					}
				}
			} elseif ($_SESSION['checkCnt'] >= 3) {
				session_destroy();
				header("Location: ".$PAGE_URL_WITH_QUERY);
				exit;
			} elseif (!empty($_SESSION["Pid"])) {


			?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>確認</title>
<link rel="shortcut icon" href="res/favicon.ico">
</head>
<body text="#FFFFFF" link="#F0FFF0" vlink="#F0FFF0" alink="#FFFFFF" bgcolor="#004040">
<form action="index.php" method="post">
<p>よろしいですか？</p>
<p>　<label for="cb1"><input type="checkbox" name="check" value="1" id="cb1"> Check.</label></p>
<input type="submit" name="confirm" value="続行">
<input type="submit" name="abort" value="破棄">
<?php global $HTML_INPUT_HIDDEN; echo $HTML_INPUT_HIDDEN; ?>
</form>
			<?php
			exit;

			} else {
				header("Location: ".$PAGE_URL_WITH_QUERY);
				exit;
			}

		} elseif (!empty($_POST['abort'])) {
			session_start();
			session_destroy();

		} elseif (!empty($_POST['post']) and !empty($_POST['text'])) {

			$text = $_POST['text'];
			$text = preg_replace('/[ 　\t]+(\r\n)/mu', '\1', $text);
			$text = preg_replace('/(\r\n)+$/mu', '', $text);

			echo $text;
			if (!($ck->CheckPost($text))) {
				session_start();

				$_SESSION["Pid"] = 'Post';
				$_SESSION["post"] = $_POST['post'];
				$_SESSION["text"] = $text;

				//簡単携帯でレスができない件の対応
				if (!empty($_POST['seq'])) {
					$_SESSION["seq"] = $_POST['seq'];
				} elseif (!empty($_POST['seqres'])) {
					$_SESSION["seq"] = $_POST['seqres'];
				} else {
					$_SESSION["seq"] = -1;
				}
				$_SESSION["check_ans"] = '1';

				global $PAGE_URL;
				global $PAGE_URL_QUERY_ARRAY;
				$arr = $PAGE_URL_QUERY_ARRAY;
				$arr["confirm"] = "1";
				header("Location: ".$PAGE_URL.'?'.http_build_query($arr));
				exit;
			}
			$ResSeq = '';

			//簡単携帯でレスができない件の対応
			if (!empty($_POST['seq'])) {
				$ResSeq = $_POST['seq'];
			} elseif (!empty($_POST['seqres'])) {
				$ResSeq = $_POST['seqres'];
			} else {
				$ResSeq = -1;
			}

			$this->execPost($text, $ResSeq);

		} elseif (!empty($_POST['flushAll'])) {

			//強制capthca

			session_start();

			$_SESSION["Pid"] = 'flushAll';
			$_SESSION["check_ans"] = '1';

			global $PAGE_URL;
			global $PAGE_URL_QUERY_ARRAY;
			$arr = $PAGE_URL_QUERY_ARRAY;
			$arr["confirm"] = "1";
			header("Location: ".$PAGE_URL.'?'.http_build_query($arr));
			exit;


		} elseif (!empty($_POST['flushOne']) and !empty($_POST['seq'])) {

			//強制capthca

			session_start();

			$_SESSION["Pid"] = 'flushOne';
			$_SESSION["seq"] = $_POST['seq'];
			$_SESSION["check_ans"] = '1';

			global $PAGE_URL;
			global $PAGE_URL_QUERY_ARRAY;
			$arr = $PAGE_URL_QUERY_ARRAY;
			$arr["confirm"] = "1";
			header("Location: ".$PAGE_URL.'?'.http_build_query($arr));
			exit;

		} elseif (!empty($_GET['like']) and !empty($_GET['seq'])) {

			if (empty($_SERVER['HTTP_REFERER'])) {
				exit;
			}

			$sql = <<<EOT
UPDATE `Contents`
   SET `Like` = `Like` + 1
 WHERE Seq = ?
EOT;

			$stmt = $this->mysqli->prepare($sql);
			$stmt->bind_param('s', $_GET['seq']);

			$stmt->execute();
			$stmt->close();


			header("Location: $PAGE_URL_WITH_QUERY");
			//	if ($this->mysqli->affected_rows > 0) {
			$this->log->addLog('', 'Like', $_GET['seq']);
			//	}
			exit;
		} else {
			global $NO_EXEC;
			$NO_EXEC = True;
		}
	}

	public function execPost($text, $seq){
		global $PAGE_URL_WITH_QUERY;

		$sql = <<<EOT
INSERT INTO `Contents`(
       `Seq`
     , `RootSeq`
     , `ParentSeq`
     , `PostDate`
     , `Text`
     , `Del`)
VALUES (
       (SELECT 1 + MAX FROM (SELECT IFNULL(MAX(Seq), 1) AS MAX FROM Contents) AS A)
     , CASE WHEN ? > 0 THEN (SELECT RootSeq FROM Contents AS B WHERE Seq = ?)
		    ELSE (SELECT 1 + MAX FROM (SELECT IFNULL(MAX(Seq), 1) AS MAX FROM Contents) AS A)
	   END
     , ?
     , NOW()
     , ?
     , 1);
EOT;

		$stmt = $this->mysqli->prepare($sql);

		$stmt->bind_param('iiis', $seq, $seq, $seq, $text);
		$stmt->execute();

		$newSeq = $this->mysqli->insert_id;

		$sql = <<<EOT
INSERT INTO CtTag (Seq, Tag) VALUE (?, ?);
EOT;

		$tags = array();

		global $AT;
		if (!empty($AT)) {
			$tags[] = $AT;
		}
		global $TAG;
		if (!empty($TAG)) {
			$tags[] = $TAG;
		}

		global $TAG_PATTERN;
		preg_match_all($TAG_PATTERN, $text, $out, PREG_PATTERN_ORDER);
		if (is_array($out[1])) {
			$tags = array_merge($tags, $out[1]);
		}

		$tags = array_unique($tags);
		foreach ($tags as $tag) {
			if (empty($tag)) { continue; }
			$stmt = $this->mysqli->prepare($sql);
			$stmt->bind_param('is', $newSeq, $tag);
			$stmt->execute();
		}

		$stmt->close();

		header("Location: $PAGE_URL_WITH_QUERY");

		$this->log->addLog('', 'Post', $newSeq );


		require "./MyFeed.php" ;


		exit;
	}


	public function execFlushAll() {
		global $PAGE_URL_WITH_QUERY;

		$sql = <<<EOT
INSERT INTO `Contents`(
       `Seq`
     , `RootSeq`
     , `ParentSeq`
     , `PostDate`
     , `Text`
     , `Del`)
VALUES (
       (SELECT 1 + MAX FROM (SELECT IFNULL(MAX(Seq), 1) AS MAX FROM Contents) AS A)
     , (SELECT 1 + MAX FROM (SELECT IFNULL(MAX(Seq), 1) AS MAX FROM Contents) AS A)
     , -1
     , NOW()
     , ?
     , 3);
EOT;

		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('s', $text);
		$text = "流されました";

		$stmt->execute();


		$newSeq = $this->mysqli->insert_id;

		global $TAG;
		if (!empty($TAG)) {
			$sql = <<<EOT
INSERT INTO CtTag (Seq, Tag) VALUE (?, ?);
EOT;
			$stmt = $this->mysqli->prepare($sql);
			$stmt->bind_param('is', $newSeq, $TAG);
			$stmt->execute();
		}

		$stmt->close();


		header("Location: $PAGE_URL_WITH_QUERY");

		$this->log->addLog('', 'FlushAll', -1 );

		exit;
	}

	public function execFlushOne($seq) {
		global $PAGE_URL_WITH_QUERY;

		$sql = <<<EOT
UPDATE `Contents`
   SET Del = 2
 WHERE Seq = ?
EOT;

		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('s', $seq);

		$stmt->execute();
		$stmt->close();

		header("Location: $PAGE_URL_WITH_QUERY");
		$this->log->addLog('', 'FlushOne', $_POST['seq'] );
		exit;
	}

}





?>