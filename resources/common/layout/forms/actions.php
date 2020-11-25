<?php switch($action['type']){ 
    case 'reset':
    case 'submit':?>
        <input <?php echo $action['attrs_string'];?>/>
    <?php break;
    case 'button': ?>
        <button <?php echo $action['attrs_string'];?>><?php echo $action['content'];?></button>
    <?php break;
    case 'href': ?>
        <a <?php echo $action['attrs_string'];?>><?php echo $action['content'];?></a>
    <?php break;
}               
