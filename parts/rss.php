<?php
/**
 * DS-Portal 用 Widget
 * GRSSリーダー
 * 
 * filename:  google_news.php
 * 
 *	専用設定
 *	'url' => [RSSフィードのURL]
 *  'max' => [最大表示記事数]
 * 
 * @package   
 * @version   1.0.0
 * @copyright Copyright (C) 2020 Creansmaerd Co.,LTD. All rights reserved.
 * @date      2020-03-23
 * @author    木屋 善夫
 */
require_once(__DIR__ . '/Widget.php');

class CRss extends CWidget
{
	//
	// コンストラクタ
	//
	public function __construct($strUser, $aryConfig)
	{
		// デフォルトインプリメンテーションの呼び出し 
		parent::__construct($strUser, $aryConfig);
	}

	//
	// 定数
	//
	const MAX_READED = 200;

	/**
	 * @var array
	 */
	public $m_aryURL    = [];

	/**
	 * @var int
	 */
	public $m_iMax      = 15;

	/**
	 * @var string
	 */
	public $m_strViewType = 'simple';

	/**
	 * @var int
	 */
	public $m_iMaxPage  = 1;

	/**
	 * @var array
	 */
	public $m_aryReaded = [];

	/**
	 * 初期化
	 */
	public function Init()
	{
		// URLの取得 
		$mixURL = $this->mixGetFeedURL();
		switch (gettype($mixURL)) {
		case 'array':
			$this->m_aryURL = $mixURL;
			break;

		case 'string':
			$this->m_aryURL = array($mixURL);
			break;

		default:
			DLOG('url is error!');
			echo "RSSフィードのURLを指定してください。";
			return;
		}

		// 最大表示数の取得 
		$this->m_iMax = iGetArrayItem($this->m_aryConfig, 'max', 0);
		if ($this->m_iMax <= 0)
			$this->m_iMax = 15;

		//
		$this->m_strViewType = strGetArrayItem($this->m_aryConfig, 'view type', 'simple');

		// 並べ替え 
		$this->m_strSortType = strGetArrayItem($this->m_aryConfig, 'sort type', 'unread new');

		// <a>タブクリック次の処理は独自に設定する 
		objGetApp()->m_blAonClick = FALSE;
	}

	//
	// URLを取得
	//
	public function mixGetFeedURL()
	{
		// 単一URL 
		if (!empty($this->m_aryConfig['url']))
			return array($this->m_aryConfig['url']);

		// 複数URL 
		$aryRet = array();
		for ($i = 1; $i <= 5; ++ $i) {
			if (!empty($this->m_aryConfig["url{$i}"])) {
				$u = $this->m_aryConfig["url{$i}"]; 
				$t = strGetArrayItem($this->m_aryConfig, "name{$i}", "TAB{$i}");
				$aryRet[$t] = $u;
			}
		}
		if (!empty($aryRet))
			return $aryRet;

		//
		return NULL;
	}

	/**
	 * RSSの読み込み
	 * @param  string $strURL URL
	 * @return SimpleXMLElement 
	 * @return NULL 読み込み失敗
	 */
	public function objLoadRss($strURL)
	{
		// RSSフィードを読み込む 
		for ($i = 1; $i; -- $i) {
			// ファイルの読み込み
			DLOG("load: '{$strURL}'");
			$strXML = @file_get_contents($strURL);

			// ITPro は & が単独が存在するのでエラーになってしまう
			$strXML = preg_replace('/\&(?=[^a-z0-9])/ius', '&amp;', $strXML);

			// SimpleXMLElement の構築
			if ($strXML !== FALSE) {
				$objXML = @simplexml_load_string($strXML);
				if ($objXML instanceof SimpleXMLElement)
					break;
			}

			// ちょっと待つ
			sleep(5);
		}
		if (!($objXML instanceof SimpleXMLElement)) {
			echo "{$strURL} が読み込めませんでした。";
			return;
		}

//		DLOG('$objXML', $objXML);

		//
		return $objXML;
	}


	/**
	 * <body></body> の中身の出力
	 */
	public function EchoBodyContents()
	{
		// タブを出力 
		if (count($this->m_aryURL) > 1)
			$this->EchoTabbar();

		// RSS フィードを出力 
		$this->m_iTabNo = 0;
		foreach ($this->m_aryURL as $k => $v) {
			$this->m_strTabTitle = $k;
			$this->m_strURL      = $v;

			// RSS読み込み
			$objXML = $this->objLoadRss($v);
			if (!empty($objXML)) {
				// 読み込んだので表示
				$this->EchoRSS($objXML);
			}

			// 次のタブ
			++ $this->m_iTabNo;
		}
	}

	/**
	 * タブを出力
	 * @param  
	 * @return 
	 * @return 
	 */
	public function EchoTabbar()
	{
?>
<div class="tab_bar">
	<div class="space_l"></div>
<?php
		$iTabNo = 0;
		foreach ($this->m_aryURL as $strTitle => $strURL) {
__();?>
	<div class="tab tab{$b[0]}" title="{$b[1]}" data-dsp='{"cmd":"page","rss":"div.rss{$b[0]}"}'>{$b[1]}</div>
	<div class="space_c"></div>
<?php
			__($iTabNo, $strTitle);
			++ $iTabNo;
		}
?>
	<div class="space_r"></div>
</div><!--tab_bar-->
<?php
	}

	/**
	 * 1つ分のRSSフィードを出力
	 * @param SimpleXMLElement $objXML
	 */
	public function EchoRSS($objXML)
	{
		//
		$this->m_strType = strtoupper($objXML->getName());
		switch ($this->m_strType) {
		case 'RDF':		// RSS1.0
		case 'RSS':		// RSS2.0
		case 'FEED':	// ATOM
			break;

		default:
			if (!empty($objXML->entry))
				$strTyle = 'FEED';
			else if (!empty($objXML->item))
			    $strTyle = 'RDF';
			else if (!empty($objXML->channel->item))
			    $strTyle = 'RSS';
			else
			{
				echo "このRSSフィードは未対応フォーマットです。";
				return;
			}
			break;
		}

		DLOG("RSSフィードタイプ", $this->m_strType);

		// 既読情報の取得 
		$this->m_aryReaded = $this->aryLoadReaded();

		// 記事アイテムの格納配列を特定 
		$iCount = $this->m_iMax;
		switch ($this->m_strType) {
		case 'RDF':		$aryFeed = $objXML->item; break;
		case 'RSS':		$aryFeed = $objXML->channel[0]->item; break;
		case 'FEED':	$aryFeed = $objXML->entry; break;
		}

		// 各記事オブジェクトから必要な情報を抜き出す 
		$this->m_aryItem = array();
		foreach ($aryFeed as $objItem) {
			$a = $this->aryMakeItem($objItem);
			if (!empty($a)) {
				$this->m_aryItem[] = $a;
			}
		}

		// 記事の並べ替え 
		switch ($this->m_strSortType) {
			// 未読優先, 新しい順
		case 'unread new':
			usort($this->m_aryItem, array('CRss', 'iSortUnreadNew'));
			break;

			// 新しい順
		case 'new':
			usort($this->m_aryItem, array('CRss', 'iSortNew'));
			break;

			// 古い順
		case 'old':
			usort($this->m_aryItem, array('CRss', 'iSortOld'));
			break;
		}

		// クラスを設定 
		foreach ($this->m_aryItem as $k => $a) {
			$ary = array($this->m_strViewType);
			if ($a['READED'])
				$ary[] = 'readed';

			if ($k & 1)
				$ary[] = 'odd';

			$this->m_aryItem[$k]['CLASS'] = (!empty($ary)) ? ' class="' . implode(' ', $ary) . '"' : '';
		}

		// 最大ページ数を算出 
		$this->m_iMaxPage = (int)floor((count($this->m_aryItem) - 1) / $this->m_iMax) + 1;

__();?>
<!--===============================================================-->
<div class="rss rss{$b[0]}" data-dsp='{"rss": "{$b[1]}", "no": "{$b[0]}"}' >
<?php __($this->m_iTabNo, $this->m_strURL);

		//
		for ($iPage = 0; $iPage < $this->m_iMaxPage; ++ $iPage)
			$this->EchoPage($iPage);
?>
</div><!--rss-->
<?php
	}

	/**
	 * 1ページ分のアイテムを切り出す
	 * @param  array 全アイテムが格納された配列
	 * @param  int   ページ番号
	 * @return array 1ページ分のアイテム
	 */
	public function aryGetPageItem($iPage)
	{
		$iStart = $iPage * $this->m_iMax;
		$iEnd   = $iStart + $this->m_iMax;
		if ($iEnd > count($this->m_aryItem))
			$iEnd = count($this->m_aryItem);

		return array_slice($this->m_aryItem, $iStart, $iEnd - $iStart);
	}

	//
	// 1ページ分の出力
	//
	function EchoPage($iPage)
	{
		$aryPage = $this->aryGetPageItem($iPage);
__();?>
<div class="page page{$b[0]}">
<?php	__($iPage);

		// ナビ 
		$a = $this->aryMakeNavibox($iPage, $aryPage);
__();?>
	<div class="navi_box" >
		<!-- 先頭 -->
		<div class="top">
			<div class="button {$a['TOP']['DISABLED']}" data-dsp='{"cmd":"page","id":"div.rss{$a["TAB"]} div.page{$a["TOP"]["PAGE"]}"}'><img src="/img/go-first.png" /><!--先頭--></div>
		</div>
		<!-- 前へ -->
		<div class="prev">
			<div class="button {$a['PREV']['DISABLED']}" data-dsp='{"cmd":"page","id":"div.rss{$a["TAB"]} div.page{$a["PREV"]["PAGE"]}"}'><img src="/img/go-previous.png" /><!--前へ--></div>
		</div>
		<div class="space"></div>
		<!-- このページを既読 -->
		<div>
			<div class="button" data-dsp='{"cmd":"readed","ids":{$a["PAGE"]}}'>このページを既読</div>
		</div>
		<!-- すべて既読 -->
		<div>
			<div class="button" data-dsp='{"cmd":"readed","ids":{$a["ALL"]}}'>すべて既読</div>
		</div>
		<div class="space"></div>
		<!-- 未読数 -->
		<div class="unread">未読:<span></span></div>
		<div class="space"></div>
		<!-- 現在のページ/全ページ -->
		<div class="pos">{$a['POS']}</div>
		<!-- 次へ -->
		<div class="next">
			<div class="button {$a['NEXT']['DISABLED']}" data-dsp='{"cmd":"page","id":"div.rss{$a["TAB"]} div.page{$a["NEXT"]["PAGE"]}"}'><img src="/img/go-next.png" /><!--次へ--></div>
		</div>
		<!-- 末尾 -->
		<div class="last">
			<div class="button {$a['LAST']['DISABLED']}" data-dsp='{"cmd":"page","id":"div.rss{$a["TAB"]} div.page{$a["LAST"]["PAGE"]}"}'><img src="/img/go-last.png" /><!--末尾--></div>
		</div>
	</div>
	<div class="border" ></div>
<?php __($a);

		// 本体 
?>
	<ul>
<?php
		foreach ($aryPage as $aryArticle)
			$this->EchoArticle($aryArticle);
?>
	</ul>
<?php
		//
?>
</div>
<?php
	}

	/**
	 * １記事の出力
	 * @param  array $aryPage
	 * @return 
	 * @return 
	 */
	public function EchoArticle($aryArticle)
	{
		__();
		switch ($this->m_strViewType) {
			// 簡素表示 
			// タイトル
		default:
		case 'simple':
?>
		<li{$a['CLASS']}>
			<a href="{$a['LINK']}">{$a['TITLE']}</a>&nbsp;{$a['AGO']}
		</li>
<?php
			break;

			// 通常表示 
			// タイトル, 画像, 詳細
		case 'normal':
			if (!empty($aryArticle['IMAGE'])) {
				// イメージ有り 
?>
		<li{$a['CLASS']}>
			<a href="{$a['LINK']}">{$a['TITLE']}</a>&nbsp;{$a['AGO']}<br />
			<img src="{$a['IMAGE']}" />
			{$a['DESC']}
			<br class="clear" />
		</li>
<?php
			} else {
				// イメージ無し 
?>
		<li{$a['CLASS']}>
			<a href="{$a['LINK']}">{$a['TITLE']}</a>&nbsp;{$a['AGO']}<br />
			{$a['DESC']}
			<br class="clear" />
		</li>
<?php
			}
			break;
		}

		//
		__($aryArticle);
	}

	/**
	 * 新しい順
	 */
	public function iSortNew($a, $b)
	{
		return $b['TIME'] - $a['TIME'];
	}

	/**
	 * 古い順
	 */
	public function iSortOld($a, $b)
	{
		return $b['TIME'] - $a['TIME'];
	}

	/**
	 * 未読で新しい順
	 */
	public function iSortUnreadNew($a, $b)
	{
		// 未読が優先 
		$ar = (!$a['READED']) ? 1 : 0;
		$br = (!$b['READED']) ? 1 : 0;

		$r = $br - $ar;
		if ($r != 0)
			return $r;

		// 記事の日時 
		return $b['TIME'] - $a['TIME'];
	}

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
		switch ($this->m_strType) {
		    //=== RSS1.0 =============================================
		case 'RDF':
			$strTitle = (string)$objItem->title;
			$strLink  = (string)$objItem->link;
			$strDesc  = (string)$objItem->description;
			$strDate = (string)$objItem->children('http://purl.org/dc/elements/1.1/')->date;
			break;

			// RSS2.0 
		case 'RSS':
			$strTitle = (string)$objItem->title;
			$strLink  = (string)$objItem->link;
			$strDesc  = (string)$objItem->description;
			$strDate  = (string)$objItem->pubDate;
			break;

			// ATOM 
		case 'FEED':
			$strTitle = (string)$objItem->title;
			$strLink  = (string)$objItem->link['href'];
			$strDesc  = (string)$objItem->content;
			if (isset($objItem->updated))
				$strDate  = (string)$objItem->updated;
			else if (isset($objItem->issued))
				$strDate  = (string)$objItem->issued;
			else if (isset($objItem->published))
				$strDate  = (string)$objItem->published;
			break;
		}
		if (empty($strTitle)) {
			return NULL;
		}

		// 日時 
		$border = strtotime('-7 days');		// 7日前
		$border = date('Y/m/d', $border);	// 00:00:00
		$border = strtotime($border);		// UNIX_TIME
	
		$iTime   = strtotime($strDate);
		if ($iTime < $border) {
			return null;
		}

		if ($iTime > time())
			$iTime = time();		// 未来は現在日時にする
		$strDate = date('Y/m/d H:i:s', $iTime);
		$strAgo = $this->strMakeTimeAgo($strDate);

		// 既読 
		$blReaded = array_key_exists($strLink, $this->m_aryReaded);

		//
		return array('TITLE'  => $strTitle
			, 'LINK'   => $strLink
			, 'DESC'   => $strDesc
			, 'TIME'   => $iTime
			, 'DATE'   => $strDate
			, 'AGO'    => $strAgo
			, 'READED' => $blReaded);
	}

	/**
	 * ナビボックス情報の作成
	 * @param  
	 * @return	array(*				  'HREF' => [このページのURI]
	 *				, 'PREV' => array(*					  'DISPLAY' => [display:スタイルの値]
	 *					, 'QUERY'   => [URLにつける開始番号]
	 *)
	 *				, 'NEXT' => 
	 *					  'DISPLAY' => [display:スタイルの値]
	 *					, 'QUERY'   => [URLにつける開始番号]
	 *)
	 *)
	 * @return 
	 */
	public function aryMakeNavibox($iPage, $aryPages)
	{
		// 戻り値の初期化 
		$aryRet = array('HREF' => $_SERVER['REQUEST_URI']
			, 'ALL'  => array()
			, 'PAGE' => array()
			, 'TAB'  => $this->m_iTabNo
			, 'POS'  => ($iPage + 1) . '/' . $this->m_iMaxPage
			, 'TOP'  => array('DISABLED' => 'disabled', 'PAGE' => 0)
			, 'PREV' => array('DISABLED' => 'disabled', 'PAGE' => 0)
			, 'NEXT' => array('DISABLED' => 'disabled', 'PAGE' => $this->m_iMaxPage - 1)
			, 'LAST' => array('DISABLED' => 'disabled', 'PAGE' => $this->m_iMaxPage - 1));

		// 先頭 
		if ($iPage != 0) {
			$aryRet['TOP']['DISABLED'] = '';
		}

		// 前へ 
		if (($iPage - 1) >= 0) {
			$aryRet['PREV']['DISABLED'] = '';
			$aryRet['PREV']['PAGE'   ] = $iPage - 1;
		}

		// 次へ 
		if (($iPage + 1) < $this->m_iMaxPage) {
			$aryRet['NEXT']['DISABLED'] = '';
			$aryRet['NEXT']['PAGE'   ] = $iPage + 1;
		}

		// 末尾 
		if ($iPage != ($this->m_iMaxPage - 1)) {
			$aryRet['LAST']['DISABLED'] = '';
		}

		// このページを既読 
		$a = array();
		foreach ($aryPages as $aryArticle)
			$a[] = $aryArticle['LINK'];

		$aryRet['PAGE'] = json_encode($a);

		// すべて既読 
		$a = array();
		foreach ($this->m_aryItem as $aryArticle)
			$a[] = $aryArticle['LINK'];

		$aryRet['ALL'] = json_encode($a);

		//
		return $aryRet;
	}


	/**
	 * 経過時間文字列の作成
	 * @param  string 時刻
	 * @return string 経過時間
	 */
	public function strMakeTimeAgo($strTime)
	{
		$iTime = strtotime($strTime);
		$iTime = time() - $iTime;

		$iTime = (int)ceil($iTime / 60);
		if ($iTime <= 0)
			return '';
		if ($iTime < 60)
			return "{$iTime}分前";

		$iTime = (int)ceil($iTime / 60);
		if ($iTime < 24)
			return "{$iTime}時間前";

		$iTime = (int)ceil($iTime / 24);
		return "{$iTime}日前";
	}

	/**
	 * 既読情報の読み込み
	 * @param  
	 * @return 
	 * @return 
	 */
	public function aryLoadReaded()
	{
		$aryReaded = array();

		// 読み込み 
		$strFile = dirname(__FILE__) . "/../config/{$this->m_strUser}-rss.txt";
		if (file_exists($strFile)) {
			DLOG("load '{$strFile}'");
			$aryReaded = unserialize(file_get_contents($strFile));

//			if (count($aryReaded) > 1000)
//			{
////				//=== 7日以上経過した既読情報を削除 ==================
////				$iLimit = strtotime('-7days');
////				$strLimit = date('Y/m/d H:i:s', $iLimit);
////				foreach ($aryReaded as $k => $v)
////				{
////					if ($v['DATE'] < $strLimit)
////						unset($aryReaded[$k]);
////				}
//
//				//=== 古い順に削除 ===================================
//				uasort($aryReaded, array('CRss', 'iSort'));
//				$aryReaded = array_slice($aryReaded, 0, 1000, TRUE);
//
//				//====================================================
//				file_put_contents($strFile, serialize($aryReaded));
//			}

			// RSS単位で分解 
			$aryRss = array();
			foreach ($aryReaded as $strUrl => $a) {
				$strRss = $a['RSS'];
				if (empty($aryRss[$strRss]))
					$aryRss[$strRss] = array();

				$aryRss[$strRss][$strUrl] = $a;
			}

			// RSS単位で最大数を超えた分を削除 
			$blModified = FALSE;
			foreach ($aryRss as $strRss => $a) {
//				echo $strRss, ' = ', count($a), '<br />';
				if (count($a) > self::MAX_READED) {
					// 古い順に削除 
					uasort($a, array('CRss', 'iSort'));
					$aryRss[$strRss] = array_slice($a, 0, self::MAX_READED, TRUE);

					// 変更したフラグ 
					$blModified = TRUE;
				}
			}

			// 変更があったら保存 
			if ($blModified === TRUE) {
				// RSSごとの既読フラグを１つにまとめる 
				$aryReaded = array();
				foreach ($aryRss as $strRss => $a)
					$aryReaded = array_merge($aryReaded, $a);

				file_put_contents($strFile, serialize($aryReaded));
			}
		}

		//
		return $aryReaded;
	}

	/**
	 * 新しい順にソート
	 */
	public function iSort($a, $b)
	{
		return strtotime($b['DATE']) - strtotime($a['DATE']);
	}

	/**
	 * ヘッダーの出力
	 */
	public function EchoHead()
	{
?>
<head>
<style type="text/css">
<?php require 'rss.css'; ?>
</style>

<script type="text/javascript">
<?php require 'rss.js'; ?>
</script>
</head>
<?php
	}
}
