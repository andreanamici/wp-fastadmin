
<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <?php fa_resource_include('admin/layout/header', array('data' => $data));?>

    <?php echo $data['listing']; ?>

    <?php fa_resource_include('admin/layout/footer', array('data' => $data));?>
</div>