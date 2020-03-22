<?php
/**
 * DS-Portal 用 Widget
 * Google NEWS 専用 RSSリーダー
 * 
 * filename:  google_news.php
 * 
 *	専用設定
 *	'max' => [最大表示記事数]
 * 
 * @package   
 * @version   1.0.0
 * @copyright Copyright (C) 2020 Creansmaerd Co.,LTD. All rights reserved.
 * @date      2020-03-23
 * @author    木屋 善夫
 */
require_once(__DIR__ . '/rss.php');
require_once(__DIR__ . '/../libphp/phpQuery-onefile.php');
class CGoogleNews extends CRss
{
	//
	// コンストラクタ
	//
	public function __construct($strUser, $aryConfig)
	{
		// デフォルトインプリメンテーションの呼び出し 
		parent::__construct($strUser, $aryConfig);
		if (!empty($aryConfig['ignore'])) {
			$a = explode(',', $aryConfig['ignore']);
			foreach ($a as $ignore) {
				self::$g_aryExcludeSource[$ignore] = true;
			}
		}

//$s=print_r($aryConfig, true);
//$s=nl2br($s);
//echo "<div>{$s}</div>";
//die();
	}

	//
	// URLを取得
	//
	public function mixGetFeedURL()
	{
		static $aryTopic = [
			//   'トップ'   => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=h&num=50'
			// , 'ビジネス' => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=b&num=50'
			// , '科学'     => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=t&num=50'
			// , '社会'     => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=y&num=50'
			// , '国際'     => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=w&num=50'
			// , '政治'     => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=p&num=50'
			// , 'エンタメ' => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=e&num=50'
			// , 'スポーツ' => 'http://news.google.com/news?hl=ja&ned=us&ie=UTF-8&oe=UTF-8&output=rss&topic=s&num=50'

			// 'トップ'       => 'https://news.google.com/news/rss/?ned=jp&hl=ja&gl=JP',
			// 'ビジネス'     => 'https://news.google.com/news/rss/headlines/section/topic/BUSINESS.ja_jp?ned=jp&hl=ja&gl=JP',
			// 'テクノロジー' => 'https://news.google.com/news/rss/headlines/section/topic/SCITECH.ja_jp?ned=jp&hl=ja&gl=JP',
			// '国内'         => 'https://news.google.com/news/rss/headlines/section/topic/NATION.ja_jp?ned=jp&hl=ja&gl=JP',
			// '国際'         => 'https://news.google.com/news/rss/headlines/section/topic/WORLD?ned=jp&hl=ja&gl=JP',
			// '政治'         => 'https://news.google.com/news/rss/headlines/section/topic/POLITICS.ja_jp?ned=jp&hl=ja&gl=JP',
			// 'エンタメ'     => 'https://news.google.com/news/rss/headlines/section/topic/ENTERTAINMENT.ja_jp?ned=jp&hl=ja&gl=JP',

			// 2020-03-23 〜
			'トップ'       => 'https://news.google.com/news/rss/?ned=jp&hl=ja&gl=JP',
			'ビジネス'     => 'https://news.google.com/news/rss/headlines/section/topic/BUSINESS.ja_jp?ned=jp&hl=ja&gl=JP',
			'テクノロジー' => 'https://news.google.com/news/rss/headlines/section/topic/TECHNOLOGY.ja_jp?ned=jp&hl=ja&gl=JP',
			'科学' => 'https://news.google.com/news/rss/headlines/section/topic/SCIENCE.ja_jp?ned=jp&hl=ja&gl=JP',
			'日本'         => 'https://news.google.com/news/rss/headlines/section/topic/NATION.ja_jp?ned=jp&hl=ja&gl=JP',
			'国際'         => 'https://news.google.com/news/rss/headlines/section/topic/WORLD?ned=jp&hl=ja&gl=JP',
			'エンタメ'     => 'https://news.google.com/news/rss/headlines/section/topic/ENTERTAINMENT.ja_jp?ned=jp&hl=ja&gl=JP',
			'健康'     => 'https://news.google.com/news/rss/headlines/section/topic/HEALTH.ja_jp?ned=jp&hl=ja&gl=JP',
			'科学'     => 'https://news.google.com/news/rss/headlines/section/topic/SCIENCE.ja_jp?ned=jp&hl=ja&gl=JP',
		];

		//
		if (!empty($this->m_aryConfig['topic'])) {
			// 表示するトピックが指定されている 
			$ary = explode(',', $this->m_aryConfig['topic']);
			$aryRet = array();
			foreach ($ary as $str) {
				$str = trim($str);
				if (array_key_exists($str, $aryTopic))
					$aryRet[$str] = $aryTopic[$str];
			}
		}

		if (!empty($aryRet))
			return $aryRet;
		else
			return $aryTopic;
	}

	/**
	 * 除外する情報ソース
	 */
	static public $g_aryExcludeSource = array(// 毎日新聞の記事は新聞購読者でないと読めないので除外
		  '毎日新聞' => TRUE
		// ウォール・ストリート・ジャーナルの記事は新聞購読者でないと読めないので除外
		, 'ウォール・ストリート・ジャーナル日本版' => TRUE
		// 記事表示前の強制宣伝画面がかなり長い
		, 'CNET Japan' => TRUE);

	/**
	 * 1つ分のアイテム情報を作成
	 * @param  SimpleXMLElement $objItem
	 * @return	array(*			  'TITLE' => [タイトル  ]
	 *			, 'LINK'  => [リンク先  ]
	 *			, 'DATE'  => [配信日時  ]
	 *			, 'AGO'   => [経過時間  ]
	 *			, 'DESC'  => [記事の内容]
	 *			, 'CLASS' => [既読時に付けるclass属性]
	 *);
	 */
	public function aryMakeItem($objItem)
	{
		// デフォルトインプリメンテーションの呼び出し 
		$aryRet = parent::aryMakeItem($objItem);
		if (empty($aryRet)) {
			return $aryRet;
		}

		//
		$pqDesc = phpQuery::newDocument($aryRet['DESC']);

		// タイトルを取得 
		// 最初のアンカー
//		$pq = $pqDesc->find('td.j div.lh a b');
//		$aryRet['TITLE'] = pq($pq->get(0))->text();

		// リンク 
		// Google経由になっているので本来のURLを取得
		// query 取得
		$str = parse_url($aryRet['LINK'], PHP_URL_QUERY);
		// query を分解
		parse_str($str, $ary);
		// url= があれば値を取得
		if (array_key_exists('url', $ary)) {
			$aryRet['LINK'] = $ary['url'];
		}

		// 既読 
		$aryRet['READED'] = array_key_exists($aryRet['LINK'], $this->m_aryReaded);

		// 7日以上経ったものは除外
		if ($aryRet['TIME'] < (time() - 7*24*60*60)) {
			return null;
		}

		// 画像を取得 
		// 最初の画像
		$pq = $pqDesc->find('img');
		$aryRet['IMAGE'] = pq($pq->get(0))->attr('src');

		// 配信者 
		$pq = $pqDesc->find('font[color="#6f6f6f"]');	// 最初の灰色
		$aryRet['AUTHOR'] = pq($pq->get(0))->text();
		$pq->remove();

		// 本文を取得 
		$pq = $pqDesc->find('li');
		$desc = pq($pq->get(1))->text();
		$desc = preg_replace('/\s*Full\scoverage\s*/', '', $desc);
		$aryRet['DESC'] = $desc;
//$s=print_r($aryRet, true);
//$s=nl2br($s);
//echo $s;

//$s=print_r(self::$g_aryExcludeSource, true);
//$s=nl2br($s);
//echo "<div>{$s}</div>";


//$s=print_r($this->m_aryConfig, true);
//$s=nl2br($s);
//echo "<div>{$s}</div>";

		// 配信元により除外
		if (array_key_exists($aryRet['AUTHOR'], self::$g_aryExcludeSource)) {
			return NULL;
		}

		//
		return $aryRet;
	}
}
