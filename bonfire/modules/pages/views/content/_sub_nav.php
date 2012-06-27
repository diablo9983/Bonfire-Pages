<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/content/pages') ?>"><?php echo lang('content_pages') ?></a>
	</li>
    <?php if ($this->auth->has_permission('Pages.Content.Create') !== false) : ?>
    <li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?>>
        <a href="<?php echo site_url(SITE_AREA . '/content/pages/create') ?>"><?php echo lang('content_create') ?></a>
    </li>
    <?php endif; ?>
</ul>