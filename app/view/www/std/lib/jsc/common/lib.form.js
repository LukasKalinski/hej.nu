var __FORM_SUBMITED = false;
var __FORM_FIELD_CLASSES = new Array();

/**
 * class Form(object)
 * Class for form handling.
 *
 * @param object/string form
 */
function Form(form)
{
  this.ref = (typeof(form) == 'string' ? d.getElementById(form) : form);
  this.verified = true;
  
  /**
   * void verify(string, string, string, bool)
   *
   * @param string elemName     # Name of the element to verify.
   * @param string condition    # The condition that evaluates to true on success.
   * @param string e_msg        # The error message to alert() on failure.
   * @param bool hlight         # Default=true, false will prevent element highlight.
   * @param bool focus          # Default=true, false will prevent element focus.
   */
  this.verify = function(elemName, condition, e_msg, hlight, focus)
  {
    if(this.verified)
  	{
  	  var re = new RegExp('this->', 'gi');
      condition = condition.replace(re, "this.ref." + elemName + '.');
      
  		if(!eval(condition))
  		{
  			if(focus !== false)  this.ref[elemName].focus();
  			if(hlight !== false) this.setFieldAppearance(elemName, true);
  			if(e_msg !== false)  alert(e_msg);
  			this.verified = false;
  		}
  		else
  		{
  		  if(hlight !== false) { this.setFieldAppearance(elemName, false); }
  		}
  	}
  };
  
  this.setFieldAppearance = function(elemName, hlight)
  {
    var failClass = 'fail';
    ref = this.ref[elemName];
    
    if(hlight)
    {
      if(typeof(__FORM_FIELD_CLASSES[elemName]) == 'undefined')
        __FORM_FIELD_CLASSES[elemName] = ref.className;
      
      if(ref.className.indexOf(' '+failClass) == -1)
        ref.className = __FORM_FIELD_CLASSES[elemName] + ' ' + failClass;
    }
    else if(typeof(__FORM_FIELD_CLASSES[elemName]) != 'undefined')
    {
      ref.className = __FORM_FIELD_CLASSES[elemName];
    }
  };
  
  /**
   * @desc Returns form data in url-vars format: name=value&name2=value2 ...
   */
  this.data2urlvars = function()
  {
    var r = this.ref; // shortcut
    var vars_str = '';
    for(var i=0; i<r.elements.length; i++)
      vars_str += r.elements[i].name + '=' + r.elements[i].value + '&';
    return vars_str.substr(0, vars_str.length-1);
  };
  
  /**
   * void submit()
   * Submits the form once.
   */
  this.submit = function()
  {
    if(!__FORM_SUBMITED && this.verified)
    {
      __FORM_SUBMITED = true;
      this.ref.submit();
    }
  };
  
  this.isFieldType = function(field, type)
  {
    return (this.ref[field].tagName.toUpperCase() == type.toUpperCase());
  };
  
  /**
   * void resetSubmit()
   */
  this.resetSubmitFlag = function()
  {
    __FORM_SUBMITED = false;
  };
  
  return this;
}