<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Ajax</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="/js/jquery-1.11.0.js"></script>
 
<script type="text/javascript">
$(document).ready(function(){
  $("#button").click(function(){
   
    var param = { "text": "email" };
 
    $.ajax({
      type: "post",
      url: "/ajax/ajax_test.php",
      data: JSON.stringify(param),
      crossDomain: false,
      dataType : "jsonp",
      scriptCharset: 'utf-8'
    }).done(function(data){
      alert(data.text);
    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
      alert(errorThrown);
    });
   });
});
</script> 
   
</head>
 
<body>
<input type="button" id="button" value="Hello" /></form>
</body>
</html>