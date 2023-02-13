<div class="fa_rowered" <?php echo $row['attrs_string'];?>>
    <?php if(!empty($field['label'])){ ?>
        <div  class="fa_rowered_inner">
            <label <?php echo $field['label']['attrs_string'];?>>
				<?php echo $field['label']['content'];?>
				<?php /*echo $field['rules'][0] == 'required' ? '<span style="color:red">*</span>' : ''; */?>
			</label>
        </div>
    <?php } ?>
    <div class="fa_rowered_outer">
        <?php 
        echo !empty($field['before']) ? $field['before'] : '';
        
        fa_resource_include($field['template'], array('form' => $form, 'field' => $field));
        
        echo !empty($field['after']) ? $field['after'] : '';
        
        echo $field['errors'];
        ?>
    </div>
</div>