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
    $.fn.dspTabbar = function (options) {
		//============================================================
		// 変数定義
		//============================================================

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend({
				  tab          : "div.tab"
				, button       : "img.button"
				, menu         : "div.menu"
				, item         : "div.item"
				, body         : "#tabbody"

				, active       : "dspActive"		// アクティブなタブ フラグ
				, classTabbody : "div.dspTabbody"	// タブボディを選択するために付与するクラス
			}
			, options
		);

		//=== 現在の要素を$barに格納 =================================
		var $bar = $(this);

		//============================================================
		// タブの設定ボタンを押した処理
		//============================================================
		$bar.find( options.button ).dspButton(
		{
			container: options.tab,
		});

		//============================================================
		// メニューアイテムをクリックした処理
		// ダイアログを開く
		// ダイアログのIDは, <img dialog="#hoge_dialog" /> にて指定
		//============================================================
		$bar.find( options.menu+" "+options.item ).dspButton(
		{
			container: options.tab,
			//=== ダイアログの初期化 =================================
			dialogInit: function( $dialog, $tab, dspData )
			{
				// ID を取得
				var id = parseInt( $tab.attr( "data-no" ) );
				if ( id < 0 )
				{
					//=== 追加 =======================================
					$dialog.find( 'input[name="V_ID"]'   ).val( '' );
					$dialog.find( 'input[name="V_TITLE"]').val( '新規タブ' );
					$dialog.find( 'input[name="V_ICON"]' ).attr( 'checked', false );
					$dialog.find( 'input[type="submit"]' )
						.attr( 'name' , "TAB_ADD" )
						.attr( 'value', "追加" );
				}
				else
				{
					//=== 変更 =======================================
					// ID を設定
					$dialog.find( 'input[name="V_ID"]' ).val( id );

					// タイトルを設定
					var title = $tab.find(".title").text().trim();
					$dialog.find('input[name="V_TITLE"]').val(title);

					// チェックボックスは一旦すべてのチェックをはずしてからチェックする
					$dialog.find( 'input[name="V_ICON"]' ).attr( 'checked', false );
					var icon = $tab.find( "img.icon" ).attr( "src" );
					$dialog.find( 'input[value="' + icon + '"]' ).attr( 'checked', true );

					// ボタン
					$dialog.find( 'input[type="submit"]' )
						.attr( 'name' , "TAB_MOD" )
						.attr( 'value', "変更" );
				}
			},
			//=== 削除 ===============================================
			remove: function( $this, $tab, dspData )
			{
				// 確認
				var title = $tab.find(".title").text().trim();
				if ( !confirm( '[' + title + '] タブを削除してもよろしいですか？') )
					return;

//				// サーバーに通知
//				$.getJSON( 
//					  "/ajax/tab_remove.php?callback=?"
//					, {"id": $tab.attr("data-no")}
//					, function( data, textStatus, xhr ) {
//					}
//				);
				alert( "現在未実装です。" );

				// 次のタブを選択

				// タブの本体を削除
				var tabbody = options.body + $tab.attr("data-no");
				$(tabbody).remove( );

				// タブを削除
				$tab.remove( );

				// タブIDを振り直す
			},
		});

		//============================================================
		// ダイアログ[☓]ボタンを押した処理
		//============================================================
		$bar.find( options.item ).each(function()
		{
			var dspData = $.parseJSON($(this).attr("data-dsp"));
			$(dspData.id).find(".close").click(function(event)
			{
				var dialog = $(this).closest( "div.dialog" );
				dialog.slideUp('fast');
			});
		});

		//============================================================
		// タブをクリックした処理
		//============================================================
		$bar.on( "click", options.tab, function()
		{
			//=== タブ番号を取得 =====================================
			var no = $(this).attr( "data-no" );

			//=== コンテンツ領域をすべて非表示にする =================
			$(options.classTabbody).hide( );

			//=== 対応する本体領域を取得 =============================
			var $tabbody = $(options.body + no);

			//=== タブのアクティブ表示を切り替える ===================
			$(options.tab).removeClass( options.active );
			$(this).addClass( options.active );

			//=== 既に取得済みならば表示切り替えのみ =================
			if ( $tabbody.html().length > 0 )
			{
				// 表示開始
				$tabbody.show( );

				// iframe の高さをコンテンツに合わせる
				$tabbody.find( 'iframe' ).each( function(id) {
					var doc = this.contentWindow.document;
					var $doc = $( doc );
					var $body = $doc.find( 'body' );
					var height = $body.attr( "height" );
					if ( height == null )
						height = doc.documentElement.scrollHeight;
					$(this).height(height);
				});

				// URL のハッシュを設定
				location.hash = no;

				// 
				return;
			}

			//=== 選択したコンテンツ領域を表示する ===================
			$tabbody.show( );

			//=== 内部に表示するHTMLを取得 ===========================
			var src = "http://" + location.hostname + "/tab/tab.html?t=" + no;
			$.get(src, function(data)
			{
				//=== HTML を設定 ====================================
				$tabbody.html( data );

				//=== ウィジェットの機能を設定 =======================
				$tabbody.find( "div.widget" ).dspWidget();

				//=== レイアウト変更機能を設定 =======================
				$tabbody.dspLayout({
					container: 'div.tabbody',	// 表全体のコンテナ
					dropper  : 'div.drop',		// 列のコンテナ(ドロップ領域)
					gripper  : 'div.gripper',	// 列幅移動グリップ
					dragger  : 'div.drag',		// 移動可能領域
				});
			});

			//========================================================
			location.hash = no;
		});

		//============================================================
		return this;
	};
})(jQuery);
