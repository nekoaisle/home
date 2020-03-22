jQuery(function($)
{
	//================================================================
	// タブを選択
	//================================================================
	$("div.tab_bar div.tab").on( "click", function()
	{
		//=== コマンドごとの処理 =====================================
		var s = $(this).attr( "data-dsp" );
		var dspData = $.parseJSON( s );
		switch ( dspData.cmd )
		{
			//========================================================
			// 表示ページの変更
			//========================================================
		case "page":

			// タブのアクティブ表示を設定
			$("div.tab_bar div.tab").removeClass( "active" );
			$(this).addClass( "active" );

			// 一旦全ページを非表示にする
			$("div.rss").hide( );
			$("div.rss div.page").hide( );

			//=== 対象を表示開始 =========================================
			$rss = $(dspData.rss);
			$rss.show( );
			$rss.find( "div.page0" ).show( );

			//=== 親フレームの高さを調整 =================================
			$.dspAutoHeight.iframe( );
//			$(window.parent).triggerHandler('resize');
			break;
		}
	});

	$("div.tab_bar div.tab0").addClass( "active" );
	$("div.rss").hide( );
	$("div.rss div.page").hide( );
	$("div.rss0").show();
	$("div.rss0 div.page0").show();

	//================================================================
	// ページを選択
	//================================================================
	$("div.navi_box div.button").on( "click", function()
	{
		$this = $(this);

		//=== 無効なときは何もしない =================================
		// なぜか <div disabled> では無効にならなかった＞＜；
		if ( $this.hasClass( "disabled" ) )
			return false;

		//=== コマンドごとの処理 =====================================
		var s = $this.attr( "data-dsp" );
		var dspData = $.parseJSON( s );
		switch ( dspData.cmd )
		{
			//========================================================
			// 指定URLの記事を既読にする
			//========================================================
		case "readed":
			var $rss = $this.closest("div.rss");
			var dspDataRss = $.parseJSON( $rss.attr( "data-dsp" ) );

			//=== サーバーに通知 =====================================
			var prm = {
				  "rss": dspDataRss.rss
				, "url": dspData.ids
			}
			$.ajax({
				type: "post",
				dataType : "jsonp",
				scriptCharset: 'utf-8',
				crossDomain: false,
				cache: false,
				url: "/ajax/rss_readed.php",
				data: prm,
				success: function( data, textStatus, xhr ) {
				},
				error: function( xhr, textStatus, errorThrown ) {
				}
			});

			//=== 指定URLの記事を既読にする ==========================
			for ( var k in prm.url )
			{
				var $a = $('a[href="'+prm.url[k]+'"]');
				$a.closest( "li" ).addClass( "readed" );
			}

			//=== 未読数の更新 =======================================
			// HTML内の全記事を対象に既読を設定しているので全div.rssを対象とする
			$.dspRSS.dispUnread( );

			//=== 親フレームの高さを調整 =============================
			$.dspAutoHeight.iframe( );

			//========================================================
			break;

			//========================================================
			// 表示ページの変更
			//========================================================
		case "page":

			// 一旦全ページを非表示にする
			$rss = $this.closest( "div.rss" );
			$rss.find( "div.page" ).hide( );

			$("div.rss").hide( );
			$rss.show( );

			//=== 対象を表示開始 =====================================
			$(dspData.id).show( );

			//=== 親フレームの高さを調整 =============================
			$.dspAutoHeight.iframe( );
//			$(window.parent).triggerHandler('resize');
			break;
		}

		//============================================================
		return false;
	});

	//=== 起動時にページを選択する処理 ===============================
	$("div.rss").hide( );
	$("div.rss div.page").hide( );

	$("div.tab_bar div.tab0").addClass( "active" );
	$("div.rss0").show( );
	$("div.rss0 div.page0").show( );

	//=== リンクをクリックしたら既読をサーバーに登録する =============
	$("body").on( "click.dspParts", "a", function(event)
	{
		if ( !this.href || (location.hostname == this.hostname) )
		{
			//=== 内部サイトならば何もしない =========================
		}
		else
		{
			//=== 指定URLの記事を既読にする ==========================
			// ※HTML 内のすべての記事を対象とする
			var $a = $( 'a[href="' + this.href + '"]' );
			$a.closest( "li" ).addClass( "readed" );

			//=== 未読数の更新 =======================================
//			$rss = $(this).closest( "div.rss" );
//			$.dspRSS.dispUnread( $rss );
			// HTML内の全記事を対象に既読を設定しているので下記が必要
			$.dspRSS.dispUnread( );

			//=== 既読登録 ===========================================
			var $rss = $(this).closest( "div.rss" );
			var dspDataRss = $.parseJSON( $rss.attr( "data-dsp" ) );

			var prm = {
				"rss": dspDataRss.rss,
				"url": this.href,
			};

			$.getJSON( 
				"/ajax/rss_readed.php?callback=?", 
				prm,
				function( data, textStatus, xhr ) {
				}
			);

			//=== 別プロセスで開く ===================================
			// 何故か window.open() が undefined を返してくる＞＜；
			var wo = window.open();
			if ( wo )
			{
				wo.opener = null;
				wo.location.href = this.href;
				return false;
			}
		}

		//============================================================
		return true;
	});

	//================================================================
	// グローバル関数
	//================================================================
	$.dspRSS = {
		//============================================================
		// 未読数を表示
		// $container "div.rss" 省略時はHTML内のすべての "div.rss"を処理する
		//============================================================
		dispUnread: function ( $container )
		{
			if ( $container == null )
				$container = $("div.rss");

			//=== すべての rss について処理する ======================
			$container.each( function( id )
			{
				var $rss = $(this);

				// [全記事数]-[既読数]=[未読数]
				var $all  = $rss.find( "div.page li" );
				var $read = $rss.find( ".readed" );

				var all = $all.length;
				var read = $read.length;
				var unread = all - read;

				// ナビゲーターに "未読:n" を表示
				var $unread = $rss.find( "div.unread span" );
				$unread.text( unread );

				//=== タブのタイトルにも表示 =========================
				var $tabbar = $( "div.tab_bar" );
				if ( $tabbar.length > 0 )
				{
					// 対応するタブの番号を取得
					var dspDataRss = $.parseJSON( $rss.attr( "data-dsp" ) );
					var no = dspDataRss.no;

					var $tab = $tabbar.find( "div.tab"+no );
					if ( unread > 0 )
						$tab.text( $tab.attr( "title" ) + ":" + unread );
					else
						$tab.text( $tab.attr( "title" ) );
				}
			});
		}
	};
	$.dspRSS.dispUnread( $("div.rss") );

});
