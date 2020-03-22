//====================================================================
//====================================================================
//====================================================================
//
//	レイアウト用divの幅を調整
//
//	Widgetの配置が変更された場合はajaxにてサーバーに保存
//	列の幅が変更された場合はstore.jsにてクライアントに保存
//
//	必要ライブラリ
//	store.js
//	md5.js
//
//====================================================================
//====================================================================
//====================================================================
(function($)
{
	$.fn.dspLayout = function (options)
	{

		//=== 変数定義 ===============================================
		var delayed = [];

		//=== 現在の要素をtabbodyに格納 ==============================
		var tabbody = this;

		//=== オプションにデフォルトオプションをマージ ===============
		var options = $.extend(
			{
				tabbody:   'div.tabbody',	// 表全体のコンテナ
				column :   'div.column', 		// 列のコンテナ(ドロップ領域)
				gripper:   'div.gripper',	// 列幅移動グリップ
				widget:    'div.widget',	// 移動可能領域
			}
			, options
		);

		//=== サイズを変更 ===========================================
		tabbody.each( function( id ) 
		{
			loadDropWidth( this );
			fitParent( this )
		});

		//=== イベントハンドラを設定 =================================
		// ウィンドウがリサイズされた時にサイズを変更
		$( window ).on( "resize", function() 
		{
			tabbody.each( function() 
			{
				fitParent( this )
			});
		});

		//=== イベントハンドラを設定 =================================
		// iframeのロードが終わったらサイズを変更
		$( document ).on( "load", "iframe", function() 
		{
			tabbody.each( function()
			{
				fitParent( this )
			});
		});

		//=== 列幅移動ドラッガー設定 =================================
		$( options.gripper ).dspDraggable(
		{
			start: function( e, ui ) 
			{
				stratGripDrag( this, ui );
			},
			drag: function( e, ui ) 
			{
//				e.preventDefault();
				e.stopImmediatePropagation();
				dragGripDrag( this, ui );
			},
			stop: function( e, ui ) 
			{
				stopGripDrag( this, ui );
			}
		});

		//=== ドラッグアンドドロップ設定 =============================
		// ドラッグ設定は動的に行う
		var o = $(options.column);

		$(options.column).droppable( 
		{
			accept: options.widget,
			tolerance: 'pointer',
			drop: function(e, ui) 
			{
				dropWidget( this, ui );
			},
		});

		//============================================================
		// Public properties 
		//============================================================
		this.getOptions = function () 
		{
			return options;
		};

		//============================================================
		// 遅延実行
		// ※同じ処理が重複した場合は最後だけが実行されます。
		//============================================================
		function requestDelayedExecution( func, delay ) 
		{
			var k = func.toString( );

			//========================================================
//			if ( typeof delayed[k] === "undefined" )
			if ( !delayed[k] ) 
			{
				clearTimeout( delayed[k] );
				delayed[k] = null;
			}

			//========================================================
			delayed[k] = setTimeout( func, delay );
		}

		//============================================================
		// WidgetのIDを振り直す
		//============================================================
		function resetWidgetID( tabbody )
		{
			var tabbody = $(tabbody);

			// 全ドロップ領域に対して処理
			tabbody.children( options.column ).each(function(col) 
			{
				var row = 0;
				$(this).children( options.widget ).each(function() 
				{
					if ( !$(this).hasClass( "ui-draggable-dragging" ) ) 
					{
						$(this).attr( "id", "row" + col + row );
						++ row;
					}
				})
			})
		}

		//============================================================
		// 現在の比率を維持したまま親の幅いっぱいにする
		//============================================================
		function fitParent( tabbody ) 
		{
			tabbody = $(tabbody);

			// 全体の幅
			var cw = tabbody.innerWidth( );
			var pl = parseInt(tabbody.css('margin-left'));
			var pr = parseInt(tabbody.css('margin-right'));
			cw = cw - (pl + pr);

			//gripperの幅
			var grips = tabbody.children( options.gripper );
			var gw = 0;
			grips.each( function( ) 
			{
				gw += $(this).outerWidth(true);
			})

			// 現在のドロップ領域の幅
			var columns = tabbody.children( options.column );
			var dw = 0;
			columns.each( function( id ) 
			{
				var w = $(this).outerWidth(true);
				dw += (w > 0) ? w : 100;
			})

			// 全体の幅からグリッパーを除いたものが新たなドロップ領域全体の幅
			var tw = cw - gw;

			// ドロップ領域の幅を設定
			columns.each( function( id ) 
			{
				var w = $(this).outerWidth(true);
				w = (w > 0) ? w : 100;
				$(this).width( Math.floor( tw * (w / dw) ) );
			})

			//=== レイアウト保存イベントのリクエスト =================
			requestDelayedExecution( function() {
				saveDropWidth( tabbody )
			}, 100 );

			//=== 高さを調整 =========================================
			$("iframe.autoHeight").each( function() 
			{
				try 
				{
					//	ググるとよく出てくるやり方(エラーとなることがある)
					var h = this.contentWindow.document.documentElement.scrollHeight;
					$(this).height(h);
					return;
				}
				catch (e)
				{
					// this.contentWindow.document.documentElement is null
				}

				try
				{
					var h = $(this.contentWindow).outerHeight( );
					$(this).height(h);
					return;
				}
				catch (e)
				{
					// read only error
				}
			});
		}

		//============================================================
		function getSaveDropKey( column, tabbody ) 
		{
			return $(column).attr("id") + "@" + $(tabbody).attr("id");
		}

		//============================================================
		// 指定コンテナ下のドロップ領域の幅を記憶
		//============================================================
		function saveDropWidth( tabbody ) 
		{
			var columns = $(tabbody).children( options.column );
			columns.each(function()
			{
				// 幅を取得
				var w = $(this).css( "width" );
				// キーを合成 ([ドロップ領域のID]@[コンテナのID]
				var k = getSaveDropKey( this, tabbody );
				// 保存
				store.set( k, w );
			});
		}

		//============================================================
		// 指定コンテナ下のドロップ領域の幅を復帰
		//============================================================
		function loadDropWidth( tabbody ) 
		{
			var columns = $(tabbody).children( options.column );
			columns.each(function()
			{
				// キーを合成 ([ドロップ領域のID]@[コンテナのID]
				var k = getSaveDropKey( this, tabbody );
				// 幅を取得
				var w = store.get( k );
				// 設定
				if ( w )
					$(this).css( "width", w );
				else
					$(this).css( "width", "32%" );
			})

			//=== 率を維持したまま親の幅いっぱいにする ===============
			fitParent( tabbody );
		}

		//============================================================
		// グリップをドラッグで列幅を変更
		//============================================================
		var left  = { obj: null, width: 0 };
		var right = { obj: null, width: 0 };
		var par   = null;

		//=== ドラッグ開始 ===========================================
		function stratGripDrag( obj, ui ) 
		{
			// 左のcolumn
			left.obj  = $( obj ).prev( options.column );
			left.width  = $( left.obj ).width( );

			// 右のcolumn
			right.obj = $( obj ).next( options.column );
			right.width = $( right.obj ).width( );

			// 親コンテナ
			par = $( obj ).parents( options.tabbody )[0];
		}

		//============================================================
		// ドラッグ中
		//============================================================
		function dragGripDrag( obj, ui ) 
		{
			// 移動量を算出
			var mv = (ui.offset.left - ui.originalPosition.left)
//			console.log( ui.offset.left + " - " + ui.originalPosition.left + " = " + mv );

			$(left.obj).width( left.width + mv );
			$(right.obj).width( right.width - mv );
			var dummy = 0;
		}

		//=== ドラッグ終了 ===========================================
		function stopGripDrag( obj, ui ) 
		{
			$( function()
			{
				setTimeout(function()
				{
					fitParent( par );
				},10);
			});
		}

		//============================================================
		// Widgetをドラッグ＆ドロップで移動
		//============================================================
		function dropWidget( obj, ui ) {
			var col = $(obj);
			var div = $(ui.draggable[0]);
			var top = ui.offset.top;

			//=== ドラッグした位置直後の子要素の前に移動 =========
			var drop = false;
			var last = null;
			var child = col.children();
			child.each( function()
			{
				if ( this == div[0] )
					return true;	// 自身は無視
				var y = $(this).offset().top;
				if ( top < y ) 
				{
					$(this).before(div);
					drop = $(this).attr("id");	// 移動先要素ID
					return false;
				}
				last = this;
				return true;
			})

			//=== 子要素の前に移動できたか調べる =================
			if ( drop == false )
			{
				// 子要素の前には移動できなかったので末尾に移動
//				var last = child[child.length-1];
				$(last).after(div);
				drop = col.attr("id");		// col?の時は列の末尾に移動
			}

			//=== ドラッグによる移動を無効にする =================
//				div.css( "position", "" )
			div.css( "left", "" )
			div.css( "top", "" )

			//=== サーバーに通知 =================================
		    var param = 
		    { 
				"tab": tabbody.attr("id"),
				"drag": div.attr("id"),
				"drop": drop,
		    };

			$.ajax(
			{
				type: "post",
				dataType : "jsonp",
				scriptCharset: 'utf-8',
				crossDomain: false,
				cache: false,
				url: "/ajax/ajax_test.php",
				data: JSON.stringify(param),
			})
			.done(function( data, textStatus, xhr )
			{
			})
			.fail(function( xhr, textStatus, errorThrown )
			{
				alert(errorThrown);
			});

			//=== WidgetのIDを振り直す ===============================
			resetWidgetID( $(col).parent( ) );
		}

		//============================================================
		return this;
	};

	//================================================================
	// 外部からも利用可能な関数を定義
	//================================================================
	$.dspLayout = 
	{
		//============================================================
		// ドラッグ機能を設定
		//============================================================
		setDraggable: function ( $widget )
		{
			$widget.draggable(
			{
				containment: 'document',
				addClasses: false,
				cursor: 'move',
//				snap: true,		// 吸着
//				snapMode: outer;
				zIndex: 1000,
				helper: 'clone',
//				opacity: 0.3,
				revert: 'invalid',
				scroll: true,
				iframeFix: true,
//				distance: 16,		// 指定領域でドラッグを開始出来るようになるでの仮
				start: function(e, ui) 
				{
					$(ui.helper).width( $(this).width() );
					$(ui.helper).height( $(this).height() );
				},
				drag: function(e, ui) 
				{
//					e.preventDefault();
					e.stopImmediatePropagation();
				},
				stop: function(e, ui) 
				{
					// 終了時には破棄する
					$(this).draggable( "destroy" );
				},
			});
		},
		//============================================================
		// 遅延実行
		// ※同じ処理が重複した場合は最後だけが実行されます。
		//============================================================
		delayed: [],
		requestDelayedExecution: function ( func, delay ) 
		{
			var k = func.toString( );

			//========================================================
//			if ( typeof delayed[k] === "undefined" )
			if ( !this.delayed[k] ) 
			{
				clearTimeout( this.delayed[k] );
				this.delayed[k] = null;
			}

			//========================================================
			this.delayed[k] = setTimeout( func, this.delay );
		}
	};
})(jQuery);
