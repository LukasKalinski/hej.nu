/**
 * @desc Takes get- or post-vars string (foo=bar&bar=foo ...) and returns them in an array (a[i].name=>a[i].value).
 */
function urlvars2array(str)
{
  var url_toks = str.split('&'), pair_toks;
  var result = new Array();
  for(var i=0; i<url_toks.length; i++)
  {
    pair_toks = url_toks[i].split('=');
    result[i] = {};
    result[i].name = pair_toks[0];
    result[i].value = pair_toks[1];
  }
  return result;
}