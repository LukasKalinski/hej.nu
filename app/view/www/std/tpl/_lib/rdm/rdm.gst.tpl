{eval@require_filter type="cf__row_prefilter" name="json"}
new Array(
{section id="ajax" src=$messages}
  {literal}{{/literal}
  'mId':'{$messages[ajax].id}',
  'wId':'{$messages[ajax].writer_id}',
  'tId':'{$messages[ajax].thread_id}',
  'username':'{$messages[ajax].username}',
  'genderage':'{$lang.txt.genderage gender=$messages[ajax].gender age=$messages[ajax].age}',
  'uphoto':'{userphoto uid=$messages[ajax].writer_id mode=$messages[ajax].photo_mode gender=$messages[ajax].gender}',
  'message':'{$messages[ajax].message}',
  'condition':{$messages[ajax].condition},
  'tstamp':'{$lang.txt.user__msg_post_date tstamp=nreq@$messages[ajax].tstamp}'
  {literal}}{/literal}
  {if $cte.section.ajax.index+1 != $cte.section.ajax.limit},{/if}
{/section}
)