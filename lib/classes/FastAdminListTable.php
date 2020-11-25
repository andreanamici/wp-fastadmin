<?php

namespace FastAdmin\lib\classes;

require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class FastAdminListTable extends \WP_List_Table
{
    const DEFAULT_ROWS_PER_PAGE = 10;
    
    const FILTERS_INPUT_NAME = 'fa_list_filters';
    
    const COLUMN_ACTIONS = '__actions__';
    
    /**
     * Id of listing
     * @var string
     */
    protected $id = null;
    
    /**
     * Model
     * @var FastAdminModel
     */
    protected $model;
    
    /**
     * array of columns
     * @var array
     */
    protected $columns;
    
    
    /**
     * Filters for each columns
     * @var array
     */
    protected $filters;
    
    /**
     * Table rows
     * 
     * @var array
     */
    protected $rows;
    
    /**
     * Fields to select, default columns fieldname
     * @var array
     */
    protected $fields;
    
    /**
     * Where base conditions
     * @var array
     */
    protected $where;
    
    /**
     * Order field
     * @var Orderby field
     */
    protected $orderby;
    
    /**
     * Join conditions
     * @var array
     */
    protected $join;
    
    /**
     * Group by condition
     * @var string
     */
    protected $groupby;
    
    
    /**
     * Having Condition
     * @var string
     */
    protected $having;
    
    /**
     * Order mode
     * @var string
     */
    protected $order;
    
    /**
     * Current page
     * @var int
     */
    protected $paged;
    
    /**
     * Current records per page
     * @var int
     */
    protected $per_page = self::DEFAULT_ROWS_PER_PAGE;
    
    /**
     * Total items
     * @var int
     */
    protected $total_items = 0;
    
    /**
     * add nonce field to form
     * 
     * @var bool
     */
    protected $nonce_field = false;
    
    /**
     * Callable callend on each row of table
     * 
     * @var callable
     */
    protected $row_attributes_callable = null;
        
    public function __construct(array $args)
    {
        parent::__construct($args);
        
        if(!empty($args['model']))
        {
            $this->model   = $args['model'];
            $this->columns = $this->model->get_list_columns();
        }
        
        if(!empty($args['columns']))
        {
            $this->columns = $args['columns'];
        }
        
        if(!empty($args['rows']))
        {
            $this->rows = $args['rows'];
        }
        
        $this->id = !empty($args['id']) ? $args['id'] : 'default';
        
        $this->fields  = !empty($args['fields'])   ? $args['fields']  : null;
        $this->join    = !empty($args['join'])     ? $args['join']    : null;
        $this->where   = !empty($args['where'])    ? $args['where']    : array();
        
        $this->groupby = !empty($args['groupby'])  ? $args['groupby'] : null;
        $this->having  = !empty($args['having'])   ? $args['having']  : null;
        
        $this->row_attributes_callable = !empty($args['row_attributes_callable']) ? $args['row_attributes_callable'] : null;
        
        $this->_init_orderby($args);
       
        if(isset($args['nonce_field'])){
            $this->nonce_field = $args['nonce_field'];
        }
        
        $this->per_page = isset($args['per_page']) ? $args['per_page'] : self::DEFAULT_ROWS_PER_PAGE;
        
        $this->paged   = isset($_GET['paged'])     ? $_GET['paged']   : null;
        
        $this->_init_filters();
    }
    
    public function get_count_rows()
    {
        if(empty($this->rows) && $this->model)
        {
            return $this->model->get_list_table_data('count', array(
                        'where'     => $this->_get_filters_values(),
                        'join'      => $this->join,
                        'groupby'   => $this->groupby,
                        'having'    => $this->having,
                   ));
        } 
        
        return count($this->rows);
    }
    
    public function get_total_items()
    {
        return $this->total_items;
    }
    
    public function get_rows()
    {
        if(empty($this->rows) && $this->model)
        {                                   
            $this->rows = $this->model->get_list_table_data('select',array(
                'where'     => $this->_get_filters_values(),
                'fields'    => $this->get_columns_fields(),
                'join'      => $this->join,
                'groupby'   => $this->groupby,
                'having'    => $this->having,
                'orderby'   => $this->orderby,
                'order'     => $this->order,
                'per_page'  => $this->per_page,
                'paged'     => $this->paged,
            ));
        }

        return $this->rows;
    }
    
    public function get_columns()
    {
        $columns = array();
        
        foreach($this->columns as $name => $column)
        {
            $columns[$name] =!empty($column['title']) ? $column['title'] : '';
        }
        
        return $columns;
    }
    
    public function get_columns_fields()
    {
        $fields = array();
        
        if($this->fields)
        {
           return $this->fields; 
        }
        
        foreach($this->columns as $name => $column)
        {
            $fieldname = !empty($column['fieldname']) ? (stristr($column['fieldname'],' as ') === false ? $column['fieldname'].' as '.$name : $column['fieldname']) : $name;
            
            if($fieldname != self::COLUMN_ACTIONS)
            {
                $fields[] = $fieldname;
            }
        }
        
        return $fields;
    }
    
    public function get_columns_sortable()
    {
        $columns = array();
        
        foreach($this->columns as $name => $column)
        {
            if(!empty($column['sortable']))
            {
                $columns[$name] = array($name,empty($column['sortable_order']) || $column['sortable_order'] == 'ASC' ? true : false);
            }
        }
        
        return $columns;
    }
    
    public function get_columns_hidden()
    {
        $columns = array();
        
        foreach($this->columns as $name => $column)
        {
            if(!empty($column['hidden']))
            {
                $columns[$name] = $name;
            }
        }
        
        return $columns;
    }
    
    public function print_column_headers($with_id = true)
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        $current_url = remove_query_arg( 'paged', $current_url );

        if ( isset( $_GET['orderby'] ) ) {
                $current_orderby = $_GET['orderby'];
        } else {
                $current_orderby = '';
        }

        if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
                $current_order = 'desc';
        } else {
                $current_order = 'asc';
        }

        if ( ! empty( $columns['cb'] ) ) {
                static $cb_counter = 1;
                $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
                        . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
                $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name ) {
                $class = array( 'manage-column', "column-$column_key" );

                if ( in_array( $column_key, $hidden ) ) {
                        $class[] = 'hidden';
                }

                if ( 'cb' === $column_key )
                        $class[] = 'check-column';
                elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
                        $class[] = 'num';

                if ( $column_key === $primary ) {
                        $class[] = 'column-primary';
                }

                if ( isset( $sortable[$column_key] ) ) {
                        list( $orderby, $desc_first ) = $sortable[$column_key];

                        if ( $current_orderby === $orderby ) {
                                $order = 'asc' === $current_order ? 'desc' : 'asc';
                                $class[] = 'sorted';
                                $class[] = $current_order;
                        } else {
                                $order = $desc_first ? 'desc' : 'asc';
                                $class[] = 'sortable';
                                $class[] = $desc_first ? 'asc' : 'desc';
                        }

                        $column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
                }

                $tag = ( 'cb' === $column_key ) ? 'td' : 'th';
                $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
                $id = $with_id ? "id='$column_key'" : '';

                if ( !empty( $class ) )
                        $class = "class='" . join( ' ', $class ) . "'";

                echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
        
        $this->print_columns_filters($with_id);
    }
    
    /**
     * Generates content for a single row of the table
     *
     * @since 3.1.0
     *
     * @param object $item The current item
     */
    public function single_row( $item ) {
            
            $attrs = '';
            
            if(is_callable($this->row_attributes_callable)){
                $attrs = call_user_func_array($this->row_attributes_callable, array($item));
                if(is_array($attrs)){
                    $attrs = fa_form_parse_attributes($attrs);
                }
            }
        
            echo '<tr '.$attrs.'>';
            $this->single_row_columns( $item );
            echo '</tr>';
    }

    
    public function print_columns_filters($with_id = true)
    {
        if(!$with_id || !$this->filters)
        {
            return false;
        }
        
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
        
        echo "<tr>";
                
        foreach($this->columns as $name => $column)
        {        
            if($name == self::COLUMN_ACTIONS || !empty($column['hidden']))
            {
                continue;
            }
            
            echo "<td class='fa-list-filters'>";
            
            $filter = !empty($column['filter']) ? $column['filter'] : null;
            
            if($filter)
            {
                $input = $filter['input'];
                
                switch($filter['type'])
                {
                    case 'input':
                    case 'text': ?>
                        <input type="<?php echo $input['type'];?>" name="<?php echo $input['name'];?>" class="fa-input <?php echo $input['type'];?> <?php echo $input['class'];?>" value="<?php echo $input['value'];?>" placeholder="<?php echo $input['placeholder'];?>" />
                    <?php   
                    break;
                    case 'range':
                    case 'between': ?>
                        <input type="<?php echo $input['type'];?>" name="<?php echo $input['name']['from'];?>" class="fa-input <?php echo $input['type'];?> input-from <?php echo $input['class'];?>" value="<?php echo $input['value']['from']; ?>" placeholder="<?php echo $input['placeholder']['from'];?>" />
                        <input type="<?php echo $input['type'];?>" name="<?php echo $input['name']['to'];?>" class="fa-input <?php echo $input['type'];?> input-to <?php echo $input['class'];?>" value="<?php echo $input['value']['to']; ?>"  placeholder="<?php echo $input['placeholder']['to'];?>" />
                    <?php   
                    break;
                    case 'select':?>
                        <select  name="<?php echo $input['name'];?>"><?php foreach($filter['options'] as $key => $value) { ?><option value="<?php echo $key;?>" <?php echo $input['value'] == $key ? 'selected' : '';?>><?php echo $value;?></option><?php } ?></select>
                    <?php
                    break;
                }                
            }
            
            echo "</td>";
        }
        
        if($this->filters)
        {
            $get_params = $_GET;
            if($this->id){
                unset($get_params[self::FILTERS_INPUT_NAME][$this->id]);    
            }else{
                unset($get_params[self::FILTERS_INPUT_NAME]);    
            }
            
            unset($get_params['paged']);
            
            echo "<td class='fa-list-filters-buttons'>".get_submit_button('Filtra','primary','',false)." ".get_submit_button('Annulla','reset','',false,array('onclick' => 'location.href="'. fa_action_path($_GET['page'], $get_params).'";return false;'))."</td>";
        }
        
        echo "</tr>";
    }
    
    public function column_default($item, $column_name)
    {
        if(!isset($this->columns[$column_name]))
        {
            return false;
        }
        
        $column   = $this->columns[$column_name];
        $value    = null;
        
        if(!empty($column['content']))
        {
            $value = is_callable($column['content']) ? call_user_func_array($column['content'], array($item, $column_name)) : $column['content'];
        }
        
        $value    = isset($item[$column_name]) ? $item[$column_name] : $value;
        
        if(is_null($value))
        {
            return '';
        }
        
        $actions  = !empty($column['actions']) ? $column['actions'] : array();
        
        if(!empty($actions))
        {
            $actions = is_callable($actions) ? call_user_func_array($actions, array($item, $column_name)) : (is_array($actions) ? $actions : null);
            
            if($actions)
            {
                return sprintf('%1$s %2$s', $value, $this->row_actions($actions));
            }
        }
        
        $modifier = isset($column['modifier']) ? $column['modifier'] : false;
        
        if($modifier)
        {
            return call_user_func_array($column['modifier'], array($value, $item));
        }
        
        return $value;
    }
    
    public function prepare_items() 
    {
        $columns  = $this->get_columns();
        $hidden   = $this->get_columns_hidden();
        $sortable = $this->get_columns_sortable();
        	    
        $per_page = $this->get_items_per_page('fa_list_per_page', $this->per_page);
        
        $this->total_items = $this->get_count_rows();
        
        $this->set_pagination_args( array(
            'total_items' => $this->total_items,                  
            'per_page'    => $this->per_page                   
        ));
        
        $this->items  = $this->get_rows();
  
        if($this->items)
        {
            if($this->filters && !isset($this->columns[self::COLUMN_ACTIONS]))
            {
                $columns[self::COLUMN_ACTIONS]  = '';
            }
            
            foreach($this->items as $key => $item)
            {
                if($this->model)
                {
                    $item['ID'] = $item[$this->model->get_field_id()];
                }
                else
                {
                    $item['ID'] = $item[$this->get_primary_column_name()];
                }

                $this->items[$key] = $item;
            }
        } 
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
    
    protected function _init_orderby($args)
    {
        $this->orderby = isset($_GET['orderby'])   ? $_GET['orderby'] : (!empty($args['orderby']) ? $args['orderby'] : null);
        $this->order   = isset($_GET['order'])     ? $_GET['order']   : (!empty($args['order'])   ? $args['order']   : null);
                
        if(empty($this->columns[$this->orderby]))
        {
            $this->orderby = null;
            $this->order   = null;
        }
    }
    
    
    protected function _init_filters()
    {
        if($this->columns)
        {
            foreach($this->columns as $name => $column)
            {
                if($name != self::COLUMN_ACTIONS && !empty($column['filter']))
                {
                    $filter         = $column['filter'];
                    $filter_name    = isset($filter['name']) ? $filter['name'] : $name;
                    $filter['name'] = $filter_name;
                    
                    $input_value    = isset($_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name]) ? $_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name] : '';
                    $input_name     = self::FILTERS_INPUT_NAME.'['.$this->id.']['.$filter_name.']';
                    $input_type     = isset($filter['input_type']) ? $filter['input_type'] : (in_array($filter['type'],array('text','range','input')) ? 'text' : $filter['type']);
                    
                    $input    = array('type' => $input_type,'name' => $input_name, 'value' =>  $input_value, 'placeholder' => !empty($filter['placeholder']) ? $filter['placeholder'] : '');
                    $operator = FastAdminModel::OPERATOR_LIKE_BOTH;
                    
                    if(in_array($filter['type'], array('range','between')))
                    {
                        $input['name'] = array(
                            'from' => $input_name.'[from]',
                            'to'   => $input_name.'[to]',
                        );
                        
                        $input_value_from  = isset($_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name]['from']) ? $_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name]['from'] : '';
                        $input_value_to    = isset($_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name]['to'])   ? $_GET[self::FILTERS_INPUT_NAME][$this->id][$filter_name]['to']   : '';
                       
                        $input['value'] = array(
                            'from' => $input_value_from,
                            'to'   => $input_value_to,
                        );
                        
                        $placeholder_from = is_array($filter['placeholder']) ? $filter['placeholder']['from'] : '';
                        $placeholder_to   = is_array($filter['placeholder']) ? $filter['placeholder']['to'] : '';
                        
                        $input['placeholder'] = array(
                            'from' => $placeholder_from,
                            'to'   => $placeholder_to,
                        );
                        
                        $operator = FastAdminModel::OPERATOR_BETWEEN;
                    }
                    
                    $filter['operator'] = isset($filter['operator']) ? $filter['operator'] : $operator;
                    $input['class'] = isset($filter['class']) ? $filter['class'] : '';
                    
                    if($input['type'] == 'date')
                    {
                        $input['type'] = 'text';
                    }
                    
                    $filter['input']      = $input;
                    $this->columns[$name]['filter'] = $filter;
                    $this->filters[$name] = $filter;
                }
            }            
        }        
    }
    
    public function display()
    {
        ?>
        <form id="fa-list-<?php echo $this->formid;?>" action="" method="GET" autocomplete="off">
            <?php foreach($_GET as $field => $value){ if($field != self::FILTERS_INPUT_NAME){ ?>
                <input type='hidden' name='<?php echo $field;?>' value='<?php echo $value;?>' />
            <?php } } ?>
            <?php parent::display(); ?>
        </form>
        <?php
    }
    
    /**
     * Generate the table navigation above or below the table
     *
     * @since 3.1.0
     * @param string $which
     */
    protected function display_tablenav( $which ) {
            if ( $this->nonce_field && 'top' === $which ) {
                    wp_nonce_field( 'bulk-' . $this->_args['plural'] );
            }
            ?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ( $this->has_items() ): ?>
            <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
            </div>
            <?php endif;
            $this->extra_tablenav( $which );
            $this->pagination( $which );
?>

            <br class="clear" />
    </div>
<?php
	}
        
    protected function _get_filters_values()
    {
        $get_filters    = isset($_GET[self::FILTERS_INPUT_NAME][$this->id]) ? $_GET[self::FILTERS_INPUT_NAME][$this->id] : array();
        $filters_values = array();
        
        if(!empty($get_filters))
        {
            foreach($get_filters as $key => $value)
            {
                if(isset($this->filters[$key]) && !empty($value))
                {
                    $fieldname = isset($this->filters[$key]['fieldname']) ? $this->filters[$key]['fieldname'] : (isset($this->columns[$key]['fieldname']) ? $this->columns[$key]['fieldname'] : $key);
                    $value = isset($this->filters[$key]['filter_value']) && is_callable($this->filters[$key]['filter_value']) ? call_user_func_array($this->filters[$key]['filter_value'], array($value)) : $value;
                    
                    $filters_values[$fieldname] = array(
                        'value'      => $value,
                        'operator'   => isset($this->filters[$key]['operator']) ? $this->filters[$key]['operator'] : FastAdminDB::OPERATOR_LIKE_BOTH
                    );
                }
            }
        }
        
        return $this->where + $filters_values;
    }
    
    public function __toString()
    {
        ob_start();
        $this->display();
        $output = ob_get_clean();
        return $output;
    }
}