<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	HTML エディター
//
//	編集したHTMLは下記に保存してください
//	./config/{$this->m_strUser}.{$this->m_strID}.html
//
//	専用設定
//	'' => []
//
//====================================================================
//====================================================================
//====================================================================
function EchoContents( $aryConfig )
{
	if ( !empty( $aryConfig['html'] ) )
	{
		echo $aryConfig['html'];
	}
	else
	{
		//=== デフォルト =============================================
?>
ここにお好みのHTMLを貼り付けてください。
<?php
	}
}
?>