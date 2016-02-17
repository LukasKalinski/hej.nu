function json2array(raw_str)
{
  var obj;
  try { eval('obj=' + raw_str + ';'); }
  catch(e0) { syserror('function.json2array failed'); }
  
  // Export object to real object-array:
  var result = new Array();
  for(var index in obj)
    result[index] = obj[index];
  
  return result;
};