<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <?php fa_resource_include('admin/layout/header', array('data' => $data));?>
        <table class="form-table" >
                <tr class="form-field form-required">
                    <th><label for="sms-phone">Nome e Cognome</label></th>
                    <td><?php echo $data['customer']['name'];?> <?php echo $data['customer']['last_name'];?></td>
                </tr>
                <?php if($data['customer']['email']){ ?>
                <tr class="form-field form-required">
                    <th>E-mail</th>
                    <td>                        
                        <a href="<?php echo fa_action_url('customers_email',array('id' => $data['id'], 'next' => $_GET['page']));?>" title="Invia una e-mail" class="fa-tooltip">
                            <?php echo $data['customer']['email'];?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
                <tr class="form-field form-required">
                    <th>Sesso</th>
                    <td><?php echo $data['customer']['gender_name'];?></td>
                </tr>
                <tr class="form-field form-required">
                    <th>Ha portato il certificato medico?</th>
                    <td><?php echo $data['customer']['has_medical_certificate'] ? 'Si' : 'No';?><?php if(!$data['customer']['has_medical_certificate'] && $data['customer']['medical_certificate_reminder_datetime']){ ?>, ultimo reminder inviato <?php echo fa_sql_to_date($data['customer']['medical_certificate_reminder_datetime'],'%A %d %B %Y alle %H:%i');?><?php } ?>
                        <?php if($data['customer']['can_send_medical_certificate_reminder']){ ?>
                        <a href="<?php echo fa_action_path('customers_send_medical_certificate_reminder',array('id' => $data['id'],'next' => $data['page'],'parent_id' => $data['id']));?>" class="button button-secondary">Invia un reminder</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php if(!empty($data['customer']['phone'])){ ?>
                <tr class="form-field form-required">
                    <th>Telefono</th>
                    <td>
                        <a href="<?php echo fa_action_url('customers_sms',array('id' => $data['id'], 'next' => $_GET['page']));?>" class="fa-tooltip" title="Invia un SMS">
                            <?php echo $data['customer']['phone'];?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
                <?php if(!empty($data['customer']['dob'])){ ?>
                <tr class="form-field form-required">
                    <th>Data di nascita</th>
                    <td><?php echo fa_sql_to_date($data['customer']['dob']);?><?php if($data['customer']['birthday_sent_datetime']){ ?>, ultimo augurio inviato <?php echo fa_sql_to_date($data['customer']['birthday_sent_datetime'],'%A %d %B %Y alle %H:%i');?><?php } ?></td>
                </tr>
                <?php } ?>
                <tr class="form-field form-required">
                    <th>Data registrazione</th>
                    <td><?php echo fa_sql_to_date($data['customer']['creation_datetime']); ?><td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="<?php echo fa_action_url('customers_sms',array('id' => $data['id'], 'next' => $_GET['page']));?>" class="button button-secondary fa-tooltip" title="Invia un SMS">SMS</a> 
                        <a href="<?php echo fa_action_url('customers_email',array('id' => $data['id'], 'next' => $_GET['page']));?>" class="button button-secondary fa-tooltip" title="Invia una e-mail">E-mail</a>
                        <a href="<?php echo fa_action_url('customers_edit',array('id' => $data['id']));?>" class="button button-primary">Modifica</a> 
                        <a href="<?php echo fa_action_url('customers_delete',array('id' => $data['id']));?>" class="button button-secondary fa-confirmbox"  data-confirm-text="Confermare l'eliminazione?">Elimina</a> 
                    </td>
                </tr>
        </table>
        
        <h3>Pacchetti acquistati</h3><hr />
        <a href="<?php echo fa_action_path('customers_products_add',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Nuovo pacchetto</a>
        <table class="form-table" >
            <?php echo $data['products_listing'];?>
        </table>
        
        <h3>Documenti</h3><hr />
        <a href="<?php echo fa_action_path('customers_files_add',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Carica un file</a>
        <table class="form-table" >
            <?php echo $data['files_listing'];?>
        </table>
        
        <h3>Appuntamenti fissati</h3><hr />
        <a href="<?php echo fa_action_path('appointments_add',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Nuovo appuntamento</a>
        <table class="form-table" >
            <?php echo $data['appointments_listing'];?>
        </table>
        
        <h3>Pagamenti</h3><hr />
        <a href="<?php echo fa_action_path('payments_add',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Nuovo pagamento</a>
        <table class="form-table" >
            <?php echo $data['payments_listing'];?>
        </table>
        
        <h3>Messaggi</h3><hr />
        <a href="<?php echo fa_action_path('customers_sms',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Nuovo SMS</a>
        <a href="<?php echo fa_action_path('customers_email',array('id' => $data['id'], 'next' => $_GET['page'], 'parent_id' => $data['id']));?>" class="button-primary">Nuova e-mail</a>
        <table class="form-table" >
            <?php echo $data['messages_listing'];?>
        </table>
        
        
    <?php fa_resource_include('admin/layout/footer', array('data' => $data));?>
</div>