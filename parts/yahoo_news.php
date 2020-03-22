<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	Yahoo NEWS 専用 RSSリーダー
//
//	専用設定
//	'max' => [最大表示記事数]
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname(__FILE__) . '/rss.php' );
class CYahooNews extends CRss
{
	//================================================================
	// コンストラクタ
	//================================================================
	public function __construct( $strUser, $aryConfig )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__construct( $strUser, $aryConfig );
	}

	//================================================================
	// URLを取得
	//================================================================
	public function mixGetFeedURL( )
	{
		return array(
//			  'トピックス' => 'http://rss.dailynews.yahoo.co.jp/fc/rss.xml'
			  '国内'       => 'http://headlines.yahoo.co.jp/rss/all-dom.xml'
			, '国際'       => 'http://headlines.yahoo.co.jp/rss/all-c_int.xml'
			, '経済'       => 'http://headlines.yahoo.co.jp/rss/all-bus.xml'
			, 'エンタメ'   => 'http://headlines.yahoo.co.jp/rss/all-c_ent.xml'
			, 'スポーツ'   => 'http://headlines.yahoo.co.jp/rss/all-c_spo.xml'
			, 'IT・科学'   => 'http://headlines.yahoo.co.jp/rss/all-c_sci.xml'
			, 'ライフ'     => 'http://headlines.yahoo.co.jp/rss/all-c_life.xml'
		);
	}
}
?>