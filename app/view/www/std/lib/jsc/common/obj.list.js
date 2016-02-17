function List()
{
  this.__objId = "List";
  this.list = new Array();
  this.i = -1;
  this.pointer = 0;
  
  this.push = function(v)
  {
    this.list[++this.i] = v;
  };
  
  this.importArray = function(A)
  {
    for(var i=0, ii=A.length; i<ii; i++)
      this.push(A[i]);
  };
  
  this.isEmpty = function()
  {
    return (this.i == -1);
  };
  
  this.clear = function()
  {
    this.list = new Array();
    this.i = -1;
  };
  
  this.getLast = function()
  {
    if(this.i != -1)
      return this.list[this.i];
    else
      return null;
  };
  
  this.hasNext = function() { return (this.pointer+1 < this.list.length); };
  this.next = function() { this.pointer = Math.min(this.list.length, this.pointer + 1); return this.current(); };
  this.current = function() { return this.list[this.pointer]; };
  this.reset = function() { this.pointer = 0; };
  
  this.pop = function()
  {
    if(this.i < 0) return false;
    var elem = this.getLast();
    var newList = new Array(this.i);
    for(var i=0, ii=this.list.length-1; i<ii; i++)
      newList[i] = this.list[i];
    this.list = newList;
    this.i--;
    return elem;
  };
  
  this.shift = function()
  {
    if(this.i < 0) return false;
    var elem = this.list[0];
    var newList = new Array(this.i);
    for(var i=0, ii=this.list.length; i<ii; i++)
      newList[i] = this.list[i+1];
    this.list = newList;
    this.i--;
    return elem;
  };
  
  return this;
}