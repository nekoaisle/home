<?php
//====================================================================
// �E�B�W�F�b�g���ړ�����
// @param  string $strUser   ���[�U�[ID
// @param  array  $aryConfig ���̃��[�U�[�̐ݒ���
// @param  array  $aryData   php://input �� json_decode �����z��
// 
//====================================================================
function aryProcAjax( $strUser, $aryConfig, $aryData )
{
	TLOG( );

	//=== �擾�����p�����[�^�[����^�u�ԍ����擾 =====================
	if ( preg_match( '/^tabbody([0-9])$/', $aryData['tab'], $m ) === 1 )
		$iTab = (int)$m[1];
	else
	{
		DLOG( "error \$aryData['tab']", $aryData['tab'] );
		$blError = TRUE;
	}

	//=== �擾�����p�����[�^�[���� drag ���擾 =======================
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

	//=== �擾�����p�����[�^�[���� drop ���擾 =======================
	// row[col][row] ���̃E�B�b�W�F�g�̑O�ɑ}��
	// col[col]      ���̗�̖����ɑ}��
	if ( preg_match( '/^row([0-9])([0-9])$/', $aryData['drop'], $m ) === 1 )
	{
		// row[col][row] ���̃E�B�b�W�F�g�̑O�ɑ}��
		$iDropCol = (int)$m[1];
		$iDropRow = (int)$m[2];
	}
	else if ( preg_match( '/^col([0-9])$/', $aryData['drop'], $m ) === 1 )
	{
		// col[col]      ���̗�̖����ɑ}��
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

		//=== �ړ�����Widget���擾 ===================================
		$aryWidget = $arySrc[$iDragRow];
		$arySrc[$iDragRow] = array( );	// �ړ��Ώۂ�z�񂩂珜��

		//=== ����ɑ}�� =============================================
		$a1 = array_slice( $aryDst, 0, $iDropRow );
		$a2 = array( $aryWidget );
		$a3 = array_slice( $aryDst, $iDropRow );
		$aryDst = array_merge( $a1, $a2, $a3 );

		//=== �����ړ��Ώۂ�z������珜�� ===========================
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