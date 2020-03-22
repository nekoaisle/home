//====================================================================
//====================================================================
//====================================================================
//
//	タブの処理
//
//====================================================================
//====================================================================
//====================================================================
(function($){
    $.fn.dspButton = function( options )
    {
		//============================================================
		// 変数定義
		//============================================================

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend({
				container: 'div',
				menu: cmdMenu, 
				dialog: cmdDialog, 
			}
			, options
		);

		//============================================================
		// タブの設定ボタンを押した処理
		//============================================================
		$(this).on( "click", function(event)
		{
			//=== 下の要素にイベントを伝えない =======================
			event.preventDefault();
			event.stopImmediatePropagation();

			//========================================================
			var $this = $(this);
			var $container = $(this).closest( options.container );

			//=== コマンドごとの処理 =================================
			var s = $this.attr( "data-dsp" );
			var dspData = $.parseJSON( s );
			if ( options[dspData.cmd] )
				return options[dspData.cmd]( $this, $container, dspData );

			//========================================================
			return true;
		});

		//============================================================
		// 	コマンド menu
		//============================================================
		function cmdMenu( $this, $container, dspData )
		{
			if ( $.dspWidget.isOpened($this) )
			{
				// 既に開いているので閉じる
				$.dspWidget.closeAllMenuDialog( );
			}
			else
			{
				// 他のメニューやダイアログはすべて閉じる
				$.dspWidget.closeAllMenuDialog( );

				// メニューを開く
				$.dspWidget.setOpened($this);

				var $menu = $container.find(dspData.menu);
				$menu.css( "z-index", 1000 );
				$menu.slideDown('fast');
			}
			return false;
		}

		//============================================================
		// 	コマンド dialog
		//============================================================
		function cmdDialog( $this, $container, dspData )
		{
			// 他のメニューやダイアログはすべて閉じる
			$.dspWidget.closeAllMenuDialog( );

			// ダイアログのidを取得
			var $dialog = $( dspData.id );

			//=== ダイアログ初期化関数が登録されていれば呼び出す =====
			if ( options.dialogInit != null )
				options.dialogInit( $dialog, $container, dspData )

			// ダイアログを開く
			$dialog.slideDown('fast');

			// デフォルトの処理をしない
			return false;
		}

		//============================================================
		return this;
	};
})(jQuery);

