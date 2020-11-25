<tr <?php echo $row['attrs_string'];?>>
    <?php if(!empty($field['label'])){ ?>
        <th>
            <label <?php echo $field['label']['attrs_string'];?>><?php echo $field['label']['content'];?></label>
        </th>
    <?php } ?>
    <td>
        <?php 
        echo !empty($field['before']) ? $field['before'] : '';
        
        fa_resource_include($field['template'], array('form' => $form, 'field' => $field));
        
        echo !empty($field['after']) ? $field['after'] : '';
        
        echo $field['errors'];
        ?>
    </td>
</tr>