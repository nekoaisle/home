//====================================================================
//====================================================================
//====================================================================
//
//	リンクバーの処理
//
//====================================================================
//====================================================================
//====================================================================
(function($){
    $.fn.dspLinkbar = function (options) {
		//============================================================
		// 変数定義
		//============================================================

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend({
				  add         : "div.add"
				, link        : "div.link"
				, button      : "div.link img.button"
				, menu        : "div.menu"
				, item        : "div.link div.menu div.item"
			}
			, options
		);

		//=== 現在の要素をbarに格納 ==================================
		var bar = $(this);

		//============================================================
		// リンクバーの右端の[＋]ボタンを押した処理
		// 追加ダイアログを表示する
		// ボツ予定＾＾；
		//============================================================
		bar.find( options.add ).dspButton(
		{
			container: options.link,
			//=== ダイアログの初期値を設定 ===========================
			dialogInit: function( $dialog, $link, dspData )
			{
				$dialog.find( 'input[name="V_ID"   ]' ).val( '' );
				$dialog.find( 'input[name="V_TITLE"]' ).val( '新規リンクボタン' );
				$dialog.find( 'input[name="V_URL"  ]' ).val( 'http://' );
				$dialog.find( 'input[name="V_ICON" ]' ).attr( 'checked', false );
				$dialog.find( 'input[type="submit" ]' ).attr( 'value', "追加" );
			}
		});

		//============================================================
		// ダイアログ[☓]ボタンを押した処理
		//============================================================
		bar.find( options.add ).each(function()
		{
			var dspData = $.parseJSON($(this).attr( "data-dsp" ));
			$(dspData.id).on( "click", ".close", function(event)
			{
				$(this).closest( '.dialog' ).slideUp( 'fast' );
				return false;
			});
		});

		//============================================================
		// リンクの設定ボタンを押した処理
		//============================================================
		bar.find( options.button ).dspButton(
		{
			container: options.link,
		});

		//============================================================
		// メニューアイテムをクリックした処理
		// ダイアログを開く
		// ダイアログのIDは, <img dialog="#hoge_dialog" /> にて指定
		//============================================================
		bar.find( options.item ).dspButton(
		{
			container: options.link,
			//========================================================
			// 削除
			//========================================================
			remove: function( $this, $link, dspData )
			{
				// 他のメニューやダイアログはすべて閉じる
				$.dspWidget.closeAllMenuDialog( );

				// 関連オブジェクトの取得
				var title = $link.find( "a" ).text().trim();

				// 確認
				if ( !confirm( '[' + title + '] リンクを削除してもよろしいですか？') )
					return;

				// サーバーに通知
				$.getJSON( 
					"/ajax/link_remove.php?callback=?",
					{
						"id": $link.attr("data-id")
					},
					function( data, textStatus, xhr ) {
//						alert(data.text);
					}
				);

				// ウィジェットを削除
				$link.remove( );

				//====================================================
				return false;
			},
			//=== ダイアログの初期化 =================================
			dialogInit: function( $dialog, $link, dspData )
			{
				// 一旦すべてのチェックを外す
				$dialog.find( 'input[name="V_ICON"]' ).attr( 'checked', false );

				var id    = parseInt( $link.attr( "data-id" ) );
				var icon  = $link.find( "img.icon" ).attr( "src" );
				var title = $link.find( "a" ).text().trim();
				var url   = $link.find( "a" ).attr( "href" );

				$dialog.find( 'input[name="V_ID"   ]' ).val( id );
				$dialog.find( 'input[name="V_TITLE"]' ).val( title );
				$dialog.find( 'input[name="V_URL"  ]' ).val( url );
				$dialog.find( 'input[value="'+icon+'"]' ).attr( 'checked', true );
				$dialog.find( 'input[type="submit" ]' ).attr( 'value', "変更" );
			},
		});

		//=== ドラッグアンドドロップ設定 =============================
		bar.find( options.link ).draggable(
		{
			containment: 'document',
			axis : 'x',
			addClasses: false,
			cursor: 'move',
//			snap: true,		// 吸着
//			snapMode: outer;
			zIndex: 1000,
			helper: 'clone',
//			opacity: 0.3,
			revert: 'invalid',
//			scroll: true,
//			iframeFix: true,
			distance: 16,
			start: function(e, ui) 
			{
				$(ui.helper).width( $(this).width() );
				$(ui.helper).height( $(this).height() );
			},
			drag: function(e, ui) 
			{
//				e.preventDefault();
				e.stopImmediatePropagation();
			},
			stop: function(e, ui) 
			{
				// 終了時には破棄する
//				$(this).draggable( "destroy" );
			},
		});

		bar.droppable( 
		{
			accept: options.link,
//			tolerance: 'pointer',
			drop: function(e, ui) 
			{
//				dropWidget( this, ui );

				var $bar  = $(this);
				var $link = $(ui.draggable[0]);
				var left   = ui.offset.left;

				//=== ドラッグした位置直後の子要素の前に移動 =========
				var drop = false;
				var last = null;
				var $child = $bar.find( options.link );
				$child.each( function()
				{
					if ( this == ui.helper[0] )
						return true;	// helperは無視

					if ( this == $link[0] )
						return true;	// 自身は無視

					var x = $(this).offset().left;
					if ( left < x ) 
					{
						$(this).before( $link );
						drop = $(this).attr("data-id");	// 移動先要素ID
						return false;
					}
					last = this;
					return true;
				})

				//=== 子要素の前に移動できたか調べる =================
				if ( drop == false )
				{
					// 子要素の前には移動できなかったので末尾に移動
					$(last).after( $link );
					drop = '';	// ''の時は列の末尾に移動
				}

				//=== ドラッグによる移動を無効にする =================
//				div.css( "position", "" )
				$link.css( "left", "" )
				$link.css( "top", "" )

				//=== サーバーに通知 =================================
				$.getJSON( 
					"/ajax/link_move.php?callback=?",
					{
						"id": $link.attr("data-id"),
						"to": drop, 
					},
					function( data, textStatus, xhr ) {
					}
				);
			},
		});

		//============================================================
		return this;
	};
})(jQuery);
