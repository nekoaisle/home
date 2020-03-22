<?php
$strURL = 'http://jp.reuters.com/article/marketsNews/idJPL3N0N73VS20140416';
$strURL = 'http://www.akibaoo.co.jp/01/main';
$strRE = '^http(s)?\:\/\/(?!www\.akibaoo\.co\.jp\/)';
echo preg_match( "/{$strRE}/", $strURL, $m );
?>