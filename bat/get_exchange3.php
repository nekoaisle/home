<?php
//====================================================================
//====================================================================
//====================================================================
//
//	為替レートの取得 HTML版
//
//====================================================================
//====================================================================
//====================================================================

	//================================================================
	// 現在時刻の確認
	//================================================================
	// 夏時間：日本時間で月曜日午前5時～土曜日午前6時
	// 冬時間：日本時間で月曜日午前6時～土曜日午前7時
	$strTime = date( 'wHi' );
	$strDow  = date( 'w' );
	if ( ($strTime < '10500') || ($strTime >= '6060000') )
	{
		echo "時間外\n";
		die( );	// 取引時間外
	}

	//================================================================
	// 為替レートを取得する
	//================================================================
	$strHTML = file_get_contents( "http://jp.currencyconverterrate.com/usd/jpy.html" );
	$strRE = <<<_EOL_
<div class="currency_today_1" align="center" style="font-size:30px;"><b> 1 USD </b><br/><br/>
<b>= </b><br/><br/>
<b>   'PRICE'  JPY  </b><br/><br/><br/>
<div class="currency_today_2" align="center">Bid Price: 'BID'</div>	<br/>
<div class="currency_today_2" align="center">Ask Price: 'ASK'</div>	<br/>
<div class="currency_today_2" align="center">為替レート 更新: 'DATE' UTC</div>
_EOL_;

	$strRE = preg_quote( $strRE, '/' );
//	echo $strRE,"\n\n";

	$aryRep = array(
		  "'PRICE'" => '([0-9\.]+)'					// 101.495
		, "'BID'"   => '([0-9\.]+)'					// 101.47
		, "'ASK'"   => '([0-9\.]+)'					// 101.52
		, "'DATE'"  => '([0-9]+)\/([0-9]+)\/([0-9]+)\s+([0-9]+)\:([0-9]+)'		// 14/04/2014 00:56
	);
	$strRE = str_replace( array_keys( $aryRep ), array_values( $aryRep ), $strRE );
//	echo $strRE,"\n\n";

	$strRE = preg_replace( '/\s+/ius', '\s+', $strRE );
//	echo $strRE,"\n\n";

	if ( preg_match( "/{$strRE}/ius", $strHTML, $m ) !== 1 )
	{
		echo "取得できなかった\n";
		die( );
	}

	$ary['PRICE'] = $m[1];
	$ary['BID'  ] = $m[2];
	$ary['ASK'  ] = $m[3];
	$ary['DATE' ] = "{$m[6]}/{$m[5]}/{$m[4]} {$m[7]}:{$m[8]} UTC";
	$ary['TIME' ] = strtotime( $ary['DATE'] );
//	var_dump( $ary );
//	echo print_r( date( 'Y/m/d H:i:s', $ary['TIME'] ) ), "\n";

	//================================================================
	// 取得した為替レートをファイルに保存する
	//================================================================
	$strDir = dirname(__FILE__);
	$strDate = date( 'Y-m-d', $ary['TIME'] );
	$strFile = "{$strDir}/../config/jpy-usd-{$strDate}.txt";

	//=== この日付のログファイルを取得 ===============================
	if ( file_exists( $strFile ) )
		$aryExchange = unserialize( file_get_contents( $strFile ) );
	else
		$aryExchange = array( );

	//========================================================
	$strTime = date( 'H:i', $ary['TIME'] );
	$aryExchange[$strTime] = (float)$ary['PRICE'];

	//=== 取得した為替レートを保存する ===============================
	file_put_contents( $strFile, serialize( $aryExchange ) );
?>
