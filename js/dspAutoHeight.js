//====================================================================
// 親ウィンドウのiframeの高さを調整(全祖先に対して処理)
//====================================================================
function iframeAutoHeight( h, a )
{
	jQuery.dspAutoHeight.iframe( h, a );
}

(function($){
	$.dspAutoHeight = {
		iframe: function ( h, a )
		{
			// 引数省略時の処理
			if ( h == null ) h = 0;		// 最初のフレームの高さ
			if ( a == null ) a = 0;		// 追加の高さ

			// このウィンドウが埋め込まれたiframeの高さを調整
			var w = window;
			while ( w.frameElement )
			{
				if ( h == 0 )
				{
					// body 取得
					var $body = $("body", w.document);

					// スクロールバーを消す
//					$(w.frameElement).attr( "scrolling", "no" );
					$body.css( "overflow-y", "hidden" );

					// 対象 body の高さを取得
					try {
						//	ググるとよく出てくるやり方(エラーとなることがある)
						h = w.document.documentElement.scrollHeight;
					} catch (e) {
						// w.document.documentElement is null
						try {
							// これだとちょっと小さくなる＞＜；
							h = $(w.contentWindow).outerHeight( );
						} catch (e) {
							// read only error
							h = $body.outerHeight();
						}
					}
				}

				// 親フレームの高さ調整
				$(w.frameElement).css("height", h+a);

				// 対象を親にする
				w = w.parent;
				h = 0;
			}
		}
	};
})(jQuery);
