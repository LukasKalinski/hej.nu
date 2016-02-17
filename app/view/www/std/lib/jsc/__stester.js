/**
 * Javascript Scrambler testing file
 */


// Object:
function myObj()
{
  this.fooVar = 'value';
  this.fooFunc = function()
  {
    alert(this.fooVar);
  };
  return this;
}
var foo = new myObj();
var bar = new myObj();
foo.fooFunc();
bar.fooFunc();

// Simple functions:
function bar()
{
  var a;
  a = 12;
  alert(a);
}
foo();

function barFunc(arg)
{
  var a, b, c="bar", d=1;
  alert(arg);
}
fooFunc("Hello!");