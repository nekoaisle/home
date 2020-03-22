<?php
//====================================================================
// ウィジェットを移動する
// @param  string $strUser   ユーザーID
// @param  array  $aryConfig このユーザーの設定情報
// @param  array  $aryData   php://input を json_decode した配列
// 
//====================================================================
function aryProcAjax( $strUser, $aryConfig, $aryData )
{
	TLOG( );

	//=== 取得したパラメーターからタブ番号を取得 =====================
	if ( preg_match( '/^tabbody([0-9])$/', $aryData['tab'], $m ) === 1 )
		$iTab = (int)$m[1];
	else
	{
		DLOG( "error \$aryData['tab']", $aryData['tab'] );
		$blError = TRUE;
	}

	//=== 取得したパラメーターから drag を取得 =======================
	// row[col][row] 
	if ( preg_match( '/^row([0-9])([0-9])$/', $aryData['drag'], $m ) === 1 )
	{
		$iDragCol = (int)$m[1];
		$iDragRow = (int)$m[2];
	}
	else
	{
		DLOG( "error \$aryData['drag']", $aryData['drag'] );
		$blError = TRUE;
	}

	//=== 取得したパラメーターから drop を取得 =======================
	// row[col][row] このウィッジェトの前に挿入
	// col[col]      この列の末尾に挿入
	if ( preg_match( '/^row([0-9])([0-9])$/', $aryData['drop'], $m ) === 1 )
	{
		// row[col][row] このウィッジェトの前に挿入
		$iDropCol = (int)$m[1];
		$iDropRow = (int)$m[2];
	}
	else if ( preg_match( '/^col([0-9])$/', $aryData['drop'], $m ) === 1 )
	{
		// col[col]      この列の末尾に挿入
		$iDropCol = (int)$m[1];
		$iDropRow = 9999;
	}
	else
	{
		DLOG( "error \$aryData['drop']", $aryData['drop'] );
		$blError = TRUE;
	}

	//================================================================
	if ( !isset( $blError ) )
	{
		$arySrc = &$aryConfig['tabs'][$iTab]['cols'][$iDragCol]['parts'];
		$aryDst = &$aryConfig['tabs'][$iTab]['cols'][$iDropCol]['parts'];

		//=== 移動したWidgetを取得 ===================================
		$aryWidget = $arySrc[$iDragRow];
		$arySrc[$iDragRow] = array( );	// 移動対象を配列から除去

		//=== 同先に挿入 =============================================
		$a1 = array_slice( $aryDst, 0, $iDropRow );
		$a2 = array( $aryWidget );
		$a3 = array_slice( $aryDst, $iDropRow );
		$aryDst = array_merge( $a1, $a2, $a3 );

		//=== 消去移動対象を配列をから除去 ===========================
		$aryTmp = array( );
		foreach ( $arySrc as $a )
		{
			if ( !empty( $a ) )
				$aryTmp[] = $a;
		}
		$arySrc = $aryTmp;

		//============================================================
		SetConfig( $strUser, $aryConfig );

		//============================================================
//		$aryData['$aryWidget'] = $aryWidget;
//		$aryData['$arySrc'] = $arySrc;
//		$aryData['$aryDst'] = $aryDst;
	}

	//================================================================
	$strText = print_r( $aryData, TRUE );
	$aryParam = array( 'text' => $strText );
	return $aryParam;
}
?>