<?php
//====================================================================
//====================================================================
//====================================================================
//!
//! @file    Top.php
//! @brief   トップページ
//! @author  木屋 善夫
//! @date    2012-12-02
//! @version $Revision: $
//!
//! Copyright (C) 2012 Yoshio Kiya  All rights reserved.
//!
//====================================================================
//====================================================================
//====================================================================
require_once( dirname( __FILE__ ) . '/config.php' );
require_once( dirname( __FILE__ ) . '/widget.php' );

//====================================================================
// コントローラー
//====================================================================
class CTopCtrl extends CKyaCtrlHttp
{
	//================================================================
	/**
	 * 構築
	 * @access    public
	*/
	//================================================================
	public function __construct( )
	{
		parent::__construct( );

//		mixSetConfig( 'NEED_LOGIN', 1 );
	}

	//================================================================
	/**
	 * 消滅
	 * @access    public
	*/
	//================================================================
	public function __destruct( )
	{
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
		//=== デフォルトインプリメンテーションの呼び出し =============
		if ( !parent::blInit( ) )
			return FALSE;

		//============================================================
		$objApp = objGetApp( );
		$objApp->m_strProgress = '';

		//=== コマンドを追加 =========================================
		$this->RegistCommand( 'LINK_MOD' );
//		$this->RegistCommand( 'TAB_ADD' );
//		$this->RegistCommand( 'TAB_MOD' );
		$this->RegistCommand( 'WIDGET_ADD' );

		//============================================================
		return TRUE;
	}

	//================================================================
	/**
	 * 通常の処理
	 * @access    public
 	*/
	//================================================================
	public function Run( )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::Run( );

		//============================================================
//		$objDB  = objGetDB( );
		$objApp = objGetApp( );
		$objDoc = objGetDoc( );

		//============================================================
		$strUser = $this->strGetUserID( );
		$aryConfig = aryGetConfig( $strUser );

		//============================================================
		$objDoc->m_aryLinkBar = $aryConfig['link_bar'];

		$aryIcon = array( array( 'file' => '', 'img' => '/icon/blank.png' ) );
		foreach ( $this->aryGetIcon( ) as $strFile )
			$aryIcon[] = array( 'file' => $strFile, 'img' => $strFile );
		$objDoc->m_aryIcon = $objDoc->m_aryIcon2 = $aryIcon;

		$objDoc->m_aryTabs = $aryConfig['tabs'];
		$objDoc->m_aryTabs2 = $aryConfig['tabs'];
		$objDoc->m_aryWidget = aryGetWidget( );
	}

	//================================================================
	/**
	 * WIDGET_ADDボタンを押した処理
	 * @access    public
	 * @param     String  $strValue $_REQUEST['SUBMIT'] の値
	 * @return    TRUE    残りのコマンドを処理しない
 	*/
	//================================================================
	public function blOnWidgetAdd( $strValue )
	{
		//============================================================
		$objDoc = objGetDoc( );

		//=== フォームから値を取得 ===================================
		$objDoc->LoadFromRequest( );

		//=== 入力エラーチェック =====================================
		if ( $objDoc->blCheckAllErrorMessage( TRUE ) )
			return TRUE;

		//============================================================
		$iTab    = (int)$objDoc->V_TAB;
		$iWidget = (int)$objDoc->V_WIDGET;

		//=== 現在の設定を読み込む ===================================
		$strUser = $this->strGetUserID( );
		$aryConfig = aryGetConfig( $strUser );

		//=== 選択したWidgetを取得 ===================================
		$aryWidgets = aryGetWidget( );
		$aryWidget  = $aryWidgets[$iWidget];

		//=== WidgetにユニークなIDを設定 =============================
		$iID = iGetConfigMaxID( $aryConfig );
		$aryWidget['id'] = $iID + 1;	// 最大ID + 1

		// 左端の列の先頭に追加する
		array_unshift( $aryConfig['tabs'][$iTab]['cols'][0]['parts'], $aryWidget );

		//============================================================
		SetConfig( $strUser, $aryConfig );

		//============================================================
		return TRUE;
	}

	//================================================================
	/**
	 * LINK_MOD ボタンを押した処理
	 * @access    public
	 * @param     String  $strValue $_REQUEST['SUBMIT'] の値
	 * @return    TRUE    残りのコマンドを処理しない
 	*/
	//================================================================
	public function blOnLinkMod( $strValue )
	{
		//============================================================
		$objDoc = objGetDoc( );

		//=== フォームから値を取得 ===================================
		$objDoc->LoadFromRequest( );

		//=== 入力エラーチェック =====================================
		if ( $objDoc->blCheckAllErrorMessage( TRUE ) )
			return TRUE;

		//============================================================
		$iID      = (int)$objDoc->V_ID;
		$strURL   = $objDoc->V_URL;
		$strTitle = $objDoc->V_TITLE;
		$strIcon  = $objDoc->V_ICON;

		//=== 現在の設定を読み込む ===================================
		$strUser = $this->strGetUserID( );
		$aryConfig = aryGetConfig( $strUser );

		if ( !empty( $iID ) )
		{
			//=== IDが指定されているので変更 =========================
			// 指定IDのリンクを探す
			foreach ( $aryConfig['link_bar'] as &$aryLink )
			{
				if ( $aryLink['id'] === $iID )
				{
					$aryLink['title'] = $strTitle;
					$aryLink['url'  ] = $strURL;
					$aryLink['icon' ] = $strIcon;
					SetConfig( $strUser, $aryConfig );
				}
			}
		}
		else
		{
			//=== IDが指定されていないので末尾に追加 =================
			$iID = iGetConfigMaxLinkID( $aryConfig );
			$iID = $iID + 1;
			$aryConfig['link_bar'][] = array( 
					  'id'    => $iID
					, 'title' => $strTitle
					, 'url'   => $strURL
					, 'icon'  => $strIcon
			);
		}

		//=== 変更を保存 =============================================
		SetConfig( $strUser, $aryConfig );

		//============================================================
		return TRUE;
	}

	//================================================================
	/**
	 * ユーザーIDを取得
	 * @access    public
	 * @param     
	 * @return    String  ユーザーID
 	*/
	//================================================================
	public function strGetUserID( )
	{
		$objApp = objGetApp( );

		if ( $objApp->m_iLoginLevel <= 0 )
			return 'default';

		return $objApp->m_rowUser['V_EMAIL'];
	}

	//================================================================
	/**
	 * アイコンファイル名を取得
	 * @access    public
	 * @return    array( array( 'file' => [アイコンファイル名] ) );
 	*/
	//================================================================
	public function aryGetIcon( )
	{
		$strDir = '/icon';
		$aryDir = array( );

		$objDir = dir( dirname(__FILE__) . $strDir );
		while ( FALSE !== ($strFile = $objDir->read()) )
		{
			if ( $strFile == 'blank.png' )
				continue;

			$strExt = strtoupper( strrchr( $strFile, '.' ) );
			switch ( $strExt )
			{
			case '.PNG':
			case '.JPG':
			case '.GIF':
				$aryDir[] = "{$strDir}/{$strFile}";
				break;
			}
		}
		$objDir->close();

		//============================================================
		return $aryDir;
	}

}

//====================================================================
// ドキュメント
//====================================================================
class CTopDoc extends CKyaDocHttp
{
	//================================================================
	/**
	 * 構築
	 * @access    public
	*/
	//================================================================
	public function __construct( )
	{
		parent::__construct( );

		//=== タイトルの設定 =========================================
		mixSetConfig( 'PAGE_TITLE', 'トップページ' );

		//=== フォーム情報 ===========================================
		$this->m_aryConfig = array(
			  'V_TAB' => array( 
				  'type'    => 'string'
				, 'title'   => 'タブ'
				, 'default' => ''
			)
			, 'V_WIDGET' => array( 
				  'type'    => 'string'
				, 'title'   => 'ウィジェット'
				, 'default' => ''
			)

			//=== LINK_MOD ===========================================
			, 'V_ID' => array( 
				  'type'    => 'string'
				, 'title'   => 'ID'
				, 'default' => ''
			)
			, 'V_ICON' => array( 
				  'type'    => 'string'
				, 'title'   => 'アイコン'
				, 'default' => ''
			)
			, 'V_TITLE' => array( 
				  'type'    => 'string'
				, 'title'   => 'タイトル'
				, 'default' => ''
			)
			, 'V_URL' => array( 
				  'type'    => 'string'
				, 'title'   => 'リンク先'
				, 'default' => ''
			)
		);
	}

	//================================================================
	/**
	 * 消滅
	 * @access    public
	*/
	//================================================================
	public function __destruct( )
	{
		parent::__destruct( );
	}

	//================================================================
	// アトリビュート(定数)
	//================================================================

	//================================================================
	// アトリビュート(変数)
	// ここで定義したメンバー変数はセッションに保存されません。
	//================================================================
	public $m_aryRow     = array( );
	public $m_aryLinkBar = array( );
	public $m_aryIcon    = array( );
	public $m_aryIcon2   = array( );
	public $m_aryTabs    = array( );
	public $m_aryTabs2   = array( );
	public $m_aryWidget  = array( );
	

	//================================================================
	/**
	 * 初期化
	 * @access    public
	 * @retval    FALSE エラー
	*/
	//================================================================
	public function blInit( )
	{
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

//====================================================================
// ヴュー
//====================================================================
class CTopView extends CKyaViewHttp
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
	}

	//================================================================
	/**
	 * 消滅
	 * @access    public
	*/
	//================================================================
	public function __destruct( )
	{
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

?>