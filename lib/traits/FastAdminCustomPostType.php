<?php

namespace FastAdmin\lib\traits;

use FastAdmin\lib\classes\FastAdminFormValidation;

/**
 * @property FastAdminFormValidation $form_validation
 */
trait FastAdminCustomPostType
{        
    protected $post;

    protected $autosave = true;

    protected function registerCustomPostType($postType, array $args)
    {
        //Add action before form
        add_action('edit_form_top', [$this, 'beforeForm'],1, 2);

        register_post_type( $postType, $args );
      
        // Set $this->post globally
        add_action('add_meta_boxes', [$this, 'setPostFromGlobals']);
        add_action('save_post_'.static::POST_TYPE, [$this, 'setPostFromGlobals'],1);

        // Admin page manage 
        add_action('add_meta_boxes', [$this, 'addMetaboxes'],10);
        
        // Form validation
        add_action('save_post_'.static::POST_TYPE, [$this, 'beforeSave'], 2);

        // Save post type
        add_action('save_post_'.static::POST_TYPE, [$this, 'savePost'],3);

        if(!$this->autosave){
            add_action( 'admin_enqueue_scripts', function(){
                if ( static::POST_TYPE == get_post_type() ){
                    wp_dequeue_script( 'autosave' );
                }
            });
        }

        add_action('delete_post', function($postID, \WP_Post $post){
            if($post->post_type == static::POST_TYPE){
                return $this->deletePost($postID, $post);
            }
        } , 2, 3);
    }


    protected function registerCustomPostStatus($post_type, $status, $label)
    {
        register_post_status( $status, array(
            'label'                     => _x($label, 'post' ),
            'label_count'               => _n_noop( $label.' <span class="count">(%s)</span>', $label.' <span class="count">(%s)</span>'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true
        ));

        $post_status_callback =  function() use($status, $label, $post_type){
            global $post;
            if($post->post_type == $post_type){
                $selected          = $post->post_status == $status;
                $selected_attr     = $selected ? ' selected=\"selected\"' : '';
                echo '<script>
                        jQuery(document).ready(function($){
                            var $select = $("select#post_status");
                            $select.append("<option value=\"'.$status.'\" '.$selected_attr.'>'.$label.'</option>");
                    ';
                            if($post->post_status == $status){
                                echo '$("span#post-status-display").html("'.$label.'");
                                      $("input#save-post").val("Save '.$label.'");';
                            }
                echo '
                            $("a.save-post-status").on("click", function(){
                                if( $select.val() == "'.$status.'" ){
                                    $("input#save-post").val("Save as '.$label.'");
                                }
                            });

                            $("#post").submit(function(){
                                if( $select.val() == "'.$status.'" ){
                                    $("#save-post").val("Save '.$label.'");
                                }
                            });
                        });
                    </script>';
            }
        };
        
        add_action('admin_footer-post.php',$post_status_callback);
        add_action('admin_footer-post-new.php', $post_status_callback);
    }

    public function beforeForm(\WP_Post $post)
    {
        if($post->post_type == static::POST_TYPE){
            $this->setPostGlobal($this->post);
        }
    }

    public function buildPost(\WP_Post $post){
        return $post;
    }

    public function setPostFromGlobals(){
        global $post;
        if($post){
            $post = $this->buildPost($post);
            $this->setPost($post);
        }
        return $this;
    }

    public function setPostGlobal(\WP_Post $post){
        $this->post      = $post;
        $GLOBALS['post'] = $post;
        return $this;
    }

    public function getPostGlobal(){
        global $post;
        return $post;
    }

    public function getPost(){
        if($this->post){
            return $this->post;
        }
        return $this->getPostGlobal();
    }

    public function setPost(\WP_Post $post){
        $this->post = $post;
        $this->setPostGlobal($this->post);
        return $this;
    }

    public function addMetaboxes(){
    }

    public function addMetaboxesBeforeEditor()
    {
        add_action('edit_form_after_title',function($post){
            do_meta_boxes(get_current_screen(), 'before_editor', $post);
        });
    }

    protected function setFormValidationRules(int $post_ID){
        return true;
    }

    protected function runValidation(int $post_ID)
    {
        if($this->form_validation->validate()){
            return true;
        }

        $autosaving =  defined('DOING_AUTOSAVE') ? DOING_AUTOSAVE : false;
        if($autosaving){
            return true;
        }

        fa_wp_set_admin_notice('errors', _f('Form data is not valid: '.$this->form_validation->get_errors_string()));
        return fa_redirect_wp_post_page($post_ID, ['message' => 0]);

        return true;
    }

    public function beforeSave(int $post_ID)
    {
        if(empty($_POST)){
            return true;
        }

        $this->setFormValidationRules($post_ID);

        $this->runValidation($post_ID);

        return true;
    }

    public function savePost(int $post_ID)
    {
    }

    public function deletePost(int $post_ID)
    {
    }

    public function registerCustomAdminColumnsOrder($post_type, $callable){
        add_filter ( 'manage_'.$post_type.'_posts_columns', function($columns) use($callable){
            return call_user_func_array($callable, [$columns]);
        });
    }

    public function registerCustomAdminColumn( $column_title, $post_type, $callable, $order_by = false, $order_by_field_is_meta = false )
    {

        // Column Header
        add_filter( 'manage_' . $post_type . '_posts_columns', function( $columns ) use ($column_title) {
            $columns[ sanitize_title($column_title) ] = $column_title;
            return $columns;
        } );
    
        // Column Content
        add_action( 'manage_' . $post_type . '_posts_custom_column' , function( $column, $post_id ) use ($column_title, $callable) {
            if( sanitize_title($column_title) === $column){
              $result = call_user_func_array($callable, [$post_id]);
              if(is_string($result)){
                echo $result;
              }
            }
        }, 10, 2 );
    
        // OrderBy Set?
        if( !empty( $order_by ) ) {
    
          // Column Sorting
          add_filter( 'manage_edit-' . $post_type . '_sortable_columns', function ( $columns ) use ($column_title, $order_by) {
              $columns[ sanitize_title($column_title) ] = $order_by;
              return $columns;
          } );
    
          // Column Ordering
          add_action( 'pre_get_posts', function ( $query ) use ($order_by, $order_by_field_is_meta) {
              if( ! is_admin() || ! $query->is_main_query() )
                return;
    
              if ( sanitize_key($order_by) === $query->get( 'orderby') ) {
                  if($order_by_field_is_meta){
                      $query->set( 'orderby', 'meta_value' );
                      $query->set( 'meta_key', sanitize_key($order_by) );
                  }
                  else {
                      $query->set( 'orderby', sanitize_key($order_by) );
                  }
              }
          } );
      
        }
    
    }
}