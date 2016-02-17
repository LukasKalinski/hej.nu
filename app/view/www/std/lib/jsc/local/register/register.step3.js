function reg_continue(form_obj)
{
  var F = new Form(form_obj);
  
  F.verify("first_name", "this->value.length >= "+CONSTR__FIRST_NAME_LEN[0]+" && this->value.length <= "+CONSTR__FIRST_NAME_LEN[1], LANG__first_name_invalid);
  F.verify("last_name", "this->value.length >= "+CONSTR__LAST_NAME_LEN[0]+" && this->value.length <= "+CONSTR__LAST_NAME_LEN[1], LANG__last_name_invalid);
  F.verify("ssn", "this->value.match(/[0-9]{6}\\-?[0-9]{4}/)", LANG__ss_num_invalid);
  F.verify("email", "this->value.match(CONSTR__EMAIL_REGEX)", LANG__email_invalid);
  F.verify("address", "this->value.length >= "+CONSTR__ADDRESS_LEN[0]+" && this->value.length <= "+CONSTR__ADDRESS_LEN[1], LANG__address_invalid);
  F.verify("phone_number", "this->value.match(CONSTR__PHONE_NUMBER_REGEX)", LANG__phone_number_invalid);
  F.verify("phone_number", "this->value.length >= "+CONSTR__PHONE_NUMBER_LEN[0]+" && this->value.length <= "+CONSTR__PHONE_NUMBER_LEN[1], LANG__phone_number_invalid);
  F.verify("citizenship_id", "this->value > 0", LANG__citizenship_not_chosen);
  F.verify("post_code", "this->value.match(CONSTR__POST_CODE_REGEX)", LANG__post_code_invalid);
  F.verify("post_city", "this->value.length >= "+CONSTR__POST_CITY_LEN[0]+" && this->value.length <= "+CONSTR__POST_CITY_LEN[1], LANG__post_code_invalid);
  
  return (F.verified && (routePassed || confirm(LANG__private_data_authenticity)));
}

function reg_abort()
{
  if(confirm(LANG__confirm__reg_abort)) document.location.href = "_route.php?r="+routeAbort;
}