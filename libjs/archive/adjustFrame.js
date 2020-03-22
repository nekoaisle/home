//====================================================================
// <iframe>の高さ調整
//====================================================================
function adjustFrame( myF )
{
	try
	{
		var myC = myF.contentWindow.document.documentElement;
		var measuredH = myC.offsetHeight;

		if ( document.all || isNaN(measuredH) || measuredH == 0 )
			measuredH = myC.scrollHeight;

		if ( isNaN(measuredH) || measuredH == 0 )
			measuredH = 100; /* 大体の表示高さ */

		myF.height = measuredH + 10; /*  <iframe> 内の余白 */
	}
	catch ( e )
	{
	}
}
