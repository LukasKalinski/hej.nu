/* Requires: lib.browser.js */

function Event(ev)
{
  this.ref = (B.gecko ? ev : event);
	this.pageAreaX = this.ref.clientX; // Page X-coord (ignoring scroll-bar pos)
	this.pageAreaY = this.ref.clientY; // Page Y-coord (ignoring scroll-bar pos)
	this.objPosX = (B.ie || B.op7 ? this.ref.offsetX : this.ref.layerX); // Object X-coord | note: the object must be a layer in ns
	this.objPosY = (B.ie || B.op7 ? this.ref.offsetY : this.ref.layerY); // Object Y-coord | simply add a style="position:relative;" to the object.
	this.pageOffsetX = (B.ie ? this.ref.clientX + document.body.scrollLeft : this.ref.pageX);	// Page X-coord (taking scroll-bar pos into account)
	this.pageOffsetY = (B.ie ? this.ref.clientY + document.body.scrollTop  : this.ref.pageY);	// Page Y-coord (taking scroll-bar pos into account)
	return this;
}