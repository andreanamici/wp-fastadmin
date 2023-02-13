<!-- FORM GENERATED -->
<form <?php echo $form['attrs_string'];?>>
    <div class="fa_row_formtable">
        <?php 
        if(!empty($form['rows'])){ 
           foreach($form['rows'] as $row){ 
             ?>
             <div class="fa_row">
                <?php
                foreach($row['fields'] as $field){
                    fa_resource_include($row['template'], array('form' => $form, 'row' => $row, 'fields' => $row['fields'],'field' => $field));
                }
                ?>
             </div>
             <?php
           } 
        }
        if(!empty($form['actions'])){ ?>
            <div  class="fa_outer_column">
                <div class="fa_inner_column">
                    <?php foreach($form['actions'] as $action){ ?>
                        <?php fa_resource_include($action['template'], array('form' => $form , 'action' => $action)); ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</form>
<!-- END -->