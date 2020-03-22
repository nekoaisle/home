<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	パズドラゲリラ時間帯取得
//
//	'group' => [表示するグループ名]
//
//====================================================================
//====================================================================
//====================================================================
require_once( dirname(__FILE__) . '/Widget.php' );

class CPuzdra extends CWidget
{
	//================================================================
	// コンストラクタ
	//================================================================
	public function __construct( $strUser, $aryConfig )
	{
		//=== デフォルトインプリメンテーションの呼び出し =============
		parent::__construct( $strUser, $aryConfig );
	}

	//================================================================
	// メンバー変数
	//================================================================
	public $m_strCacheFile = '';

	//================================================================
	//! 初期化
	//================================================================
	public function Init( )
	{
		$strDir = dirname( __FILE__);
		$this->m_strCacheFile = "{$strDir}/../config/puzdra.txt";
	}

	//================================================================
	//! ヘッダーの出力
	//================================================================
	public function EchoHead( )
	{
?>
<head>
<style type="text/css">
div.padding {
	padding: 8px;
}
table.schedule {
	width: 100%;
	font-size: 0.875em;
	background-color: #FFFFFF;
	border: 1px #CCCCCC solid;
	border-collapse: collapse;
}
table.schedule th {
	padding: 4px 8px;
	background-color: #EEEEEE;
	border: 1px #CCCCCC solid;
}
table.schedule td {
	padding: 4px 8px;
	border: 1px #CCCCCC solid;
}
div.date {
	font-size: 1.25em;
	margin-bottom: 4px;
}
</style>
</head>
<?php
	}

	//================================================================
	//! <body></body> の中身の出力
	//================================================================
	public function EchoBodyContents( )
	{
		//=== 本日のスケジュールを取得 ===============================
		$ary = $this->aryGetTodaysSchedule( );
		if ( $ary === NULL ) {
			ob_clean( );
			header( "Location: http://pad.zap.jp.net/" );
			die( );
?>
スケジュールの取得に失敗しました。<br/>
下記のページにてご確認ください。<br />
<a href="http://pad.zap.jp.net/">パズドラ ゲリラ 時間割</a>
<?php
			return;
		}
		$strDate = $ary[0];
		$aryTime = $ary[1];

		//=== 表示するグループを設定から取得 =========================
		if ( !empty( $this->m_aryConfig['group'] ) )
			$strGroup = strtoupper( mb_convert_kana( $this->m_aryConfig['group'], 'a' ) );
		else
			$strGroup = 'ABCDE';

		$a = str_split( $strGroup );
		$aryGroup = array_combine( $a, $a );

		//============================================================
		foreach ( $aryTime as $k => $a )
		{
			if ( empty( $aryGroup[$a['GROUP']] ) )
				unset( $aryTime[$k] );
		}
?>
<div class="padding">
<div class="date"><?php echo $strDate; ?> のゲリラダンジョン</div>
<table class="schedule">
<?php
	ob_start();
?>
<tr>
	<th>{$a['TIME']}</th>
	<td style="background-image: url('/img/{$a['BG']}');color:#{$a['COLOR']}">{$a['TITLE']}</td>
	<td>{$a['GROUP']} グループ</td>
</tr>
<?php
		$str = ob_get_clean();
		foreach ( $aryTime as $a )
		{
			echo eval( "return <<<_EOL_\n" . $str . "\n_EOL_;\n" );
		}
?>
</table>
</div><!--padding-->
<?php
	}

	//================================================================
	// 本日のスケジュールの取得
	//================================================================
	public function aryGetTodaysSchedule( )
	{
		$strToday = date( "ymd" );

		//=== キャッシュファイルの確認 ===============================
		if ( !file_exists( $this->m_strCacheFile ) ) {
			// キャッシュファイルが存在しない
			$arySchedule = array( );
		} else {
			// キャッシュファイルが存在する
			$arySchedule = unserialize( file_get_contents( $this->m_strCacheFile ) );
			if ( !empty( $arySchedule[$strToday] ) )
				return array( $strToday, $arySchedule[$strToday] );
		}

		//============================================================
		list( $strDate, $aryNewest ) = $this->aryGetNewestSchedule( );
		if ( !$strDate ) {
			return NULL;
		}

		if ( empty( $arySchedule[$strDate] ) )
		{
			//========================================================
			$arySchedule[$strDate] = $aryNewest;

			//=== 保存 ===============================================
			file_put_contents( $this->m_strCacheFile, serialize( $arySchedule ) );
		}

		//============================================================
		return array( $strDate, $arySchedule[$strDate] );
	}

	//================================================================
	// 最新のスケジュールの取得
	//================================================================
	function aryGetNewestSchedule( )
	{
		//============================================================
		$strDate = urlencode( date( 'Y年n月j日ゲリラダンジョン時間割' ) );
		$strURL  = "http://xn--0ck4aw2hv46qq3yavh3e.com/{$strDate}/";

		$strHTML = @file_get_contents( $strURL );
		if ( !$strHTML ) {
			$strHTML = '';
		}

		//=== 表を取得 ===============================================
/*
<table class="guerrilla" style="width:100%;">
<tbody>
<tr>
<th class="style1">DAY</th>
<th class="style1">A</th>
<th class="style1">B</th>
<th class="style1">C</th>
<th class="style1">D</th>
<th class="style1">E</th>
</tr>
<tr>
<td rowspan="2" style="font-size:0.8em;">4/15</td>
<td class="style5">10</td>
<td class="style5">11</td>
<td class="style5">12</td>
<td class="style5">13</td>
<td class="style5">9</td>
</tr>
<tr>
<td class="style10">23</td>
<td class="style10">20</td>
<td class="style10">18:30</td>
<td class="style10">21:30</td>
<td class="style10">17</td>
</tr>
</tbody>
</table>
*/
		$strRE = '/<table class="guerrilla" style="width:100%;">(.*?)<\/table>/ius';
		if ( preg_match( $strRE, $strHTML, $m ) !== 1 )
			return NULL;

		$strHTML = $m[1];

		//=== 日付を取得 =============================================
		// <td rowspan="2" style="font-size:0.8em;">4/15</td>
		$strRE = '/<td rowspan="[0-9]+?" style="font-size:0\.8em;">([0-9]+?)\/([0-9]+?)<\/td>/ius';
		if ( preg_match( $strRE, $strHTML, $m ) !== 1 )
			return NULL;

		$strDate = sprintf( '%02d/%02d', (int)$m[1], (int)$m[2] );

		//=== 行を取得 ===============================================
		$strRE = '/<tr>(.*?)<\/tr>/ius';
		if ( preg_match_all( $strRE, $strHTML, $aryTR ) <= 0 )
			return NULL;

		//=== 変換テーブル ===========================================
		$aryNo2Group = array( 'A', 'B', 'C', 'D', 'E' );
		$aryType  = array( 
			  'class="style1"'              => array( 'PR' =>  0, 'BG' => 'pd_meta.png'   , 'COLOR' => '333333', 'TITLE' => '【緊急】メタドラ大発生！' )
			, 'class="style2"'              => array( 'PR' =>  1, 'BG' => 'pd_gol.png'    , 'COLOR' => '333333', 'TITLE' => 'ゴルドラダンジョン'       )
			, 'class="style3"'              => array( 'PR' =>  2, 'BG' => 'pd_rub.png'    , 'COLOR' => '333333', 'TITLE' => 'ルビドラダンジョン'       )
			, 'class="style4"'              => array( 'PR' =>  3, 'BG' => 'pd_sap.png'    , 'COLOR' => '333333', 'TITLE' => 'サファドラダンジョン'     )
			, 'class="style5"'              => array( 'PR' =>  4, 'BG' => 'pd_eme.png'    , 'COLOR' => '333333', 'TITLE' => 'エメドラダンジョン'       )
			, 'class="style6"'              => array( 'PR' =>  5, 'BG' => 'pd_smeta.png'  , 'COLOR' => 'EEEEEE', 'TITLE' => '超メタドラ 降臨！'        )
			, 'class="style8"'              => array( 'PR' =>  6, 'BG' => 'pd_srub.png'   , 'COLOR' => 'EEEEEE', 'TITLE' => '超ルビドラ 降臨！'        )
			, 'class="style9"'              => array( 'PR' =>  7, 'BG' => 'pd_ssap.png'   , 'COLOR' => 'EEEEEE', 'TITLE' => '超サファドラ 降臨！'      )
			, 'class="style10"'             => array( 'PR' =>  8, 'BG' => 'pd_seme.png'   , 'COLOR' => 'EEEEEE', 'TITLE' => '超エメドラ 降臨！'        )
			, 'class="style11"'             => array( 'PR' =>  9, 'BG' => 'pd_sgol.png'   , 'COLOR' => 'EEEEEE', 'TITLE' => '超ゴルドラ 降臨！'        )
			, 'class="style7"'              => array( 'PR' => 10, 'BG' => 'pd_pen.png'    , 'COLOR' => '333333', 'TITLE' => 'ペンドラの里'             )
			, 'class="style12"'             => array( 'PR' => 11, 'BG' => 'pd_dra.png'    , 'COLOR' => '333333', 'TITLE' => '【緊急】ドラプラ大発生！' )
			, 'class="style13"'             => array( 'PR' => 12, 'BG' => 'pd_king.png'   , 'COLOR' => '333333', 'TITLE' => 'キングカーニバル'         )
			, 'class="style14"'             => array( 'PR' => 13, 'BG' => 'pd_metagol.png', 'COLOR' => '333333', 'TITLE' => 'メタゴルダンジョン'       )
			, 'class="style15"'             => array( 'PR' => 14, 'BG' => 'pd_rush.png'   , 'COLOR' => '333333', 'TITLE' => '集結！進化ラッシュ！！'   )
			, 'background:#ffa12f;' => array( 'PR' => 15, 'BG' => 'pd_king.png'   , 'COLOR' => '333333', 'TITLE' => 'キングカーニバル'         )
			, 'background:#0084ff;' => array( 'PR' => 16, 'BG' => 'pd_shou.png'   , 'COLOR' => 'EEEEEE', 'TITLE' => '星宝の遺跡'               )
			, 'unknown'                     => array( 'PR' => 99, 'BG' => 'pd_unknown.png', 'COLOR' => 'FF0000', 'TITLE' => '不明'                     )
		);

		//=== グループと時間を取得 ===================================
		// $aryTime = array( [時刻] => [ダンジョン名] );
		$aryTime = array( );
		foreach ( $aryTR[1] as $strTD )
		{
			$strRE = '/<td .*?(class="style[0-9]+?"|background:#[0-9a-f]+?;).*?>(.*?)<\/td>/ius';
			if ( preg_match_all( $strRE, $strTD, $aryTD, PREG_SET_ORDER ) <= 0 )
				continue;

			foreach ( $aryTD as $iGroup => $m )
			{
				// 列がグループを表す
				$strGroup = $aryNo2Group[$iGroup];

				// 時刻は改行(<br />)区切りで複数存在する
				$s = $m[2];
				$s = preg_replace( '/<br\s+\/>/ius', "\n", $s );
				$s = strip_tags( $s );		// 不要なタグを除去
				$a = explode( "\n", $s );	// 改行で分解

				foreach ( $a as $strTime )
				{
					// 00分の時は分はが省略されている
					if ( strpos( $strTime, ':' ) === FALSE )
						$strTime = "{$strTime}:00";

					// 時が1桁の時のための処理
					$strTime = substr( "0{$strTime}", -5 );

					// スタイルからスペシャルダンジョンを識別
					if ( isset( $aryType[$m[1]] ) )
						$ary = $aryType[$m[1]];
					else
						$ary = $aryType['unknown'];

					// グループ名と時刻を追加して記憶
					$ary['GROUP'] = $strGroup;
					$ary['TIME' ] = $strTime;
					$aryTime[] = $ary;
				}
			}
		}

		//=== キー(時刻)でソート =====================================
		usort( $aryTime, array( 'CPuzdra', 'sort_time' ) );

		//============================================================
		return array( $strDate, $aryTime );
	}

	//================================================================
	//! 早い順にソート
	//================================================================
	public function sort_time( $a, $b )
	{
		//=== 時刻が違う場合は早い順 =================================
		if ( $a['TIME'] < $b['TIME'] )
			return -1;
		else if ( $a['TIME'] > $b['TIME'] )
			return 1;

		//=== 時刻が同じ場合はPRIORITY順 =============================
		if ( $a['PR'] < $b['PR'] )
			return -1;
		else if ( $a['PR'] > $b['PR'] )
			return 1;
		else
			return 0;
	}


}
?>