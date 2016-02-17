{*
  Some comment ...
*}
{*
{ignore}
Hello world!
{/ignore}
*}

{!$foo.bar.war[i]}
{$someInstance.key[i]}->get("someProp" + $var + #const, "string", + true)}
{!$name|mod:upper|parse:tpl|eval:once|export:newName}
{!$name|mod:upper|parse:tpl|eval:always|export:newName}


{*
Plugin functions:

{cycle:"foo","bar"} // function cycle([no named args])
{someFunc|eval:once argName1="some value" argName2="some value"}

Plugin functions:
{load_css|eval:once}
*}
