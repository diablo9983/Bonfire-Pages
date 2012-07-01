<input id="page-id" type="hidden" value="<?php echo $page['id']; ?>" />
<input id="page-uri" type="hidden" value="<?php echo ( ! empty($page['uri'])) ? $page['uri'] : $page['slug']; ?>" />

<h2><?php echo lang('content_detail_label'); ?></h2>
<div class="page-details">
    <p>
    	<strong>ID:</strong> #<?php echo $page['id']; ?>
    </p>
    <p>
    	<strong><?php echo lang('content_status_label'); ?>:</strong> <?php echo lang('content_' . $page['status'] . '_label'); ?>
    </p>
    <p>
    	<strong><?php echo lang('content_slug_label');?>:</strong>
    	<a href="<?php echo site_url('admin/content/pages/preview/'.$page['id']);?>?iframe" rel="modal-large" target="_blank">
    		<?php echo site_url(!empty($page['uri']) ? $page['uri'] : $page['slug']); ?>
    	</a>
    </p>
</div>

<!-- Meta data tab -->

<h2><?php echo lang('content_meta_label');?></h2>
<div class="page-details">
    <p>
    	<strong><?php echo lang('content_meta_title_label');?>:</strong> <?php echo !empty($page['meta_title']) ? $page['meta_title'] : '&mdash;'; ?>
    </p>
    <p>
    	<strong><?php echo lang('content_meta_keywords_label');?>:</strong> <?php echo !empty($page['meta_keywords']) ? $page['meta_keywords'] : '&mdash;'; ?>
    </p>
    <p>
    	<strong><?php echo lang('content_meta_desc_label');?>:</strong> <?php echo !empty($page['meta_description']) ? $page['meta_description'] : '&mdash;'; ?>
    </p>
</div>

<!-- Butrons -->

<br />
<div class="buttons">
	<?php echo anchor('admin/content/pages/create/' . $page['id'], '<i class="icon-plus"></i> '.lang('content_create_label'), 'class="btn"'); ?>
	<?php echo anchor('admin/content/pages/duplicate/' . $page['id'], '<i class="icon-repeat"></i> '.lang('content_duplicate_label'), 'class="btn"'); ?>
	<?php echo anchor('admin/content/pages/edit/' . $page['id'], '<i class="icon-pencil"></i> '.lang('content_edit'), 'class="btn"'); ?>
	<?php echo anchor('admin/content/pages/delete/' . $page['id'],  '<i class="icon-remove icon-white"></i> '.lang('content_delete'), 'class="confirm btn btn-danger"'); ?>
</div>