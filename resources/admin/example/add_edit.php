<div class="wrap">
    <?php if (!defined('ABSPATH')) exit; ?>

    <?php fa_resource_include('admin/layout/header', array('data' => $data));?>
    <div class="wrap">
        <?php echo $data['form'];?>
    </div>
    <?php fa_resource_include('admin/layout/footer', array('data' => $data));?>
</div>