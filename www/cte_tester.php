<?php
function get_microtime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}
define('PAGE_LOAD', get_microtime());

class sampleClass
{
  private $life = 'some life';
  
  public function get_a_life()
  {
    return $this->life;
  }
  
  public function call_with_arg($arg)
  {
    return 'Used argument: '.$arg;
  }
  
  public function call_with_args($arg1, $arg2)
  {
    return 'Used arguments: '.$arg1.', '.$arg2;
  }
}

$obj = new sampleClass();

require_once('cte/engine/class.CTE.php');

define('PREPROCESSOR_CONST', 'En konstant!');
define('SOME_CONST', '[value of SOME_CONST]');
define('BG_COLOR', '#FF00FF');
define('NUMBER', 2);

CTE::create_var('some_var',    '[value of $some_var]');
CTE::create_var('empty_var',   '');
CTE::create_var('name',        'Pekka');
CTE::create_var('jssrc',       'sample.js');
CTE::create_var('some_color',  '#00FF00');
CTE::create_var('bool_var',    true);
CTE::create_var('gender',      'm');
CTE::create_var('some_date',   '2004-12-22');
CTE::create_var('some_number', 123);
CTE::create_var('number',      10);
CTE::create_var('browser',     'ie');
CTE::create_var('assoc_key',   'key_2');
CTE::register_var('some_instance', $obj);
CTE::create_var('simple_array',    array('apple', 'orange', 'kiwi', 'mango'));
CTE::create_var('assoc_array',     array('key_1' => 'assoc_array value 1', 'key_2' => 'assoc_array value 2'));
CTE::create_var('empty_array',     array());
CTE::create_var('assoc_array2',    array('apple'   => 'green',
                                         'orange'  => 'orange',
                                         'kiwi'    => 'brown',
                                         'mango'   => 'red?'));
CTE::create_var('simple_array_2D', array(array('simple_array_2D value 0->1', 'simple_array_2D value 0->2'),
                                          array('simple_array_2D value 1->2', 'simple_array_2D value 1->2')
                                         ));
CTE::create_var('assoc_array_2D',  array(array('key_1' => 'assoc_array_2D value 0->1', 'key_2' => 'assoc_array_2D value 0->2'),
                                          array('key_1' => 'assoc_array_2D value 1->2', 'key_2' => 'assoc_array_2D value 1->2')
                                         ));

CTE::debug();
?>