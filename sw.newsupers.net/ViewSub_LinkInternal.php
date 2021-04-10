<?php
global $PAGE_URL;
global $PAGE_URL_QUERY_ARRAY;
global $TAG;

$arr = $PAGE_URL_QUERY_ARRAY;
if(!empty($arr['tag'])) {
	unset($arr['tag']);
}
$urWoTag = $PAGE_URL.'?'.http_build_query($arr);

$arr = $PAGE_URL_QUERY_ARRAY;
$arr['view'] = 'min';
$urlViewMin = $PAGE_URL.'?'.http_build_query($arr);

$arr = $PAGE_URL_QUERY_ARRAY;
$arr['view'] = 'tree';
$urlViewDef = $PAGE_URL.'?'.http_build_query($arr);

$arr = $PAGE_URL_QUERY_ARRAY;
$arr['tag'] = '広報室';
$urlTagInfo = $PAGE_URL.'?'.http_build_query($arr);

$arr = $PAGE_URL_QUERY_ARRAY;
$arr['tag'] = 'リンク集';
$urlTagLink = $PAGE_URL.'?'.http_build_query($arr);

?>
 | <a href="<?php echo $urWoTag; ?>">TOP</a>
 | <a href="<?php echo $urlTagInfo; ?>">広報室</a><?php //require('ViewSub_New.php'); ?>
 | <a href="<?php echo $urlTagLink; ?>">リンク集</a><?php //require('ViewSub_New.php'); ?>
 | <a href="<?php echo $urlViewDef; ?>">ツリー</a>
 | <a href="<?php echo $urlViewMin; ?>">携帯用</a>
 | <a href="<?php echo $PAGE_URL.'feed/rss2.xml'; ?>">RSS</a>
 | <a href="http://sw.nago.me/" target="_blank">なごみ</a>
 | <a href="http://sw.nago.me/up/" target="_blank">あぷろだ</a>
 |