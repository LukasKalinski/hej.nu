function strfill(str, length, fillChar)
{
  var str = (typeof(str) == 'string' ? str.toString() : str);
  for(var i=0, ii=(length - str.length); i<ii; i++) { str = fillChar + str; }
  return str;
}