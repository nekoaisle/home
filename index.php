<?php
//====================================================================
//====================================================================
//====================================================================
//!
//! @file    index.php
//! @brief   風の伝説ザナドゥ アンケート2014
//! @author  木屋 善夫
//! @date    2014-02-04
//! @version $Revision: $
//!
//! Copyright (C) 2014 Yoshio Kiya  All rights reserved.
//!
//====================================================================
//====================================================================
//====================================================================

//=== インクルード ===================================================
require_once( '../libkya/KyaApp.php' );

//=== クラスファイルのオートロード定義を追加 =========================

//====================================================================
//! main関数
//====================================================================
class CIndex extends CKyaAppHttp
{
	//================================================================
	/**
	 * 構築
	 * @access    public
	*/
	//================================================================
	public function __construct( )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__construct( );

		//============================================================
		$this->AddClassFile( 'top'      );	// トップページ
		$this->AddClassFile( 'login'    );	// ログイン
		$this->AddClassFile( 'logout'   );	// ログアウト

//		これらは iFrame 内なので独立アプリとしました
//		$this->AddClassFile( 'tab'      );	// タブ
//		$this->AddClassFile( 'demo'     );	// colResizable demo
//		$this->AddClassFile( 'ajax'     );	// AJAX
	}

	//================================================================
	/**
	 * 消滅
	 * @access    public
	*/
	//================================================================
	public function __destruct( )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__destruct( );
	}

	//================================================================
	// アトリビュート(定数)
	//================================================================

	//================================================================
	// アトリビュート(変数)
	//================================================================

	//================================================================
	/**
	 * 初期化
	 * @access    public
	 * @retval    FALSE エラー
	*/
	//================================================================
	public function blInit( )
	{
		//=== DB 使用宣言 ============================================
		mixSetConfig( 'USE_DB'   , TRUE );
		mixSetConfig( 'DB_SERVER', 'localhost' );
		mixSetConfig( 'DB_USER'  , 'dsportal'  );
		mixSetConfig( 'DB_PASS'  , 'dsportal'  );
		mixSetConfig( 'DB_NAME'  , 'dsportal'  );

		//=== デフォルトインプリメンテーションの呼び出し =============
		if ( !parent::blInit( ) )
			return FALSE;

		//============================================================
		return TRUE;
	}

	//================================================================
	/**
	 * 処理
	 * @access    public
 	*/
	//================================================================
	public function Run( )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::Run( );
	}
}


new CIndex( );
objGetApp( )->Exec( );
?>