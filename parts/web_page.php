<?php
//====================================================================
//====================================================================
//====================================================================
//
// DS-Portal 用 Widget
//
//	指定ウェッブページを開きます
//
//	'url' => [開くページのURL]
//
//====================================================================
//====================================================================
//====================================================================

function EchoContents( $aryConfig )
{
	throw new CKyaExceptionRedirect( $aryConfig['url'] );
}
?>