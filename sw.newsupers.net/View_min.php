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
-->
</style>
</head>

<body text="#FFFFFF" link="#F0FFF0" vlink="#F0FFF0" alink="#FFFFFF" bgcolor="#004040">

<?php if (!empty($TAG)) {
	require('View_Tag.php');
 } else {
 ?>
	<h1 style="margin-bottom:0;"><a href="<?php echo $PAGE_URL_WITH_QUERY; ?>" style="text-decoration: none; margin-bottom: 0;"><font color="#ffffff"><?php echo $PAGE_TITLE; ?></font></a></h1>
	<p style="margin: 0 0 1em 0;"><small><small>'Complete the Mission, You're the only one left!'</small></small></p>
<?php } ?>

<div>



<form action="index.php" method="post">

<textarea name="text" cols="70" rows="3" style="font-size:1em; vertical-align: top;"></textarea><br>

<input type="submit" name="post" value="　投 稿　">


<?php echo $HTML_INPUT_HIDDEN; ?>
</div>

<small>
<?php require('ViewSub_LinkInternal.php'); ?>
</small>

<hr>

<div>

<?php

require_once('Node.php');

if (!empty($_GET['seq']) and !empty($_GET['res'])) {
	if (!empty($_GET['seq'])) {
			$Node = new Node($mysqli);
			$Node->setBySeqWithTextParent($_GET['seq']);
			echo $Node->GetText();
			//echo '<hr size="1">';
	}
} else {

	If (!empty($TAG)) {
		$sql = <<<EOT

SELECT A.Seq
  FROM Contents A
 WHERE EXISTS (
       SELECT *
	     FROM CtTag B
        WHERE B.Seq = A.Seq
		  AND B.Tag = ?
	   )
 ORDER BY A.PostDate DESC
 LIMIT 30;

EOT;
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $TAG);
		$stmt->execute();

	} else {
	$sql = <<<EOT
SELECT Seq
  FROM Contents
 ORDER BY PostDate DESC
 LIMIT 30;
EOT;
		$stmt = $mysqli->prepare($sql);
		$stmt->execute();
	}

	$stmt->bind_result($rootSeq);

	$rootSeqs = array();
	while ($stmt->fetch()) {
		$rootSeqs[] = $rootSeq;
	}
	$stmt->close();

	foreach($rootSeqs as $rootSeq) {
		$Node = new Node($mysqli);
		$Node->setBySeqWithTextParent($rootSeq);
		echo $Node->GetText();
		echo '<hr size="1">';
		//echo "<br>";
	}

}

echo '</div>';
echo '<hr>';

?>

<div style="text-align:right">
<font size="-1">
<a href="<?php echo $PAGE_SCRIPT_URL; ?>"><?php echo $PAGE_SCRIPT_NM.' '.$PAGE_SCRIPT_VER; ?></a>
</font>
</div>

</form>
</body>
</html>