<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	為替レート表示
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname(__FILE__) . '/parts.php' );

class CExchange extends CParts
{
	//================================================================
	// コンストラクタ
	//================================================================
	public function __construct( $strUser, $aryConfig )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__construct( $strUser, $aryConfig );
	}

	//================================================================
	// メンバー変数
	//================================================================

	//================================================================
	//! 初期化
	//================================================================
	public function Init( )
	{
	}

	//================================================================
	//! ヘッダーの出力
	//================================================================
	public function EchoHead( )
	{
		if ( !empty( $this->m_aryConfig['height'] ) )
			$iHeight = $this->m_aryConfig['height'];
		else
			$iHeight = 150;

		//============================================================
		$strDir = dirname( __FILE__ );
		$iTime = time( );
		do
		{
			$strDate = date( 'Y-m-d', $iTime );
			$strFile = "{$strDir}/../config/jpy-usd-{$strDate}.txt";
			$iTime -= 24*60*60;
		} while ( !file_exists( $strFile ) );

		$aryExch = unserialize( file_get_contents( $strFile ) );
		ksort( $aryExch );

		$strData = '';
		$fMin = 10000;
		$fMax = 0;
		foreach ( $aryExch as $strTime => $fYen )
		{
			$strData .= "[new Date( '{$strDate} {$strTime}' ), {$fYen} ],";
			if ( $fMin > $fYen )
				$fMin = $fYen;
			if ( $fMax < $fYen )
				$fMax = $fYen;
		}

		end( $aryExch );
		$fLast = current( $aryExch );
		$strLast = key( $aryExch );
		$strTitle = "'米ドル/日本円 {$fLast}円 ({$strLast})'";
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript">
jQuery(function($){
});
</script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
// Load the Visualization API and the piechart package.
google.load( 'visualization', '1.0', {'packages':['corechart']} );

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(function drawChart()
{
	// Create the data table.
	var data = google.visualization.arrayToDataTable([
		['time', 'JPY'],
		<?php echo $strData; ?>
	]);

	var options = {
		curveType: "function",
		title: <?php echo $strTitle; ?>,
		width: $(window).innerWidth(),
		height: <?php echo $iHeight; ?>,
/*		legend: 'none',*/
		vAxis: {
			minValue: <?php echo $fMin - 0.1;?>,
			maxValue: <?php echo $fMax + 0.1;?>,
		},
/*		pointSize: 1,*/
	};

	// Create and draw the visualization.
	var div = document.getElementById('visualization');
	var objChart = new google.visualization.LineChart( div );
	objChart.draw( data, options );
});
</script>
</head>
<?php
	}

	//================================================================
	//! <body></body> の開始
	//================================================================
	public function EchoBodyStart( )
	{
?>
<body>
<?php
	}

	//================================================================
	//! <body></body> の中身の出力
	//================================================================
	public function EchoBodyContents( )
	{
?>
<!--===============================================================-->
<div class="contents">
<div id="visualization" style="width:400; height:300"></div>
</div><!--contents-->
<?php
	}

	//================================================================
	//! <body></body> の終了
	//================================================================
	public function EchoBodyEnd( )
	{
?>
</body>
<?php
	}
}
?>