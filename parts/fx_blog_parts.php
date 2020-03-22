<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//fx_blog_parts.php
//	FX BLOG PARTS 
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname(__FILE__) . '/parts.php' );

class CFxBlogParts extends CParts
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
?>
<head>
<style type="text/css">
div.contents {
	padding: 8px;
}
</style>

<script type="text/javascript">
jQuery(function($){
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

<iframe width="350px" height="327px" marginwidth="0" marginheight="0" hspace="0" vspace="0" scrolling="no" frameborder="0"src="http://ck2-blogger.jp/cgi-bin/blogparts/ck2-blogger.jp/chartMain/chartMain_view.pl?color=10&layout=10&size=350&pairs=USDJPY,EURJPY,GBPJPY,AUDJPY,NZDJPY,CADJPY,CHFJPY,TRYJPY,USDCHF"></iframe>

<div style="margin:0px; padding: 0px; border: 0px; border-left: solid 1px #999; border-right: solid 1px #999; border-bottom: solid 1px #999; text-align: right; width: 348px;">
	<a href="http://www.munehiro.jp/" target="_blank" style="margin:0px; padding: 0px;">
		<img src="http://ck2-blogger.jp/blogparts/ck2-blogger.jp/chartMain/img/rate-foot10-1.gif" alt="ＦＸ" border="0" style="margin:0px; padding: 0px;">
	</a>
	<a href="http://ck2-blogger.jp/" target="_blank" style="margin:0px; padding: 0px;">
		<img src="http://ck2-blogger.jp/blogparts/ck2-blogger.jp/chartMain/img/rate-foot10-2.gif" alt="ＦＸ投資ツール" border="0" style="margin:0px; padding: 0px;">
	</a>
</div>

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