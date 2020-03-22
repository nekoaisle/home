<?php
//====================================================================
// 既読URLを登録
// @param  string $strUser   ユーザーID
// @param  array  $aryConfig このユーザーの設定情報
// @param  array  $aryData   php://input を json_decode した配列
// 
//====================================================================
function aryProcAjax( $strUser, $aryConfig, $aryData )
{
	TLOG( );

	//================================================================
	$aryRes = array( );

	//=== 引数の取得 =================================================
	if ( empty( $aryData['rss'] ) )
	{
		DLOG( "rss is empty!" );
		return $aryRes;
	}
	$strRSS = $aryData['rss'];

	if ( empty( $aryData['url'] ) )
	{
		DLOG( "url is empty!" );
		return $aryRes;
	}
	if ( is_array( $aryData['url'] ) )
		$aryURL = $aryData['url'];
	else
		$aryURL = array( $aryData['url'] );

	//=== 既読ログの取得 =============================================
	$strReaded = dirname( __FILE__ ) . "/../config/{$strUser}-rss.txt";
	if ( file_exists( $strReaded ) )
	{
		//=== ログが存在する =========================================
		DLOG( "load '{$strReaded}'" );
		$aryReaded = unserialize( file_get_contents( $strReaded ) );
	}
	else
	{
		//=== ログが存在しない =======================================
		$aryReaded = array( );
	}

	//================================================================
	foreach ( $aryURL as $strURL )
	{
		$aryReaded[$strURL] = array(
			  'RSS'  => $strRSS
			, 'DATE' => date( 'Y/m/d H:i:s' )
		);
	}

	//=== 既読ログを保存 =============================================
	DLOG( "save '{$strReaded}'" );
	file_put_contents( $strReaded, serialize( $aryReaded ) );

	//================================================================
	return $aryRes;
}
?>