
<div class="admin-box">
    <h3><?php 
        if($this->router->method == 'edit') :
            echo lang('content_edit_page');
        else :
            echo lang('content_new_page');
        endif;
    ?>
    </h3>
    <?php echo form_open(uri_string(), 'id="page-form" class="form-horizontal"'); ?>
    <?php echo form_hidden('parent_id', empty($page['parent_id']) ? 0 : $page['parent_id']); ?>
    <div class="tabbable">
        <ul class="nav nav-tabs">
			<li class="active">
				<a data-toggle="tab" href="#tab-content"><?php echo lang('content_tab_content') ?></a>
			</li>
			<li>
				<a data-toggle="tab" href="#tab-metadata"><?php echo lang('content_tab_metadata') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab-appearance"><?php echo lang('content_tab_appearance') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab-script"><?php echo lang('content_tab_script') ?></a>
			</li>
            <li>
                <a data-toggle="tab" href="#tab-options"><?php echo lang('content_tab_options') ?></a>
			</li>
        </ul>
        <div class="tab-content">
            <div id="tab-content" class="tab-pane active">
                <fieldset>
                    <div class="control-group">
                        <label for="title" class="control-label"><?php echo lang('content_title_label') ?></label>
                        <div class="controls">
                            <input name="title" type="text" id="title" value="<?php echo $page['title'] ?>" />
                        </div>
                        <span class="label label-important label-required"><?php echo lang('content_required_label') ?></span>
                    </div>
                    <div class="control-group">
                        <label for="slug" class="control-label"><?php echo lang('content_slug_label') ?></label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">
                                <?php if ( ! empty($page['parent_id'])): ?>
            						<?php echo site_url($parent_page['uri']); ?>/
            					<?php else: ?>
            						<?php echo site_url() . (config_item('index_page') ? '/' : ''); ?>
            					<?php endif; ?>                                
                                <?php if (in_array($page['slug'], array('home', '404'))): ?>
            						<?php echo form_hidden('slug', $page['slug']); ?>
            						</span><input name="slug" type="text" id="slug" value="<?php echo $page['slug'] ?>" disabled="disabled" />
            					<?php else: ?>
                                    </span><input name="slug" type="text" id="slug" value="<?php echo $page['slug'] ?>" />
            					<?php endif;?>                                
                                <?php if ($this->router->method == 'edit'): ?>
            						<?php echo form_hidden('old_slug', $page['slug']); ?>
            					<?php endif; ?>
                            </div>
                        </div>
                        <span class="label label-important label-required"><?php echo lang('content_required_label') ?></span>
                    </div>
                    <?php echo form_dropdown('status',array('live' => lang('content_live_label'),'draft' => lang('content_draft_label')),$page['status'],lang('content_status_label'),'id="status"') ?>     
                    <div id="chunks"> 
                        <?php $counts = count($page['chunks']) ?>
                        <?php foreach ($page['chunks'] as $chunk): ?>
                        <div class="control-group page-chunk">                        
                            <div class="controls">
                                <input type="text" name="chunk_slug[<?php echo $chunk['id']?>]" value="<?php echo $chunk['slug'] ?>" />
                                <select name="chunk_type[<?php echo $chunk['id'] ?>]">
                                    <option value="html"<?php echo ($chunk['type'] == 'html' ? ' selected=\"selected\"' : '') ?>>html</option>
                                    <option value="wysiwyg"<?php echo ($chunk['type'] == 'wysiwyg' ? ' selected=\"selected\"' : '') ?>>wysiwyg</option>
                                </select>
        						<div class="fright pr20">
        							<a href="javascript:void(0)"<?php echo ($counts == 1 ? ' style="opacity: 0.01;display: none;"' : ' ') ?>class="remove-chunk btn btn-danger"><i class="icon-remove icon-white"></i> <?php echo lang('content_delete') ?></a>
                                </div>
                                <br class="clear" /><br />
                                <textarea class="<?php echo $chunk['type'] ?>" style="width:90%;" rows="12" name="chunk_body[<?php echo $chunk['id'] ?>]" id="<?php echo $chunk['slug'].'_'.$chunk['id'] ?>"><?php echo $chunk['body'] ?></textarea>
                            </div>
                        </div>                
                        <?php endforeach; ?>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <a href="javascript:void(0);" class="add-chunk btn btn-success"><i class="icon-plus icon-white"></i> Add chunk</a>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="tab-metadata" class="tab-pane">
                <fieldset>
                    <?php echo form_input('meta title',$page['meta_title'], lang('content_meta_title_label'), 'id="meta title" maxlength="255"') ?>
                    <?php echo form_input('meta keywords',$page['meta_keywords'], lang('content_meta_keywords_label'), 'id="meta keywords" maxlength="255"') ?>
                    <div class="control-group">
                        <label for="meta description" class="control-label"><?php echo lang('content_meta_description_label') ?></label>
                        <div class="controls">
                            <textarea id="meta description" style="width:90%;" rows="5" name="meta description"><?php echo $page['meta_description'] ?></textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="tab-appearance" class="tab-pane">
                <fieldset>     
                    <div class="control-group">
                        <label for="page_template" class="control-label"><?php echo lang('content_page_template_label') ?></label>
                        <div class="controls">
                            <?php echo $page['page_template'] ?>
                            <select name="page_template" id="page_template">
                            <?php foreach($templates AS $name => $value) : ?>
                                <option<?php echo ($page['page_template'] == $value ? ' selected="selected" ' : ' ') ?>value="<?php echo $value; ?>"><?php echo $name ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>           
                    <div class="control-group">
                        <label for="css" class="control-label"><?php echo lang('content_css_label') ?></label>
                        <div class="controls">
                            <textarea id="css" style="width:90%;" rows="10" name="css"><?php echo $page['css'] ?></textarea>
                        </div>  
                    </div>
                </fieldset>
            </div>
            <div id="tab-script" class="tab-pane">
                <fieldset>
                    <div class="control-group">
                        <label for="js" class="control-label"><?php echo lang('content_js_label') ?></label>
                        <div class="controls">
                            <textarea id="js" style="width:90%;" rows="10" name="js"><?php echo $page['js'] ?></textarea>
                        </div>  
                    </div>
                </fieldset>
            </div>
            <div id="tab-options" class="tab-pane">
                <fieldset>
                    <div class="control-group">
                        <label for="comments_enabled" class="control-label"><?php echo lang('content_comments_enabled_label') ?></label>
                        <div class="controls">
                            <?php echo form_checkbox('comments_enabled',1,$page['comments_enabled'] == 1,'id="comments_enabled"') ?>
                            <p class="help-inline"><?php echo lang('content_comments_enabled_desc') ?></p>
                        </div>                        
                    </div>
                    <div class="control-group">
                        <label for="rss_enabled" class="control-label"><?php echo lang('content_rss_enabled_label') ?></label>
                        <div class="controls">
                            <?php echo form_checkbox('rss_enabled',1,$page['rss_enabled'] == 1,'id="rss_enable"d') ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="is_home" class="control-label"><?php echo lang('content_is_home_label') ?></label>
                        <div class="controls">
                            <?php echo form_checkbox('is_home',1,$page['is_home'] == 1,'id="is_home"') ?>
                            <p class="help-inline"><?php echo lang('content_is_home_desc') ?></p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="strict_uri" class="control-label"><?php echo lang('content_strict_uri_label') ?></label>
                        <div class="controls">
                            <?php echo form_checkbox('strict_uri',1,$page['strict_uri'] == 1,'id="strict_uri"') ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    
    
    
    </div>
    <div class="form-actions">
        <button name="btnAction" type="submit" class="btn btn-primary" value="save">
            <i class="icon-check icon-white"></i> <span><?php echo lang('bf_action_save') ?></span>
        </button>     
        <button name="btnAction" type="submit" class="btn btn-primary" value="save_back">
            <i class="icon-share icon-white"></i> <span><?php echo lang('content_save_back') ?></span>
        </button>   
        <?php echo anchor('admin/content/pages', '<i class="icon-ban-circle icon-white"></i> '.lang('bf_action_cancel'),'class="confirm btn btn-danger"') ?>
    </div>
    
    <?php echo form_close(); ?>






</div>