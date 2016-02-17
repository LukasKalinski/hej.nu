/**
 * Radio Input Module
 * Creates a radio button group which uses custom gif images instead of standard form radio inputs.
 *
 * @Example:
 *    var a = new Inp_Radio("form_id", "test");
 *    a.add("value 1", 0, "img_id");
 *
 * @css-requires:
 *    forms.css
 *
 * @param string Form       - Form (id) to create a hidden input in and send values to.
 * @param string radioName  - Name of the value holder (<input ... name="" />).
 */
function Inp_Radio(formId, radioName)
{
  this.form = document.getElementById(formId);
  this.defaultElem;
  
  this.lastChecked = null; // The last checked input (to reset on further clicks).
  this.valueHolder = document.createElement('input');
  this.valueHolder.id = radioName;
  this.valueHolder.name = radioName;
  this.valueHolder.type = 'hidden';
  this.valueHolder.value = '';
  this.form.appendChild(this.valueHolder);
  this.form.__rObj = this;
  this.form.onreset = function() { this.__rObj.defaultElem.__click(true); }; // The onreset is performed BEFORE the reset, keep that in mind.
  
  this.add = function(value, checked, imgId)
  {
    if(this.defaultElem != null) { checked = false; } // Make sure we don't add more than one default elements.
    
    var img = (imgId == null ? document.createElement('img') : document.getElementById(imgId));
    img.src = GFX_ROOT + 'radio0.gif';
    img.alt = '';
    img.className = 'inp_r';
    img.__rObj = this;
    img.__value = value;
    img.__checked = checked;
    if(checked) this.lastChecked = img;
    img.__set = function(b)
    {
      if(b) { this.__rObj.lastChecked = this; }
      this.src = GFX_ROOT + 'radio' + (b ? '1' : '0') + '.gif';
      this.__checked = b;
    };
    img.__click = function(forceClick)
    {
      if(!this.__checked || forceClick)
      {
        this.__rObj.valueHolder.value = this.__value;
        
        // Reset previous radio input if exists:
        if(this.__rObj.lastChecked != null)
          this.__rObj.lastChecked.__set(false);
        
        // Set current radio input:
        this.__set(true);
      }
    };
    img.onmousedown = function() { this.__click(false); };
    
    // Add labels if found:
    var label = null;
    if(imgId == null) { this.form.appendChild(img); } // Create image element if necessary.
    else if(label = document.getElementById(imgId + '_label'))
    {
      label.className = 'inp_r_label';
      label.onmousedown = function() { document.getElementById(imgId).__click(false); };
    }
    
    if(checked)
    {
      this.defaultElem = img; // Store default element for use during a possible form reset.
      this.valueHolder.defaultValue = value; // Make sure a form reset don't mess things up.
      img.__click(true); // Do not remove.
    }
  };
  
  return this;
}