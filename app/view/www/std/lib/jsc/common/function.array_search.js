/**
 * @desc Searches for value in arr and returns true if found; false otherwise.
 * @param string value
 * @param array arr
 */
function array_search(value, arr)
{
  for(var i=0; i<arr.length; i++)
    if(arr[i] == value)
      return true;
  return false;
}