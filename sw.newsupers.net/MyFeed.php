<?php
	require_once('Conf.php');
	require_once('Node.php');
	global $PAGE_TITLE;
	global $PAGE_URL_ABS;
	global $mysqli;

	// ライブラリの読み込み
	require_once "./sub/UniFeedGen/Item.php" ;
	require_once "./sub/UniFeedGen/Feed.php" ;
	require_once "./sub/UniFeedGen/RSS2.php" ;

	// デフォルトのタイムゾーンをセット
	date_default_timezone_set( "Asia/Tokyo" ) ;

	use \FeedWriter\RSS2 ;	// エイリアスの作成
	$feed = new RSS2 ;

	// チャンネル情報の登録
	$feed->setTitle( $PAGE_TITLE ) ;			// チャンネル名
	$feed->setLink( $PAGE_URL_ABS ) ;		// URLアドレス
	$feed->setDescription( $PAGE_TITLE ) ;	// チャンネル紹介テキスト
	//$feed->setImage( "Syncer" , "https://syncer.jp" , "https://syncer.jp/images/DHFgXv5Rfe4d1Lej1lnQfuffZtzsj/assets/logo/490x196.png" ) ;	// ロゴなどの画像
	$feed->setDate( date( DATE_RSS , time() ) ) ;	// フィードの更新時刻
	$feed->setChannelElement( "language" , "ja-JP" ) ;	// 言語
	$feed->setChannelElement( "pubDate" , date( DATE_RSS , time() ) ) ;	// フィードの変更時刻
	$feed->setChannelElement( "category" , "Blog" ) ;	// カテゴリー




	$sql = <<<EOT
SELECT Seq
  FROM Contents
 WHERE Del > 0
 ORDER BY PostDate DESC
 LIMIT 30;
EOT;

	if ($result = $mysqli->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$Node = new Node($mysqli);
			$Node->setBySeqWithTextParent($row['Seq']);
			$disc = $Node->GetTextFeed();
			$urlRes = $Node->urlRes(true, false);

			// インスタンスの作成
			$item = $feed->createNewItem() ;

			// アイテムの情報
			$item->setTitle( preg_replace('/\n/u', '<br />', $Node->Text) ) ;	// タイトル
			$item->setLink( $urlRes ) ;	// リンク
			$item->setDescription( $disc ) ;	// 紹介テキスト
			$item->setDate( $Node->PostDate ) ;	// 更新日時
			//$item->setAuthor( "あらゆ" , "info@syncer.jp" ) ;	// 著者の連絡先(E-mail)
			$item->setId( $urlRes , true ) ;	// 一意のID(第1引数にURLアドレス、第2引数にtrueで通常は大丈夫)



			// アイテムの追加
			$feed->addItem( $item ) ;

		}
	}

	// コードの生成
	$xml = $feed->generateFeed() ;

	// ファイルの保存場所を設定
	$file = "./feed/rss2.xml" ;

	// ファイルの保存を実行
	@file_put_contents( $file , $xml ) ;
?>