/**
 * Plugin: jquery.zRSSFeed
 * 
 * Version: 1.2.0
 * (c) Copyright 2010-2013, Zazar Ltd
 * 
 * Description: jQuery plugin for display of RSS feeds via Google Feed API
 *              (Based on original plugin jGFeed by jQuery HowTo. Filesize function by Cary Dunn.)
 * 
 * History:
 * 1.2.1 - Added AMD support
 * 1.2.0 - Added month names to date formats
 * 1.1.9 - New dateformat option to allow feed date formatting
 * 1.1.8 - Added historical option to enable scoring in the Google Feed API
 * 1.1.7 - Added feed offset, link redirect & link content options
 * 1.1.6 - Added sort options
 * 1.1.5 - Target option now applies to all feed links
 * 1.1.4 - Added option to hide media and now compressed with Google Closure
 * 1.1.3 - Check for valid published date
 * 1.1.2 - Added user callback function due to issue with ajaxStop after jQuery 1.4.2
 * 1.1.1 - Correction to null xml entries and support for media with jQuery < 1.5
 * 1.1.0 - Added support for media in enclosure tags
 * 1.0.3 - Added feed link target
 * 1.0.2 - Fixed issue with GET parameters (Seb Dangerfield) and SSL option
 * 1.0.1 - Corrected issue with multiple instances
 *
 **/
(function (factory) {
	if ( typeof define === 'function' && define.amd ) {
		// AMD. Register as an anonymous module.
		define("jquery.zrssfeed", ['jquery'], factory);
	} else if (typeof exports === 'object') {
		// Node/CommonJS style for Browserify
		module.exports = factory;
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {

	$.fn.rssfeed = function(url, options, fn) {	
	
		// Set plugin defaults
		var defaults = {
			limit: 10,
			offset: 1,
			header: true,
			titletag: 'h4',
			date: true,
			dateformat: 'datetime',
			content: true,
			snippet: true,
			media: true,
			showerror: true,
			errormsg: '',
			key: null,
			protocol: null,
			linktarget: '_self',
			linkredirect: '',
			linkcontent: false,
			sort: 'date',		// date | title | function()
			sortasc: false,
			historical: false
			//@@add kiya {
			, getArticleAttrib: null
			, modifyLink: null
			, modifyContent: null
			, postProcessing: null
			//@@add kiya }
		};  

		var options = $.extend(defaults, options); 
		if ( !options.protocol || (options.protocol == 'auto') ) {
			// プロトコルをドキュメントに合わせる
			options.protocol = document.location.protocol;
		}

		// Functions
		return this.each(function(i, e) {
			var $e = $(e);

			// Add feed class to user div
			if (!$e.hasClass('rssFeed')) $e.addClass('rssFeed');
			
			// Check for valid url
			if(url == null) return false;

			// Add start offset to feed length
			if (options.offset > 0) options.offset -= 1;
			options.limit += options.offset;

			// Send request
			$.getJSON( createGoogleFeedApiAddress(), function(data) {
				
				// Check for error
				if (data.responseStatus == 200) {
	
					// Process the feeds
					_process(e, data.responseData, options);

					// Optional user callback function
					if ($.isFunction(fn)) fn.call(this,$e);
					
				} else {

					// Handle error if required
					if (options.showerror)
						if (options.errormsg != '') {
							var msg = options.errormsg;
						} else {
							var msg = data.responseDetails;
						};
						$(e).html('<div class="rssError"><p>'+ msg +'</p></div>');
				};
			});				
		});

		/**
		 * Google Feed API のアドレスを作成
		 */
		function createGoogleFeedApiAddress() {
			// クエリーを設定
			var a = {
				'v': '1.0',
				'callback': '?',
				'q': encodeURIComponent( url ),
				'num': options.limit,
				'scoring': (options.historical) ? 'h' : null,
				'key': (options.key) ? options.key : null,
				'output': 'json_xml',
				'dq': (new Date()).getTime(),
			};

			var b = [];
			for ( var k in a ) {
				if ( a[k] ) {
					b.push( k + '=' + a[k] );
				}
			}

			var api = options.protocol 
				+ "//ajax.googleapis.com/ajax/services/feed/load?" 
				+ b.join( '&' )
			;
			return api;
		};
	};

	// Function to create HTML result
	var _process = function(e, data, options) {

		// 前処理
		if ( typeof options.preProcessing === 'function' ) {
			options.preProcessing( e );
		}

		// Get JSON feed data
		var feeds = data.feed;
		if (!feeds) {
			return false;
		}
		var rowArray = [];
		var html = '';	
		
		// Get XML data for media (parseXML not used as requires 1.5+)
		if (options.media) {
			var xml = _getXMLDocument(data.xmlString);
			var xmlEntries = xml.getElementsByTagName('item');
		}

		// Add header if required
		if (options.header) {
			html +=	'<div class="rssHeader">' +
				'<a href="'+feeds.link+'" title="'+ feeds.description +'">'+ feeds.title +'</a>' +
				'</div>';
		}

		// Add body
		html += '<div class="rssBody">' +
			'<ul>';


		// Add feeds
		for (var i=options.offset; i<feeds.entries.length; i++) {
			var rowData = [];

			// Get individual feed
			var entry = feeds.entries[i];
			var pubDate;
			var feedLink = entry.link;

			// タイトル
			rowData['title'] = entry.title;

			// Format published date
			if (entry.publishedDate) {

				var entryDate = new Date(entry.publishedDate);
				var pubDate = entryDate.toLocaleDateString() + ' ' + entryDate.toLocaleTimeString();

				switch (options.dateformat) {
					case 'datetime':
						break;
					case 'date':
						pubDate = entryDate.toLocaleDateString();
						break;
					case 'time':
						pubDate = entryDate.toLocaleTimeString();
						break;
					case 'timeline':
						pubDate = _getLapsedTime(entryDate);
						break;
					default:
						pubDate = _formatDate(entryDate,options.dateformat);
						break;
				}
			}
			rowData['date'] = pubDate;

			// Add feed row
			if ( options.modifyLink ) {
				feedLink = options.modifyLink( feedLink );
			}
			rowData['feedLink'] = feedLink;

			if (options.linkredirect) feedLink = encodeURIComponent(feedLink);
			rowData['html'] = '<'+ options.titletag +'><a href="'+ options.linkredirect + feedLink +'" title="View this feed at '+ feeds.title +'">'+ entry.title +'</a></'+ options.titletag +'>'

			if (options.date && pubDate) rowData['html'] += '<div>'+ pubDate +'</div>'
			if (options.content) {
			
				// Use feed snippet if available and optioned
				if (options.snippet && entry.contentSnippet != '') {
					var content = entry.contentSnippet;
				} else {
					var content = entry.content;
				}

				//@@add kiya {
				if ( options.modifyContent ) {
					content = options.modifyContent( content );
					if ( !content ) {
						continue;
					}
				}
				//@@add kiya }

				if (options.linkcontent) {
					content = '<a href="'+ options.linkredirect + feedLink +'" title="View this feed at '+ feeds.title +'">'+ content +'</a>'
				}
				
				rowData['html'] += '<p>'+ content +'</p>'
			}
			
			// Add any media
			if (options.media && xmlEntries.length > 0) {
				var xmlMedia = xmlEntries[i].getElementsByTagName('enclosure');
				if (xmlMedia.length > 0) {
					
					rowData['html'] += '<div class="rssMedia"><div>Media files</div><ul>'
					
					for (var m=0; m<xmlMedia.length; m++) {
						var xmlUrl = xmlMedia[m].getAttribute("url");
						var xmlType = xmlMedia[m].getAttribute("type");
						var xmlSize = xmlMedia[m].getAttribute("length");
						rowData['html'] += '<li><a href="'+ xmlUrl +'" title="Download this media">'+ xmlUrl.split('/').pop() +'</a> ('+ xmlType +', '+ _formatFilesize(xmlSize) +')</li>';
					}
					rowData['html'] += '</ul></div>'
				}
			}
	
			// 記事ごとの属性を設定
			rowData['attrib'] = {
				class: 'rssRow',
			};
			// 記事ごとの追加属性を取得
			if ( options.getArticleAttrib ) {
				options.getArticleAttrib( rowData );
			}

			// store row
			rowArray[rowArray.length] = rowData;
		}
		
		// Sort if required
		if ( typeof options.sort === 'function' ) {
			// user function
			rowArray.sort( options.sort );
		} else if ( (options.sort == 'date') || (options.sort == 'title') ) {
			// date | title
			rowArray.sort(function(a,b) {
				// Apply sort direction
				if ( options.sortasc ) {
					var c = a[options.sort];
					var d = b[options.sort];
				} else {
					var c = b[options.sort];
					var d = a[options.sort];
				}

				if ( options.sort == 'date' ) {
					return new Date(c) - new Date(d);
				} else {
					c = c.toLowerCase();
					d = d.toLowerCase();
					return (c < d) ? -1 : (c > d) ? 1 : 0;
				}
			});
		}

		// Add rows to output
		var row = 'odd';
		$.each(rowArray, function(e) {

			// 記事ごとの属性を取得
			var attrib = rowArray[e]['attrib'];
			attrib.class += ' ' + row;

			// オブジェクトに設定された属性を文字列に変換
			var attr = '';
			$.each( attrib, function( key, val ) {
				attr += key + '="' + val + '" ';
			});

			//
			html += '<li ' + attr + '>' + rowArray[e]['html'] + '</li>';

			// Alternate row classes
			row = (row == 'odd') ? 'even' : 'odd';
		});

		html += '</ul>' + '</div>';

		$(e).html(html);

		// Apply target to links
		$('a',e).attr('target',options.linktarget);

		// 後処理
		if ( typeof options.postProcessing === 'function' ) {
			options.postProcessing( e );
		}
	};

	var _formatFilesize = function(bytes) {
		var s = ['bytes', 'kb', 'MB', 'GB', 'TB', 'PB'];
		var e = Math.floor(Math.log(bytes)/Math.log(1024));
		return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
	}

	var _formatDate = function(date,mask) {

		// Convert to date and set return to the mask
		var fmtDate = new Date(date);
		date = mask;

		// Replace mask tokens
		date = date.replace('dd', _formatDigit(fmtDate.getDate()));
		date = date.replace('MMMM', _getMonthName(fmtDate.getMonth()));
		date = date.replace('MM', _formatDigit(fmtDate.getMonth()+1));
		date = date.replace('yyyy',fmtDate.getFullYear());
		date = date.replace('hh', _formatDigit(fmtDate.getHours()));
		date = date.replace('mm', _formatDigit(fmtDate.getMinutes()));
		date = date.replace('ss', _formatDigit(fmtDate.getSeconds()));

		return date;
	}

	var _formatDigit = function(digit) {
		digit += '';
		if (digit.length < 2) digit = '0' + digit;
		return digit;
	}

	var _getMonthName = function(month) {
		var name = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		return name[month];
	}

	var _getXMLDocument = function(string) {
		var browser = navigator.appName;
		var xml;
		if (browser == 'Microsoft Internet Explorer') {
			xml = new ActiveXObject('Microsoft.XMLDOM');
			xml.async = 'false';
			xml.loadXML(string);
		} else {
			xml = (new DOMParser()).parseFromString(string, 'text/xml');
		}
		return xml;
	}

	var _getLapsedTime = function(date) {
		
		// Get current date and format date parameter
		var todayDate = new Date();	
		var pastDate = new Date(date);

		// Get lasped time in seconds
		var lapsedTime = Math.round((todayDate.getTime() - pastDate.getTime())/1000)

		// Return lasped time in seconds, minutes, hours, days and weeks
		if (lapsedTime < 60) {
			return '< 1 min';
		} else if (lapsedTime < (60*60)) {
			var t = Math.round(lapsedTime / 60) - 1;
			var u = 'min';
		} else if (lapsedTime < (24*60*60)) {
			var t = Math.round(lapsedTime / 3600) - 1;
			var u = 'hour';
		} else if (lapsedTime < (7*24*60*60)) {
			var t = Math.round(lapsedTime / 86400) - 1;
			var u = 'day';
		} else {
			var t = Math.round(lapsedTime / 604800) - 1;
			var u = 'week';
		}
		
		// Check for plural units
		if (t > 1) u += 's';
		return t + ' ' + u;
	}

}));
