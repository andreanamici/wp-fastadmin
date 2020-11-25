<!-- FORM GENERATED -->
<form <?php echo $form['attrs_string'];?>>
    <table class="form-table">
        <?php 
        if(!empty($form['rows'])){ 
           foreach($form['rows'] as $row){ 
             foreach($row['fields'] as $field){
                fa_resource_include($row['template'], array('form' => $form, 'row' => $row, 'fields' => $row['fields'],'field' => $field));
             }
           } 
        }
        if(!empty($form['actions'])){ ?>
            <tr>
                <td colspan="3">
                    <?php foreach($form['actions'] as $action){ ?>
                        <?php fa_resource_include($action['template'], array('form' => $form , 'action' => $action)); ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</form>
<!-- END -->