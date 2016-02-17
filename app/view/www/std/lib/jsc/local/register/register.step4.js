var __user_id = null;
function __cu_callback()
{
  if(__user_id != null)
  {
    alert(LANG__username_taken);
  }
  else
  {
    alert(LANG__username_available);
  }
}

function check_username()
{
  var F = new Form(document.getElementsByTagName("form")[0]);
  F.verify("username", "this->value.match(CONSTR__USERNAME_REGEX)", LANG__username_invalid);
  
  if(F.verified)
  {
    RDM_load("rdm.getuserbyname.php", "__cu_callback", "data_c=__user_id&username="+F.ref.username.value);
  }
}

function reg_save(form_obj)
{
  var F = new Form(form_obj);
  var y,m,d;
  with(F.ref)
  {
    y = dob_year.value;
    m = dob_month.value;
    d = dob_day.value;
  }
  
  F.verify("dob_year", "this->value > 0", LANG__dob_invalid);
  F.verify("dob_month", "this->value > 0", LANG__dob_invalid);
  F.verify("dob_day", "dateIsValid("+y+","+m+","+d+")", LANG__dob_invalid);
  F.verify("gender", "this->value.match(CONSTR__GENDER_REGEX)", LANG__gender_not_chosen);
  F.verify("username", "this->value.match(CONSTR__USERNAME_REGEX)", LANG__username_invalid);
  F.verify("password", "this->value.match(CONSTR__PASSWORD_REGEX)", LANG__password_invalid);
  F.verify("password_repeat", "this->value == this.ref.password.value", LANG__password_not_confirmed);
  
  return F.verified;
}

function reg_abort()
{
  if(confirm(LANG__confirm__reg_abort)) document.location.href = "_route.php?r="+routeAbort;
}