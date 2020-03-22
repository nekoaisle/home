<?php
function aryGetWidget( )
{
	return array(
		  array(
			  'id'     => 0
			, 'type'   => 'html_editor'
			, 'icon'   => '/icon/editor.png'
			, 'title'  => 'HTML Editor'
			, 'link'   => ''
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
			, 'html'   => ''
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'rss'
			, 'icon'   => '/icon/rss.png'
			, 'title'  => 'RSSリーダー'
			, 'link'   => ''
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
			, 'url'    => ''
			, 'view type' => 'simple'
			, 'sort type' => 'unread new'
			, 'max'    => 10
		)
		, array(
			  'id'       => 0
			, 'type'     => 'gmail'
			, 'icon'     => '/icon/gmail.png'
			, 'title'    => 'GMail未読表示'
			, 'link'     => 'http://mail.google.com/'
			, 'height'   => ''
			, 'account'  => ''
			, 'password' => ''
			, 'fit'      => 'true'
			, 'reload'   => ''
		)
		, array( 
			  'id'     => 0
			, 'type'     => 'google_calendar'
			, 'icon'     => '/icon/google_calendar.png'
			, 'title'    => 'Googleカレンダー'
			, 'link'     => 'http://www.google.com/calendar/'
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
			  'id'     => 0
			, 'type'   => 'google_news'
			, 'icon'   => '/icon/google_news.png'
			, 'title'  => 'Googleニュース'
			, 'link'   => 'http://news.google.co.jp/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
			, 'view type' => 'normal'
			, 'sort type' => 'unread new'
			, 'max'    => 8
			, 'topic'  => 'トップ,ビジネス,科学,社会,国際,政治,エンタメ,スポーツ'
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'iGoogle_news'
			, 'icon'   => '/icon/google_news.png'
			, 'title'  => 'iGoogle NEWS'
			, 'link'   => 'http://news.google.co.jp/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'yahoo_news'
			, 'icon'   => '/icon/yahoo.png'
			, 'title'  => 'Yahoo!ニュース'
			, 'link'   => 'http://news.yahoo.co.jp/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
			, 'view type' => 'simple'
			, 'sort type' => 'unread new'
			, 'max'    => 10
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'itpro'
			, 'icon'   => '/icon/itpro.png'
			, 'title'  => 'ITPro最新ニュース'
			, 'link'   => 'http://itpro.nikkeibp.co.jp/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => '30m'
			, 'view type' => 'simple'
			, 'sort type' => 'unread new'
			, 'max'    => 15
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'dsp-twtimeline'
			, 'icon'   => '/icon/twitter.png'
			, 'title'  => 'Twitterタイムライン表示'
			, 'link'   => 'http://twitter.com/'
			, 'height' => '400'
			, 'fit'    => ''
			, 'reload' => ''
			, 'access_token'  => ''
			, 'access_secret' => ''
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'tweetswind'
			, 'icon'   => '/icon/twitter.png'
			, 'title'  => 'tweetswind'
			, 'link'   => 'http://twitter.com/'
			, 'height' => '400'
			, 'fit'    => ''
			, 'reload' => ''
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'weather'
			, 'icon'   => '/icon/weather.png'
			, 'title'  => 'ピンポイント天気予報'
			, 'link'   => 'http://weather.tmyymmt.net/'
			, 'height' => '300'
			, 'fit'    => ''
			, 'reload'   => ''
		)
		, array( 
			  'id'     => 0
			, 'type'   => 'horoscope'
			, 'icon'   => ''
			, 'title'  => '１２星座占い'
			, 'link'   => 'http://shizlabs.amonya.com/igoogle/'
			, 'width'  => '300'
			, 'height' => '240'
			, 'fit'    => 'true'
			, 'reload'   => ''
		)
		, array( 
			  'id'     => 0
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
			  'id'     => 0
			, 'type'   => 'stock'
			, 'icon'   => '/icon/yahoo.png'
			, 'title'  => '株価'
			, 'link'   => 'http://stocks.finance.yahoo.co.jp/stocks'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
			, 'stock code' => '8267,8107'
		)
		, array(
			  'id'     => 0
			, 'type'   => 'exchange'
			, 'icon'   => '/icon/editor.png'
			, 'title'  => '円/ドル 為替チャート'
			, 'link'   => 'http://jpy.jp.fxexchangerate.com/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
		)
		, array(
			  'id'     => 0
			, 'type'   => 'investing'
			, 'icon'   => '/icon/editor.png'
			, 'title'  => '為替レート 円/ドル'
			, 'link'   => 'http://jp.investing.com/charts/%E5%A4%96%E7%82%BA%E3%83%81%E3%83%A3%E3%83%BC%E3%83%88'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
		)
		, array(
			  'id'     => 0
			, 'type'   => 'fx_blog_parts'
			, 'icon'   => '/icon/editor.png'
			, 'title'  => '外国為替チャート'
			, 'link'   => 'http://ck2-blogger.jp/'
			, 'height' => ''
			, 'fit'    => 'true'
			, 'reload' => ''
		)
	);
}
?>