<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	Twitter タイムライン表示
//	専用設定
//	'access_token'  => [access_token]
//	'access_secret' => [access_token_secret]
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname( __FILE__ ) . '/../twitteroauth/twitteroauth.php' );
define( 'API_KEY'   , 'KmXt2y93wfyAOMObSKLvA' );
define( 'API_SECRET', 'kjtm6h8jJHIUhB70kmw8AdwUlnbRLTVUM29lt3Z8L0' );
function EchoContents( $aryConfig )
{
	//================================================================
	$objApp = objGetApp( );
	$strPageID = $objApp->m_strID;

	//================================================================
	if ( !array_key_exists( 'twitter', $_SESSION ) )
		$_SESSION['twitter'] = array( );

	$arySession = &$_SESSION['twitter'];

//	$arySession['access_token' ] = '';
//	$arySession['access_secret'] = '';

//	$arySession['access_token' ] = '101938517-PnjPx8lXcEXsTIxeXnH6NASoTKtPhYLFhepFBv7j';
//	$arySession['access_secret'] = 'oBDRrMTPCeAFEVJ6yVVkZkfcsagIKiiqG0gmTT1csK6lN';

	//=== 設定からアクセストークンを取得する =========================
	// @@ セッションは使用しないようにすること
	if ( empty( $arySession['access_token' ] ) || empty( $arySession['access_secret'] ) )
	{
		//=== どちらか一方でも空なら設定から取得 =====================
		if ( !empty( $aryConfig['access_token' ] ) && !empty( $aryConfig['access_secret'] ) )
		{
			// 設定に正しくトークンが設定されている
			$arySession['access_token' ] = $aryConfig['access_token' ];
			$arySession['access_secret'] = $aryConfig['access_secret'];
		}
		else
		{
			// セッションにどちらか片方が入っていた場合クリア
			$arySession['access_token' ] = '';
			$arySession['access_secret'] = '';
		}
	}

	//================================================================
	if ( empty( $arySession['access_token'] ) && empty( $_REQUEST['oauth_token'] ) )
	{
		//============================================================
		// 連携アプリ認証ページヘのリンクを表示
		// 
		// 本来はリダイレクトしてそのまま表示したいが Twitter の 連携ア
		// プリ認証ページは X-Frame-Options SAMEORIGIN が設定されている
		// ので iframe 内には表示できない。
		//============================================================
		$objTW = new TwitterOAuth(API_KEY, API_SECRET);
		$aryToken = $objTW->getRequestToken( "http://home.prograrts.com/parts/{$strPageID}.html" );
		if ( !isset( $aryToken['oauth_token'] ) )
		{
			// $aryToken['oauth_token']がなかったらエラー
			echo "error: getRequestToken\n";
			return;
		}

		$arySession['oauth_token' ] = $aryToken['oauth_token'       ];
		$arySession['oauth_secret'] = $aryToken['oauth_token_secret'];

		// 認証用URL取得
		$strAuth = $objTW->getAuthorizeURL( $arySession['oauth_token'] );
?>
<style>
body {
	width: 100%;
}
div.contents{
	margin: 8px;
}
h1 {
	font-size: 1.5em;
	font-weight: bold;
	color: #0088FF;
	margin-bottom: 4px;
}
span.sub {
	color: #AAAAAA;
}
a.button {
	width: 160px;
	text-align: center;
	text-decoration: none;
	color: #333333;
	padding: 4px 8px;
	border: 1px #CCCCCC solid;
	border-radius: 4px;
    background-color: #EEEEEE;
	background: -moz-linear-gradient(top,#FFF 0%,#AAA);
	background: -webkit-gradient(linear, left top, left bottom, from(#FFF), to(#AAA));
}
a.center {
	display: block;
	margin: 0px auto;
}
img {
	vertical-align: middle;
}
</style>
<body>
<div class="contents">
<h1>dsp-twtimeline</h1>
<span class="sub">Twitter タイムライン表示ウィジェット</span><br />
<br />
このウィジェットはTwitterと連携して動作します。<br />
<br />
1. 下記のボタンをクリックして連携アプリ認証ページを開き、連携アプリ認証を行ってください。<br />
<br />
<a class="button center" href="<?php echo $strAuth; ?>">Twitter連携アプリ認証へ</a><br />
<br />
2. 認証が完了したら下記のボタンか右上の<img src="/icon/reload.png" />(リロード)ボタンを押すとタイムラインが表示されます。
<br />
<br />
<a class="button center" href="<?php echo "/parts/{$strPageID}.html"; ?>">タイムラインを表示</a><br />
</div><!--contents-->
</body>
<?php
//		throw new CKyaExceptionRedirect( $strAuth );
		return;
	}
	else if ( empty( $arySession['access_token'] ) )
	{
		//============================================================
		// コールバック処理
		// アクセストークンを保存する
		//============================================================
		// getToken.phpで取得した$token['oauth_token']と一致するかチェック
		if ( $arySession['oauth_token'] !== $_REQUEST['oauth_token'] )
		{
			echo 'トークンが一致しません';
			unset( $arySession['oauth_token' ] );
			unset( $arySession['oauth_secret'] );
			return;
		}

		// アクセストークンを取得
		$objTW = new TwitterOAuth( 
			  API_KEY
			, API_SECRET
			, $arySession['oauth_token']
			, $arySession['oauth_secret']
		);
		$aryAccess = $objTW->getAccessToken( $_REQUEST['oauth_verifier'] );

		// TwitterのユーザID + スクリーンネームを取得し変数へ格納
		$arySession['access_token' ] = $aryAccess['oauth_token'       ];
		$arySession['access_secret'] = $aryAccess['oauth_token_secret'];
		$arySession['user_id'      ] = $aryAccess['user_id'           ];
		$arySession['screen_name'  ] = $aryAccess['screen_name'       ];


		//=== アクセストークンをコンフィグに保存 =====================
		$objApp->m_aryParts['access_token' ] = $aryAccess['oauth_token'       ];
		$objApp->m_aryParts['access_secret'] = $aryAccess['oauth_token_secret'];
		SetConfig( $objApp->m_strUser, $objApp->m_aryConfig );
?>
<style>
a.button {
	width: 160px;
	text-align: center;
	text-decoration: none;
	color: #333333;
	padding: 4px 8px;
	border: 1px #CCCCCC solid;
	border-radius: 4px;
	background-color: #EEEEEE;
	background: -moz-linear-gradient(top,#FFF 0%,#AAA);
	background: -webkit-gradient(linear, left top, left bottom, from(#FFF), to(#AAA));
}
img {
	vertical-align: middle;
}
</style>
<body>
<br />
連携アプリ認証が完了しました。ウィジェトの <a class="button">タイムラインを表示</a> ボタンか右上の<img src="/icon/reload.png" />(リロード)ボタンを押すとタイムラインが表示されます。<br />
<br />
</body>
<?php
	}

	//================================================================
	// タイムライン表示
	//================================================================
	//OAuthオブジェクトを生成する
	$objTW = new TwitterOAuth( 
		  API_KEY
		, API_SECRET
		, $arySession['access_token' ]
		, $arySession['access_secret']
	);

	//home_timelineを取得するAPIを利用。Twitterからjson形式でデータが返ってくる
	$vRequest = $objTW->OAuthRequest( 
		  'https://api.twitter.com/1.1/statuses/home_timeline.json'
		, 'GET'
		, array("count"=>"50")
	);

	//Jsonデータをオブジェクトに変更
	$aryTweet = json_decode( $vRequest );

	//オブジェクトを展開
	if ( !empty( $aryTweet->errors ) )
	{
?>
取得に失敗しました。<br/>
エラー内容：<br/>
<pre>
<?php var_dump($aryTweet); ?>
</pre>
<?php
	}
	else
	{
?>
<style>
div.tweet {
	border-top: solid 1px #CCCCCC;
	padding: 4px;
}
div.tweet:first-child {
	border-top: none;
	padding-topp: 0px;
}
div.tweet div.image {
	float:left;
	margin-right: 0.5em;
	margin-bottom: 4px;
}
div.tweet div.name {
	float:left;
	color: #AAAAAA;
}
div.tweet div.name a {
	font-size: 1.0em;
	font-weight: bold;
	color: #0088FF;
	text-decoration: none;
}
div.tweet div.name a:link {
	color: #0088FF;
}
div.tweet div.name a:visited {
	color: #0088FF;
}
div.tweet div.name a:hover {
	color: #0088FF;
	text-decoration: underline;
}

div.tweet div.at {
	float:right;
	color: #888888;
}
div.tweet div.text {
}
</style>
<?php
		//オブジェクトを展開
//		DLOG( VDMP( $aryTweet ) );
		foreach ( $aryTweet as $obj )
		{
			$strText    = $obj->text;
			$strName    = $obj->user->name;
			$strAccount = $obj->user->screen_name;
			$strImage   = $obj->user->profile_image_url;
			$strTime    = $obj->created_at;
			$i = time() - strtotime($strTime);
			if ( $i < 60*60 )
				$strTime = (int)ceil( $i / 60 ) . '分';
			else if ( $i < 24*60*60 )
				$strTime = (int)ceil( $i / (60*60) ) . '時間';
			else
				$strTime = date('Y-m-d H:i:s', $i);
?>
<div class="tweet">
	<div class="image">
		<img src="<?php echo $strImage; ?>" width="32" />
	</div>
	<div class="name">
		<a href="https://twitter.com/<?php echo $strAccount; ?>"><?php echo $strName; ?></a><br />
		@<?php echo $strAccount; ?>
	</div>
	<div class="at">
		<?php echo $strTime; ?>
	</div>
	<div style="clear:both"></div>
	<div class="text">
		<?php echo $strText; ?>
	</div>
</div>
<?php
		}
	}
	return;
}
?>