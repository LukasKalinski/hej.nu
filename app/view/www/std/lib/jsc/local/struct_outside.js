@{
@include "function.md5.js"
@include "lib.form.js"
@include "modifier.strfill.js"
@scramble_skip_name "sys_login"
@}

/**
 * bool login(object)
 */
function sys_login(f_elem)
{
  var F = new Form(f_elem);
  
  F.verify('username', 'this->value.match(constr_username_regex) || this->value.substr(1).match(constr_username_regex)', LANG__usr_fail, true);
  F.verify('password', 'this->value.match(constr_password_regex)', LANG__pwd_fail, true);
  
  // Encrypt password.
  if(F.verified)
  {
    F.ref.onsubmit = function() { return false; };
    F.ref.pwd_check.value = md5(pwdCheckKey + md5(F.ref.password.value));
    F.ref.password.value = strfill('', F.ref.password.value.length, '*');
  }
  
  return F.verified;
}

window.onload = function()
{
  fref = document.getElementById('frm_login');
  if(fref.username.value.length == 0) { fref.username.focus(); }
  else { fref.password.focus(); }
};