<?php

if(!empty($data['title_actions']))
{
    foreach($data['title_actions'] as $action)
    {
        ?>
            <a href="<?php echo $action['href'];?>" class="page-title-action" title="<?php echo !empty($action['title']) ? $action['title'] : '';?>"><?php echo $action['name'];?></a>
        <?php
    }
}