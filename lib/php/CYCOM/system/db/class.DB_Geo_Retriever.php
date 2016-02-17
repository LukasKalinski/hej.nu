<?php
require_once('system/lib.db.php');

/**
 * @desc GEO Database Retriever
 */
class DB_Geo_Retriever extends Cylab_DB
{
  /**
   * @desc Default constructor.
   */
  public function __construct()
  {
    parent::__construct(true);
  }
  
  /**
   * @desc Closes connection and unsets object.
   * @return void
   */
  public function destroy()
  {
    parent::destroy();
  }
  
  /**
  * @desc Overload of parent method.
  */
  protected function register_query($query_id, $query)
  {
    parent::register_query($query_id, QUERY_TYPE__SELECT, $query);
  }
  
  /**
   * @return bool
   * @todo remove type conversion from here... -> handled in Cylab_DB_Query
   */
  public function location_is_valid($country_id, $region_id=null, $city_id=null) // Make this check if country is enabled ...
  {
    $where_condition = 'country_id='.Cylab_DB__convert_type_php2psql($country_id);
    if(!is_null($region_id)) $where_condition .= ' AND region_id='.Cylab_DB__convert_type_php2psql($region_id);
    if(!is_null($city_id)) $where_condition .= ' AND id='.Cylab_DB__convert_type_php2psql($city_id);
    
    $this->register_query('validate_location', 'SELECT id FROM geo_city WHERE '.$where_condition);
    $result = parent::execute_query('validate_location');
    return (key_exists(0, $result) ? true : false);
  }
  
  /**
   * @return array
   */
  public function get_countries($only_enabled=true, $cols='id,name')
  {
    $this->register_query('get_countries', 'SELECT '.$cols.' FROM geo_country '.($only_enabled ? 'WHERE enabled=TRUE ' : '').'ORDER BY id');
    return parent::execute_query('get_countries');
  }
  
  /**
   * @return array
   */
  public function get_regions($country_ID, $cols='id,name')
  {
    if(!is_numeric($country_ID))
      parent::trigger_error('dbr_geo__country_id_not_numeric', 'Country id must be a numeric value.', __LINE__, __FILE__);
    
    $this->register_query('get_regions', 'SELECT '.$cols.' FROM geo_region WHERE country_id='.$country_ID);
    return parent::execute_query('get_regions');
  }
  
  /**
   * @return array
   */
  public function get_cities($region_ID, $cols='id,name')
  {
    if(!is_numeric($region_ID))
      parent::trigger_error('dbr_geo__region_id_not_numeric', 'Region ID must be a numeric value.', __LINE__, __FILE__);
    
    $this->register_query('get_cities', 'SELECT '.$cols.' FROM geo_city WHERE region_id='.$region_ID);
    return parent::execute_query('get_cities');
  }
}