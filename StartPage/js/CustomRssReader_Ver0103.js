/*
 * CustomRssReader - http://hkdesign.blog91.fc2.com/
 *
 * Licensed GPL
 *
 * Date: 2011-05-12
 *
 * Ver: 1.01
 *      Line256 : forの走査方法を変更
 * Ver: 1.02
 *      2011-09-29 複数起動に対応
 *      2011-09-29 config記述ミス修正
 *      2011-10-02 日付情報がない場合の処理追加 Line:386
 *      2011-10-02 変数名の変更 sort→sortItem
 *      2011-10-02 displayItemにseqの追加
 *      2011-10-02 ソート方法seqの追加　newmarkとbookmarkの削除
 * Ver: 1.03
 *      2011-11-20 日付が未来になっている記事の表示設定機能を追加
 *
 * Yahoo! Query Language http://developer.yahoo.com/yql/
 *
 * HeartRails Capture - http://capture.heartrails.com/
*/
(function(){
	jQuery.fn.CustomRssReader = function(config){
		// 引数のデフォルト値を渡す {}内は、カンマ（,）で区切って複数可能
		config = jQuery.extend({
			rssArr: '',
			sortItem:{name: 'date', to: 'down'},
			rssItemLimit: 5,
			displayItem:{0: 'date', 1: 'category', 2: 'title', 3: 'bookmark'},
			titleLength: 30,
			descriptionLength: 60,
			dateFormat: '{Y4}/{M}/{D}/({y}) {h}:{m}:{s}',
			effectSpeed: 0.3,
			newmark:  {'name': 'https://blog-imgs-29.fc2.com/h/k/d/hkdesign/newIcon01.gif', 'hour': 24},
			thumnail: {'mode': 'off', 'size': 'm', 'x': 0, 'y': -100},
			futureEntry: {'mode': 'off'}
		},config);

		var target = this;
		var thumnailBox = '';
		var customRSSItem = Array();
		var urlKey = 0;
		var urlMax = config.rssArr.length;
		var effectSpeed = config.effectSpeed*1000;
		
		target.prepend('<div class="crr_container" style="position:relative;"></div><div class="hkdLink"></div><ul class="thumnailBox"></ul>');
		thumnailBox = $('ul.thumnailBox', target);
		var _w = $('div.crr_container', target).css('width');
		setHkdLink($('.hkdLink', target), _w);
		target = $('div.crr_container', target);
		target.html('<ul class="rssBox" style="position:relative;"></ul>');

		//最初のYQL実行
		if(config.rssArr[0].url != undefined){
			loadYQL(urlKey);
		}
		
		//Seqの初期設定
		var _seq = 0;
		function loadYQL(urlKey){
			$.ajax({
				   scriptCharset:"UTF-8",
				   dataType: 'json',
				   cache: false,
				   url : 'https://query.yahooapis.com/v1/public/yql?callback=?',
				   type : 'GET',
				   data : {
						q: "select item, channel.item, entry from xml where url = '"+config.rssArr[urlKey].url+"' limit "+config.rssItemLimit,
						format: "json"},
				   success : function (json) {
					   if(json.query.results){
						   //config.rssArrのどのURLからYQLを実行されているのか調べる
						   var _yqlUrl = $(this)[0].url;
						   _yqlUrl = _yqlUrl.slice(_yqlUrl.indexOf("'")+1);
						   _yqlUrl = decodeURIComponent(_yqlUrl);
						   //URLに一致するcategoryを取得する
						   var _rssArrCategory = '';
						   for(var i in config.rssArr){
							   if(_yqlUrl.indexOf(config.rssArr[i].url) != -1){
								   _rssArrCategory = config.rssArr[i].category;
							   }
						   }
						   //ここからRSSの各タイトル・日付などの取得
							for(var i in json.query.results){
								for(var j in json.query.results[i]){
									var _item = '';
									var _title = '';
									var _link = '';
									var _description = '';
									var _date = '';
									
									//itemがあればRSS1.0
									if(json.query.results[i][j].item){
										_item = json.query.results[i][j].item;
										//要約を作成
										if(_item.description != undefined){
											_description = _item.description;
										}
										//dateに2011-05-01T00:00:00+09:00の形式で日付が格納されているので
										//それを各ブラウザで読み込めるように2011/05/00 00:00:00の形式に変換する
										//変換後格納
										if(_item.date != undefined){
											var _date = Date.parse(setTimeStamp(_item.date));
										}
										//pubDataにMon, 02 May 2011 00:00:00 +0000の形式で日付が格納されているので
										//そのままタイムスタンプにし新たに格納
										else if(_item.pubDate != undefined){
											var _date = Date.parse(_item.pubDate);
										}else if(_item.date != undefined){
											var _date = Date.parse(setTimeStamp(_item.date));
										}
									}
									//channelがあればRSS2.0
									else if(json.query.results[i][j].channel){
										_item = json.query.results[i][j].channel.item;
										//要約を作成
										if(_item.description != undefined){
											_description = _item.description;
										}
										//pubDataにMon, 02 May 2011 00:00:00 +0000の形式で日付が格納されているので
										//そのままタイムスタンプにし新たに格納
										if(_item.pubDate != undefined){
											var _date = Date.parse(_item.pubDate);
										}else if(_item.date != undefined){
											var _date = Date.parse(setTimeStamp(_item.date));
										}
									}
									//entryがああればAtom0.3かAtom1.0
									else if(json.query.results[i][j].entry){
										var _item = json.query.results[i][j].entry;
										//modifiedがあればAtom0.3
										if(_item.modified){
											//modifiedがあれば2011-05-01T00:00:00Zの形式で日付が格納されているので
											//それを2011/05/01 00:00:00の形式に変換する
											var _date = Date.parse(setTimeStamp(_item.modified));
										}
										//updatedがあればAtom1.0
										else if(_item.updated){
											//updatedがあれば2011-05-01T00:00:00Zの形式で日付が格納されているので
											//それを2011/05/01 00:00:00の形式に変換する
											var _date = Date.parse(setTimeStamp(_item.updated));
										}
										//要約
										if(_item.summary){
											if(_item.summary['content'] != undefined){
												var _description = _item.summary['content'];
											}else if(_item.summary != undefined){
												var _description = _item.summary;
											}
										}else{
											if(_item.content['content'] != undefined){
												var _description = _item.content['content'];
											}else if(_item.content != undefined){
												var _description = _item.content;
											}
										}
									}
									//1件取得の場合
									else if(json.query.results[i].item){
										_item = json.query.results[i].item;
										//要約を作成
										if(_item.description != undefined){
											_description = _item.description;
										}
										//dateに2011-05-01T00:00:00+09:00の形式で日付が格納されているので
										//それを各ブラウザで読み込めるように2011/05/00 00:00:00の形式に変換する
										//変換後格納
										if(_item.date != undefined){
											var _date = Date.parse(setTimeStamp(_item.date));
										}
									}
									else{
										//それでもみつからなければスキップ
										continue;
									}
									//タイトル
									if(_item.title['content'] != undefined){
										var _title = _item.title['content'];
									}else if(_item.title != undefined){
										var _title = _item.title;
									}
									//リンク
									if(_item.link instanceof Array){
										if(_item.link[0]['href'] != undefined){
											var _link = _item.link[0]['href'];
										}else if(_item.link[0] != undefined){
											var _link = _item.link[0];
										}	
									}else{
										if(_item.link['href'] != undefined){
											var _link = _item.link['href'];
										}else if(_item.link != undefined){
											var _link = _item.link;
										}	
									}
									if(_title.length > config.titleLength){
										_title = _title.slice(0, config.titleLength);
										_title += '・・・';
									}
									if(typeof _description == 'string'){
										//要約内のHTMLタグを削除
										_description = _description.replace(/<\/?[^>]+>/gi, '');
										//要約内の改行コード等を削除
										_description = _description.replace(/\s/g,'');
										if(_description.length > config.descriptionLength){
											_description = _description.slice(0, config.descriptionLength);
											_description += '・・・';
										}
									}
									//未来の記事を表示しない場合
									if(config.futureEntry['mode'] == 'off'){
										var _nowTime = new Date();
										if(_nowTime.getTime() < _date){
											continue;
										}
									}
									//シーケンス番号の設定
									_seq++;
									//取得・整形したものを配列に格納
									customRSSItem.push({
														'seq': _seq,
														'title': _title,
														'description': _description,
														'link': _link,
														'date': _date,
														'category': _rssArrCategory
														});
									
								}
							}
							//ソートとアニメーション実行
							animation();
					   }else{
						   //RSSが取得できません
							target.prepend('<div class="yqlLoadErr" style="font-weight:bold;text-align:center;color: red;background-color: #EEEEEE;z-index: 1;">"'+config.rssArr[urlKey].url+'" is not found</div>');
	
							$('div.yqlLoadErr', target).delay(5000).fadeOut(effectSpeed);
					   }
						//url配列の次のurlからYQL実行（次のRSS取得）
						if(urlKey < urlMax-1){
							urlKey++;
							//アニメーション実行中はYQL実行しないように遅延を設定（アニメーションを見せるため）
							//effectSpeed=0の時はすぐにloadYQL()が実行される
							//ここで再起処理が行われている
							setTimeout(function (){loadYQL(urlKey)}, effectSpeed*urlKey);
						}else{
							//全てのRSSの取得が終了
						}
				   },
				   //YQLの通信に失敗
					error: function(request, status, thrown){
						target.prepend('<div class="yqlLoadErr" style="font-weight:bold;text-align:center;color: red;background-color: #EEEEEE;z-index: 1;">YQL error</div>');

						$('div.yqlLoadErr', target).delay(5000).fadeOut(effectSpeed);
						//url配列の次のurlからYQL実行（次のRSS取得）
						if(urlKey < urlMax-1){
							urlKey++;
							//再起処理（成功時の内容と一緒）
							setTimeout(function (){loadYQL(urlKey)}, effectSpeed*urlKey);
						}else{
							//全てのRSSの取得が終了
						}
					}
			});
		}
		//アニメーション関数
		function animation(){
			var rssLi = '';
			//ソート
			if(config.sortItem.to == 'down'){
				customRSSItem.sort ( function (b1, b2) { return b1[config.sortItem.name] < b2[config.sortItem.name] ? 1 : -1; } );
			}else if(config.sortItem.to == 'up'){
				customRSSItem.sort ( function (b1, b2) { return b1[config.sortItem.name] > b2[config.sortItem.name] ? 1 : -1; } );
			}
			//ブラウザに表示
			var _crssLength = customRSSItem.length;
			for(var i=0; i<_crssLength; i++){
				//高さが設定されていたら（既に読み込んであったら）
				if(customRSSItem[i].height != undefined){
					rssLi += '<li class="rssList" style="top: '+customRSSItem[i].height+'px;position: absolute;">'+setListHtml(customRSSItem[i])+'</li>';
				}
				//そうでなかったら非表示
				else{
					rssLi += '<li class="rssList hide" style="top: 0px;position: absolute; display:none;">'+setListHtml(customRSSItem[i])+'</li>';
					if(config.thumnail['mode'] == 'on'){
						var _size ='';
						var _height = '0px';
						switch(config.thumnail['size']){
							case 's':
								_size = 'small';
								_height = '92px'
								break;
							case 'l':
								_size = 'large';
								_height = '302px'
								break;
							default:
								_size = 'medium';
								_height = '152px'
								break;
						}
						thumnailBox.prepend('<li class="crr_thumnail thum'+customRSSItem[i].seq+'" style="position:absolute;z-index:100;display:none;height:'+_height+';"><img src="https://capture.heartrails.com/'+_size+'/delay=2?'+customRSSItem[i].link+'" /></li>');
					}
				}
			}
			$('ul.rssBox', target).html(rssLi);
			
			//アニメーション設定（目的の高さへ移動）
			//補足：このため各RSSが表示される<li>タグの部分は絶対配置にしている。
			var _h = 0;
			$('ul.rssBox li.rssList', target).each(function(i){
				//高さを設定
				customRSSItem[i].height = _h;
				//移動アニメーション
				$(this).animate({'top': _h}, effectSpeed, function(){
					//移動が終わったら非表示に設定されているのを表示
					$(this, '.hide').fadeIn(effectSpeed).removeClass('hide');
				});
				//高さの再設定（次回持ち越し）
				//補足：outerHeightで現在の<li>の高さを取得し、次回の<li>の位置に設定する
				_h += $(this).outerHeight(true);
				
				//サムネイル表示の設定
				if(config.thumnail['mode'] == 'on'){
					var _a = $('span.crr_title a', this);
					_a.hover(
						function(){
							//<a>タグのクラス名からサムネイル名を取得
							var _thumNo = $(this).attr('class');
							$('li.'+_thumNo, thumnailBox).fadeIn(0);
						},
						function(){
							//<a>タグのクラス名からサムネイル名を取得
							var _thumNo = $(this).attr('class');
							$('li.'+_thumNo, thumnailBox).fadeOut(0);
						}
					);
					_a.mousemove(function(e){
						//<a>タグのクラス名からサムネイル名を取得
						var _thumNo = $(this).attr('class');
						var _top = e.pageY + config.thumnail.y;
						var _left = e.pageX + config.thumnail.x;
						$('li.'+_thumNo, thumnailBox).css("top",_top+"px").css("left",_left+"px");
					});					
				}
			});
		}
		//タイムスタンプ作成関数
		function setTimeStamp(_dateStr){
			if(typeof _dateStr == 'string'){
				var timeStamp = _dateStr;
				timeStamp = timeStamp.slice(0,19);
				timeStamp = timeStamp.replace(/-/g, '/');
				timeStamp = timeStamp.replace('T', ' ');
				return timeStamp;
			}else{
				return;
			}
		}
		//<li>内のHTML整形
		function setListHtml(customRSSItem){
			var liHtml = ''
			var _item = new Array();
			_item.seq = '<span class="crr_seq">'+customRSSItem.seq+'</span>';
			_item.date = '<span class="crr_date">'+replaceDate(customRSSItem.date)+'</span>';
			_item.newmark = '<span class="crr_newmark">'+setNewmark(customRSSItem.date)+'</span>';
			var _cStr = customRSSItem.category;
			if(_cStr.match(/http:\/\/[^\s]*?\.jpg|png|gif/)){
				_item.category = '<span class="crr_category"><img src="'+_cStr+'" style="border:none;" /></span>';
			}else{
				_item.category = '<span class="crr_category">'+_cStr+'</span>';
			}
			_item.title = '<span class="crr_title">'+'<a href="'+customRSSItem.link+'" class="thum'+customRSSItem.seq+'" title="'+customRSSItem.description+'" target="_blank">'+customRSSItem.title+'</a>'+'</span>';
			var _encLink = encodeURIComponent(customRSSItem.link);
			var _encTitle = encodeURIComponent(customRSSItem.title);
			var hatebuTag = '&nbsp;<a href="https://b.hatena.ne.jp/entry/'+customRSSItem.link+'"><img src="https://b.hatena.ne.jp/entry/image/'+customRSSItem.link+'" title="'+customRSSItem.title+'" style="border:none;" /></a>';
			var tweetTag = '&nbsp;<a href="https://tweetbuzz.jp/redirect?url='+_encLink+'"><img src="https://tools.tweetbuzz.jp/imgcount?url='+_encLink+'" style="border: none;" /></a>';
			var faceTag = '&nbsp;<iframe src="https://www.facebook.com/plugins/like.php?href='+_encLink+'&layout=button_count&show_faces=false&width=100&action=like&colorscheme=light&height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe> ';
			_item.bookmark = '<ul class="crr_bookmark"><li class="crr_hatebu">'+hatebuTag+'</li><li class="crr_tweet">'+tweetTag+'</li><li class="crr_face">'+faceTag+'</li></ul>';
			for(var i in config.displayItem){
				liHtml += _item[config.displayItem[i]];
			}
			return liHtml;
		}
		//タイムスタンプからnewマークを出すかどうかの処理
		function setNewmark(_rssd){
			var _newmark = '';
			var _date = new Date();
			var _nowTime = _date.getTime();
			var _setMilSec = config.newmark['hour'] * 60 * 60 * 1000;
			//現在の時間がRSSの日付と設定した時間の和を越えればNewマーク表示
			//補足：時間とはトータルのミリ秒（タイムスタンプ）
			if(_nowTime < _rssd + _setMilSec  ){
				if(config.newmark['name'].match(/http:\/\/[^\s]*?\.jpg|png|gif/)){
					_newmark = '<img src="'+config.newmark['name']+'" style="border:none;" />';
				}else{
					_newmark = config.newmark['name'];
				}
			}
			return _newmark;
		}
		//日付をフォーマットの形に整形
		function replaceDate(_d){
			//日付情報がない場合
			if(_d != ''){
				var monthE = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
				var weekJ = new Array('日', '月', '火', '水', '木', '金', '土');
				var weekE = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	
				var date = new Date();
				date.setTime(_d);
				var result = config.dateFormat;
				
				var _Y = date.getFullYear();
				result = result.replace('{Y4}', _Y);
				result = result.replace('{Y2}', _Y.toString().slice(2));
				var _M = date.getMonth();
				result = result.replace('{M}', _M+1);
				result = result.replace('{ME}', monthE[_M]);
				var _D = date.getDate();
				if(_D < 10)
					result = result.replace('{D}', '0'+_D);
				else
					result = result.replace('{D}', _D);
				var _h = date.getHours();
				if(_h < 10)
					result = result.replace('{h}', '0'+_h);
				else
					result = result.replace('{h}', _h);
				var _m = date.getMinutes();
				if(_m < 10)
					result = result.replace('{m}', '0'+_m);
				else
					result = result.replace('{m}', _m);
				var _s = date.getSeconds();
				if(_s < 10)
					result = result.replace('{s}', '0'+_s);
				else
					result = result.replace('{s}', _s);
				var _day = date.getDay();
				result = result.replace('{y}', weekJ[_day]);
				result = result.replace('{yE}', weekE[_day]);
			}
			else{
				result = '日付情報なし';
			}
			return result;
		}
		//HKDへのリンクやアニメーションの作成
		function setHkdLink(target, _w){
			var _css = {
				'font-size': 'x-small',
				'width': _w,
				'text-align': 'right',
				'font-style': 'italic'
			};
			target.html('<a href="https://hkdesign.blog91.fc2.com/blog-entry-119.html">Created by HKD</a>').css(_css);
		}
	};
})(jQuery);