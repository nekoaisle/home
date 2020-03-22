<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	Widget １２星座占い
//	iGoogle module
//
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	if ( array_key_exists( 'width', $aryConfig ) )
		$strWidth = $aryConfig['width'];
	else
		$strWidth = '300';

	if ( array_key_exists( 'height', $aryConfig ) )
		$strHeight = $aryConfig['height'];
	else
		$strHeight = '240';

	$aryOption = array(
		  'url'     => 'http://www.amonya.com/igoogle/fortune/fortune.xml'
		, 'up_sign' => '双子座'
		, 'up_kind' => '9'
		, 'up_bgc'  => 'orange'
		, 'synd'    => 'open'
		, 'w'       => $strWidth
		, 'h'       => '240'
		, 'title'   => '１２星座占い'
		, 'border'  => '#ffffff|3px,1px solid #999999'
		, 'output'  => 'js'
	);

	$ary = array( );
	foreach ( $aryOption as $k => $v )
	{
		$v = urlencode( $v );
		$ary[] = "{$k}={$v}";
	}

	$strQuery = implode( '&', $ary );

//	$strUrl = 'http://www.amonya.com/igoogle/fortune/fortune.xml&amp;up_sign=%E5%8F%8C%E5%AD%90%E5%BA%A7&amp;up_kind=9&amp;up_bgc=orange&amp;synd=open&amp;w=340&amp;h=240&amp;title=%EF%BC%91%EF%BC%92%E6%98%9F%E5%BA%A7%E5%8D%A0%E3%81%84&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js';
/*
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript" src="/js/dspResizeFunc.js"></script>
<script type="text/javascript">
jQuery(function($){
	// ウィンドウがリサイズされた時にサイズを変更
	$(window).dspResizeFunc({
		delay: 1000,
		func : function(){
			var iframe = window.frameElement;
			iframe.src = iframe.src;
		},
	});

	(function($){
		var option = {
			  url    : 'http://www.amonya.com/igoogle/fortune/fortune.xml'
			, up_sign: '双子座'
			, up_kind: '9'
			, up_bgc : 'orange'
			, synd   : 'open'
			, w      : '340'
			, h      : '240'
			, title  : '１２星座占い'
			, border : '#ffffff|3px,1px solid #999999'
			, output : 'js'
		}

		var q = [];
		for ( var k in option) {
			q.push( k + "=" + encodeURI(option[k]) );
		}

		var src = "//www.gmodules.com/ig/ifr?" + q.join( "&" );
		var tag = $('<script class="horoscope" src="' + src + '"><\/script>');
		$("body").append( tag );
	})(jQuery);
});
</script>
</head>
<?php
*/
?>
<body>
<div style="margin: 0px 8px;">
<script src="//www.gmodules.com/ig/ifr?<?php echo $strQuery; ?>"></script>
</div>
</body>
<?php
}
?>