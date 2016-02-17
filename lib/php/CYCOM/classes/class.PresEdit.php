<?php
/********************************************************************************
 * File:          classes/class.PresEdit.php
 * Description:   Presentation Editor Class
 * Begin:         2006-01-05
 * Edit:          
 * Author:        Lukas Kalinski
 * Copyright:     2006 CyLab Sweden
 ********************************************************************************/

require_once('modifier.apply_html_entities.php');
require_once('system/db/globals.usr.php');

if(defined('DEBUG')) // uh.. eh.. make this more standard..
{
  /**
   * Purpose: Test of the prepare process of pres_compiled.
   */
  define('__DEB_PRES_COMPILED', 'Script 1: <script   language="javascript"></script>'.
                                'Script 2: <script type="text/javascript"></script>'.
                                'Script 3: <script a language="javascript"></script>'.
                                'Script 4: <script language="javascript" a></script>'.
                                'Script 5: <script></script>'.
                                'Script 6: <script a></script>'.
                                'Not allowed tag 1: <some_tag some_attr="val">'.
                                'Not allowed tag 2: <some_tag />'.
                                'Not allowed tag 3: <some_tag/>'.
                                'Not allowed tag 4: </some_tag>'.
                                'Valid tag 1: <div></div>'.
                                'Valid tag 2: <div id="someId">foo</div>'.
                                'Valid tag 3: <a href="http://www.somedomain.com/">link</a>'.
                                'Invalid attr value 1: <div id="foo)">'.
                                'Invalid attr value 2: <div class="0bar">'.
                                'Invalid attr value 3: <a href="http://www.hej.nu/">Hej.nu!</a>'.
                                'Invalid attr value 4: <a href="javascript:evilJs();">evil</a>'.
                                'Invalid attr 1: <a href="http://www.some.nu" onclick="evilJs();">evil</a>');
  define('__DEB_CSS_COMPILED', 'msie=a.CMLR_a:link{display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px}'.
                                    'a.CMLR_a:visited{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_a:active{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_a:hover{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_user:link{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_user:visited{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_user:active{display:inline;font-weight:bold;text-decoration:none}'.
                                    'a.CMLR_user:hover{display:inline;font-weight:bold;text-decoration:none;}'.
                                    'CMLR_body{position:relative;display:block;overflow:hidden;width:517px;height:400px;padding:5px;background-color:#858454}'.
                                    'CMLR_b{font-weight:bold}'.
                                    'CMLR_i{font-style:italic}'.
                                    'CMLR_s{text-decoration:line-through}'.
                                    'CMLR_u{text-decoration:underline}'.
                                    'CMLR_center{text-align:center}'.
                                    'CMLR_right{text-align:right}'.
                                    'CMLR_hr{background-color:#000000;border-style:none;height:1px;width:300px}'.
                                    'CMLR_test{position:relative;display:block;margin-bottom:5px;border-color:#000000;overflow:hidden;background-color:#567546}'.
                                    '#CMLR_inline{display:inline;font-size:10px}'.
                                    '#CMLR_block_rel{display:block;border-width:1px;border-style:solid;font-size:10px;position:relative;width:120px}'.
                                    '#CMLR_block_abs{display:block;border-width:1px;border-style:solid;padding:3px;height:100px;font-size:10px}'.
                                    '#CMLR_wrap_abs{display:block;height:50px;background:#678678;font-size:10px;left:5px;position:absolute;width:120px}'.
                               '|'.
                               'gecko=a.CMLR_a:link{display:inline;font-weight:bold;text-decoration:none;font-size:10px;padding:0px}'.
                                     'a.CMLR_a:visited{display:inline;font-weight:bold;text-decoration:none}'.
                                     'a.CMLR_a:active{display:inline;font-weight:bold;text-decoration:none}'.
                                     'a.CMLR_a:hover{display:inline;font-weight:bold;text-decoration:none}'.
                                     'a.CMLR_user:link{display:inline;font-weight:bold;text-decoration:none}'.
                                     'a.CMLR_user:visited{display:inline;font-weight:bold;text-decoration:none;background-color:transparent}'.
                                     'a.CMLR_user:active{display:inline;font-weight:bold;background-color:transparent}'.
                                     'a.CMLR_user:hover{display:inline;font-weight:bold;text-decoration:none;background-color:transparent}'.
                                     'CMLR_body{position:relative;display:block;overflow:hidden;padding:5px;background-color:#858454;width:507px;height:390px}'.
                                     'CMLR_b{font-weight:bold}'.
                                     'CMLR_i{font-style:italic}'.
                                     'CMLR_s{text-decoration:line-through}'.
                                     'CMLR_u{text-decoration:underline}'.
                                     'CMLR_center{text-align:center}'.
                                     'CMLR_right{text-align:right}'.
                                     'CMLR_hr{background-color:#000000;border-style:none;width:300px;height:1px}'.
                                     'CMLR_test{position:relative;display:block;border-color:#000000;overflow:hidden;background-color:#567546;height:92px}'.
                                     '#CMLR_inline{display:inline;font-size:10px}'.
                                     '#CMLR_block_rel{display:block;border-width:1px;border-style:solid;padding:3px;font-size:10px}'.
                                     '#CMLR_block_abs{display:block;border-width:1px;border-style:solid;padding:3px;font-size:10px}'.
                                     '#CMLR_wrap_abs{display:block;border-width:1px;border-style:solid;padding:3px;background:#678678}');
}

define('PE__CSS_RULE_PREFIX', 'CMLR_'); // Remember: Using '__' as prefix will make IE ignore the rules.
define('PE__ERR_C_CSS_INVALID', -1);

class PresEdit
{
  private $escape_chars = '"\'<>'; // Chars that we'll escape by default.
  private $valid_tags;
  
  private $pres_raw;
  private $pres_compiled;
  private $css_raw;
  private $css_compiled;
  private $re = array();
  
  /**
   * __construct()
   */
  public function __construct()
  {
    /**
     * Regular expressions:
     * !! WARNING: Changing the @JS@-marked properties affects javascript expressions too; avoid changing the catching parenthesis!
     */
    $this->re['tag_name'] = '(?:[a-z_][a-z0-9_]*)';               // @catches: N/A
    $this->re['attr_name'] = '(?:[a-z_][a-z0-9_]*)';
    $this->re['attrs'] = '(?:\s'.$this->re['attr_name'].'=".*?")';          // @catches: N/A
    $this->re['valid_tag_open'] = '(?:\<('.$this->re['tag_name'].')('.$this->re['attrs'].'*)\>)'; // @catches: tag name and attributes string
    $this->re['valid_tag_end'] = '(?:\<\/('.$this->re['tag_name'].')\>)';      // @catches: tag name
    $this->re['valid_empty_tag'] = '(?:\<('.$this->re['tag_name'].')\s*\/\>)'; // @catches: tag name
    $this->re['tamper_tag'] = '(?:\<.*?\>)';                            // @catches: N/A
    $this->re['attr_id'] = '(?:[a-z_][a-z0-9_]*)';                      // @catches: N/A
    $this->re['attr_class'] = '(?:[a-z_][a-z0-9_]*)';                   // @catches: N/A
    $this->re['attr_href'] = '(?:http\:\/\/)|(?:\/)[a-z0-9,._\-?;{}&%#=~\/]{3,}';  // @JS@ @catches: N/A
    $this->re['attr_href_restricted'] = 'www\.hej\.nu';                 // @JS@ @catches: N/A ### !!! Update when project has more domains...
    $this->re['attr_style'] = '[a-z0-9-:;\(\)\'#]+';
    $this->re['attr_target'] = '_new';
    
    $this->re['css_rule_name'] = '(?:'.
                                   '(?:\#|[a-z]{1,4}\.|\.|)'. // Prefix (tag_name. or #).
                                   PE__CSS_RULE_PREFIX.'[a-z_][a-z0-9_]*'.
                                   '(?:\:(?:link|visited|active|hover))?'.
                                 ')';
    $this->re['css_props_str'] = '(?:[a-z0-9-:;\/=#\(\)]*)';
    $this->re['css_full_rule'] = '(?:'.$this->re['css_rule_name'].'\{'.$this->re['css_props_str'].'\})';
    
    $this->valid_tags = array('br' => null,
                              'div' => array('id', 'class'),
                              'span' => array('id', 'class', 'style'),
                              'a' => array('href', 'target', 'class'),
                              'big' => null,
                              'hr' => null); // haven't had time to bother about its attributes yet...
  }
  
  /**
   * void import_data(string, string, string, string)
   */
  public function import_data($pres_raw, $css_raw, $pres_c, $css_c)
  {
    $this->pres_raw = $pres_raw;
    $this->pres_compiled = $pres_c;
    $this->css_raw = $css_raw;
    $this->css_compiled = $css_c;
  }
  
  /**
   * string re(string)
   * Function for getting regular expressions from this->re in the public scope.
   */
  public function re($k)
  {
    return $this->re[$k];
  }
  
  /**
   * string get_prepared_raw_pres()
   * 
   */
  public function get_prepared_raw_pres()
  {
    return CYCOM__apply_html_entities($this->pres_raw, $this->escape_chars);
  }
  
  /**
   * string get_prepared_raw_css()
   * 
   */
  public function get_prepared_raw_css()
  {
    return $this->css_raw;
  }
  
  /**
   * bool validate_attr_value(string, string)
   * 
   */
  private function validate_attr_value($tag_name, $attr_name, $attr_value)
  {
    return in_array($attr_name, $this->valid_tags[$tag_name]) &&                                  // - Check that attribute exists for chosen tag.
           preg_match('/^'.$this->re['attr_'.$attr_name].'$/i', $attr_value) &&                   // - We want to match the attribute value pattern.
           (!key_exists('attr_'.$attr_name.'_restricted', $this->valid_tags[$tag_name]) ||        // - Either we have no restrictions or 
            !preg_match('/^'.$this->re['attr_'.$attr_name.'_restricted'].'$/i', $attr_value));    //   we don't want to match them.
  }
  
  /**
   * string get_prepared_compiled_pres()
   * 
   * 
   */
  public function get_prepared_compiled_pres()
  {
    $toks = preg_split('/('.$this->re['tamper_tag'].')/i', $this->pres_compiled, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    
    $is_valid = true;
    for($i=0, $ii=count($toks); $i<$ii && $is_valid; $i++)
    {
      if(!preg_match('/('.$this->re['tamper_tag'].')/i', $toks[$i])) // Skip harmless strings.
        continue;
      
      $is_valid = false;
      
      // Open tag: empty or normal
      if(preg_match('/^'.$this->re['valid_tag_open'].'$/i', $toks[$i], $match) || preg_match('/^'.$this->re['valid_empty_tag'].'$/i', $toks[$i], $match))
      {
        $tag_name = $match[1];
        $tag_attrs = key_exists(2, $match) ? $match[2] : null;
        
        // Check if tag name is valid:
        if(key_exists($tag_name, $this->valid_tags))
        {
          $is_valid = true;
          
          // Validate arguments if found:
          if($tag_attrs != null)
          {
            preg_match_all('/\s('.$this->re['attr_name'].')="(.*?)"/i', $match[2], $attr_toks, PREG_SET_ORDER);
            for($j=0, $jj=count($attr_toks); $j<$jj && $is_valid; $j++)
              $is_valid = $this->validate_attr_value($tag_name, $attr_toks[$j][1], $attr_toks[$j][2]);
          }
        }
      }
      elseif(preg_match('/^'.$this->re['valid_tag_end'].'$/i', $toks[$i], $match))
      {
        // Check if tag name is valid:
        if(key_exists($match[1], $this->valid_tags))
          $is_valid = true;
      }
      
      if(!$is_valid)
        break;
    }
    
    $return = implode(null, $toks);
    return $is_valid ? $return : CYCOM__apply_html_entities($return, $this->escape_chars); // Apply entities to everything if we found anything suspicious.
  }
  
  /**
   * int/string[] get_prepared_compiled_css()
   * Returns:
   *         - The prepared css string in browser-case array; if it's considered valid.
   *         - PE__ERR_C_CSS_INVALID on error (abuse suspicion and so on).
   */
  public function get_prepared_compiled_css()
  {
    // Check if we have a secure css-string:
    if(preg_match('/^msie=('.$this->re['css_full_rule'].'*)\|gecko=('.$this->re['css_full_rule'].'*)$/i', $this->css_compiled, $match))
      return array(CYCOM_DB_Usr::BROWSER_CASE_MSIE => $match[1], CYCOM_DB_Usr::BROWSER_CASE_GECKO => $match[2]);
    else // Possible tampering attempt: return empty string.
      return PE__ERR_C_CSS_INVALID;
  }
}