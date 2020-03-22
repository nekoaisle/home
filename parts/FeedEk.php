<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	RSSリーダー FeedEk
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	$strURL = $aryConfig['url'];
	$strMax = $aryConfig['max'];
?>
<head>
<style type="text/css">
li {
	margin-top: 0.5em;
}
li:first-child {
	margin-top: 0px;
}
li a {
	color: #333333;
	text-decoration: none;
}
li a:visited {
	color: #AAAAAA;
}
li a:hover {
	color: #3333FF;
	text-decoration: underline;
}
</style>

<script type="text/javascript" src="http://momentjs.com/downloads/moment-with-langs.min.js"></script>
<script type="text/javascript" src="../js/FeedEk.js"></script>
<script type="text/javascript">
jQuery(function($){
	$('#rss').FeedEk({
		FeedUrl: '<?php echo $strURL; ?>',
		MaxCount: <?php echo $strMax; ?>,
		ShowPubDate: false,
		ShowDesc: false
	});
});
</script>
</head>
<!--===============================================================-->
<body>
<div id="rss"></div>
</body>
</body>
<?php
}
?>