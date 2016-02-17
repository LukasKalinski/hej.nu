/**
 * bool dateIsValid(int, int, int)
 *
 */
function dateIsValid(y,m,d)
{
  if(y <= 0 || m <= 0 || d <= 0) return false;
  
  var isLeap = (y % 4 == 0 ? true : false);
  var daysMonths = new Array(31,(isLeap ? 29 : 28),31,30,31,30,31,31,30,31,30,31);
  
  var D = new Date();
  if(y > (D.getYear()+1900)) return false;
  if(m > 12) return false;
  if(d > daysMonths[m-1]) return false;
  
  return true;
}