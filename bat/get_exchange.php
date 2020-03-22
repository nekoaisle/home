<?php
	//================================================================
	// 現在時刻の確認
	//================================================================
	// 夏時間：日本時間で月曜日午前5時～土曜日午前6時
	// 冬時間：日本時間で月曜日午前6時～土曜日午前7時
	$strTime = date( 'wHi' );
	if ( ($strTime < '10500') || ($strTime >= '61100') ) {
		echo "時間外\n";
		return;	// 取引時間外
	}

	//================================================================
	// 為替レートを取得する
	// http://jp.currencyconverterrate.com/jpy/rss.xml にて日本円からのレートが取得できるが
	// 何故か不正確なので米ドルからのレートを取得する
	//================================================================
	$objXML = simplexml_load_file( "http://jp.currencyconverterrate.com/usd/rss.xml" );
	$aryItem = array( );
	$strRE = '米ドル\(USD\)\/.*\(([^\)]+)\)';
//	$strRE = 'United States Dollar\(USD\)\/.*\(([^\)]+)\)';
	switch ( strtoupper( $objXML->getName() ) )
	{
	    //=== RSS1.0 =============================================
	case 'RDF':
		echo "RDF\n";
		foreach ( $objXML->item as $objItem )
		{
			$ary = array( );
			$ary['TITLE'] = (string)$objItem->title;
			$ary['LINK' ] = (string)$objItem->link;
			$ary['DESC' ] = (string)$objItem->description;
			$ary['DATE' ] = (string)$objItem->children('http://purl.org/dc/elements/1.1/')->date;

			$ary['TIME' ] = strtotime( $ary['DATE' ] );

			if ( preg_match( "/{$strRE}/ius", $ary['TITLE'], $m ) === 1 )
				$aryItem[$m[1]] = $ary;
		}
		break;

		//=== RSS2.0 =============================================
	case 'RSS':
		echo "RSS\n";
		foreach ( $objXML->channel[0]->item as $objItem )
		{
			$ary = array( );
			$ary['TITLE'] = (string)$objItem->title;
			$ary['LINK' ] = (string)$objItem->link;
			$ary['DESC' ] = (string)$objItem->description;
			$ary['DATE' ] = (string)$objItem->pubDate;

			$ary['TIME' ] = strtotime( $ary['DATE' ] );
//			echo $ary['DATE' ], "\n";
//			echo date( 'Y/m/d H:i:s', $ary['TIME' ] ), "\n";
//			echo $ary['TITLE'], "\n";

			if ( preg_match( "/{$strRE}/ius", $ary['TITLE'], $m ) === 1 )
			{
//				var_dump( $m );
				$aryItem[$m[1]] = $ary;
			}
		}
		break;

		//=== ATOM ===============================================
	case 'FEED':
		echo "FEED\n";
		foreach ( $objXML->entry as $objItem )
		{
			$ary['TITLE'] = (string)$objItem->title;
			$ary['LINK' ] = (string)$objItem->link['href'];
			$ary['DESC' ] = (string)$objItem->content;
			if ( isset( $objItem->updated) )
				$ary['DATE' ] = (string)$objItem->updated;
			else if ( isset( $objItem->issued ) )
				$ary['DATE' ] = (string)$objItem->issued;
			else if ( isset( $objItem->published ) )
				$ary['DATE' ] = (string)$objItem->published;

			$ary['TIME' ] = strtotime( $ary['DATE' ] );

			if ( preg_match( "/{$strRE}/ius", $ary['TITLE'], $m ) === 1 )
				$aryItem[$m[1]] = $ary;
		}
		break;

		//============================================================
	default: die( );
	}
//var_dump( $aryItem );

	//================================================================
	// 取得した為替レートをファイルに保存する
	//================================================================
	$strDir = dirname(__FILE__);

	//================================================================
	$aryExchange = array( );
	foreach ( $aryItem as $key => $ary )
	{
		if ( $key != 'JPY' )
			continue;
//var_dump( $ary );

		//=== RSSフィードから現在のレートを抽出 ======================
		$strRE = '1 米ドル = "RATE" 日本 円';
//		$strRE = '1 United States Dollar = "RATE" Japanese Yen';
		$strRE = preg_quote( $strRE, '/' );
		$strRE = preg_replace( '/\s+/ius', '\s+', $strRE );
		$strRE = str_replace( '"RATE"', '([0-9\.]+)', $strRE );
		if ( preg_match( "/{$strRE}/ius", $ary['DESC'], $m ) === 1 )
		{
			//========================================================
			$fRate = (float)$m[1];
			echo "\$1 = \\{$fRate}\n";

			//=== この日付のログファイルを取得 =======================
			$strDate = date( 'Y-m-d', $ary['TIME'] );
			$strFile = "{$strDir}/../config/jpy-usd-{$strDate}.txt";
			if ( !array_key_exists( $strDate, $aryExchange ) )
			{
				//=== まだ読み込んでいない ===========================
				if ( file_exists( $strFile ) )
					$aryExchange[$strDate] = unserialize( file_get_contents( $strFile ) );
				else
					$aryExchange[$strDate] = array( );
			}

			//========================================================
			$strTime = date( 'H:i', $ary['TIME'] );
			$aryExchange[$strDate][$strTime] = $fRate;

//			echo $strTime, "\n";
//			echo $fRate, "\n";
		}
	}

	//=== 取得した為替レートを保存する ===============================
	foreach ( $aryExchange as $strDate => $aryDate )
	{
		$strFile = "{$strDir}/../config/jpy-usd-{$strDate}.txt";
		file_put_contents( $strFile, serialize( $aryDate ) );
	}
?>
