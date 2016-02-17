<?php
require_once('system/lib.xml.php');

class XML2HTML_Parser extends XML_File
{
  public function __construct($filepath)
  {
    parent::__construct($filepath);
  }
  
  public function to_html()
  {
    $content = &parent::parse();
    
    $document_id = $content['attrs']['id'];
    $document = $content['children'];
    
    $html_contents = '';
    $section = null;
    for($i=0, $ii=count($document); $i<$ii; $i++)
    {
      $section = $document[$i]['children'];
      for($j=0, $jj=count($section); $j<$jj; $j++)
      {
        $html_contents .= '<div class="'.$document_id.'__'.$section[$j]['name'].'">'.$section[$j]['cdata'].'</div>';
      }
    }
    
    return $html_contents;
  }
}
?>