<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $PAGE_TITLE; ?></title>
<link rel="shortcut icon" href="res/favicon.ico">
<style type="text/css">
<!--
html {
	overflow: auto;
}
body {
	overflow: hidden;
}
.new, .new a {
	color: #aaccff;
}
.dark a {
	color: #408080;
	text-decoration: none;
}
.dark .l1 {
	color: #60a0a0;
	text-decoration: none;
}
.dark .l2 {
	color: #80c0c0;
	text-decoration: none;
}
.dark a:hover {
	color: #ffffff;
	text-decoration: none;
}
-->
</style>
</head>

<body text="#FFFFFF" link="#F0FFF0" vlink="#F0FFF0" alink="#FFFFFF" bgcolor="#004040">

<?php if (!empty($TAG)) {
	require('View_Tag.php');
	require('View_Tag2.php');
 } else {
 ?>
	<h1 style="margin-bottom:0;"><a href="<?php echo $PAGE_URL_WITH_QUERY; ?>" style="text-decoration: none; margin-bottom: 0;"><font color="#ffffff"><?php echo $PAGE_TITLE; ?></font></a></h1>
<!-- <p style="margin: 0 0 1em 0;"><small><small>'Complete the Mission, You're the only one left!'</small></small></p> -->


<?php
global $PAGE_URL;
global $PAGE_URL_QUERY_ARRAY;

$urlQueryArray = $PAGE_URL_QUERY_ARRAY;


$sql = <<<EOT
SELECT Tag
     , COUNT(*) Cnt
  FROM CtTag
 GROUP BY Tag
 ORDER BY MAX(Seq) DESC
     , COUNT(*) DESC
EOT;

echo '<p class="dark" style="margin: 0 0 1em 0;"><font size="-2">';
if ($result = $mysqli->query($sql)) {
	// 連想配列を取得
	while ($row = $result->fetch_assoc()) {
		$tag = $row['Tag'];
		$urlQueryArray["tag"] = $tag;
		$url = $PAGE_URL.'?'.http_build_query($urlQueryArray);
		if ($row['Cnt'] > 10) {
			$cls = ' class="l1"';
		} elseif ($row['Cnt'] > 50) {
			$cls = ' class="l2"';
		} else {
			$cls = ' ';
		}
		echo ' <a href="'.$url.'" '.$cls.'>#'.htmlspecialchars($row['Tag']).' ('.($row['Cnt']).')</a>';
	}
	// 結果セットを閉じる
	$result->close();
}
echo '</font></p>';

?>

<?php } ?>


<?php

if (!empty($_POST['prevPage']) AND !empty($DT_FR)) {
	$DT_TO = $DT_FR;
} else {
	$DT_TO = '9999-12-31 23:59:59';
/*
	$sql = <<<EOT
SELECT NOW() PostDate FROM DUAL;
EOT;
	if ($result = $mysqli->query($sql)) {
		if ($row = $result->fetch_assoc()) {
			$DT_TO = $row['PostDate'];
		}
		$result->close();
	}
*/
}

$sql = <<<EOT
SELECT IFNULL(MAX(A.PostDate), 0) PostDate
  FROM Contents A
 WHERE (A.Del = 3 OR A.Del = -3)
   AND A.PostDate < ?
   AND (
          (? <> '' AND EXISTS ( SELECT * FROM CtTag B
                               WHERE B.Seq = A.Seq
                                 AND B.Tag = ?
                       )
          )
       OR
          (?  = '' AND NOT EXISTS ( SELECT * FROM CtTag B
                                     WHERE B.Seq = A.Seq
                           )
          )
       )
EOT;

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ssss', $DT_TO, $TAG, $TAG, $TAG);
$stmt->execute();
$stmt->bind_result($DT_FR);
$stmt->fetch();
$stmt->close();




?>

<div style="margin-left:1em;">

<form action="index.php" method="post">

<input type="radio" name="seq" value="-1"
<?php
if (!empty($_GET['res'])) {
	echo 'disabled';
} else {
	echo 'checked';
}
?>
 style="margin:0; vertical-align: middle;">
<textarea name="text" cols="80" rows="3" style="font-size:1em; vertical-align: top;"></textarea><br>
<div style="margin:0.1em 1.2em;">
<input type="submit" name="post" value="　　投 稿　　">
　<input type="submit" name="flushAll" value="総流し">
<input type="submit" name="flushOne" value="個流し">
</div>

</div>

<small>
<?php require('ViewSub_LinkInternal.php'); ?>
 <small>リンク</small>
<?php require('ViewSub_LinkExternal.php'); ?>
</small>
<hr>

<?php
//新規投稿件数
global $TIME_LAST_VIEW;
global $NO_EXEC;
if (isset($TIME_LAST_VIEW)) {

$sql = <<<EOT
SELECT COUNT(*) CNT
  FROM Contents A
 WHERE A.PostDate > ?
EOT;
	$timeLastView = date('Y-m-d H:i:s', $TIME_LAST_VIEW);
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('s', $timeLastView);
	$stmt->execute();

	$stmt->bind_result($cntNew);
	$stmt->fetch();
	$stmt->close();


	if ($cntNew > 0) {
		echo "　${cntNew}<small> 件の新しい投稿があります</small>";
		echo "<hr>";
	} elseif (empty($TAG) And $NO_EXEC And empty($_GET['res']) And $DT_TO == '9999-12-31 23:59:59') {
//		echo "<small>　新しい投稿はありません</small>";
//		echo "<hr>";

		$sql = <<<EOT
SELECT T.Seq
  FROM CtTag T
 INNER JOIN Contents C
    ON T.Seq = C.Seq
 WHERE T.Tag = '名言'
   AND C.ParentSeq = '-1'
   AND C.Del >= -1
 ORDER BY rand()
 LIMIT 1
;
EOT;
		$stmt = $mysqli->prepare($sql);
		$stmt->execute();

		$stmt->bind_result($seq);
		$stmt->fetch();
		$stmt->close();

		require_once('Node.php');
		if (!empty($seq)) {
			$Node = new Node($mysqli);
			$Node->setBySeq($seq);
			echo '<tt>'.$Node->GetStrLineQuote().'</tt>';
		}
		echo "<hr>";

// 		//仮
// 		echo "<br>";
// 		$pts = rand(1, 9);
// 		while (rand(1, 20) == 1) {
// 			$pts += 10;
// 		}
// 		echo "<small><small>　　　ボーナスポイント： </small></small><b>$pts</b>";
// 		echo "<hr>";
	}
}
?>


<div style="margin:0 0 1em 1em;">

<?php

require_once('Node.php');

if (!empty($_GET['res'])) {
	if (!empty($_GET['seq'])) {
		$Node = new Node($mysqli);
		$Node->setBySeq($_GET['seq']);
		echo '<tt>'.$Node->GetStrLine(True).'</tt>';
	}
} else {

	$sql = <<<EOT
SELECT A.RootSeq
  FROM Contents A
 WHERE EXISTS ( SELECT * FROM Contents B
                 WHERE B.RootSeq = A.RootSeq
                   AND (? = '' OR EXISTS ( SELECT * FROM CtTag C
                                            WHERE C.Seq = B.Seq
                                              AND C.Tag = ?
                                  )
                       )
       )
 GROUP BY A.RootSeq
HAVING MAX(A.PostDate) >= ?
   AND MAX(A.PostDate) < ?
 ORDER BY MAX(A.PostDate) DESC
 LIMIT 60
EOT;
//HAVING MAX(A.PostDate) BETWEEN ? AND ?
	global $DT_FR;
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('ssss', $TAG, $TAG, $DT_FR, $DT_TO);
	$stmt->execute();

	$stmt->bind_result($rootSeq);

	$rootSeqs = array();
	while ($stmt->fetch()) {
		$rootSeqs[] = $rootSeq;
	}
	$stmt->close();

	foreach($rootSeqs as $rootSeq) {
		$Node = new Node($mysqli);
		$Node->setBySeq($rootSeq);
		echo '<tt>'.$Node->GetSubTree().'</tt>';
		//echo '<hr size="1">';
		//echo "<br>";
		$DT_FR = $Node->PostDate;
	}

	$HTML_INPUT_HIDDEN .= <<<EOT
<input type="hidden" name="dtTo" value="{$DT_TO}">
<input type="hidden" name="dtFr" value="{$DT_FR}">
EOT;
}

echo '</div>';
echo "\n";
echo '<hr>';


if (empty($DT_FR)) {
	//echo '<hr><input type="submit" name="prevPage" value="次のページ" disabled>';
} else {
	echo '<input type="submit" name="prevPage" value="次のページ">';
}



if (!empty($_GET['res'])) {
	;
} else {


	$sql = <<<EOT
SELECT IFNULL(COUNT(*), 0) Cnt
  FROM (

SELECT A.RootSeq
  FROM Contents A
 WHERE EXISTS ( SELECT * FROM Contents B
                 WHERE B.RootSeq = A.RootSeq
                   AND (? = '' OR EXISTS ( SELECT * FROM CtTag C
                                            WHERE C.Seq = B.Seq
                                              AND C.Tag = ?
                                  )
                       )
       )
 GROUP BY A.RootSeq
HAVING MAX(A.PostDate) < ?
 ORDER BY MAX(A.PostDate) DESC

       ) AA
  LEFT JOIN Contents BB
    ON BB.RootSeq = AA.RootSeq
;
EOT;

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('sss', $TAG, $TAG, $DT_FR);
	$stmt->execute();
	$stmt->bind_result($cnt);
	$stmt->fetch();
	$stmt->close();

	echo '<font size="-1" color="#408080">';
	echo " 以下{$cnt}件 ";
	echo '</font>';

	echo '<p style="overflow-wrap : break-word; margin: 0.5em 0">';
	echo '<font size="-2" color="#408080">';
	$sql = <<<EOT
SELECT BB.Text
  FROM (

SELECT A.RootSeq
  FROM Contents A
 WHERE EXISTS ( SELECT * FROM Contents B
                 WHERE B.RootSeq = A.RootSeq
                   AND (? = '' OR EXISTS ( SELECT * FROM CtTag C
                                            WHERE C.Seq = B.Seq
                                              AND C.Tag = ?
                                  )
                       )
       )
 GROUP BY A.RootSeq
HAVING MAX(A.PostDate) < ?
 ORDER BY MAX(A.PostDate) DESC

       ) AA
  LEFT JOIN Contents BB
    ON BB.RootSeq = AA.RootSeq
 ORDER BY BB.PostDate DESC
 LIMIT 30
;
EOT;

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('sss', $TAG, $TAG, $DT_FR);
	$stmt->execute();
	$stmt->bind_result($text);

	$flg = false;
	while ($stmt->fetch()) {
		echo ' '.htmlspecialchars($text);
	}
	$stmt->close();

	if ($cnt > 50) {
		echo ' …';
	}
}

?>

<div style="text-align:right">
<font size="-1">
<a href="<?php echo $PAGE_SCRIPT_URL; ?>"><?php echo $PAGE_SCRIPT_NM.' '.$PAGE_SCRIPT_VER; ?></a>
</font>
</div>

<?php echo $HTML_INPUT_HIDDEN; ?>
</form>
</body>
</html>