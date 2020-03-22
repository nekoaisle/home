//====================================================================
//====================================================================
//====================================================================
//
//	レイアウト用divの幅を調整
//
//====================================================================
//====================================================================
//====================================================================

// 初期設定
(function($){
    $.fn.dspLayout = function (options) {

		//============================================================
		// 変数定義
		//============================================================
		var defaults = {
			container: 'div.contents',	// 表全体のコンテナ
			dropper : 'div.drop', 		// 列のコンテナ(ドロップ領域)
			gripper: 'div.gripper',		// 列幅移動グリップ
			dragger: 'div.drag',		// 移動可能領域
		};

		//============================================================
		// 俗にいう constructor 処理
		//============================================================

		// 現在の要素をcontentsに格納
//		var container = $("div.container");
		var contents = this;

		// オプションにデフォルトオプションをマージ
		var options = $.extend(defaults, options);

		//=== サイズを変更 ===========================================
		contents.each( function(){
			fitParent(this)
		});

		//=== イベントハンドラを設定 =================================
		// ウィンドウがリサイズされた時にサイズを変更
		$(window).bind( "resize", function(){
			contents.each( function(){
				fitParent(this)
			});
		});

		//=== 列幅移動ドラッガー設定 =================================
		$(options.gripper).draggable({
			axis: "x",
			containment: options.container,
			addClasses: false,
			cursor: 'move',
			zIndex: 1000,
			helper: 'clone',
			revert: 'true',
			start: function(e, ui) {
				stratDrag( this, ui );
			},
			drag: function(e, ui) {
				dragDrag( this, ui );
			},
			stop: function(e, ui) {
				stopDrag( this, ui );
			}
		});

		//=== ドラッグアンドドロップ設定 =============================
		$(options.dragger).draggable({
			containment: 'document',
			addClasses: false,
			cursor: 'move',
	//		snap: true,		// 吸着
	//		snapMode: outer;
			zIndex: 1000,
			helper: 'clone',
	//		opacity: 0.3,
			revert: 'invalid',
			scroll: true,
		});

		$(options.dropper).droppable({
			accept: options.dragger,
			tolerance: 'pointer',
			drop: function(e, ui) {
				var td = $(this);
				var div = $(ui.draggable[0]);
				var top = ui.offset.top;

				//=== ドラッグした位置直後の子要素の前に移動 =========
				var move = false;
				var child = td.children();
				child.each(function(){
					if ( this == div[0] )
						return true;	// 自身は無視
					var y = $(this).offset().top;
					if ( top < y )
					{
						$(this).before(div);
						move = true;	// 移動したフラグ
						return false;
					}
					return true;
				})

				//=== 子要素の前に移動できたか調べる =================
				if ( move == false ){
					// 子要素の前には移動できなかったので末尾に移動
					var lst = child[child.length-1];
					$(lst).insertBefore(div);
				}

				//=== ドラッグによる移動を無効にする =================
//				div.css( "position", "" )
				div.css( "left", "" )
				div.css( "top", "" )
			}
		});
		//============================================================
		// Public properties 
		//============================================================
		this.getOptions = function () {
			return options;
		};

		//============================================================
		// 現在の比率を維持したまま親の幅いっぱいにする
		//============================================================
		function fitParent( container ) {
			container = $(container);

			// 全体の幅
			var cw = container.innerWidth( );
			var pl = parseInt(container.css('margin-left'));
			var pr = parseInt(container.css('margin-right'));
			cw = cw - (pl + pr);

			//gripperの幅
			var grips = container.children( "div.gripper" );
			var gw = 0;
			grips.each(function(){
				gw += $(this).outerWidth(true);
			})

			// 現在の全ドロップ領域の幅の合計
			var drops = container.children( "div.drop" );
			var dw = 0;
			drops.each(function(id){
				dw += $(this).outerWidth(true);
			})

			// 新たなドロップ領域全体の幅
			// (全体の幅からグリッパーを除いたもの)
			var tw = cw - gw;

			// ドロップ領域の幅を設定
			drops.each(function(id){
				// 現在の幅
				var w = $(this).outerWidth(true);
				// 新しい幅
				var w = tw * (w / dw);
				// %単位にする
				var w = (string)((w / cw) * 100) + "%";
				$(this).width( w );
			})
		}

		//============================================================
		// グリップをドラッグで列幅を変更
		//============================================================
		var left  = { obj: null, width: 0 };
		var right = { obj: null, width: 0 };
		var par   = null:

		//=== ドラッグ開始 ===========================================
		function stratDrag( obj, ui ) {
			// 左のdrop
			left.obj  = $(obj).prev(options.dropper);
			left.width  = $(left.obj).width( );

			// 右のdrop
			right.obj = $(obj).next(options.dropper);
			right.width = $(right.obj).width( );

			// 親コンテナ
			par = $(obj).parents(options.container);
		}

		//=== ドラッグ中 =============================================
		function dragDrag( obj, ui ) {
			// 移動量を算出
			var mv = (ui.offset.left - ui.originalPosition.left)

			$(left.obj).width( left.width + mv );
			$(right.obj).width( right.width - mv );
			var dummy = 0;
		}

		//=== ドラッグ終了 ===========================================
		function stopDrag( obj, ui ) {
			fitParent( par );
		}

		//============================================================
		return this;
	};

})(jQuery);

