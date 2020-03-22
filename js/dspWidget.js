//====================================================================
//====================================================================
//====================================================================
//
//	ウィジェットの処理
//
//====================================================================
//====================================================================
//====================================================================
(function($){
    $.fn.dspWidget = function (options) {
		//============================================================
		// 変数定義
		//============================================================

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend({
				  widget      : "div.widget"
				, head        : "div.head"
				, button      : "img.button"
				, menu        : "div.menu"
				, item        : "div.item"
			}
			, options
		);

		//=== 現在の要素をwidgetに格納 ===============================
		var $widgets = this;

		//============================================================
		// ウィジェットの設定ボタンをクリック処理
		// メニューを開く
		//============================================================
		$widgets.find( options.button ).dspButton(
		{
			container: options.widget,
			//=== 再読み込み =========================================
			reload : function( $this, $widget, dspData )
			{
				// 他のメニューやダイアログはすべて閉じる
				$.dspWidget.closeAllMenuDialog( );

				// 現在の url を取得
				var $iframe = $widget.find( "iframe" );

				// urlを補正
				var src = $iframe.attr("src");
				src = src.split("#")[0];	// ハッシュを除去
				src = src.split("?")[0];	// クエリーを除去

				// リロード
				$iframe.attr( "src", src );

				// デフォルトの処理をしない
				return false;
			},
		});

		//============================================================
		// メニューアイテムをクリックした処理
		// ダイアログを開く
		// ダイアログのIDは, <img dialog="#hoge_dialog" /> にて指定
		//============================================================
		$widgets.find( options.menu+" "+options.item).dspButton(
		{
			container: options.widget,
			//=== 設定変更 ===========================================
			// ウィジェットの設定ページを表示
			preference : function( $this, $widget, dspData )
			{
				// 他のメニューやダイアログはすべて閉じる
				$.dspWidget.closeAllMenuDialog( );

				//
				var $iframe = $widget.find( "iframe" );

				// src に ?PREFERENCE クエリーを追加
				var src = $iframe.attr("src");
				var a = src.split("?");		// クエリを除去
				var src = a[0] + "?PREFERENCE"

				// 再読み込み
				$iframe.attr( "src", src );

				// デフォルトの処理をしない
				return false;
			},
			//=== 削除 ===============================================
			remove : function( $this, $widget, dspData )
			{
				// 他のメニューやダイアログはすべて閉じる
				$.dspWidget.closeAllMenuDialog( );

				// 確認
				var title = $widget.find( "a" ).text().trim();
				if ( !confirm( '[' + title + '] ウィジェットを削除してもよろしいですか？') )
					return false;

				// 関連オブジェクトの取得
				var tabbody = $widget.closest( "div.tabbody" );
				var iframe   = $widget.find( "iframe" );

				// サーバーに通知
				$.getJSON( 
					  "/ajax/widget_remove.php?callback=?"
					, {"id":dspData.id}
					, function( data, textStatus, xhr ) {
					}
				);

				// ウィジェットを削除
				$widget.remove( );

				// widget の ID を振り直す
				$.dspWidget.renumberWidget( tabbody );

				// デフォルトの処理をしない
				return false;
			},
		});

		//============================================================
		// ウィジェットの設定ボタンを押した処理
		// このボタンの下のヘッダーにmousedown()イベントが設定してある
		// ボタンを押した際に下のヘッダーのイベントが起動しないようブロック
		//============================================================
		$widgets.on( "mousedown", options.button, function()
		{
			// 下の要素にイベントを伝えない
//			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		});

		//============================================================
		// タイトルバーをクリックした処理
		// ドラッグ・アンド・ドロップ開始
		// ※ドラッグ開始をウィジェット全体ではなくヘッダーだけに限定するため
		// draggable("disabe") だとウィジェットの表示が薄くなってしまう
		//============================================================
		$widgets.on( "mousedown", options.head, function()
		{
			// 他のメニューやダイアログはすべて閉じる
			$.dspWidget.closeAllMenuDialog( );

			// ドラッグ機能を設定
			var $widget = $(this).closest( options.widget );
			$.dspLayout.setDraggable( $widget );

			// クリックしたことにする
			$.dspLayout.requestDelayedExecution( 
				function() { $widget.mousedown(); },
				10
			);
		});

		//============================================================
		return this;
	};

	//================================================================
	// 外部からも利用可能な関数を定義
	//================================================================
	$.dspWidget = {
		//============================================================
		// widget の ID を振り直す
		//============================================================
		renumberWidget: function( $tabbody )
		{
			$tabbody.find( "div.column" ).each( function( cid )
			{
				var rows = $(this).find( "div.widget" );
				rows.each( function( rid )
				{
					$(this).attr( "id", "row" + cid + rid );
				});
			});
		},
		//============================================================
		// メニューやダイアログをすべて閉じる
		//============================================================
		closeAllMenuDialog: function( )
		{
			//=== 他のメニューやダイアログはすべて閉じる =============
			$("div.dspMenu"  ).slideUp('fast');
			$("div.dspDialog").slideUp('fast');

			//=== 選択中フラグをすべて消去 ===========================
			$(".dspOpened").removeClass( "dspOpened" );
		},
		//============================================================
		// 既に開いているか調べる
		//============================================================
		isOpened: function( $button )
		{
			return $button.hasClass( "dspOpened" );
		},
		//============================================================
		// 開いているフラグをセット
		//============================================================
		setOpened: function( $button )
		{ 
			$button.addClass( "dspOpened" );
		}
	};

})(jQuery);
