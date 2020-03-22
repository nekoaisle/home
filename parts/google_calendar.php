<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	Google カレンダー
//	専用設定
//	'mode'            => [表示モード] 'WEEK', '', 'AGENDA'
//	'Calendar ID1'    => [カレンダー1ID]
//	'Calendar Color1' => [カレンダー1の色] '#711616'
//	'Calendar ID2'    => [カレンダー2ID]
//	'Calendar Color2' => [カレンダー2の色] 
//	'Calendar ID3'    => [カレンダー3ID]
//	'Calendar Color3' => [カレンダー3の色] 
//	'Calendar ID4'    => [カレンダー4ID]
//	'Calendar Color4' => [カレンダー4の色] 
//	'Calendar ID5'    => [カレンダー5ID]
//	'Calendar Color5' => [カレンダー5の色] 
//	'Calendar ID6'    => [カレンダー6ID]
//	'Calendar Color6' => [カレンダー6の色] 
//	'Calendar ID7'    => [カレンダー7ID]
//	'Calendar Color7' => [カレンダー7の色] 
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

	//=== 表示するカレンダーIDの取得 =================================
	$strSrc = '';
	for ( $i = 1; $i <= 9; ++ $i )
	{
		// カレンダーID
		if ( !empty( $aryConfig["Calendar ID{$i}"] ) )
		{
			$s = urlencode( $aryConfig["Calendar ID{$i}"] );
			$strSrc .= "&amp;src={$s}";

			// その色
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