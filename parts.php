<?php
//====================================================================
//====================================================================
//====================================================================
//!
//! @file    parts.php
//! @brief   ブログパーツ出力制御
//! @author  木屋 善夫
//! @date    2014-03-08
//! @version $Revision: $
//!
//! Copyright (C) 2014 Yoshio Kiya  All rights reserved.
//!
//====================================================================
//====================================================================
//====================================================================

//=== インクルード ===================================================
require_once( '../libkya/KyaApp.php' );
require_once( './config.php' );

//====================================================================
//! main関数
//====================================================================
class CPartsApp extends CKyaAppHttp
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
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__destruct( );
	}

	//================================================================
	// アトリビュート(定数)
	//================================================================

	//================================================================
	// アトリビュート(変数)
	//================================================================
	public $m_aryParts = array( );

	// <a>タブをクリック時の処理入れる
	public $m_blAonClick = TRUE;

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
		//=== ユーザー名の取得 =======================================
		$this->m_strUser = 'default';
		if ( !empty( $_SESSION['CIndex.m_iLoginLevel'] ) )
			$this->m_strUser = $_SESSION['CIndex.m_rowUser']['V_EMAIL'];

		//=== IDを取得 ===============================================
		// Widget-IDををクエリーに格納している
		// RewriteRule ^parts/(.*)\.html$ /parts.php?NAME=$1 [L]
		if ( empty ( $_REQUEST['NAME'] ) )
		{
			echo "Widget-ID が取得できませんでした。";
			return;
		}
		$this->m_strID = $_REQUEST['NAME'];

		//=== 設定の読み込み =========================================
		$this->m_aryConfig = aryGetConfig( $this->m_strUser );

		//=== Config からこのファイル名のパーツを検索 ================
		foreach ( $this->m_aryConfig['tabs'] as $iTab => $aryTab )
		{
			foreach ( $aryTab['cols'] as $iCol => $aryCol )
			{
				foreach ( $aryCol['parts'] as $iRow => $aryParts )
				{
					if ( $aryParts['id'] == $this->m_strID )
					{
						$this->m_aryParts = $aryParts;
						break 3;
					}
				}
			}
		}

		if ( empty( $this->m_aryConfig['tabs'][$iTab]['cols'][$iCol]['parts'][$iRow] ) )
		{
			echo "Widget が見つかりませんでした。'{$this->m_strID}'";
			return;
		}
		$this->m_aryParts = &$this->m_aryConfig['tabs'][$iTab]['cols'][$iCol]['parts'][$iRow];

		//=== ファイル名のベース名取得 ===============================
		// 設定ファイルの type がベースファイル名
		$this->m_strType  = $this->m_aryParts['type'];
		$this->m_strHTML  = "./parts/{$this->m_strType}.html";
		$this->m_strPHP   = "./parts/{$this->m_strType}.php";
		$this->m_strClass = 'C' . strToCamelcase( $this->m_strType );

		//============================================================
		if ( array_key_exists( 'PREFERENCE', $_REQUEST ) )
		{
			//=== 設定変更画面 =======================================
			$aryRep = $this->aryMakePreference( );
		}
		else if ( array_key_exists( 'SUBMIT', $_REQUEST ) )
		{
			//=== 設定変更処理 =======================================
			$this->OnSubmit( );

			//=== リダイレクト例外を発行して処理を中断 ===============
			throw new CKyaExceptionRedirect( "/parts/{$this->m_strID}.html" );
		}
		else
		{
			//=== 設定変更処理 =======================================
			$aryRep = $this->aryMakeNormal( );
		}

		//=== 置換 ===================================================
		$strHTML = str_replace( 
			  array_keys( $aryRep )
			, array_values( $aryRep )
			, file_get_contents( './parts/frame.html' )
		);

		//============================================================
		echo $strHTML;
	}

	//================================================================
	/**
	 * ログファイル名に付加するサフィックスを作成
	 * @access    public
	 * @return string サフィックス
 	*/
	//================================================================
	public function strCreateLogNameSuffix( )
	{
		$strFile = basename($_SERVER['SCRIPT_NAME'], '.php' );
		$strName = $_REQUEST['NAME'];

		return "{$strFile}-{$strName}";
	}

	//================================================================
	/**
	 * 設定変更画面
	 * @access public
	 * @return array 置換用データー
 	*/
	//================================================================
	public function aryMakePreference( )
	{
		//============================================================
		$aryRep = array(
			  '/* script */'  => ''
			, '/* ready */'   => ''
			, '/* style */'   => ''
			, '<!-- head -->' => ''
			, '<!-- body -->' => ''
			, 'body-attr'     => ''
		);

		//=== 設定画面用スタイル =====================================
		// CSS に入れたほうが良いのだがキャッシングされて確認しにくいの
		// でHTMLに含める
		$aryRep['/* style */'] = <<<_EOL_
div.pref {
	margin: 4px;
}

div.pref table {
	width: 100%;
	font-size: 0.875em;
/*	background-color: #FFFFFF;*/
/*	border: 1px #CCCCCC solid;*/
/*  border-collapse: separate; でないと効かない
	border-radius: 6px; */
}

div.pref table th {
	text-align: left;
}

div.pref input[type="submit"] {
	width: 128px;
}
div.pref textarea {
	width: 97%;
}

div.pref>div {
	text-align: center;
	margin: 4px;
}
_EOL_;

		//=== 設定画面は高さの自動調整必須 ===========================
		$aryRep['/* ready */'] = <<<_EOL_
	// load完了時にiframeの高さを調整
	$(window).load( function(){
		$.dspAutoHeight.iframe();
	});

_EOL_;

		//========================================================
		$aryParts = $this->m_aryParts;
		unset( $aryParts['id'] );	// id は変更できない
		unset( $aryParts['type'] );	// type は変更できない

		//=== タイプごとの設定 ===================================
		switch ( $this->m_strType )
		{
		case 'html_editor':
			break;
		}

		//=== タイプは変更できない ===============================
		_S(); ?>
<tr>
	<th>type</th>
	<td>{$a}</td>
</tr>
<?php
		$strRow = _S( $this->m_strType );

		//=== 共通設定 ===========================================
		foreach ( $aryParts as $k => $v )
		{
			$v = htmlspecialchars( $v );
			switch ( $k )
			{
				//=== textarea =======================================
			case 'html':
				$v = str_replace( "\t", ' ', $v );
				_S(); ?>
<tr>
	<th>{$b[0]}</th>
	<td><textarea name="{$b[0]}" rows="5" wrap="off">{$b[1]}</textarea></td>
</tr>
<?php
				$strRow .= _S( $k, $v );
				break;

				//=== input ==========================================
			default:
				_S(); ?>
<tr>
	<th>{$b[0]}</th>
	<td><input type="text" name="{$b[0]}" value="{$b[1]}" /></td>
</tr>
<?php
				$strRow .= _S( $k, $v );
				break;
			}
		}

		//=== 個別設定 ===============================================

		//=== body の作成 ============================================
		_S(); ?>
<form action="/parts/{$b[0]}.html" method="POST">
<div class="pref">
	<table>
{$b[1]}
	</table>
	<div><input type="submit" name="SUBMIT" value="送信" /></div>
</div>
</form>
<?php
		$aryRep['<!-- body -->'] = _S( $this->m_strID, $strRow );

		//============================================================
		return $aryRep;
	}

	//================================================================
	/**
	 * 設定画面でSUBMITボタンを押した処理
	 * @access public
	 * @return 
 	*/
	//================================================================
	public function OnSubmit( )
	{
		foreach ( $this->m_aryParts as $pk => $v )
		{
			$rk = str_replace( ' ', '_', $pk );
			if ( array_key_exists( $rk, $_REQUEST ) === TRUE )
			{
				DLOG( "'{$pk}' '{$this->m_aryParts[$pk]}' => '{$_REQUEST[$rk]}'" ); 
				if ( $this->m_aryParts[$pk] != $_REQUEST[$rk] )
				{
					DLOG( "change '{$pk}' = '{$_REQUEST[$rk]}'" ); 
					$this->m_aryParts[$pk] = $_REQUEST[$rk];
				}
			}
		}

		//=== 設定の保存 =============================================
		SetConfig( $this->m_strUser, $this->m_aryConfig );
	}

	//================================================================
	/**
	 * 通常表示
	 * @access public
	 * @return array 置換用データー
 	*/
	//================================================================
	public function aryMakeNormal( )
	{
		//============================================================
		$aryRep = array(
			  '/* script */'  => ''
			, '/* ready */'   => ''
			, '/* style */'   => ''
			, '<!-- head -->' => ''
			, '<!-- body -->' => ''
			, 'body-attr'     => ''
		);

		//============================================================
		if ( file_exists( $this->m_strPHP ) )
		{
			DLOG( "include: '{$this->m_strPHP}'" );
			ob_start( );
			require_once( $this->m_strPHP );
			if ( function_exists( 'EchoContents' ) )
			{
				//=== 関数が定義されているので実行 ===================
				EchoContents( $this->m_aryParts );
			}
			else if ( class_exists( $this->m_strClass ) )
			{
				//=== type のキャメルケースを名前とするクラスが定義されている
				$obj = new $this->m_strClass( $this->m_strUser, $this->m_aryParts );
				$obj->Exec( );
			}
			else if ( class_exists( 'CParts' ) )
			{
				//=== オブジェクトが定義されているのでインスタンスを作って実行
				$obj = new CParts( $this->m_strUser, $this->m_aryParts );
				$obj->Run( );
			}
			$strParts = ob_get_clean( );
		}
		else
		{
			DLOG( "load: '{$this->m_strHTML}'" );
			$strParts = file_get_contents( $this->m_strHTML );
		}

		//=== <style></style> ブロックを抽出 =========================
		$strRE = '/<style>(.*)<\/style>\s*/isu';
		if ( preg_match( $strRE, $strParts, $m ) === 1 )
		{
			$aryRep['/* style */'] .= $m[1];
			$strParts = str_replace( $m[0], '', $strParts );
		}

		//=== <head></head> ブロックを抽出 ===========================
		$strRE = '/<head>(.*)<\/head>\s*/isu';
		if ( preg_match( $strRE, $strParts, $m ) === 1 )
		{
			$aryRep['<!-- head -->'] .= $m[1];
			$strParts = str_replace( $m[0], '', $strParts );
		}

		//=== <body></body> ブロックを抽出 ===========================
		$strRE = '/<body>(.*)<\/body>(\s*)/isu';
		if ( preg_match( $strRE, $strParts, $m ) === 1 )
		{
			$aryRep['<!-- body -->'] .= $m[1];
			$strParts = str_replace( $m[0], '', $strParts );
		}
		else
		{
			// <body></body> が無いときには全体を body とする
			$aryRep['<!-- body -->'] .= $strParts;
		}

		//=== 高さの自動調整 =========================================
		if ( !empty( $this->m_aryParts['fit'] ) )
		{
			// 自動調整(最大)
			$aryRep['/* ready */'] .= <<<_EOL_
	// load完了時にiframeの高さを調整
	$(window).load( function(){
		$.dspAutoHeight.iframe();
	});

_EOL_;
		}
		else if ( !empty( $this->m_aryParts['height'] ) )
		{
			// 固定高さ
			$h = $this->m_aryParts['height'];

			// 親ページから固定か最大化を決めるため
			// <body> の height 属性に高さを設定している
			$aryRep['body-attr'] .= " height='{$h}'";

			$aryRep['/* ready */'] .= <<<_EOL_
	// load完了時にiframeの高さを調整
	$(window).load( function(){
		$.dspAutoHeight.iframe({$h});
	});

_EOL_;
		}

		//=== 自動リロード ===========================================
		if ( !empty( $this->m_aryParts['reload'] ) )
		{
			if ( preg_match( '/([0-9]+h)?\s*([0-9]+m)?\s*([0-9]+s)?/iu', $this->m_aryParts['reload'], $m ) === 1 )
			{
				$iSec = 0;
				if ( !empty( $m[1] ) )
					$iSec += (int)$m[1] * 60 * 60;

				if ( !empty( $m[2] ) )
					$iSec += (int)$m[2] * 60;

				if ( !empty( $m[3] ) )
					$iSec += (int)$m[3];

				//====================================================
				if ( $iSec > 0 )
				{
					$iSec *= 1000;
					$aryRep['/* ready */'] .= <<<_EOL_
	// 一定時間ごとにリロード
	setTimeout(function(){
		location.reload();
	},{$iSec});

_EOL_;
				}
			}
		}

		//=== 別プロセスでタブを開く =================================
		if ( $this->m_blAonClick )
		{
			$aryRep['/* ready */'] .= <<<_EOL_
	// 別プロセスで開く
	$("body").on( "click.dspParts", "a", function(event){
		if ( this.href && (location.hostname != this.hostname) )
		{
			// 何故か window.open() が undefined を返してくる＞＜；
			var wo = window.open();
			if ( wo )
			{
				wo.opener = null;
				wo.location.href = this.href;
				return false;
			}
		}
		return true;
	});
_EOL_;
		}

		//============================================================
		return $aryRep;
	}
}

//====================================================================
new CPartsApp( );
objGetApp( )->Exec( );
?>