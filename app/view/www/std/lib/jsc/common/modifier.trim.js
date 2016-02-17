function trim(str)
{
  return str.replace(/^\s*([^\s]+)\s*$/g, '$1');
}