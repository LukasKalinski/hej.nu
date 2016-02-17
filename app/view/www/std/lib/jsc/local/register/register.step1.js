function uag_accept()
{
  if(confirm(LANG__user_agreement_accept_confirm))
    document.location.href = "_route.php?r="+nextRoute;
}