//====================================================================
// <iframe>�̍�������
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
			measuredH = 100; /* ��̂̕\������ */

		myF.height = measuredH + 10; /*  <iframe> ���̗]�� */
	}
	catch ( e )
	{
	}
}
