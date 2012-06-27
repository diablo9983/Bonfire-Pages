
<div class="admin-box">
    <h3><?php echo lang('content_new_page') ?></h3>
    <?php echo form_open(uri_string(), 'id="page-form" class="form-horizontal"'); ?>
    <?php echo form_hidden('parent_id', empty($page['parent_id']) ? 0 : $page['parent_id']); ?>
    <div class="tabbable">
        <ul class="nav nav-tabs">
			<li class="active">
				<a data-toggle="tab" href="#tab_content"><?php echo lang('content_tab_content') ?></a>
			</li>
			<li>
				<a data-toggle="tab" href="#tab_metadatay"><?php echo lang('content_tab_metadata') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab_appearance"><?php echo lang('content_tab_appearance') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab_script"><?php echo lang('content_tab_script') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab_options"><?php echo lang('content_tab_options') ?></a>
			</li>
        </ul>
    
    
    
    
    </div>







</div>