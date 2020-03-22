(function($) {
	$.fn.dspTimeAgo = function(options){

		var defaults = {
		};

		var setting = $.extend(defaults,options);

		// 処理間隔 (msec)
		var interval = 1000;

		// 設定した要素
		var $part  = $(this);
		// 開始時刻
		var start = $.now( );

		// 繰り返し処理
		var intervalJob = function() {
			var sec = Math.floor(($.now()-start)/1000);
			if ( sec < 60*60 ) {
				sec = Math.floor( sec / 60 ) + "分前";
				interval = 60 * 1000;
			} else {
				sec = Math.floor( sec / (60*60) ) + "時間前";
				interval = 10 * 60 * 1000;
			}

			// 表示
			$part.text( sec );

			// 次の処理を設定
			setTimeout( intervalJob, interval );
		}

		// トリガー
		intervalJob( );
	}
})(jQuery);
