<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	株価パーツ
//	stock market wind 
//	http://stockmarketwind.com/
//
//	専用設定
//	'stock code' => [銘柄コード]
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	//=== リロードの設定 =============================================
	$objApp = objGetApp( );
	if ( !empty( $objApp->m_aryParts['reload'] ) )
	{
		$iNow = time( );
		$strTime = date( 'His', $iNow );
		$aryDate = getdate( $iNow );
		$y = $aryDate['year'];
		$m = $aryDate['mon' ];
		$d = $aryDate['mday'];
		$w = $aryDate['wday'];
		switch ( $w )
		{
			//=== 月～木 =============================================
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
			if ( $strTime < '090000' )
				// 今日の09:00
				$iNext = mktime( 9, 0, 0, $m, $d, $y );
			else if ( $strTime >= '150000' )
			{
				if ( $w != 5 )
					// 月～木は翌日の09:00
					$iNext = mktime( 9, 0, 0, $m, $d+1, $y );
				else
					// 金曜日は翌月曜日の9:00
					$iNext = mktime( 9, 0, 0, $m, $d+3, $y );
			}
			break;

			//=== 土曜日 =============================================
		case 6:
			// 翌月曜日の9:00
			$iNext = mktime( 9, 0, 0, $m, $d+2, $y );
			break;

			//=== 日曜日 =============================================
		case 0:
			// 翌月曜日の9:00
			$iNext = mktime( 9, 0, 0, $m, $d+1, $y );
			break;
		}

		// 次回読み込みまでの秒数
		if ( !empty( $iNext ) )
			$objApp->m_aryParts['reload'] = $iNext - $iNow . 's';
	}

	//================================================================
	$aryCode = explode( ',', $aryConfig['stock code'] );
	$aryStocks = array( );
	foreach ( $aryCode as $strCode )
	{
		$strHTML = file_get_contents( "http://stocks.finance.yahoo.co.jp/stocks/detail/?code={$strCode}" );
		if ( preg_match( '/<table class="stocksTable" summary="株価詳細">(.*?)<\/table>/ius', $strHTML, $m ) === 1 )
		{
			$strHTML = $m[1];
			if ( preg_match( '/<th class="symbol"><h1>(.*?)<\/h1><\/th>/ius', $strHTML, $m ) === 1 )
				$strName = $m[1];
			else
				$strName = '不明';

			if ( preg_match( '/<td class="stoksPrice">(.*?)<\/td>/ius', $strHTML, $m ) === 1 )
				$strPrice = $m[1];
			else
				$strPrice = '';

			if ( preg_match( '/<span class="yjSt">前日比<\/span><span class="icoUpGreen yjMSt">(.*?)<\/span>/ius', $strHTML, $m ) === 1 )
				$strChange = $m[1];
			else
				$strChange = '';

			$aryStocks[] = array(
				  'code'   => trim( $strCode   )
				, 'name'   => trim( $strName   )
				, 'price'  => trim( $strPrice  )
				, 'change' => trim( $strChange )
			);
		}
	}

	$iNow = time( );

?>
<head>
<style type="text/css">
div.padding {
	padding: 8px;
}
table.stock {
	width: 100%;
	font-size: 0.875em;
	background-color: #FFFFFF;
	border: 1px #CCCCCC solid;
	border-collapse: collapse;
}
table.stock th {
	padding: 4px 8px;
	background-color: #EEEEEE;
	border: 1px #CCCCCC solid;
}
table.stock td {
	padding: 4px 8px;
	border: 1px #CCCCCC solid;
}
div.ago {
	margin-top: 4px;
	float: right;
}
</style>

<script type="text/javascript" src="/js/dspTimeAgo.js"></script>
<script type="text/javascript">
jQuery(function($){
	$("div.ago span").dspTimeAgo();
});
</script>
</head>

<body>
<div class="padding">
<table class="stock">
	<tr>
		<th class="code"  >コード</th>
		<th class="symbol">企業名</th>
		<th class="price" >現在値</th>
		<th class="change">前日比</th>
	</tr>
<?php
	foreach( $aryStocks as $a )
	{
?>
	<tr>
		<td class="code"  ><?php echo $a['code'  ]; ?></td>
		<td class="symbol"><?php echo $a['name'  ]; ?></td>
		<td class="price" ><?php echo $a['price' ]; ?></td>
		<td class="change"><?php echo $a['change']; ?></td>
	</tr>
<?php
	}
?>
</table><!-- stock -->
<div class="ago">取得: <span></span></div>
<div style="clear:both"></div>
</div><!-- padding -->
</body>
<?php
}
?>