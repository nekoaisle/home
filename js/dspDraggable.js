//====================================================================
//====================================================================
//====================================================================
//
//	レイアウト用divの幅を調整
//
//	Widgetの配置が変更された場合はajaxにてサーバーに保存
//	列の幅が変更された場合はstore.jsにてクライアントに保存
//
//====================================================================
//====================================================================
//====================================================================
(function($){
	$.fn.dspDraggable = function (options) {

		//============================================================
		// 変数定義
		//============================================================
		var ui = {
			object: null,
			cover: null,
			offset: {
				left: 0,
				top: 0,
			},
			originalPosition: {
				left: 0,
				top: 0,
			},
		};

		var iframeBlocks = null;

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		//=== 現在の要素をcontentsに格納 =============================
		var contents = this;

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend({
				start: function( ev, ui ){},	// 開始時に呼ばれる関数
				drag : function( ev, ui ){}, 	// 移動時に呼ばれる関数
				drop : function( ev, ui ){}, 	// 終了時に呼ばれる関数
			}
			, options
		);

		//============================================================
		// マウスボタンダウンイベントを登録
		//============================================================
		$(this).on( "mousedown.dspDraggable", function(event)
		{
			// 下の要素にイベントを伝えない
			event.preventDefault();
			event.stopImmediatePropagation();

			//=== ドキュメント全体を覆うdivを作成 ====================
			ui.cover = $("<div class='dspDraggable-cover' style=''></div>");
			ui.cover.css({
				position: "absolute",
				left: 0,
				top: 0,
				zIndex: 1000,
				width: $(document).width() + "px",
				height: $(document).height() + "px",
				background: "#FF0000",
				opacity: "0.001",
//				opacity: "0.301",
				cursor: $(this).css( "cursor" )
			})
			.appendTo("body");

			//=== マウス移動イベントを登録 ===========================
			$(document).on( "mousemove.dspDraggable", function(event)
			{
				// 下の要素にイベントを伝えない
				event.preventDefault();
				event.stopImmediatePropagation();

				//========================================================
				ui.offset.left = event.pageX;
				ui.offset.top  = event.pageY;

				//=== コールバック関数を呼び出す =========================
				options.drag.call(ui.obj, event, ui );

				//========================================================
				return false;
			});

			//=== マウスアップイベントを登録 =========================
			$(document).on( "mouseup.dspDraggable", function(event)
			{
				// 下の要素にイベントを伝えない
				event.preventDefault();
				event.stopImmediatePropagation();

				//====================================================
				ui.offset.left = event.pageX;
				ui.offset.top  = event.pageY;

				//=== コールバック関数を呼び出す =====================
				options.stop.call(ui.obj, event, ui );

				//=== イベントを削除 =================================
				$(document).off( "mousemove.dspDraggable" );
				$(document).off( "mouseup.dspDraggable"   );

				//=== カバーを削除 ===================================
				$(".dspDraggable-cover").remove();

				//=== 終了 ===============================================
				ui.object = null;
				ui.cover  = null;

				//========================================================
				return false;
			});

			//========================================================
			ui.object = this;
			ui.originalPosition.left = event.pageX;
			ui.originalPosition.top = event.pageY;
			ui.offset.left = event.pageX;
			ui.offset.top  = event.pageY;

			//=== コールバック関数を呼び出す =========================
			options.start.call(this, event, ui );

			//========================================================
			return false;
		});

		//============================================================
		return this;
	};
})(jQuery);
