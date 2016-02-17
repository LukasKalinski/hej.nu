@{
@include "lib.rdm.js"
@}

add_onload_func(function(){parent.secNavSet('home','news');});

//window.location.href = '/test.xml.php?action=msgget';
function testfunc()
{
  var s = '';
  
  e = request.responseXML.getElementsByTagName('mId');
  for(var i=0; i<e.length; i++)
    s += e[i].childNodes[0].nodeValue + '\n';
  
  document.getElementById('test').innerHTML = s;
//  document.getElementById('test').innerHTML = request.responseXML.getElementsByTagName("name")[0].childNodes[0].nodeValue;
}