function Browser_Object()
{
	this.v = navigator.appVersion;
	this.a = navigator.userAgent;
	this.op  = (this.a.indexOf('Opera')    > -1 ? 1 : 0);
	this.op6 = (this.a.indexOf('Opera 6')  > -1 ? 1 : 0);
	this.op7 = (this.a.indexOf('Opera 7')  > -1 ? 1 : 0);
	this.ie  = (this.v.indexOf('MSIE')   > -1 && !this.op ? 1 : 0);
	this.ie5 = (this.v.indexOf('MSIE 5') > -1 && !this.op ? 1 : 0);
	this.ie6 = (this.v.indexOf('MSIE 6') > -1 && !this.op ? 1 : 0);
	this.gecko = (this.a.indexOf('Gecko') > -1 ? 1 : 0);
	this.ns6 = (this.ns && this.a.match(/Netscape.+6/gi) > -1 ? 1 : 0);
	this.ns7 = (this.ns && this.a.indexOf('Netscape 7')  > -1 ? 1 : 0);
	return this;
}
var B = new Browser_Object();