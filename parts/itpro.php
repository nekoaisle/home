<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	ITPro 専用 RSSリーダー
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname(__FILE__) . '/rss.php' );
class CITPro extends CRss
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
//		return 'http://itpro.nikkeibp.co.jp/rss/news.rdf';
		return 'http://tech.nikkeibp.co.jp/rss/index.rdf';
	}

	//================================================================
	//! 1つ分のアイテム情報を作成
	//! @param  SimpleXMLElement $objItem
	//! @return	array( 
	//!			  'TITLE' => [タイトル  ]
	//!			, 'LINK'  => [リンク先  ]
	//!			, 'DATE'  => [配信日時  ]
	//!			, 'AGO'   => [経過時間  ]
	//!			, 'DESC'  => [記事の内容]
	//!			, 'CLASS' => [既読時に付けるclass属性]
	//!		);
	//================================================================
	public function aryMakeItem( $objItem )
	{
		// デフォルトインプリメンテーションの呼び出し
		$aryRet = parent::aryMakeItem( $objItem );
		if ( empty( $aryRet ) ) {
			return $aryRet;
		}

		// タイトルが '［PR］' で始まるものは無視
		if ( preg_match( '/^［PR］/ius', $aryRet['TITLE'] ) === 1 ) {
			return NULL;
		}

		// 末尾の '（ニュース）' を削除
		$aryRet['TITLE'] = preg_replace( '/（ニュース）$/ius', '', $aryRet['TITLE'] );

		//============================================================
		return $aryRet;
	}
}
?>
