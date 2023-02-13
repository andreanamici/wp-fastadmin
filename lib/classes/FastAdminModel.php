<?php 

namespace FastAdmin\lib\classes;

/**
 * Class for access data of post type
 */
class FastAdminModel extends FastAdminDB
{        
    /**
     * Current wp table
     * @var string
     */
    protected $table_name;
    
    /**
     * wp fetch from db mode
     * @var string
     */
    protected $fetch_mode = ARRAY_A;
    
    /**
     * Current wp table pk
     * @var string
     */
    protected $field_id;
    
    /**
     * Current wp table field label
     * @var string
     */
    protected $field_label;
    
    /**
     * Current wp table sql for fetch field label
     * @var string
     */
    protected $field_label_sql;
    
    /**
     * Field for delete datetime
     * @var string
     */
    protected $field_deleted;
    
    /**
     * Field for creation datetime
     * @var string 
     */
    protected $field_creation;
    
    /**
     * Field for updating datetime
     * @var string 
     */
    protected $field_updating;
    
    /**
     * Bind model for wp table
     * 
     * @param $modelinfo params 
     * 
     */
    public function __construct(array $modelinfo = array())
    {
        parent::__construct();
        $this->init_model($modelinfo);
    }
    
    
    public function get_field_id()
    {
        return $this->field_id;
    }
    
    /**
     * Return fields for table list class
     * 
     * @return array
     */
    public function get_list_columns()
    {
        return array();
    }
    
    /**
     * Retrive list rows
     * 
     * @param string  $mode    mode "count" or "select"
     * @param array   $options options for filter query
     * 
     * @return array
     */
    public function get_list_table_data($mode, array $options = array())
    {
        $where = !empty($options['where']) ? $options['where'] : array();                
        
        if($this->field_deleted)
        {
            $where[$this->table_name.'.'.$this->field_deleted] = null;
        }
                       
        $orderby    = !empty($options['orderby']) ? $options['orderby'] : '';
        $ordermode  = !empty($options['order'])   ? $options['order']   : '';
        
        if(!empty($orderby))
        {
            $orderby = array(
                $orderby => $ordermode
            );
        }
        
        $join       = !empty($options['join'])    ? $options['join']    : '';
        $fields     = !empty($options['fields'])  ? $options['fields']  : '';
        $groupby    = !empty($options['groupby']) ? $options['groupby'] : '';
        $having     = !empty($options['having'])  ? $options['having']  : '';
        
        $pagination = array();
        
        if(isset($options['paged']) || isset($options['per_page']))
        {
            $pagination['limit'] = $options['per_page'];
            if($options['paged'] >= 1)
            {
                $pagination['offset'] = ($options['paged'] - 1) *  $options['per_page'];
            }
        }
        
        if($mode == 'count')
        {
            return (int) $this->get_count_records_by($where, $join, $groupby, $having);
        }
        
        return  $this->get_records_by($where, $fields, $join, $orderby, $pagination, $groupby, $having);
    }
    
    /**
     * Retrive current model table fields
     * 
     * @param string $table table, default $this->table_name
     * 
     * @return array
     */
    public function get_table_fields($table = null)
    {
        $table = $table ? $table : $this->table_name;
        return parent::get_table_columns($table);
    }
    
    public function set_table_name($table, $prefixed = true, $primary_key = null)
    {
        $table = $prefixed ? parent::get_table_name($table) : $table;
        $this->table_name = $table;

        $primary_key = $primary_key ? $primary_key : $this->get_table_primary_key($this->table_name);
        $this->field_id = $primary_key;

        return $this;
    }

    /**
     * Retrive model table name
     * 
     * @param string $table table, default $this->table_name
     * 
     * @return array
     */
    public function get_table_name($table = null)
    {
        $table = $table ? $table : $this->table_name;
        return parent::get_table_name($table);
    }
    
    /**
     * Retrive model table field id
     * 
     * @return array
     */
    public function get_table_field_id()
    {
        return $this->field_id;
    }
    
    /**
     * Get a record from database
     * 
     * @param mixed $id pk value
     * 
     * @return array
     */
    public function get_record($id)
    {
        $record =  $this->wpdb->get_row('SELECT * FROM ' . $this->table_name.' WHERE '.$this->field_id.' = "'. $id . '"', $this->fetch_mode);
        return  $this->_post_get_record($record);
    }
    
    /**
     * Get a record from database
     * 
     * @param mixed $id pk value
     * @param mixed $orderby orderby array field => value
     * @return array
     */
    public function get_record_by(array $where, $orderby = NULL)
    {
        $sql = $this->_build_sql(array(
                'from'       => $this->table_name,
                'where'      => $where, 
                'orderby'    => $orderby, 
        ));

        $record = $this->wpdb->get_row($sql, $this->fetch_mode);
        return  $this->_post_get_record($record);
    }
    
    /**
     * Get records dropdown
     * 
     * @return array
     */
    public function get_records_dropdown()
    {
       $where = array(); 
       
       if($this->field_deleted)
       {
          $where[$this->field_deleted] = null;
       }
       
       $records = $this->get_records_by($where,array($this->field_id,$this->field_label_sql), null, $this->field_label);
       $dropdown = array();
       
       if(!empty($records))
       {
           foreach($records as $key => $record)
           {
               $record = $this->_post_get_record($record);
               $dropdown[$record[$this->field_id]] = $record[$this->field_label];
           }
       }
       
       return $dropdown;
    }
        
    /**
     * Fetch records by 
     * 
     * @param array  $where
     * @param mixed  $fields
     * @param array  $join
     * @param mixed  $orderby
     * @param mixed  $pagination
     * @param string $groupby
     * @param string $having
     * 
     * @return array
     */                         
    public function get_records_by(array $where, $fields = '*', $join = null, $orderby = null, $pagination = null, $groupby = null, $having = null)
    {
        $sql = $this->_build_sql(array('fields'     => $fields,
                                       'from'       => $this->table_name,
                                       'join'       => $join,
                                       'where'      => $where, 
                                       'orderby'    => $orderby, 
                                       'pagination' => $pagination,
                                       'groupby'    => $groupby,
                                       'having'     => $having
               ));

        $records    = $this->wpdb->get_results($sql, $this->fetch_mode);
        
        if(!empty($records))
        {
            foreach($records as $key => $record)
            {
                $records[$key] = $this->_post_get_record($record);
            }
        }
        
        return $records;
    }
    
    public function get_all($fields = '*',$orderby = null, $pagination = null)
    {
        $sql = $this->_build_sql(array(
                'fields'     => $fields,
                'from'       => $this->table_name,
                'orderby'    => $orderby, 
                'pagination' => $pagination
        ));

        $records    = $this->wpdb->get_results($sql, $this->fetch_mode);
        
        if(!empty($records))
        {
            foreach($records as $key => $record)
            {
                $records[$key] = $this->_post_get_record($record);
            }
        }
        
        return $records;
    }
    
    /**
     * Count records by conditions
     * 
     * @param array $where
     * @param array $join
     * @param string $groupby
     * 
     * @return int
     */
    public function get_count_records_by(array $where, $join = null, $groupby = null, $having = null)
    {
        $sql = $this->_build_sql(array('fields'     => 'COALESCE(COUNT(*),0) as tot',
                                        'from'       => $this->table_name,
                                        'where'      => $where,
                                        'join'       => $join, 
                                        'groupby'    => $groupby,
                                        'having'     => $having
               ));

        $result  = $this->wpdb->get_row($sql, $this->fetch_mode);
        return (int) $result['tot'];
    }
    
    /**
     * Get Sql single result
     * 
     * @param string $sql
     * @param fetch  $fetchMode
     * 
     * @return array
     */
    public function fetch($sql, $fetchMode = null)
    {
        $fetchMode = $fetchMode ? $fetchMode : $this->fetch_mode;
        return $this->wpdb->get_row($sql,$fetchMode);
    }
    
    /**
     * Get Sql all results
     * 
     * @param string $sql
     * @param fetch  $fetchMode
     * 
     * @return array
     */
    public function fetch_all($sql, $fetchMode = null)
    {
        $fetchMode = $fetchMode ? $fetchMode : $this->fetch_mode;
        return $this->wpdb->get_results($sql,$fetchMode);
    }
    
    /**
     * Insert a record 
     * 
     * @param array  $data        associative array  (column name => value)
     * 
     * @return int (pk of insert record, default null)
     */
    public function add($data)
    {
        if(!empty($this->field_creation) && empty($data[$this->field_creation]))
        {
            $data[$this->field_creation] = date("Y-m-d H:i:s");
        }
        
        $data        = $this->filter_data_by_table($this->table_name, $data);
        
        $res         = $this->wpdb->insert($this->table_name, $data);
        
        if($this->wpdb->last_error !== ''){
            wp_die($this->wpdb->last_error);
        }
        
        $id          = $res ? $this->wpdb->insert_id : null;    
        
        return $id;
    }
    
    /**
     * Update a record
     * 
     * @param string $id            sql table field id value
     * @param array  $data          data to update, associative array (column name => value)
     * 
     * @return int  number of records updated
     */
    public function update($id, $data)
    {
        if(!empty($this->field_updating) && empty($data[$this->field_updating]))
        {
            $data[$this->field_updating] = fa_date_now();
        }

        if(!$where){
            $where = array($this->field_id => $id);
        }
        
        $data  = $this->filter_data_by_table($this->table_name, $data);
        $res = $this->wpdb->update($this->table_name, $data, $where);

        if($this->wpdb->last_error !== ''){
            wp_die($this->wpdb->print_error());
        }
    
        return $res;
    }
    
    /**
     * Delete a record  
     * 
     * @param mixed  $id            sql table field id value
     * @param array  $where         array of conditions, associative array (column => value)
     * @param string $table_name    sql table name, default NULL
     * 
     * @return int number of records deleted/updated
     */
    public function delete($id, $where = array())
    {
        $where = array_merge(array($this->field_id => $id),$where);
        
        if(!$this->field_deleted)
        {
            $res = $this->wpdb->delete($this->table_name, $where);
            
            if($this->wpdb->last_error !== ''){
                wp_die($this->wpdb->print_error());
            }

            return $res;
        }
        
        return $this->update($id, array($this->field_deleted => fa_date_now()));
    }
    
    
    public function save(array $data)
    {
        if(isset($data[$this->field_id]))
        {
            $this->update($data[$this->field_id], $data);
            return $data[$this->field_id];
        }
        
        return $this->add($data);
    }
    
    protected function init_model(array $modelinfo)
    {
        if(!empty($modelinfo))
        {
            $this->table_name        = isset($modelinfo['table_name']) ? $this->get_table_name($modelinfo['table_name']) : null;
            $this->field_id          = isset($modelinfo['field_id']) ? $modelinfo['field_id'] : null;
            $this->field_deleted     = isset($modelinfo['field_deleted'])    ? $modelinfo['field_deleted']   : null;
            $this->field_creation    = isset($modelinfo['field_creation'])   ? $modelinfo['field_creation']  : null;
            $this->field_updating    = isset($modelinfo['field_updating'])   ? $modelinfo['field_updating']  : null;
            $this->field_label       = isset($modelinfo['field_label'])      ? $modelinfo['field_label']     : null;
            $this->field_label_sql   = isset($modelinfo['field_label_sql'])  ? $modelinfo['field_label_sql'] : $this->field_label;
        }
        
        return $this;
    }
    
    /**
     * Called after each row fetch from database
     * 
     * @param array $record record
     * 
     * @return array
     */
    protected function _post_get_record($record)
    {
        return $record;
    }
}
