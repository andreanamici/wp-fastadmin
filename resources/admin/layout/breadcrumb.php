<!-- FASTADMIN: breadcrumb -->
<?php if(!empty($data['breadcrumb'])){ ?>
<ul class="fa-breadcrumb">
    <?php foreach($data['breadcrumb'] as $crumb){ ?>
        <li>
            <?php if(!empty($crumb['url'])){ ?><a href="<?php echo $crumb['url'];?>"><?php } ?>
                <?php echo $crumb['title'];?>
            <?php if(!empty($crumb['url'])){ ?></a><?php } ?>
        </li>
    <?php } ?>
</ul>
<?php } ?>
<!-- END -->