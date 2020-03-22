<?php

//====================================================================
// コンフィグの取得
//====================================================================
function aryGetConfig( $strEMail )
{
	$aryConfig = array(
		  'default'          => aryGetDefault( )
		, 'ja1gji@gmail.com' => aryGetJa1Gji( )
	);

	//================================================================
	if ( empty( $aryConfig[$strEMail] ) )
		$strEMail = "default";

	//=== 設定ファイルは存在する？ ===================================
	$strFile = "./config/{$strEMail}.txt";
	if ( file_exists( $strFile ) )
	{
		//=== 設定ファイルはこのファイルより新しい？ =================
		$iConf = filemtime( $strFile );
		$iThis = filemtime( __FILE__ );

		if ( $iConf > $iThis )
		{
			//=== 保存されている設定を返す ===========================
			$aryRet = unserialize( file_get_contents( $strFile ) );
			DLOG( "Loaded [{$strFile}]" );
//			DLOG( "Loaded [{$strFile}]", $aryRet );
			return $aryRet;
		}
	}

	//=== このファイルの設定を取得 ===================================
	if ( !array_key_exists( $strEMail, $aryConfig ) )
	{
		DLOG( "{$strEMail} not found in \$aryConfig." );
//		DLOG( VDMP( $aryConfig ) );
		die( );
	}

	$aryRet = $aryConfig[$strEMail];

	//=== 設定ファイルに保存 =========================================
	SetConfig( $strEMail, $aryRet );

	//================================================================
	return $aryRet;
}

//====================================================================
//====================================================================
function aryGetDefault( )
{
	return array(
		// 全体設定
		  'global' => array(
			  'id'       => 'default'
			, 'email'    => 'default'
			, 'password' => 'password'
		)
		// リンクバー設定
		, 'link_bar' => array(
			  array( 
				  'id'    => 100000015
				, 'title' => ''
				, 'url'   => 'http://mail.google.com/'
				, 'icon'  => '/icon/gmail.png'
			)
			, array( 
				  'id'    => 100000009
				, 'title' => ''
				, 'url'   => 'https://www.google.com/calendar/render?tab=cc#g'
				, 'icon'  => '/icon/google_calendar.png'
			)
			, array( 
				  'id'    => 100000011
				, 'title' => ''
				, 'url'   => 'https://contacts.google.com/'
				, 'icon'  => '/icon/google_contacts.png'
			)
			, array( 
				  'id'    => 100000001
				, 'title' => ''
				, 'url'   => 'https://www.google.co.jp/maps/'
				, 'icon'  => '/icon/google_maps.png'
			)
			, array( 
				  'id'    => 100000008
				, 'title' => ''
				, 'url'   => 'https://plus.google.com/photos/search/%23AutoBackup'
				, 'icon'  => '/icon/google_photo.png'
			)
			, array( 
				  'id'    => 100000002
				, 'title' => ''
				, 'url'   => 'https://drive.google.com/'
				, 'icon'  => '/icon/google_drive.png'
			)
			, array( 
				  'id'    => 100000003
				, 'title' => ''
				, 'url'   => 'https://plus.google.com/'
				, 'icon'  => '/icon/google_plus.png'
			)
			, array( 
				  'id'    => 100000013
				, 'title' => ''
				, 'url'   => 'https://play.google.com/store/apps'
				, 'icon'  => '/icon/google_play.png'
			)
			, array( 
				  'id'    => 100000004
				, 'title' => ''
				, 'url'   => 'https://www.blogger.com/'
				, 'icon'  => '/icon/blogger.png'
			)
			, array( 
				  'id'    => 100000005
				, 'title' => ''
				, 'url'   => 'https://drive.google.com/keep/'
				, 'icon'  => '/icon/google_keep.png'
			)
			, array( 
				  'id'    => 100000012
				, 'title' => ''
				, 'url'   => 'https://dl.dropboxusercontent.com/u/5229661/StartPage/index.html'
				, 'icon'  => '/icon/google_news.png'
			)
			, array( 
				  'id'    => 100000014
				, 'title' => ''
				, 'url'   => 'https://twitter.com/'
				, 'icon'  => '/icon/twitter.png'
			)
			, array( 
				  'id'    => 100000006
				, 'title' => ''
				, 'url'   => 'http://www.amazon.co.jp/?&tag=shihoriwish-22&linkCode=wsw'
				, 'icon'  => '/icon/amazon.png'
			)
			, array( 
				  'id'    => 100000007
				, 'title' => ''
				, 'url'   => 'https://www.evernote.com/Home.action'
				, 'icon'  => '/icon/evernote.png'
			)
			, array( 
				  'id'    => 100000010
				, 'title' => ''
				, 'url'   => 'http://www.navitime.co.jp/'
				, 'icon'  => '/icon/navitime.png'
			)
		)
		// タブ設定
		, 'tabs' => array(
			  array( 
				  'title' => 'Home'
				, 'icon'  => '/icon/home.png'
				, 'cols'  => array(
					  array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array( 
								  'id'     => 100000000
								, 'type'     => 'google_calendar'
								, 'icon'     => '/icon/google_calendar.png'
								, 'title'    => 'Googleカレンダー'
								, 'link'     => 'http://www.google.com/calendar/'
								, 'mode'            => 'AGENDA'
								, 'Calendar ID1'    => ''
								, 'Calendar Color1' => ''
								, 'Calendar ID2'    => ''
								, 'Calendar Color2' => ''
								, 'Calendar ID3'    => ''
								, 'Calendar Color3' => ''
								, 'Calendar ID4'    => ''
								, 'Calendar Color4' => ''
								, 'Calendar ID5'    => ''
								, 'Calendar Color5' => ''
								, 'Calendar ID6'    => ''
								, 'Calendar Color6' => ''
								, 'Calendar ID7'    => ''
								, 'Calendar Color7' => ''
								, 'Calendar ID8'    => 'ja.japanese#holiday@group.v.calendar.google.com'
								, 'Calendar Color8' => '#711616'
								, 'Calendar ID9'    => '#contacts@group.v.calendar.google.com'
								, 'Calendar Color9' => '#2952A3'
								, 'height'   => '340'
								, 'fit'      => 'true'
								, 'reload'   => ''
							)
							, array( 
								  'id'     => 100000001
								, 'type'   => 'weather'
								, 'icon'   => '/icon/weather.png'
								, 'title'  => 'ピンポイント天気予報'
								, 'link'   => 'http://weather.tmyymmt.net/'
								, 'height' => '300'
								, 'fit'    => ''
								, 'reload'   => ''
							)
						)
					)
					, array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array(
								  'id'     => 100000010
								, 'type'     => 'gmail'
								, 'icon'     => '/icon/gmail.png'
								, 'title'    => 'GMail'
								, 'link'     => 'http://mail.google.com/'
								, 'height'   => ''
								, 'fit'      => 'true'
								, 'reload'   => ''
								, 'account'  => ''
								, 'password' => ''
							)
							, array( 
								  'id'     => 100000011
								, 'type'   => 'dsp-twtimeline'
								, 'icon'   => '/icon/twitter.png'
								, 'title'  => 'Twitter'
								, 'link'   => 'http://twitter.com/'
								, 'height' => '400'
								, 'fit'    => ''
								, 'reload'   => ''
							)
							, array(
								  'id'     => 100000012
								, 'type'   => 'html_editor'
								, 'icon'   => '/icon/editor.png'
								, 'title'  => 'HTML Editor'
								, 'link'   => ''
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
								, 'html'   => 'ここにお好みのHTMLを貼り付けてください。'
							)
						)
					)
					, array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array(
								  'id'     => 100000020
								, 'type'   => 'investing'
								, 'icon'   => '/icon/editor.png'
								, 'title'  => '為替レート 円/ドル'
								, 'link'   => 'http://jp.investing.com/charts/%E5%A4%96%E7%82%BA%E3%83%81%E3%83%A3%E3%83%BC%E3%83%88'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
							)
							, array( 
								  'id'     => 100000021
								, 'type'   => 'stock'
								, 'icon'   => '/icon/yahoo.png'
								, 'title'  => '株価'
								, 'link'   => 'http://stocks.finance.yahoo.co.jp/stocks'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
								, 'stock code' => '8267,8107'
							)
							, array( 
								  'id'     => 100000023
								, 'type'   => 'puzdra'
								, 'icon'   => '/icon/puzdra.png'
								, 'title'  => 'パズドラ ゲリラダンジョン'
								, 'link'   => 'http://xn--0ck4aw2hv46qq3yavh3e.com/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => ''
								, 'group'  => 'ABCDE'
							)
							, array( 
								  'id'     => 100000022
								, 'type'   => 'horoscope'
								, 'icon'   => '/icon/editor.png'
								, 'title'  => '１２星座占い'
								, 'link'   => 'http://shizlabs.amonya.com/igoogle/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
							)
						)
					)
				)
			)
			, array( 
				  'title' => 'NEWS'
				, 'icon'  => '/icon/news.png'
				, 'cols' => array(
					  array( 
						  'width' => '60%'
						, 'parts' => array(
							  array( 
								  'id'     => 100000100
								, 'type'   => 'google_news'
								, 'icon'   => '/icon/google_news.png'
								, 'title'  => 'Googleニュース'
								, 'link'     => 'http://news.google.co.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
								, 'view type' => 'normal'
								, 'sort type' => 'unread new'
								, 'max'    => 8
								, 'topic'  => '科学,トップ,ビジネス,社会'
								, 'ignore' => '毎日新聞,ウォール・ストリート・ジャーナル日本版,CNET Japan'
							)
							, array( 
								  'id'     => 100000101
								, 'type'   => 'yahoo_news'
								, 'icon'   => '/icon/yahoo.png'
								, 'title'  => 'Yahoo!ニュース'
								, 'link'   => 'http://news.yahoo.co.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => ''
								, 'max'    => 10
							)
						)
					)
					, array( 
						  'width' => '40%'
						, 'parts' => array(
							  array( 
								  'id'     => 100000110
								, 'type'   => 'itpro'
								, 'icon'   => '/icon/itpro.png'
								, 'title'  => 'ITPro最新ニュース'
								, 'link'   => 'http://itpro.nikkeibp.co.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'max'    => 15
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
							, array( 
								  'id'     => 100000111
								, 'type'   => 'itmedia'
								, 'icon'   => 'http://www.itmedia.co.jp/favicon.ico'
								, 'title'  => 'ITMedia PC・AV・スマートフォン'
								, 'link'   => ''
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'max'    => 15
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
							, array( 
								  'id'     => 100000112
								, 'type'   => 'rss'
								, 'icon'   => '/icon/rss.png'
								, 'title'  => '趣味'
								, 'link'   => 'http://autoc-one.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'name1'  => 'オートックワン'
								, 'url1'   => 'http://autoc-one.jp/news/rss/'
								, 'name2'  => ''
								, 'url2'   => ''
								, 'name3'  => ''
								, 'url3'   => ''
								, 'name4'  => ''
								, 'url4'   => ''
								, 'name5'  => ''
								, 'url5'   => ''
								, 'max'    => 10
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
						)
					)
				)
			)
		)
	);
}

//====================================================================
// ja1gji@gmail.com
//====================================================================
function aryGetJa1Gji( )
{
	return array(
		// 全体設定
		  'global' => array(
			  'id'       => 'ja1gji@gmail.com'
			, 'email'    => 'ja1gji@gmail.com'
			, 'password' => 'pwdkya'
		)
		// リンクバー設定
		, 'link_bar' => array(
			  array( 
				  'id'    => 100000015
				, 'title' => ''
				, 'url'   => 'http://mail.google.com/'
				, 'icon'  => '/icon/gmail.png'
			)
			, array( 
				  'id'    => 100000009
				, 'title' => ''
				, 'url'   => 'https://www.google.com/calendar/render?tab=cc#g'
				, 'icon'  => '/icon/google_calendar.png'
			)
			, array( 
				  'id'    => 100000011
				, 'title' => ''
				, 'url'   => 'https://contacts.google.com/'
				, 'icon'  => '/icon/google_contacts.png'
			)
			, array( 
				  'id'    => 100000001
				, 'title' => ''
				, 'url'   => 'https://www.google.co.jp/maps/'
				, 'icon'  => '/icon/google_maps.png'
			)
			, array( 
				  'id'    => 100000008
				, 'title' => ''
				, 'url'   => 'https://plus.google.com/photos/search/%23AutoBackup'
				, 'icon'  => '/icon/google_photo.png'
			)
			, array( 
				  'id'    => 100000002
				, 'title' => ''
				, 'url'   => 'https://drive.google.com/'
				, 'icon'  => '/icon/google_drive.png'
			)
			, array( 
				  'id'    => 100000003
				, 'title' => ''
				, 'url'   => 'https://plus.google.com/'
				, 'icon'  => '/icon/google_plus.png'
			)
			, array( 
				  'id'    => 100000013
				, 'title' => ''
				, 'url'   => 'https://play.google.com/store/apps'
				, 'icon'  => '/icon/google_play.png'
			)
			, array( 
				  'id'    => 100000004
				, 'title' => ''
				, 'url'   => 'https://www.blogger.com/'
				, 'icon'  => '/icon/blogger.png'
			)
			, array( 
				  'id'    => 100000005
				, 'title' => ''
				, 'url'   => 'https://drive.google.com/keep/'
				, 'icon'  => '/icon/google_keep.png'
			)
			, array( 
				  'id'    => 100000012
				, 'title' => ''
				, 'url'   => 'https://dl.dropboxusercontent.com/u/5229661/StartPage/index.html'
				, 'icon'  => '/icon/google_news.png'
			)
			, array( 
				  'id'    => 100000014
				, 'title' => ''
				, 'url'   => 'https://twitter.com/'
				, 'icon'  => '/icon/twitter.png'
			)
			, array( 
				  'id'    => 100000006
				, 'title' => ''
				, 'url'   => 'http://www.amazon.co.jp/?&tag=shihoriwish-22&linkCode=wsw'
				, 'icon'  => '/icon/amazon.png'
			)
			, array( 
				  'id'    => 100000007
				, 'title' => ''
				, 'url'   => 'https://www.evernote.com/Home.action'
				, 'icon'  => '/icon/evernote.png'
			)
			, array( 
				  'id'    => 100000010
				, 'title' => ''
				, 'url'   => 'http://www.navitime.co.jp/'
				, 'icon'  => '/icon/navitime.png'
			)
		)
		// タブ設定
		, 'tabs' => array(
			  array( 
				  'title' => 'Home'
				, 'icon'  => '/icon/home.png'
				, 'cols'  => array(
					  array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array( 
								  'id'     => 100000000
								, 'type'     => 'google_calendar'
								, 'icon'     => '/icon/google_calendar.png'
								, 'title'    => 'Googleカレンダー'
								, 'link'     => 'http://www.google.com/calendar/'
								, 'mode'            => 'AGENDA'
								, 'Calendar ID1'    => 'ja1gji@gmail.com'
								, 'Calendar Color1' => '#29527A'
								, 'Calendar ID2'    => '93ce34t2h3d2m4j27a1hrnks0s@group.calendar.google.com'
								, 'Calendar Color2' => '#333333'
								, 'Calendar ID3'    => 'nsijobq9q4egivq6e74c9i1oo4@group.calendar.google.com'
								, 'Calendar Color3' => '#333333'
								, 'Calendar ID4'    => 'dgq50ed74m8ldpaatbd4ovuoso@group.calendar.google.com'
								, 'Calendar Color4' => '#333333'
								, 'Calendar ID5'    => '09d4106lqvopqk568i7f78vl4g@group.calendar.google.com'
								, 'Calendar Color5' => '#333333'
								, 'Calendar ID6'    => 'mayumi@kiya.info'
								, 'Calendar Color6' => '#125A12'
								, 'Calendar ID7'    => ''
								, 'Calendar Color7' => ''
								, 'Calendar ID8'    => ''
								, 'Calendar Color8' => ''
								, 'Calendar ID9'    => '#contacts@group.v.calendar.google.com'
								, 'Calendar Color9' => '#2952A3'
								, 'height'   => '340'
								, 'fit'      => 'true'
								, 'reload'   => ''
							)
							, array( 
								  'id'     => 100000001
								, 'type'   => 'weather'
								, 'icon'   => '/icon/weather.png'
								, 'title'  => 'ピンポイント天気予報'
								, 'link'   => 'http://weather.tmyymmt.net/'
								, 'height' => '300'
								, 'fit'    => ''
								, 'reload' => ''
							)
						)
					)
					, array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array(
								  'id'     => 100000010
								, 'type'     => 'gmail'
								, 'icon'     => '/icon/gmail.png'
								, 'title'    => 'GMail'
								, 'link'     => 'http://mail.google.com/'
								, 'height'   => ''
								, 'account'  => 'ja1gji@gmail.com'
								, 'password' => 'rzrnyasamblberju'
								, 'fit'      => 'true'
								, 'reload'   => '10m'
							)
							, array( 
								  'id'     => 100000011
								, 'type'   => 'dsp-twtimeline'
								, 'icon'   => '/icon/twitter.png'
								, 'title'  => 'Twitter'
								, 'link'   => 'http://twitter.com/'
								, 'height' => '600'
								, 'fit'    => ''
								, 'reload'   => '5m'
								, 'access_token' => '101938517-PnjPx8lXcEXsTIxeXnH6NASoTKtPhYLFhepFBv7j'
								, 'access_secret' => 'oBDRrMTPCeAFEVJ6yVVkZkfcsagIKiiqG0gmTT1csK6lN'
							)
						)
					)
					, array( 
						  'width' => '32%'
						, 'parts' => array( 
							  array(
								  'id'     => 100000020
								, 'type'   => 'exchange'
								, 'icon'   => '/icon/editor.png'
								, 'title'  => '外国為替チャート'
								, 'link'   => 'http://jp.currencyconverterrate.com/usd/jpy.html'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => ''
							)
							, array( 
								  'id'     => 100000021
								, 'type'   => 'stock'
								, 'icon'   => '/icon/yahoo.png'
								, 'title'  => '株価'
								, 'link'   => 'http://stocks.finance.yahoo.co.jp/stocks'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => '5m'
								, 'stock code' => '8267,8107,4680'
							)
							, array( 
								  'id'     => 100000022
								, 'type'   => 'fortune'
								, 'icon'   => '/icon/horoscope05.png'
								, 'title'  => '１２星座占い'
								, 'link'   => 'http://jugemkey.jp/api/waf/api_free.php'
								, 'height' => ''
								, 'fit'    => 'true'
//								, 'reload' => '1h'
								, 'sign'   => '双子座'
							)
							, array(
								  'id'     => 100000024
								, 'type'   => 'html_editor'
								, 'icon'   => '/icon/editor.png'
								, 'title'  => 'Link'
								, 'link'   => ''
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload'   => ''
								, 'html'   => <<<_EOL_
<head>
<style type="text/css">
.i {
	width:100%;
	border: 1px #CCCCCC solid;
	border-collapse: collapse;
}

.i th {
	text-align:left;
	vertical-align:center;
	background-color:#F4F4F4;
	font-weight: normal;
	white-space: nowrap;
}

.i th,
.i td {
	padding: 4px 8px;
	border:solid 1px #CCCCCC;
	text-align: center;
	width:25%;
}
textarea {
	width: 96%;
}
</style>

<script language="JavaScript">
<!--
function Submit( )
{
	var no = document.MON.NO.value;
	no = "000" + no;
	no = no.substr( -3, 3 );
	var url = "http://pd.appbank.net/m" + no;
	window.open( url, "_blank" );
	return false;
}
-->
</script>
</head>

<body>
<div class="main">
<table class="i">
<tbody>
	<tr>
		<td><strong><a href="https://plus.google.com/u/0/photos/instantupload" target="_blank">Google+</a></strong></td>
		<td><strong><a href="https://www.google.com/calendar?tab=wc" target="_blank">カレンダー</a></strong></td>
		<td><strong><a href="https://drive.google.com/?tab=wo&amp;authuser=0" target="_blank">ドライブ</a></strong></td>
		<td><strong><a href="http://www.blogger.com/?tab=wj" target="_blank">ブロガー</a></strong></td>
	</tr>
	<tr>
		<td><strong><a href="https://docs.google.com/spreadsheet/ccc?key=0An4AVvROjHBCdDFMR2dPa2dTWE9oMWJzRWNLUnJ5QVE#gid=0" target="_blank">カロリー表</a></strong></td>
		<td><strong><a href="https://www.evernote.com/Home.action?__fp=Z78Smsd-TWo3yWPvuidLz-TPR6I9Jhx8&amp;username=yoshiokiya&amp;rememberMe=true&amp;login=Sign+in&amp;login=true&amp;_sourcePage=CvZ6HoX-6FDiMUD9T65RG9ZCS8k3ZqiOcvMVtI39TuE%3D&amp;targetUrl=#st=p" target="_blank">Evernote</a></strong></td>
		<td><strong><a href="http://www.amazon.co.jp/?&tag=shihoriwish-22&linkCode=wsw&" target="_blank">Amazon</a></strong></td>
		<td><strong><a href="https://drive.google.com/keep/" target="_blank">Keep</a></strong></td>
	</tr>
</tbody>
</table>
</div>
</body>
_EOL_
							)
						)
					)
				)
			)
			, array( 
				  'title' => 'NEWS'
				, 'icon'  => '/icon/news.png'
				, 'cols' => array(
					  array( 
						  'width' => '49%'
						, 'parts' => array(
							  array( 
								  'id'     => 100000100
								, 'type'   => 'google_news'
								, 'icon'   => '/icon/google_news.png'
								, 'title'  => 'Googleニュース'
								, 'link'   => 'http://news.google.co.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'view type' => 'normal'
								, 'sort type' => 'unread new'
								, 'max'    => 7
								, 'topic'  => '科学,トップ,ビジネス,社会'
								, 'ignore' => '毎日新聞,ウォール・ストリート・ジャーナル日本版,CNET Japan'
							)
							, array( 
								  'id'     => 100000101
								, 'type'   => 'rss'
								, 'icon'   => '/icon/rss.png'
								, 'title'  => '趣味'
								, 'link'   => 'http://autoc-one.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'name1'  => 'オートックワン'
								, 'url1'   => 'http://autoc-one.jp/news/rss/'
								, 'name2'  => 'DS10開発室'
								, 'url2'   => 'http://ds10.prograrts.com/rss.xml'
								, 'name3'  => ''
								, 'url3'   => ''
								, 'name4'  => ''
								, 'url4'   => ''
								, 'name5'  => ''
								, 'url5'   => ''
								, 'max'    => 10
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
						)
					)
					, array( 
						  'width' => '49%'
						, 'parts' => array(
							  array( 
								  'id'     => 100000110
								, 'type'   => 'itpro'
								, 'icon'   => '/icon/itpro.png'
								, 'title'  => 'ITPro最新ニュース'
								, 'link'   => 'http://itpro.nikkeibp.co.jp/'
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'max'    => 15
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
							, array( 
								  'id'     => 100000111
								, 'type'   => 'itmedia'
								, 'icon'   => 'http://www.itmedia.co.jp/favicon.ico'
								, 'title'  => 'ITMedia PC・AV・スマートフォン'
								, 'link'   => ''
								, 'height' => ''
								, 'fit'    => 'true'
								, 'reload' => '30m'
								, 'max'    => 15
								, 'view type' => 'simple'
								, 'sort type' => 'unread new'
							)
						)
					)
				)
			)
		)
	);
}

//====================================================================
// コンフィグの保存
//====================================================================
function SetConfig( $strEMail, $aryConfig )
{
	//=== メアドは必須 ===============================================
	if ( empty( $strEMail ) )
		$strEMail = "default";

	//================================================================
	$strFile = "./config/{$strEMail}.txt";
	DLOG( "Save [{$strFile}]" );
//	DLOG( "Save [{$strFile}]", $aryConfig );
	file_put_contents( $strFile, serialize( $aryConfig ) );
}

//====================================================================
// コンフィグから指定IDのウィジェットを削除
//====================================================================
function blRemoveWidget( &$aryConfig, $iID )
{
	foreach ( $aryConfig['tabs'] as $iTab => $aryTab )
	{
		foreach ( $aryTab['cols'] as $iCol => $aryCol )
		{
			foreach ( $aryCol['parts'] as $iPart => $aryPart )
			{
				if ( $iID == $aryPart['id'] )
				{
					unset( $aryConfig['tabs'][$iTab]['cols'][$iCol]['parts'][$iPart] );
					return TRUE;
				}
			}
		}
	}

	//=== 指定IDが見つからなかった ===================================
	return FALSE;
}

//================================================================
// ウィジェットの最大IDを取得
//================================================================
function iGetConfigMaxID( $aryConfig )
{
	$iID = 0;
	foreach ( $aryConfig['tabs'] as $aryTab )
	{
		foreach ( $aryTab['cols'] as $aryCol )
		{
			foreach ( $aryCol['parts'] as $aryPart )
			{
				if ( $iID < $aryPart['id'] )
					$iID = $aryPart['id'];
			}
		}
	}

	return $iID;
}

//================================================================
// リンクの最大IDを取得
//================================================================
function iGetConfigMaxLinkID( $aryConfig )
{
	$iID = 0;
	foreach ( $aryConfig['link_bar'] as $aryLink )
	{
		if ( $iID < $aryLink['id'] )
			$iID = $aryLink['id'];
	}

	return $iID;
}

//====================================================================
// コンフィグから指定IDのリンクを削除
//====================================================================
function blRemoveLink( &$aryConfig, $iID )
{
	TLOG( );

	foreach ( $aryConfig['link_bar'] as $iLink => $aryLink )
	{
		if ( $aryLink['id'] == $iID )
		{
			DLOG( "blRemoveLink(): {$iID} found." );
			unset( $aryConfig['link_bar'][$iLink] );
			return TRUE;
		}
	}

	//=== 指定IDが見つからなかった ===================================
	DLOG( "blRemoveLink(): {$iID} not found." );
	return FALSE;
}

//====================================================================
// コンフィグのリンクを移動
//====================================================================
function blMoveLink( &$aryConfig, $iID, $iTo )
{
	TLOG( );

	//=== 移動するリンクを抜き出す ===================================
	$aryLeft = array( );
	$aryMove = NULL;
	foreach ( $aryConfig['link_bar'] as $aryLink )
	{
		if ( $aryLink['id'] == $iID )
			$aryMove = $aryLink;
		else
			$aryLeft[] = $aryLink;
	}

	if ( empty( $aryMove ) )
		return FALSE;

	//=== $iTo の前に挿入 ============================================
	$aryConfig['link_bar'] = array( );
	foreach ( $aryLeft as $aryLink )
	{
		if ( $aryLink['id'] == $iTo )
		{
			$aryConfig['link_bar'][] = $aryMove;
			$aryMove = NULL;
		}

		$aryConfig['link_bar'][] = $aryLink;
	}

	if ( !empty( $aryMove ) )
		$aryConfig['link_bar'][] = $aryMove;

	//================================================================
	return TRUE;
}

?>
