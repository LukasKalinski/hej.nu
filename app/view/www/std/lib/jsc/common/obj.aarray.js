/**
 * Object: Associative Array
 * Main purpose is to guarantee 100% safety with function names as 'pop' and so on, ie: a['pop'] is already defined when you create an Array.
 * Another purpose is to offer a compatible foreach-function (since the in-operator won't work in all browsers, although this is a bad argument...).
 */
function AArray()
{
  this.__objId = "AArray";
  this.prefix = 'aar__';
  this.AA = new Array();
  this.Ak = new Array();
  this.Av = new Array();
  this.A_idx = new Array();
  this.idx  = -1;
  this.idxPointer = -1;
  
  this.prefK = function(k) { return this.prefix + k; };
  
  /**
   * int importOverwrite(AArray)
   * Import another AArray overwriting elements when duplicated.
   */
  this.importOverwrite = function(src)
  {
    if(src != null && src.__objId == this.__objId)
    {
      var r;
      while((r = src.foreach("k","v")) !== false)
        this.set(r.k, r.v, true);
      return FUNC_OK;
    }
    else
    {
      return FUNC_FAIL;
    }
  };
  
  this.set = function(k, v, overwrite)
  {
    // Create new.
    if(!this.isset(k))
    {
      this.idx++;
      this.Ak[this.idx] = k;
      this.Av[this.idx] = v;
      this.A_idx[this.prefK(k)] = this.idx;
    }
    else if(overwrite === false)
    {
      return false;
    }
    // Update existing.
    else
    {
      this.Av[this.A_idx[this.prefK(k)]] = v;
    }
    
    this.AA[this.prefK(k)] = v;
    return true;
  };
  
  this.get = function(k)
  {
    return this.AA[this.prefK(k)];
  };
  
  this.isset = function(k)
  {
    return (typeof(this.AA[this.prefK(k)]) != 'undefined');
  };
  
  this.foreach = function(kVarNam,vVarNam)
  {
    this.idxPointer++;
    if(this.idxPointer <= this.idx)
    {
      var result = new Array();
      result[kVarNam] = this.Ak[this.idxPointer];
      result[vVarNam] = this.Av[this.idxPointer];
      return result;
    }
    else
    {
      this.idxPointer = -1;
      return false;
    }
  };
  
  return this;
}

// ## Import test
//var a1 = new AArray();
//var a2 = new AArray();
//a1.set('k1', 'val 1');
//a1.set('k2', 'val 2');
//a1.set('k3', 'val 3');
//a2.set('k4', 'val 4');
//a2.set('k5', 'val 5');
//a2.set('k6', 'val 6');
//a1.importOverwrite(a2);
//while((r = a1.foreach('k','v')) !== false)
//  alert(r.k+' => '+r.v);

// ## Foreach test
//var r, a = new AArray();
//a.set('k1', 'value 1');
//a.set('k2', 'value 2');
//a.set('k3', 'value 3');
//a.set('k4', 'value 4');
//while((r = a.foreach('k','v')) !== false)
//  alert(r.k+' => '+r.v);