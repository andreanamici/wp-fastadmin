<!-- FASTADMIN: header -->
<?php fa_message(); ?>
<?php if(!empty($data['title'])){ ?>
<h1 class="wp-heading-inline"><?php echo $data['title'];?></h1>
<?php } ?>
<?php include 'title_actions.php' ?>
<?php if(!empty($data['subtitle'])){ ?>
<div class="fa-page-subtitle"><?php echo $data['subtitle'];?></div>
<?php } ?>
<?php include 'breadcrumb.php' ?>
<!-- FASTADMIN: endheader -->