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
	$strStockCode = $aryConfig['stock code'];
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript" src="/js/jquery.xdomainajax.js"></script>
<script type="text/javascript">
jQuery(function($){
	function LoadYahooFinance( $ )
	{
		$.ajax({
			type: 'GET',
			url: 'http://stocks.finance.yahoo.co.jp/stocks/detail/?code=<?php echo $strStockCode; ?>',
			success: function(html) {
				var symbol = $(html).find("table.stocksTable th.symbol").text();
				var price  = $($(html).find("table.stocksTable td.stoksPrice")[1]).text();
				var change = $(html).find("table.stocksTable td.stoksPrice").text();
				console.log( html );
				$('div.symbol').text( symbol );
				$('div.price' ).text( price );
				$('div.change').text( change );
			},
			error:function() {
				alert('株価を取得できませんでした。');
			}
		});
	}

//	setTimeout(function(){
//		LoadYahooFinance( jQuery );
//	},5*60*1000);

	LoadYahooFinance( jQuery );
});
</script>
</head>

<body>
<!-- start stock market wind code -->
<div>
<div class="symbol"></div>
現在値: <div class="price"></div>
前日比: <div class="change"></div>
</div>
</body>
<?php
}
?>