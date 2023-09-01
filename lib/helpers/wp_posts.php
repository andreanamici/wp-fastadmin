<?php 

if(!function_exists('fa_get_add_post_link'))
{
    function fa_get_add_post_link($post_type, array $query_args = [])
    {
        $query_args = ['post_type'=>$post_type] + $query_args;
        return add_query_arg($query_args,admin_url('post-new.php'));
    }
}


if(!function_exists('fa_get_delete_post_link'))
{
    function fa_get_delete_post_link($post, $force_delete = false, array $query_args = [])
    {
        return add_query_arg($query_args, get_delete_post_link($post, '', $force_delete));
    }
}