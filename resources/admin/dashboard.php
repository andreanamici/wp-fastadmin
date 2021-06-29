<div class='wrap'>
        
    <?php fa_resource_include('admin/layout/header',array('data' => $data));?>
    
    <div id="poststuff">
    </div>
    <div class="fa-main-wrapper">
    <?php if(!empty($data['mainpages'])) { ?>
        <div class="fa-main-list-actions">
            <h2>Ciao <?php echo fa_get_current_user_data('display_name');?>, cosa vuoi fare?</h2><hr />
            <ul>
                <?php foreach($data['mainpages'] as $action => $page){ ?>
                    <li><a href="<?php echo fa_action_path($action);?>"><?php echo !empty($page['mainpage_title']) ? $page['mainpage_title'] : $page['page_title'];?></a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    </div>
    <?php fa_resource_include('admin/layout/footer',array('data' => $data));?>

</div>