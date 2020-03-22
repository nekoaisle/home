<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal �p Widget
//
//	Google �J�����_�[
//	��p�ݒ�
//	'mode'            => [�\�����[�h] 'WEEK', '', 'AGENDA'
//	'Calendar ID1'    => [�J�����_�[1ID]
//	'Calendar Color1' => [�J�����_�[1�̐F] '#711616'
//	'Calendar ID2'    => [�J�����_�[2ID]
//	'Calendar Color2' => [�J�����_�[2�̐F] 
//	'Calendar ID3'    => [�J�����_�[3ID]
//	'Calendar Color3' => [�J�����_�[3�̐F] 
//	'Calendar ID4'    => [�J�����_�[4ID]
//	'Calendar Color4' => [�J�����_�[4�̐F] 
//	'Calendar ID5'    => [�J�����_�[5ID]
//	'Calendar Color5' => [�J�����_�[5�̐F] 
//	'Calendar ID6'    => [�J�����_�[6ID]
//	'Calendar Color6' => [�J�����_�[6�̐F] 
//	'Calendar ID7'    => [�J�����_�[7ID]
//	'Calendar Color7' => [�J�����_�[7�̐F] 
//	'Calendar ID8'    => 'ja.japanese#holiday@group.v.calendar.google.com'
//	'Calendar Color8' => '#711616'
//	'Calendar ID9'    => '#contacts@group.v.calendar.google.com'
//	'Calendar Color9' => '#2952A3'
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	$strHeight = $aryConfig['height'];

	$strMode = '';
	if ( !empty( $aryConfig['mode'] ) )
	{
		$s = urlencode( $aryConfig['mode'] );
		$strMode = "&amp;mode={$s}";
	}

	//=== �\������J�����_�[ID�̎擾 =================================
	$strSrc = '';
	for ( $i = 1; $i <= 9; ++ $i )
	{
		// �J�����_�[ID
		if ( !empty( $aryConfig["Calendar ID{$i}"] ) )
		{
			$s = urlencode( $aryConfig["Calendar ID{$i}"] );
			$strSrc .= "&amp;src={$s}";

			// ���̐F
			if ( !empty( $aryConfig["Calendar Color{$i}"] ) )
			{
				$s = urlencode( $aryConfig["Calendar Color{$i}"] );
				$strSrc .= "&amp;color={$s}";
			}
		}
	}
?>
<head>
<style type="text/css">
iframe {
	border: 0px;
}
</style>
</head>
<body>
<iframe src="https://www.google.com/calendar/embed?showTitle=0&amp;showPrint=0&amp;showTz=0<?php echo $strMode; ?>&amp;height=<?php echo $strHeight;?>&amp;wkst=1&amp;bgcolor=%23FFFFFF<?php echo $strSrc; ?>&amp;ctz=Asia%2FTokyo" style=" border-width:0 " width="100%" height="<?php echo $strHeight;?>" frameborder="0" scrolling="no"></iframe>
</body>
<?php
}
?>