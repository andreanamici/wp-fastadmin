<?php switch($field['type']){
   case 'content': ?>
        <?php echo $field['content'];?>
   <?php break;
   case 'select': ?>
        <select <?php echo $field['attrs_string'];?>>
            <?php foreach($field['options'] as $key => $value) { ?>
               <?php if(is_array($value)){ ?>
                    <optgroup label="<?php echo $value['label'];?>">
                    <?php foreach($value['options'] as $key => $value) { ?>
                         <option value="<?php echo $key;?>" <?php echo $field['selected'] == $key ? 'selected' : '';?>><?php echo $value;?></option>
                    <?php } ?>
               <?php } else { ?>
                    <option value="<?php echo $key;?>" <?php echo $field['selected'] == $key ? 'selected' : '';?>><?php echo $value;?></option>
               <?php } ?>
            <?php } ?>
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
