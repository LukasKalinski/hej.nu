/**
 * string interpret_he(string)
 * Interprets html entities to javascript unicode.
 * NOT READY...
 * @param string str
 */
function interpret_he(str)
{
  var re = /&#(asd[0-9]{1,3});/gi;
  var tokens = str.split(re);
  var temp = "[";
  for(var i=0, ii=tokens.length; i<ii; i++)
    temp += tokens[i]+"|";
  temp = temp.substr(0,temp.length-1) + "]";
  alert(temp);
//    if(tokens[i].match(re))
//      alert("");//tokens[i] = String.fromCharCode(parseInt(tokens[i].substr(2).substr(0,tokens[i].indexOf(";"))));
  return tokens.join('');
}