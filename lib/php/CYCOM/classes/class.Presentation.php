<?
/**
 * Class: Presentation
 *
 * Author: Lukas Kalinski @ CyLab.se
*/

require_once("/home/h/hej/_include/modifiers.php");
require_once("/home/h/hej/_include/gui/gui.globals.php");
require_once("/home/h/hej/_include/gui/gui.system.php");
require_once("/home/h/hej/_include/system/system.conditions.php");

class Presentation
{
  var $DB;
	var $VALID_URL;			// Valid chars in URL (REGEXP) - string
	var $TAGS;					// Tags to progress, to disable a tag just remove it from here - array()
	var $VALID_TAG;			// Tags supported on the presentation (REGEXP) - array(array())
	
	var $OPEN_TAGS;			// Store open tags and reset them if no end-tag is found - array()
	var $STANDALONE_TAG_OPEN; // Some tags must be closed before using other tags - bool
	
	var $BODY_BGCOLOR;	// Presentation background color - string
	var $BODY_HEIGHT;		// Presentation height - int
	var $LINK_NORMAL;		// Link normal - array()
	var $LINK_HOVER;		// Link hover - array()
	
  function Presentation($mysql_link=FALSE)
  {
    $this->DB = $mysql_link;
  }
  
	# Name: P_setup_body();
	# 
	# 
	function P_setup_body($bodyColor, $bodyHeight)
	{
		if(eregi('('.REGEXP__COLOR.')', $bodyColor) && $bodyHeight >= MIN__PRESENTATION_HEIGHT && $bodyHeight <= MAX__PRESENTATION_HEIGHT)
		{
			$this->BODY_BGCOLOR = $bodyColor;
			$this->PRESENTATION_HEIGHT = $bodyHeight;
		}
		else
    {
      $this->BODY_BGCOLOR = '#'.COLOR__PRESENTATION_DEFAULT;
		  $this->PRESENTATION_HEIGHT = MIN__PRESENTATION_HEIGHT;
    }
	}
  
	# Name: P_setup_links();
	# 
	# 
	function P_setup_links($nColor, $nStyle, $nDec, $nWeight, $nBgColor, $hColor, $hStyle, $hDec, $hWeight, $hBgColor)
	{
    function addProp($name, $value) { return $name.":".$value.";"; }
    
    $this->LINK_NORMAL = "";
		if(eregi('('.REGEXP__COLOR.')+', $nColor))                { $this->LINK_NORMAL .= addProp("color",           $nColor);       }
    else                                                      { $this->LINK_NORMAL .= addProp("color",           "#281100");     }
    if(eregi('italic|normal', $nStyle))                       { $this->LINK_NORMAL .= addProp("font-style",      $nStyle);       }
    else                                                      { $this->LINK_NORMAL .= addProp("font-style",      "normal");      }
    if(eregi('underline|overline|line-through|none', $nDec))  { $this->LINK_NORMAL .= addProp("text-decoration", $nDec);         }
    else                                                      { $this->LINK_NORMAL .= addProp("text-decoration", "none");        }
    if(eregi('500|700', $nWeight))                            { $this->LINK_NORMAL .= addProp("font-weight",     $nWeight);      }
    else                                                      { $this->LINK_NORMAL .= addProp("font-weight",     "700");         }
    if(eregi('('.REGEXP__COLOR.')+', $nBgColor))              { $this->LINK_NORMAL .= addProp("background",      $nBgColor);     }
    else                                                      { $this->LINK_NORMAL .= addProp("background",      "transparent"); }
    
    $this->LINK_HOVER = "";
    if(eregi('('.REGEXP__COLOR.')+', $hColor))                { $this->LINK_HOVER .= addProp("color",           $hColor);       }
    else                                                      { $this->LINK_HOVER .= addProp("color",           "#8C3F0F");     }
    if(eregi('italic|normal', $hStyle))                       { $this->LINK_HOVER .= addProp("font-style",      $hStyle);       }
    else                                                      { $this->LINK_HOVER .= addProp("font-style",      "normal");      }
    if(eregi('underline|overline|line-through|none', $hDec))  { $this->LINK_HOVER .= addProp("text-decoration", $hDec);         }
    else                                                      { $this->LINK_HOVER .= addProp("text-decoration", "none");        }
    if(eregi('500|700', $hWeight))                            { $this->LINK_HOVER .= addProp("font-weight",     $hWeight);      }
    else                                                      { $this->LINK_HOVER .= addProp("font-weight",     "700");         }
    if(eregi('('.REGEXP__COLOR.')+', $hBgColor))              { $this->LINK_HOVER .= addProp("background",      $hBgColor);     }
    else                                                      { $this->LINK_HOVER .= addProp("background",      "transparent"); }
	}
	
	# Name: P_open_tag(); & P_close_tag();
	# Purpose: Store all open tags and their $pHTML list-ID.
	# If the tag remains open it will be reset to plain text. P_close_tag() is used to unset an open tag.
	function P_open_tag($tagName, $loop_id, $decompiled)
	{
		$this->OPEN_TAGS[$tagName] = array("id" => $loop_id, "decompiled" => $decompiled);
	}
	function P_close_tag($tagName) { $this->OPEN_TAGS[$tagName] = FALSE; }
	
	# Name: P_setup_compilator();
	# Purpose: Set all variables needed for the presentation compilator
	# 
	function P_setup_compilator()
	{
		$this->VALID_URL		=	"[^<>\"]*";
		$this->OPEN_TAGS = array();
		$this->STANDALONE_TAG_OPEN = FALSE;
		if(is_PLUS())
      $this->TAGS = array('A','USER','PHOTO','HR','FONT','BOX','SWITCH','BR','B','I','U','BIG','S','CENTER','RIGHT');
    else
      $this->TAGS = array('A','USER','PHOTO','HR','FONT','BOX','BR','B','I','U','BIG','S','CENTER','RIGHT');
    
		$this->VALID_TAG = array
    (
		'BR' => array
          (
          'RE_startTag' => '<BR>',
					'tagProps'    => FALSE,
					'RE_endTag'   => FALSE,
					'startTag'    => '<BR>',
					'endTag'      => FALSE,
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'B' => array
          (
          'RE_startTag' => '<B>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</B>',
					'startTag'    => '<B>',
					'endTag'      => '</B>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'I' => array
          (
          'RE_startTag' => '<I>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</I>',
					'startTag'    => '<I>',
					'endTag'      => '</I>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'U' => array
          (
          'RE_startTag' => '<U>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</U>',
					'startTag'    => '<U>',
					'endTag'      => '</U>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'S' => array
          (
          'RE_startTag' => '<S>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</S>',
					'startTag'    => '<S>',
					'endTag'      => '</S>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'BIG' => array
          (
          'RE_startTag' => '<BIG>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</BIG>',
					'startTag'    => '<BIG>',
					'endTag'      => '</BIG>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'CENTER' => array
          (
          'RE_startTag' => '<CENTER>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</CENTER>',
					'startTag'    => '<DIV align="center">',
					'endTag'      => '</DIV>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'RIGHT' => array
          (
          'RE_startTag' => '<RIGHT>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</RIGHT>',
					'startTag'    => '<DIV align="right">',
					'endTag'      => '</DIV>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'FONT' => array
          (
          'RE_startTag' => '<FONT.*>',
					'tagProps'    => array(0 => array('RE' => ' color="('.REGEXP__COLOR.')"', 'prop' => '[p]', 'propDefault' => '')),
					'RE_endTag'   => '</FONT>',
					'startTag'    => '<FONT color="[0]">',
					'endTag'      => '</FONT>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'A' => array
          (
          'RE_startTag' => '<A.*>',
					'tagProps'    => array
          (
0 => array('RE' => ' HREF="((http://){1}('.$this->VALID_URL.'))"', 'prop' => '[p]', 'propDefault' => '')
					),
					'RE_endTag'   => '</A>',
					'startTag'    => '<A HREF="[0]" target="_new" class="uDefLink">',
					'endTag'      => '</A>',
					'tagOpen'     => FALSE,
					'standAlone'  => TRUE,
					'extras'      => '$temp[\'startTag\'] = str_replace("'.
                           str_replace("http://", "", WWW_ROOT).'", "", $temp[\'startTag\']);'
					),
    'USER' => array
          (
          'RE_startTag' => '<USER>',
					'tagProps'    => FALSE,
					'RE_endTag'   => '</USER>',
					'startTag'    => '<A HREF="<USER_SEARCH><USERNAME>" target="HEJ_main" class="uDefLink">',
					'endTag'      => '</A>',
					'tagOpen'     => FALSE,
					'standAlone'  => TRUE,
					'extras'      => FALSE
					),
    'PHOTO' => array
          (
          'RE_startTag' => '<PHOTO.*>',
					'tagProps'    => array
          (
0 => array('RE' => ' user="([0-9a-z_]{2,16})"',           'prop' => '[p]',                 'propDefault' => ''),
1 => array('RE' => ' size="(small)"',                     'prop' => 'S',                   'propDefault' => 'M'),
2 => array('RE' => ' bordercolor="('.REGEXP__COLOR.')"',  'prop' => 'border-color:[p];',   'propDefault' => 'border-color:#000000;'),
3 => array('RE' => ' borderwidth="([0-9]{1})"',           'prop' => 'border-width:[p]px;', 'propDefault' => 'border-width:1px;'),
4 => array('RE' => ' borderstyle="('.REGEXP__BORDER.')"', 'prop' => 'border-style:[p];',   'propDefault' => 'border-style:solid;'),
5 => array('RE' => ' alt="([^<>"]{1,30})"',               'prop' => '[p]',                 'propDefault' => ''),
6 => array('RE' => ' top="([0-9]{1,4})"',                 'prop' => 'top:[p]px;',          'propDefault' => ''),
7 => array('RE' => ' left="([0-9]{1,3})"',                'prop' => 'left:[p]px;',         'propDefault' => ''),
8 => array('RE' => ' position="(relative|absolute)"',     'prop' => 'position:[p];',       'propDefault' => ''),
9 => array('RE' => ' display="(inline|block)"',           'prop' => 'display:[p];',        'propDefault' => '')
					),
					'RE_endTag'   => FALSE,
					'startTag'    => '<div style="[8][9][7][6]" title="[5]">|<PHOTO>[0]:[1]:[2][3][4]</PHOTO>|</div>',
					'endTag'      => FALSE,
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
    'HR' => array
          (
          'RE_startTag' => '<HR.*>',
					'tagProps'    => array
          (
0 => array('RE' => ' color="('.REGEXP__COLOR.')"',    'prop' => 'background:[p];', 'propDefault' => 'background:#000000;'),
1 => array('RE' => ' width="([0-9]{1,3})"',           'prop' => 'width:[p]px;',    'propDefault' => 'width:100%;'),
2 => array('RE' => ' height="([0-9]{1,3})"',          'prop' => 'height:[p]px;',   'propDefault' => 'height:1px;'),
3 => array('RE' => ' top="([0-9]{1,2})"',             'prop' => 'top:[p]px;',      'propDefault' => 'top:8px;'),
4 => array('RE' => ' left="([0-9]{1,3})"',            'prop' => 'left:[p]px;',     'propDefault' => 'left:8px;'),
5 => array('RE' => ' position="(relative|absolute)"', 'prop' => 'position:[p];',   'propDefault' => 'position:static;'),
6 => array('RE' => ' width="([0-9]{1,3}%)"',          'prop' => 'width:[p];',      'propDefault' => '')
					),
					'RE_endTag'   => FALSE,
					'startTag'    => '<table cellspacing="0" cellpadding="0" border="0" style="[5][0][3][4][1][6]"><tr><td style="[2]">'.
                           '</td></tr></table>',
					'endTag'      => FALSE,
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
		'BOX' => array
          (
          'RE_startTag' => '<BOX.*>',
					'tagProps'    => array
          (
0 =>  array('RE' => ' bgcolor="('.REGEXP__COLOR.')"',     'prop' => 'background:[p];',     'propDefault' => ''),
1 =>  array('RE' => ' bordercolor="('.REGEXP__COLOR.')"', 'prop' => 'border-color:[p];',   'propDefault' => 'border-color:#000000;'),
2 =>  array('RE' => ' borderwidth="([0-9]{1})"',          'prop' => 'border-width:[p]px;', 'propDefault' => 'border-width:1px;'),
3 =>  array('RE' => ' borderstyle="('.REGEXP__BORDER.')"','prop' => 'border-style:[p];',   'propDefault' => 'border-style:solid;'),
4 =>  array('RE' => ' width="([0-9]{1,3})"',              'prop' => 'width:[p]px;',        'propDefault' => ''),
5 =>  array('RE' => ' height="([0-9]{1,4})"',             'prop' => 'height:[p]px;',       'propDefault' => ''),
6 =>  array('RE' => ' top="([0-9]{1,4})"',                'prop' => 'top:[p]px;',          'propDefault' => ''),
7 =>  array('RE' => ' left="([0-9]{1,3})"',               'prop' => 'left:[p]px;',         'propDefault' => ''),
8 =>  array('RE' => ' padding="([0-9]{1})"',              'prop' => 'padding:[p]px',       'propDefault' => 'padding:5px;'),
9 =>  array('RE' => ' zindex="([0-9]{1,2})"',             'prop' => 'z-index:[p];',        'propDefault' => ''),
10 => array('RE' => ' align="(left|center|right)"',       'prop' => ' align="[p]"',        'propDefault' => ''),
11 => array('RE' => ' position="(relative|absolute)"',    'prop' => 'position:[p];',       'propDefault' => 'position:static;'),
12 => array('RE' => ' width="([0-9]{1,3}%)"',             'prop' => 'width:[p];',          'propDefault' => ''),
13 => array('RE' => ' height="([0-9]{1,3}%)"',            'prop' => 'height:[p];',         'propDefault' => ''),
14 => array('RE' => ' id="([a-z0-9]{1,20})"',             'prop' => ' id="uDef[p]"',       'propDefault' => ''),
15 => array('RE' => ' visibility="(visible|hidden)"',     'prop' => 'visibility:[p];',     'propDefault' => 'visibility:visible;'),
16 => array('RE' => ' display="(inline|block)"',          'prop' => 'display:[p];',        'propDefault' => '')
					),
					'RE_endTag'   => '</BOX>',
					'startTag'    => '<div[14] style="[11][15][16][0][4][5][6][7][9][12][13]">'.
                           '<div class="text"[10] style="[1][2][3][8]">',
					'endTag'      => '</div></div>',
					'tagOpen'     => FALSE,
					'standAlone'  => FALSE,
					'extras'      => FALSE
					),
    'SWITCH' => array
          (
          'RE_startTag' => '<SWITCH.*>',
					'tagProps'    => array
          (
0 => array('RE' => ' show="([a-z0-9]*|_all_)"',   'prop' => "\'[p]\'", 'propDefault' => 'false'),
1 => array('RE' => ' hide="([a-z0-9]*|_all_)"',   'prop' => "\'[p]\'", 'propDefault' => 'false'),
2 => array('RE' => ' toggle="([a-z0-9]*|_all_)"', 'prop' => "\'[p]\'", 'propDefault' => 'false')
					),
					'RE_endTag'   => '</SWITCH>',
					'startTag'    => '<A href="javascript:box([0],[1],[2]);" class="uDefLink">',
					'endTag'      => '</A>',
					'tagOpen'     => FALSE,
					'standAlone'  => TRUE,
					'extras'      => FALSE
					)
		);
	}
	
	# Name: P_compile();
	# 
	# 
	function P_compile($pHTML, $pTEXT)
	{
		$pTEXT = explode("|", $pTEXT);
		$pHTML = explode("|", str_replace("\\\"","\"",$pHTML));
		
		// Tag-translation
		for($i=0; $i<count($pHTML); $i++)
		{
			$tag_validated = FALSE;
			// Loop all allowed tags
			for($j=0; $j<count($this->TAGS); $j++)
			{
				// If tag isn't already open and matched the start-tag REGEXP
				if(!$this->VALID_TAG[$this->TAGS[$j]]['tagOpen'] && 
					 eregi($this->VALID_TAG[$this->TAGS[$j]]['RE_startTag'], $pHTML[$i]) && 
					 $this->STANDALONE_TAG_OPEN === FALSE)
				{
					// Store start-tag in temp-var
					$temp['startTag'] = $this->VALID_TAG[$this->TAGS[$j]]['startTag'];
					
					// PROPERTIES :: If there are any TAG-properties
					if(is_array($this->VALID_TAG[$this->TAGS[$j]]['tagProps']))
					{
						// Loop all tag properties
						for($k=0; $k<count($this->VALID_TAG[$this->TAGS[$j]]['tagProps']); $k++)
						{
							// If property is found in string it will be stored in the tag
							if(!empty($this->VALID_TAG[$this->TAGS[$j]]['tagProps'][$k]['RE']) && 
								 eregi($this->VALID_TAG[$this->TAGS[$j]]['tagProps'][$k]['RE'], $pHTML[$i], $regs))
							{
								$temp['startTag'] = str_replace("[".$k."]",
                                                str_replace('[p]', $regs[1], $this->VALID_TAG[$this->TAGS[$j]]['tagProps'][$k]['prop']),
                                                $temp['startTag']);
								$tag_validated = TRUE;
								continue;
							}
							else
							{
								$temp['startTag'] = str_replace("[".$k."]",
                                                $this->VALID_TAG[$this->TAGS[$j]]['tagProps'][$k]['propDefault'],
                                                $temp['startTag']);
								$tag_validated = TRUE;
								continue;
							}
						}
					}
					
					// In the extras-key you can put some custom actions to perform for that tag
					if($this->VALID_TAG[$this->TAGS[$j]]['extras'] !== FALSE)
					{
						eval($this->VALID_TAG[$this->TAGS[$j]]['extras']);
					}
					
					// Check if tag is standAlone
					if($this->VALID_TAG[$this->TAGS[$j]]['standAlone'])
					{
						$this->STANDALONE_TAG_OPEN = $this->TAGS[$j];
					}
					
					// If the tag requires a end-tag; set the tagOpen to TRUE and open tag
					if($this->VALID_TAG[$this->TAGS[$j]]['endTag'] !== FALSE)
					{
						$this->VALID_TAG[$this->TAGS[$j]]['tagOpen'] = TRUE;
						$this->P_open_tag($this->TAGS[$j], $i, $pHTML[$i]);
					}
					
					$pHTML[$i] = $temp['startTag'];
					
					$tag_validated = TRUE;
					unset($temp['startTag']);
					break;
				}
				// If tag isn't already closed, requires an end-tag and matched the end-tag REGEXP
				elseif($this->VALID_TAG[$this->TAGS[$j]]['tagOpen'] && 
							 $this->VALID_TAG[$this->TAGS[$j]]['RE_endTag'] && 
							 eregi($this->VALID_TAG[$this->TAGS[$j]]['RE_endTag'], $pHTML[$i]))
				{
					// Check if tag is standAlone
					if($this->STANDALONE_TAG_OPEN == $this->TAGS[$j])
					{
						$this->STANDALONE_TAG_OPEN = FALSE;
					}
					
					if(!$this->STANDALONE_TAG_OPEN)
					{
						$pHTML[$i] = $this->VALID_TAG[$this->TAGS[$j]]['endTag'];
						$this->P_close_tag($this->TAGS[$j]);
						$this->VALID_TAG[$this->TAGS[$j]]['tagOpen'] = FALSE;
						$tag_validated = TRUE;
					}
					else { $tag_validated = FALSE; }
					break;
				}
			}
			# Secure tag if not supported
			if(!$tag_validated)
			{
				$pHTML[$i] = encode_html_entities($pHTML[$i]);
			}
		}
		
		# Disable invalid tags (missing end-tags)
		foreach($this->OPEN_TAGS as $tag)
		{
			if(is_array($tag)) { $pHTML[$tag["id"]] = encode_html_entities($tag["decompiled"]); }
		}
		
		# Join tags and text
		$pCompiled = "";
		for($i=0; $i<max(count($pHTML), count($pTEXT)); $i++) { $pCompiled .= $pHTML[$i].encode_html_entities($pTEXT[$i]); }
		
    # Search and replace <PHOTO>username|photosize(1|2)</PHOTO>
    if($this->DB !== FALSE)
    {
      $pCompiled = explode('|',$pCompiled);
      for($i=0; $i<count($pCompiled); $i++)
      {
        if(eregi("<PHOTO>([a-z0-9_]{2,16}):(S|M):([^<>]*)</PHOTO>",$pCompiled[$i],$regs))
        {
          $user = $this->DB->DB_runQuery("SELECT user_GUID,photo FROM |users| WHERE username='".$regs[1]."' LIMIT 1",TRUE,TRUE);
          $pCompiled[$i] = makeUserPhoto($user['photo'], $user['user_GUID'], $regs[2], $photoLink='PREVIEW', FALSE, 'style="'.$regs[3].'"');
        }
      }
      $pCompiled = implode($pCompiled);
    }
    
    # Set <USER> tags
    $pCompiled = eregi_replace("<USERNAME>([^>]*>)([^<]*)","\\2\\1\\2",$pCompiled);
    
		# Final setup (background, styles, etc)
		$pCompiled = '<style type="text/css">'.
		'a.uDefLink:link {font-family:verdana,sans-serif,helvetica;font-size:10px;'.$this->LINK_NORMAL.'}'.
		'a.uDefLink:visited {font-family:verdana,sans-serif,helvetica;font-size:10px;'.$this->LINK_NORMAL.'}'.
		'a.uDefLink:active {font-family:verdana,sans-serif,helvetica;font-size:10px;'.$this->LINK_NORMAL.'}'.
		'a.uDefLink:hover {font-family:verdana,sans-serif,helvetica;font-size:10px;'.$this->LINK_HOVER.'}'.
		'</style>'.
		'<div style="background:'.$this->BODY_BGCOLOR.';width:519;height:'.($this->PRESENTATION_HEIGHT-2).';">'. // -2 to count border
    '<div style="position:absolute;top:8px;left:8px;width:100%;height:100%;">'.
    $pCompiled.'</div></div>';
    
		return $pCompiled;
	}
}
?>