<?php switch($field['type']){
   case 'content': ?>
        <?php echo $field['content'];?>
   <?php break;
   case 'select': ?>
        <select <?php echo $field['attrs_string'];?>>
            <?php foreach($field['options'] as $value => $text) { ?><option value="<?php echo $value;?>" <?php echo $field['selected'] == $value ? 'selected' : '';?>><?php echo $text;?></option><?php } ?>
        </select>
   <?php break;
   case 'radio': ?>
        <?php foreach($field['options'] as $value => $text) { ?>
               <label <?php echo $field['label_option_attrs_string'];?>>
                    <input <?php echo $field['attrs_string'];?> value="<?php echo $value;?>" <?php echo $field['selected'] == $value ? 'checked' : '';?>>
                    <?php echo $text;?>
               </label>
        <?php } ?>
   <?php break;
   case 'textarea': ?>
        <textarea  <?php echo $field['attrs_string'];?>><?php echo $field['value'];?></textarea>
   <?php break;
   default: ?>
       <input <?php echo $field['attrs_string'];?>/>
   <?php break;
}
