<?php


class content extends Admin_Controller {
    
    
   	public function __construct()
	{
		parent::__construct();
        
        // Restirc the access	
        $this->auth->restrict('Pages.Content.View');
        
        // Load required classes
        $this->load->model('page_model','page_m');
        $this->load->model('page_chunk_model', 'page_chunk_m');
        $this->load->model('page_layouts_model','page_layouts_m');
        $this->load->helper('array');
        // Load language file
        $this->lang->load('content');
        
        // Set module sub-navigation block
        Template::set_block('sub_nav', 'content/_sub_nav');
        Template::set('footer_text','Module based on <a href="http://www.pyrocms.com/" target="_blank">PyroCMS</a> Pages Module');
	}
    
   	public function index()
	{  
        $this->load->helper('tree');
        Assets::add_js(array('jquery-ui-1.8.13.min.js','jquery.ui.nestedSortable.js','jquery.cooki.js','jquery.stickyscroll.js'));
        Assets::add_module_js('pages','index.js');
        Assets::add_module_css('pages', 'index.css');
        Template::set('toolbar_title', lang('content_manage'));
        Template::set('pages',$this->page_m->get_page_tree());
        Template::render();
        echo 'lol';
	}
    
    
    public function create($parent_id = 0)
    {
        if(1 == 5) {
            
        } else {
 			$page = array();
			$page['chunks'] = array(array(
				'id' => 'NEW',
				'slug' => 'default',
				'body' => '',
				'type' => 'wysiwyg-advanced',
			));
        }
        
        // Loop through each rule
		foreach ($this->page_m->fields() as $field)
		{
			if ($field === 'restricted_to[]' || $field === 'strict_uri')
			{
				$page['restricted_to'] = set_value($field, array('0'));
				// we'll set the default for strict URIs here also
				$page['strict_uri'] = 1;

				continue;
			}

			$page[$field] = set_value($field);
		}
        
		$parent_page = array();
		// If a parent id was passed, fetch the parent details
		if ($parent_id > 0)
		{
			$page['parent_id'] = $parent_id;
			$parent_page = $this->page_m->get($parent_id);
		}
        
        // Set some data that both create and edit forms will need
		$this->_form_data();
        
        Template::set('toolbar_title', lang('content_new_page'));
        Template::set('page', $page);
        Template::set('parent_page',$parent_page);
        Template::set_view('content/form');
        Template::render();
    }
    
    public function order()
    {
        $order	= $this->input->post('order');
		$data	= $this->input->post('data');
		$root_pages	= isset($data['root_pages']) ? $data['root_pages'] : array();

		if (is_array($order))
		{
			//reset all parent > child relations
			$this->page_m->update_all(array('parent_id' => 0));

			foreach ($order as $i => $page)
			{
				$id = str_replace('page_', '', $page['id']);
				
				//set the order of the root pages
				$this->page_m->update($id, array('order' => $i), true);

				//iterate through children and set their order and parent
				$this->page_m->_set_children($page);
			}

			// rebuild page URIs
			$this->page_m->update_lookup($root_pages);

			//$this->pyrocache->delete_all('navigation_m');
			//$this->pyrocache->delete_all('page_m');

			//Events::trigger('post_page_order', array($order, $root_pages));
            $this->activity_model->log_activity($this->current_user->id, 'Updated pages order from: ' . $this->input->ip_address(), 'pages');
		}
    }
    
   	/**
	 * Get the details of a page.
	 *
	 * @param int $id The id of the page.
	 */
	public function ajax_page_details($id)
	{
		$page = $this->page_m->get($id);

		$this->load->view('content/ajax/page_details', array('page' => $page));
	}
    
    /**
	 * Sets up common form inputs.
	 *
	 * This is used in both the creation and editing forms.
	 */
	private function _form_data()
	{
		$page_layouts = $this->page_layouts_m->find_all();
		$this->template->page_layouts = array_for_select($page_layouts, 'id', 'title');

		// Load navigation list
		/*$this->load->model('navigation/navigation_m');
		$navigation_groups = $this->navigation_m->get_groups();
		$this->template->navigation_groups = array_for_select($navigation_groups, 'id', 'title');*/
        $this->load->model('roles/role_model');
        $groups = $this->role_permission_model->find_all_roles();
		foreach ($groups as $group)
		{
			$group->role_name !== 'Administrator' && $group_options[$group->role_id] = $group->role_name;
		}
		$this->template->group_options = $group_options;
/*
		$this->template
			->append_js('jquery/jquery.tagsinput.js')
			->append_js('jquery/jquery.cooki.js')
			->append_js('module::form.js')
			->append_css('jquery/jquery.tagsinput.css');*/
	}
}