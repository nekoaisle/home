<?php
function EchoContents( $aryConfig )
{
	if ( objGetApp()->m_strUser == 'default' )
	{
		EchoDemo( );
		return;
	}

	//================================================================
	$strAccount  = urlencode( $aryConfig['account' ] );
	$strPassword = urlencode( $aryConfig['password'] );

	//=== GMailのRSSを取得 ============================================
	$s = error_reporting( 0 );
	$strUrl = "https://{$strAccount}:{$strPassword}@mail.google.com/mail/feed/atom/";
	DLOG( "get {$strUrl}" );
	$strXML = @file_get_contents( $strUrl );
	error_reporting( $s );
	if ( !$strXML )
	{
		//=== RSSが取得できなければデモ表示 ==========================
		$strAccount = urldecode( $strAccount );
		echo "{$strAccount} は取得できませんでした。";
		return;
	}

	//=== パース =====================================================
	try
	{
		$objXML = new SimpleXMLElement( $strXML );
	}
	catch ( Exception $e )
	{
		//=== RSSが取得できなければデモ表示 ==========================
		echo "{$strAccount} はパースできませんでした。";
		return;
	}

	//================================================================
	// 以下通常モード
	//================================================================
	if ( empty( $objXML->entry ) )
	{
?>
未読のメッセージはありません。
<?php
		return;
	}
?>
<head>
<style type="text/css">
a {
	color: #888888;
	text-decoration: none;
}

a:link {
	color: #444444;
}
a:visited {
	color: #888888;
}

ul {
	list-style-type: none;
	padding-left: 0px;
}
li {
	border-top: 1px solid #DDE8F5;
	padding: 4px 8px;
}
li.first {
	margin-top: 0px;
	border: none;
}
li:hover {
	background-color: #FFFFCC;
}
</style>
</head>
<body>
<ul>
<?php
	$a['class'] = ' class="first"';
	foreach ( $objXML->entry as $k => $o )
	{
		$a['time']    = date( 'H:i', strtotime( $o->issued ) );
		$a['from']    = !empty( $o->author->name ) ? $o->author->name : $o->author->email;
		$a['mail']    = $o->author->email;
		$a['subject'] = $o->title;
		$a['body']    = trim( $o->summary );
		$a['link']    = $o->link['href'];

__();/*===========================================================*/?>
	<li{$a['class']}>
		<a href="{$a['link']}" target="_blank">
			{$a['time']} <b>{$a['from']}</b> - {$a['subject']}
		</a>
	</li>
<?php __($a);/*=====================================================s*/

		// class指定は最初だけ
		$a['class'] = '';
	}
?>
</ul>
</body>
<?php
}

//====================================================================
// デモ用コンテンツを返す
//====================================================================
function EchoDemo( )
{
?>
<head>
<style type="text/css">
a {
	color: #888888;
	text-decoration: none;
}

a:link {
	color: #444444;
}
a:visited {
	color: #888888;
}
li {
	border-top: 1px solid #DDE8F5;
	padding: 4px 0px;
}
li.first {
	margin-top: 0px;
	border: none;
}
li:hover {
	background-color: #FFFFCC;
}
</style>
</head>
<body>
<ul>
	<li class="first"><a href="http://mail.google.com/" target="_blank">16:12 <b>上海問屋 本店</b> - デモです。</a></li>
	<li><a href="http://mail.google.com/" target="_blank">11:49 <b>alerts-auctions</b> - ヤフオク!アラート：PCエンジン DUO - ■PCエンジンDUO本体一式+周辺機器5点+ソフト12本(レア含む)■</a></li>
	<li><a href="http://mail.google.com/" target="_blank">11:11 <b>カブドットコム証券</b> - 【カブドットコム証券】3月優待→一年で最も多い約600社が優待実施</a></li>
</ul>
</body>
<?php
}
?>