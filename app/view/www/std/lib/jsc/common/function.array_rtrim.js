function array_rtrim(arr, by)
{
  by = typeof(by) == "undefined" ? 1 : by;
  var newArr = new Array();
  for(var i=arr.length-by, ii=0; i>ii; i--)
    newArr[i-1] = arr[i-1];
  return newArr;
}