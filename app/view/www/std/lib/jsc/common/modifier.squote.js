/**
 * @desc Single quotes a string for valid use in javascript-context.
 */
function squote(s)
{
  return '\'' + s.replace(/([\']+)/g, '\\$1').replace(/\n/g, '\\n') + '\'';
}