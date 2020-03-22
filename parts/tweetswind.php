<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	Twitter タイムライン表示ウィジェット
//	http://www.tweetswind.com/
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	$aryOption = array(
		  "isOnlyMe"                         => "false"
		, "twitterwind_frame_width"          => "360"
		, "twitterwind_frame_height"         => "378"
		, "twitterwind_frame_border"         => "on"
		, "twitterwind_frame_border_color"   => "E3E3E3"
		, "twitterwind_base_font_size"       => "12"
		, "twitterwind_logoimage"            => "blueonwhite"
		, "twitterwind_username"             => "none"
		, "twitterwind_username_bgcolor"     => ""
		, "twitterwind_username_color"       => "333333"
		, "twitterwind_username_follow"      => "off"
		, "twitterwind_max_length"           => "128"
		, "twitterwind_logo_bgcolor"         => ""
		, "twitterwind_twit"                 => "on"
		, "twitterwind_twit_scroll_color"    => "C0DEED"
		, "twitterwind_twit_scroll_bg_color" => "FFFFFF"
		, "twitterwind_twit_bgcolor"         => ""
		, "twitterwind_twit_color"           => "333333"
		, "twitterwind_twit_link_color"      => "0084B4"
		, "twitterwind_opacity"              => "on"
		, "twitterwind_follower"             => "none"
		, "twitterwind_follower_bgcolor"     => "FFFFFF"
		, "usn"                              => "143536"
	);

	$strOption = json_encode( $aryOption );
	$strOption = urlencode( $strOption );
?>
<body>
<!-- start TweetsWind code -->
<iframe scrolling="no" frameborder="0" id="twitterWindIframe" style="width:360px;height:378px; border:solid 1px; border-color:#E3E3E3;" src="http://www.tweetswind.com/show?option=<?php echo $strOption; ?>" allowTransparency="true" > </iframe>
<!--利用規約に従ってページ内に必ずリンクを表示してください-->
<div style="font-size:12px; text-align:right; width:360px"><a target="_blank" href="http://www.tweetswind.com">TweetsWind</a></div> 
<!-- end TweetsWind code -->
</body>
<?php
}
?>