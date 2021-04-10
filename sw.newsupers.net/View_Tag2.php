<?php
global $PAGE_URL;
global $PAGE_URL_QUERY_ARRAY;

$urlQueryArray = $PAGE_URL_QUERY_ARRAY;


$sql = <<<EOT
SELECT DISTINCT Tag
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

<?php if ($TAG == '広報室') { ?>

<div style="width:100%; margin:0 0 1.5em 0; padding: 0.5em 0.5em; background-color:#006060;">
<!-- Complete the Mission, You're the only one left!<br> -->
ヽ(´ー｀)ノ<br>
</div>

<?php } elseif ($TAG == 'リンク集') { ?>

<div style="width:100%; margin:0 0 1.5em 0; padding: 0.5em 0.5em; background-color:#006060;">
･.+*'+.まだなにもない.+*'+.･<br>
</div>

<?php } else { ?>

<!-- ヽ(´ー｀)ノ -->

<?php } ?>
