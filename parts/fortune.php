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
require_once( dirname(__FILE__) . '/Widget.php' );

class CFortune extends CWidget
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
		__();
?>
<head>
<style type="text/css">
</style>

<script type="text/javascript">
Date.prototype.toFormatedString = function( str )
{
	str = str.replace( "%Y", this.getFullYear() );
	str = str.replace( "%m", ( '0' + (this.getMonth()+1) ).slice( -2 ) );
	str = str.replace( "%d", ( '0' + this.getDate()    ).slice( -2 ) );
	str = str.replace( "%H", ( '0' + this.getHours()   ).slice( -2 ) );
	str = str.replace( "%i", ( '0' + this.getMinutes() ).slice( -2 ) );
	str = str.replace( "%s", ( '0' + this.getSeconds() ).slice( -2 ) );
	return str;
}

jQuery(function($)
{
	LoadFortune( );

	// 一定時間ごとにリロード
	setInterval( function(){
		LoadFortune( );
	}, 1 * 60 * 60 * 1000 );
});

function LoadFortune( )
{
	var now = new Date( );
	var url = now.toFormatedString( "http://api.jugemkey.jp/api/horoscope/free/jsonp/%Y/%m/%d" );
	var time = now.toFormatedString( "%Y/%m/%d %H:%i:%s" );

	var prm = {
		type: 'GET',
		url: url,
		dataType: 'jsonp',
//		jsonpCallback: 'android',
		success: function( json )
		{
			// json.horoscope["2014/05/01"][][
			//   "content" : "義理や人情に囚われていると…",
			//   "item"    : "五百円玉",
			//   "money"   : 2,
			//   "total"   : 1,
			//   "job"     : 1,
			//   "color"   : "茶色",
			//   "love"    : 1,
			//   "rank"    : 12,
			//   "sign"    : "牡羊座"
			// ]
			var json2 = json.horoscope;
			var contents = $(".contents" );
			for ( var n in json2 )
			{
				var json3 = json2[n];
				var len = json3.length;
				for ( var i = 0; i < len; ++ i )
				{
					if ( json3[i].sign == '{$a['sign']}' )
					{
						$( ".date"   , contents ).text( n );
						$( ".sign"   , contents ).text( json3[i].sign );
						$( ".content", contents ).text( json3[i].content );
						$( ".time"   , contents ).text( time );

						$( ".general" , contents ).text( json3[i].total );
						$( ".business", contents ).text( json3[i].job   );
						$( ".money"   , contents ).text( json3[i].money );
						$( ".love"    , contents ).text( json3[i].love  );
					}
				}
			}
		}
	};

	//================================================================
	$.ajax( prm );
}
</script>
</head>
<?php
		__( $this->m_aryConfig );
	}

	//================================================================
	//! <body></body> の中身の出力
	//================================================================
	public function EchoBodyContents( )
	{
?>
<style>
div.contents {
	padding: 4px 8px;
	line-height: 1.6em;
}
div.title {
	font-size: 1.2em;
	font-weight: bold;
}

div.content {
	font-size: 1.0em;
	border: 1px #CCCCCC solid;
	padding: 4px;
	border-radius: 6px;
	background-color: #DDEEFF;
}
div.time {
/*	display: none;*/
}

div.pr {
	font-size: 0.7em;
	color: #AAAAAA;
}
div.pr a:link,
div.pr a:visited,
div.pr a:hover,
div.pr a:active
{
	color: #AAAAFF;
	text-decoration: none;
}
</style>
<!--===============================================================-->
<div class="contents">
	<div class="title"><span class="date"></span> <span class="sign"></span> の運勢</div>
	<div class="level">
		総合運:<span class="general" ></span>
		金運:<span class="money"   ></span>
		仕事運:<span class="business"></span>
		恋愛運:<span class="love"    ></span>
	</div>
	<div class="content"></div>
	<div class="time"></div>
<?php
?>
	<div class="pr">
		powerd by <a href="http://jugemkey.jp/api/waf/api_free.php">JugemKey</a>
		【PR】<a href="http://www.tarim.co.jp/">原宿占い館 塔里木</a>
	</div>
</div><!--contents-->
<?php
	}
}
?>