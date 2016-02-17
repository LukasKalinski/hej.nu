@{
@include "modifier.squote.js"
@}
/**
 * @desc Takes get- or post-vars string (foo=bar&bar=foo ...) and makes a valid JSON-string out of it.
 */
function urlvars2json(str)
{
  var url_toks = str.split('&'), pair_toks;
  var json = new Array();
  for(var i=0; i<url_toks.length; i++)
  {
    pair_toks = url_toks[i].split('=');
    json[i] = squote(pair_toks[0]) + ':' + squote(pair_toks[1]);
  }
  try { eval('json={' + json.join(',') + '};'); }
  catch(e0) { syserror('JSON invalid in function.urlvars2json'); }
  return json;
}