<?php
//====================================================================
// リンクを削除する
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

	//=== ============================================================
	if ( array_key_exists( 'id', $_REQUEST ) )
	{
		$iID = (int)$_REQUEST['id'];

		//============================================================
		$blRes = blRemoveLink( $aryConfig, $iID );
		if ( $blRes === TRUE )
			SetConfig( $strUser, $aryConfig );

		//============================================================
		$aryRes = array( 'text' => print_r( $aryConfig, TRUE ) );
	}

	//================================================================
	return $aryRes;
}
?>