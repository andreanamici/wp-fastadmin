<?php

namespace FastAdmin\lib\classes;

class FastAdminDB extends FastAdminCore
{
    const OPERATOR_BETWEEN = 'between';
    
    const OPERATOR_EQUAL = 'eq';
    
    const OPERATOR_GT = 'gt';
    
    const OPERATOR_GTE = 'gte';
    
    const OPERATOR_LT = 'lt';
    
    const OPERATOR_LTE = 'lte';
    
    const OPERATOR_LIKE = 'like';
    
    const OPERATOR_LIKE_BOTH = 'likeb';
    
    const OPERATOR_LIKE_RIGHT = 'liker';
    
    const OPERATOR_LIKE_LEFT = 'likel';
    
    const OPERATOR_DIFF = 'diff';
    
    /**
     * Wordpress query manager
     * 
     * @var \wpdb
     */
    public $wpdb;
        
    public function __construct()
    {        
        global $wpdb;/*@var $wpdb \wpdb*/
        $this->wpdb = $wpdb;
        fa_set('wpdb', $this->wpdb);
    }

    public function get_table_name($table)
    {
        if(strpos($table, $this->wpdb->prefix) !== false)
        {
            return $table;
        }

        return $this->wpdb->prefix . $table;
    }
    

    public function get_table_prefix()
    {
        return $this->wpdb->prefix;
    }
    
    public function get_table_columns($table)
    {
        return  $this->wpdb->get_col("DESC {$table}", 0);
    }
       
    
    public function get_table_primary_key($table)
    {
        $res = $this->wpdb->get_col("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$this->wpdb->dbname}'  AND TABLE_NAME = '{$table}' AND COLUMN_KEY = 'PRI'", 0);
        return !empty($res) ? $res[0] : null;
    }

    /**
     * Filter data by sql table column's name
     * 
     * @param string    $table         sql table
     * @param array     $data          column's value to store/update
     * @param boolean   $stripsplashes call wp function "stripslashes_deep" on data to bypass wp magic quote
     * 
     * @return array
     */
    public function filter_data_by_table($table, array $data, $stripsplashes = TRUE)
    {
        $table_columns = $this->wpdb->get_col("DESC {$table}", 0);
        
        foreach($data as $field => $value)
        {
            if(!in_array($field, $table_columns))
            {
                unset($data[$field]);
            }
        }
        
        if($stripsplashes && function_exists('stripslashes_deep'))
        {
            $data = stripslashes_deep($data);
        }
        
        return $data;
    }
    
    /**
     * Execlute SQL file by name in configs path
     * 
     * @param string $filename filename without extension
     * 
     * @return boolean
     */
    public function execute_sql_file($filename)
    {        
        $sql_file_content = file_get_contents(WP_FA_BASE_PATH_CONFIGS.'/' .$filename.'.sql');
        $sql_file_content = str_replace(array('{{table_prefix}}','{{version}}'),array($this->wpdb->prefix,WP_FA_PLUGIN_VERSION),$sql_file_content);
            
        if(!empty($sql_file_content))
        {
            $queries = explode(';',$sql_file_content);
            
            foreach($queries as $query)
            {
                if(!empty($query))
                {
                    $this->wpdb->query($query);   
                }
            }

            return true;
        }
        
        return false;
    }
    
    /**
     * Build Sql string by parts
     * 
     * @param array $sql_parts  sql parts
     * 
     * @return string
     */
    protected function _build_sql(array $sql_parts, $params = array())
    {
        $fields = !empty($sql_parts['fields']) ? (is_array($sql_parts['fields']) ? implode(',',$sql_parts['fields']) : $sql_parts['fields']) : '*';
        
        $table  = !empty($sql_parts['from']) ? $sql_parts['from'] : null;
        
        if(!$table)
        {
            wp_die('Sql table must be set!');
        }
        
        $join       = !empty($sql_parts['join'])        ? $this->_build_sql_join($sql_parts['join'])              : '';
        $where      = !empty($sql_parts['where'])       ? $this->_build_sql_where($sql_parts['where'])            : '';
        $orderby    = !empty($sql_parts['orderby'])     ? $this->_build_sql_orderby($sql_parts['orderby'])        : '';
        $groupby    = !empty($sql_parts['groupby'])     ? $this->_build_sql_groupby($sql_parts['groupby'])        : '';
        $having     = !empty($sql_parts['having'])      ? $this->_build_sql_having($sql_parts['having'])          : '';
        $pagination = !empty($sql_parts['pagination'])  ? $this->_build_sql_pagination($sql_parts['pagination'])  : '';
        
        if($where)
        {
            $where = ' WHERE 1 '.$where;
        }
        
        return "SELECT {$fields} FROM {$table} $join $where $groupby $having $orderby $pagination";
    }
    
    /**
     * Biuil join by join info
     * 
     * @param array $join join info, "table", "condition", "type"
     * 
     * @return string
     */
    protected function _build_sql_join(array $join)
    {
        if(empty($join))
        {
            return '';
        }
        
        $sql = '';
       
        foreach($join as $joinData)
        {
            $jointype = !empty($joinData['type'])      ? $joinData['type']  : '';
            $table    = !empty($joinData['table'])     ? $joinData['table'] : '';
            $condition= !empty($joinData['condition']) ? $joinData['condition'] : '';
            
            $sql.=" $jointype JOIN $table ON ($condition) ";
        }        
        
        return $sql;
    }
    
    /**
     * Build where string by array conditions
     * 
     * @param array $where conditions
     * 
     * @return string
     */
    protected function _build_sql_where(array $where)
    {   
        $criteria = array();
                
        foreach($where as $field => $value)
        {
            if(is_array($value))
            {     
                if(isset($value['sql']))
                {
                   $criteria[] = '('.$value['sql'].')';
                }
                else if(isset($value['value']))
                {
                    $operator = isset($value['operator']) ? $value['operator'] : 'eq';
                    $value    = $value['value'];
                    
                    if((is_string($value) && trim($value) === '') || (is_array($value) && trim($value['from']) === '' && trim($value['to']) === '')){
                        continue;
                    }
                    
                    switch($operator)
                    {
                        case self::OPERATOR_EQUAL:
                            $criteria[] = $field.' = "'.$value.'"';
                        break;
                        
                        case self::OPERATOR_LIKE:
                            $criteria[] = $field.' LIKE "'.$value.'"';
                        break;
                    
                        case self::OPERATOR_LIKE_BOTH:
                            $criteria[] = $field.' LIKE "%'.$value.'%"';
                        break;

                        case self::OPERATOR_LIKE_RIGHT:
                            $criteria[] = $field.' LIKE "'.$value.'%"';
                        break;

                        case self::OPERATOR_LIKE_LEFT:
                            $criteria[] = $field.' LIKE "%'.$value.'"';
                        break;
                    
                        case self::OPERATOR_DIFF:
                            $criteria[] = $field.' !="'.$value.'"';
                        break;
                         
                        case self::OPERATOR_LT:
                            $criteria[] = $field.' < "'.$value.'"';
                        break;
                    
                        case self::OPERATOR_LTE:
                            $criteria[] = $field.' <= "'.$value.'"';
                        break;
                    
                        case self::OPERATOR_GT:
                            $criteria[] = $field.' > "'.$value.'"';
                        break;
                    
                        case self::OPERATOR_GTE:
                            $criteria[] = $field.' >= "'.$value.'"';
                        break;
                    
                        case self::OPERATOR_BETWEEN:
                        
                                if(isset($value['from']) && $value['from'] != '')
                                {
                                    $criteria[] = $field.' >= "'.$value['from'].'"';
                                }

                                if(isset($value['to']) && $value['to'] != '')
                                {
                                    $criteria[] = $field.' <= "'.$value['to'].'"';
                                }
                                
                        break;
                    }
                }
            }
            else if(is_null($value))
            {
                $cond = $field;
                                
                if(!$this->_fetch_sql_subquery($field) && !$this->_fetch_sql_operators($field) && stristr($field,' null') === false){
                      $cond.=' IS NULL';
                }
                
                $criteria[] = $cond;
            }
            else
            {
                $cond = $field;
                
                if(!is_null($value) && $value !== ''){
                     if(!$this->_fetch_sql_operators($value) && !$this->_fetch_sql_operators($field)){
                         $cond.=" = ";
                     }
                }
                if(!$this->_fetch_sql_subquery($field)){
                    $cond.= is_numeric($value) ? $value : '"'.$value.'"';
                }else{
                    $cond.= $value;
                }
                
                $criteria[] = $cond;
            }
        }        
        
        if(count($criteria) > 0)
        {
            return 'AND '.implode(' AND ',$criteria);
        }
        
        return '';
    }
    
    /**
     * Build LIMIT and OFFSET sql 
     * 
     * @param mixed $pagination pagination (can be integer / array with limit and offset key)
     * 
     * @return string
     */
    protected function _build_sql_pagination($pagination)
    {
        $sql = '';
        
        if(is_numeric($pagination))
        {
            $sql =  "LIMIT ".$pagination;
        }
        
        if(is_array($pagination))
        {
            if(isset($pagination['limit']) && !isset($pagination['offset']))
            {
                $sql = 'LIMIT '.$pagination['limit'];
            }
            
            if(isset($pagination['limit']) && isset($pagination['offset']))
            {
                $sql = 'LIMIT '.$pagination['offset'].','.$pagination['limit'];
            }
        }
        
        return $sql;
    }

    /**
     * Build sql orderby
     * 
     * @param string $orderby   field
     * @param string $mode      mode, default ASC
     * 
     * @return string
     */
    protected function _build_sql_orderby($orderby, $mode = null)
    {
        if(!empty($orderby) && is_array($orderby))
        {
            $parts = array();
            
            foreach($orderby as $key => $value)
            {
                if(is_string($key))
                {
                    $parts[] = "{$key} {$value}";
                }
                else
                {
                    $parts[] = "{$value}";
                }
            }
            
            $orderby = $parts ? implode(",",$parts) : '';
        }
        
        return !empty($orderby) ?  ('ORDER BY '.$orderby. ' '. ($mode ? $mode : '')) : '';
    }
    
    /**
     * Build sql having
     * 
     * @param string $having   having clause
     * 
     * @return string
     */
    protected function _build_sql_having($having)
    {
        return !empty($having) ? "HAVING $having" : '';
    }
    
    /**
     * Build sql groupby
     * 
     * @param string $groupby   groupby fields
     * 
     * @return string
     */
    protected function _build_sql_groupby($groupby)
    {
        return !empty($groupby) ? "GROUP BY $groupby" : '';
    }

    /**
     * Check if value can be a subquery
     * 
     * @param string $value value string
     * 
     * @return boolean
     */
    protected function _fetch_sql_subquery($value)
    {
        $sql_parts = array(
            'select',
            'from',
            'where',
            'and'
        );
        
        foreach($sql_parts as $sql_part){
            if(stristr($value,$sql_part.' ')){
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    /**
     * Fetch sql operators inside string
     * 
     * @param string $value
     * 
     * @return boolean
     */
    protected function _fetch_sql_operators($value)
    {
        if(strstr($value,'=') !== FALSE)
        {
            return TRUE;
        }
        
        if(strstr($value,'>') !== FALSE)
        {
            return TRUE;
        }
        
        if(strstr($value,'<') !== FALSE)
        {
            return TRUE;
        }
        
        if(strstr($value,'>=') !== FALSE)
        {
            return TRUE;
        }
        
        if(strstr($value,'<=') !== FALSE)
        {
            return TRUE;
        }
        
        return FALSE;
        
    }
}
