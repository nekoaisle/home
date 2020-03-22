/**
 * スタートページ用
 *
 * filename: kya-rss.js
 * 
 * @version   0.1.1
 * @copyright Copyright (C) 2014- Yoshio Kiya  All rights reserved.
 * @date      2014-11-20
 * @author    Yoshio Kiya
 */

/*====================================================================
 汎用関数群
====================================================================*/
/**
 * 数値の0パディング
 */
var paddingDigit = function( digit, min, max ) {
	// 最小文字数制限
	digit += '';
	min -= digit.length;
	while ( min -- > 0 ) {
		digit = '0' + digit;
	}

	// 最大文字数制限
	if ( max != "undefind" ) {
		digit = digit.substr( 0, max );
	}

	return digit;
};

/*====================================================================
 Date オブジェクトへ拡張メソッドを追加
====================================================================*/
/**
 * 月 名前を省略形で取得
 */
Date.prototype.getMonthName = function( ) {
	var name = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	return name[this.getMonth()];
};

/**
 * 月 名前をフルスペルで取得
 */
Date.prototype.getFullMonthName = function( ) {
	var name = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	return name[this.getMonth()];
};

/**
 * 日 名前を省略形で取得
 */
Date.prototype.getDayName = function( ) {
	var name = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
	return name[this.getDay()];
};

/**
 * 日 名前をフルスペルで取得
 */
Date.prototype.getFullDayName = function( ) {
	var name = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	return name[this.getDay()];
};

/**
 * 書式に従った日時文字列を取得
 */
Date.prototype.formatTime = function( format ) {

	var y = this.getFullYear();
	var m = this.getMonth() + 1;
	var d = this.getDate();
	var h = this.getHours();
	var i = this.getMinutes();
	var s = this.getSeconds();
	var w = this.getDay();

	format = format.replace( '%%', '{{パーセント}}' );

	// Replace mask tokens
	format = format.replace( '%Y', paddingDigit( y, 4 ) );
	format = format.replace( '%y', paddingDigit( y, 2, 2 ) );

	format = format.replace( '%m', paddingDigit( m, 2 ) );
	format = format.replace( '%B', this.getFullMonthName() );
	format = format.replace( '%b', this.getMonthName()     );

	format = format.replace( '%d', paddingDigit( d, 2 ) );

	format = format.replace( '%H', paddingDigit( h, 2 ) );
	format = format.replace( '%I', paddingDigit( (h < 12) ? h : h - 12, 2 ) );
	format = format.replace( '%p', (h < 12) ? 'AM' : 'PM' );

	format = format.replace( '%M', paddingDigit( i, 2 ) );

	format = format.replace( '%S', paddingDigit( s, 2 ) );

	format = format.replace( '%w', this.getDay()         );
	format = format.replace( '%A', this.getFullDayName() );
	format = format.replace( '%a', this.getDayName()     );

	format = format.replace( '{{パーセント}}', '%%' );

	return format;
};

/*====================================================================
 リンクボックス プラグイン
====================================================================*/
(function($) {
	//プラグイン定義
	$.fn.linkBox = function( options ) {
		//引数を設定する
		var defaults = {
			data: {}
		};
		var setting = $.extend(defaults, options);
		var $this = $(this);

		$.each( $this, function( key, dom ) {
			var $box = $( dom );
			$.each( setting.data, function( key, val ) {
				var title = (val.title) ? val.title : key;
				var html = '<a target="_blank" href="' + val.url + '">'
					+ '<img src="./img/' + val.img + '" title="' + title + '" />'
					+ '</a>';
				$box.append( html );
			});
		} );

		//メソッドチェーン対応(thisを返す)
		return(this);
	};
})(jQuery);

/*====================================================================
 index.html用
====================================================================*/
$(document).ready(function () {
	/**
	 * 既読マークの整理
	 */
	var cleanupRead = function ( ) {
		var keys = [];
		for ( var i = 0; i < localStorage.length; ++ i ) {
			var key = localStorage.key( i );
			var val = localStorage.getItem( key );
	//		console.log( key + " = '" + val + "'" );
			if ( val == 'true' ) {
				// 旧既読マークは除去
				console.log( key + " is old version." );
				keys.push( key );
			} else {
				var sec = (Date.now() - new Date( val )) / 1000;
				if ( sec > (60*60*24*3) ) {
					// ３日以上経過した記事は除去
					console.log( key + " passes more than three days." );
					keys.push( key );
				}
			}
		}

		// 旧既読マークをクリア
		for ( var i in keys ) {
			console.log( i + " removeItem( '" + keys[i] + "' )" );
			localStorage.removeItem( keys[i] );
		}
	};

	/**
	 * 指定記事を既読にする
	 * $article 既読にする記事ノード
	 */
	var setRead = function ( $article ) {
		// ローカルストレージに記憶
		var md5  = $article.attr( "data-md5"  );
		var date = $article.attr( "data-date" );
		localStorage.setItem( md5, date );

		// 表示をみどく状態に移行
		$article.addClass( 'read' );

		// 未読数カウント
		var $feed = $article.closest( ".feed" );
		countArticles( $feed );
	}

	/**
	 * 記事数をカウント
	 */
	var countArticles = function ( $feed ) {
		var totalArticles = $feed.find( "li.rssRow" ).length;
		var readArticles  = $feed.find( "li.read"   ).length;
		var unreadArticles = totalArticles - readArticles;

		// 記事数の設定
		var $header = $feed.children( ".header" );
		$header.find( ".unread" ).text( unreadArticles );
		$header.find( ".total"  ).text( totalArticles );
	}

	/**
	 * 	rssfeed 共通オプション
	 */
	var options = {
		ssl: true,
		header: false,
		limit: 50,
		snippet: false,
		linktarget: '_blank',

		/**
		 * 記事の並べ替え
		 */
		sort: function ( a, b ) {
			var c = b['read'];
			var d = a['read'];
			if ( c == d ) {
				// 既読状態が同じ時かは記事の日付が新しい順
				var c = b['date'];
				var d = a['date'];
				return new Date(c) - new Date(d);
			} else if ( c < d ) {
				// 未読を優先
				return 1;
			} else {
				// 既読は非優先
				return -1;
			}
		},

		/**
		 * 各記事を収めたタグの属性を設定
		 */
		getArticleAttrib: function ( rowData ) {
			var attrib = rowData['attrib'];

			var md5 = $.md5( rowData['feedLink'] );
			attrib["data-md5" ] = md5;
			attrib["data-date"] = rowData['date'];

			if ( typeof localStorage.getItem( md5 ) === 'string' ) {
				attrib.class += " read";
				rowData['read'] = 1;
			} else {
				rowData['read'] = 0;
			}
		},

		/**
		 * リンクの補正
		 */
		modifyLink: function( link ) {
			var url = $.parseURL( link ).query.url;
			if ( url ) {
				link = url;
			}

			return link;
		},

		/**
		 * 本文の補正
		 */
		modifyContent: function( content ) {
			var $k = $( content );
			var $td0 = $k.find( "td:eq(0)" );

			// イメージ枠
			$td0.children( "font" ).after( "<br /><button class=\"read\">既読</button>" );

			// 本文枠
			var $td1 = $k.find( "td:eq(1)" );

			// タイトル前の無駄な空白を除去
			$td1.find( "font>br" ).remove( );
			$td1.find( "font>div:first" ).remove( );

			// 最初以外のリンクを除去
			var $items = $td1.find( "font>div:first" ).children( );
			$.each( $items, function( k, v ) {
				switch ( k ) {
	//			case 0:	// <a>
	//			case 1:	// <br>
	//			case 2:	// <font>ソース名
	//			case 3:	// <br>
				case 4:	// <font>
					break;
				default:
					$(v).remove( );
					break;
				}
			});

			// table の cellspacing 属性を除去
			$k.removeAttr( "cellspacing" );
			$k.removeAttr( "cellpadding" );

			// 結果を返す
			return $k.get(0).outerHTML;
		},

		/**
		 * 前処理
		 */
		preProcessing: function ( articles ) {
			var $header = $( articles ).closest( ".feed" ).children( ".header" );

			// 記事取得日時の設定
			var $date = $header.find( ".date" );
			var date = new Date( );
			$date.text( date.formatTime( "%Y-%m-%d %H:%M" ) );
		},

		/**
		 * 後処理
		 */
		postProcessing: function ( articles ) {
			// 記事数の設定
			var $feed = $( articles ).closest( ".feed" );
			countArticles( $feed );
		},
	};

	/**
	 * 既読マークの整理
	 */
	cleanupRead( );

	/**
	 * div.feed に RSS を読み込む
	 */
	$.each( $( ".feed" ), function ( key, dom ) {
		var $feed = $( dom );
		var $articles = $feed.find( ".articles" );
		var topic = $articles.attr( "data-topic" );
		var url = 'https://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&num=100&topic=' + topic;
		$articles.rssfeed( url, options );
	});

	/**
	 * 	クリックイベント
	 */
	// 既読ボタン
	$( ".feeds" ).on( 'click', "button.read", function( ) {
		setRead( $( this ).closest( "li.rssRow" ) );
	} );

	// 記事へのリンク
	$( ".feeds" ).on( 'click', "a", function( ) {
		setRead( $( this ).closest( "li.rssRow" ) );
	} );

	// 再読み込みボタン
	$( ".feeds" ).on( 'click', ".reload button", function( ) {
		var $articles = $( this ).parents( ".feed" ).find( ".articles" );
		$articles.html( '' );

		var topic = $articles.attr( "data-topic" );
		var url = 'https://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&num=100&topic=' + topic;
		$articles.rssfeed( url, options );
	} );
});

