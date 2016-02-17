function ObjClone(o)
{
  for(var i in o)
  {
    // Clone all sub-objects recursively.
    if(typeof(o[i]) == 'object')
      this[i] = new ObjClone(o[i]);
    else
      this[i] = o[i];
  }
  return this;
}