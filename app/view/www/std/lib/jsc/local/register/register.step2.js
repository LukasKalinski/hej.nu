@{
@include "lib.form.js"
@include "lib.rdm.js"
@}

function __c_obj(id,name)
{
  this.id = id;
  this.name = name;
}

function __callback(action)
{
  switch(action)
  {
    case 'getregionlist':
      s = document.getElementById('sel_region');
      break;
    
    case 'getcitylist':
      s = document.getElementById('sel_city');
      break;
    
    default: return;
  }
  
	s.options.length = 1;
  
	var i, ii;
	for(i=0, ii=__RDM_result.length; i<ii; i++)
		s.options[i+1] = new Option(__RDM_result[i].name, __RDM_result[i].id);
	s.value = 0;
  s.disabled = false;
}

function refreshForm(caller)
{
  var city = document.getElementById('sel_city');
  var region = document.getElementById('sel_city');
  var file = 'rdm.geo.php';
  var url_vars = 'data_c=__RDM_result&data_co=__c_obj';
  
  if(caller.value > 0)
  {
    switch(caller.id)
    {
      case 'sel_country':
        url_vars += '&action=getregionlist&country_id='+caller.value;
        city.value = 0;
        city.options.length = 1;
        city.disabled = true;
        region.value = 0;
        region.options.length = 1;
        break;
      
      case 'sel_region':
        url_vars += '&action=getcitylist&region_id='+caller.value;
        city.value = 0;
        city.options.length = 1;
        break;
      
      default: return;
    }
    RDM_load(file, '__callback', url_vars);
  }
}

function reg_continue(f_obj)
{
  var F = new Form(f_obj);
  
  F.verify('language_id', 'this->value != 0', LANG__language_fail);
  F.verify('country_id', 'this->value > 0', LANG__country_fail);
  F.verify('region_id', 'this->value > 0', LANG__region_fail);
  F.verify('city_id', 'this->value > 0', LANG__city_fail);
  
  return F.verified;
}

function reg_abort()
{
  if(confirm(LANG__confirm__reg_abort)) document.location.href = '_route.php?r='+routeAbort;
}