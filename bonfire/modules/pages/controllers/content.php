<?php


class content extends Admin_Controller {
    
   	public function __construct()
	{
		parent::__construct();
        
        // Restirc the access	
        $this->auth->restrict('Pages.Content.View');
        
        // Prepare Javascript base data
        M_Assets::add_data('bonfire','{ \'lang\' : {} }',false,false);
        M_Assets::add_data('SITE_URL',site_url());
        M_Assets::add_data('BASE_URL',base_url());
        M_Assets::add_data('bonfire.csrf_cookie_name','default_csrf_cookie',true,false);
        M_Assets::add_data('bonfire.foreign_characters', '[{"search":"\/\u00e4|\u00e6|\u01fd\/","replace":"ae"},{"search":"\/\u00f6|\u0153\/","replace":"oe"},{"search":"\/\u00fc\/","replace":"ue"},{"search":"\/\u00c4\/","replace":"Ae"},{"search":"\/\u00dc\/","replace":"Ue"},{"search":"\/\u00d6\/","replace":"Oe"},{"search":"\/\u00c0|\u00c1|\u00c2|\u00c3|\u00c4|\u00c5|\u01fa|\u0100|\u0102|\u0104|\u01cd|\u0391|\u0386\/","replace":"A"},{"search":"\/\u00e0|\u00e1|\u00e2|\u00e3|\u00e5|\u01fb|\u0101|\u0103|\u0105|\u01ce|\u00aa|\u03b1|\u03ac\/","replace":"a"},{"search":"\/\u00c7|\u0106|\u0108|\u010a|\u010c\/","replace":"C"},{"search":"\/\u00e7|\u0107|\u0109|\u010b|\u010d\/","replace":"c"},{"search":"\/\u00d0|\u010e|\u0110|\u0394\/","replace":"D"},{"search":"\/\u00f0|\u010f|\u0111|\u03b4\/","replace":"d"},{"search":"\/\u00c8|\u00c9|\u00ca|\u00cb|\u0112|\u0114|\u0116|\u0118|\u011a|\u0395|\u0388\/","replace":"E"},{"search":"\/\u00e8|\u00e9|\u00ea|\u00eb|\u0113|\u0115|\u0117|\u0119|\u011b|\u03ad|\u03b5\/","replace":"e"},{"search":"\/\u011c|\u011e|\u0120|\u0122|\u0393\/","replace":"G"},{"search":"\/\u011d|\u011f|\u0121|\u0123|\u03b3\/","replace":"g"},{"search":"\/\u0124|\u0126\/","replace":"H"},{"search":"\/\u0125|\u0127\/","replace":"h"},{"search":"\/\u00cc|\u00cd|\u00ce|\u00cf|\u0128|\u012a|\u012c|\u01cf|\u012e|\u0130|\u0397|\u0389|\u038a|\u0399|\u03aa\/","replace":"I"},
        {"search":"\/\u00ec|\u00ed|\u00ee|\u00ef|\u0129|\u012b|\u012d|\u01d0|\u012f|\u0131|\u03b7|\u03ae|\u03af|\u03b9|\u03ca\/","replace":"i"},{"search":"\/\u0134\/","replace":"J"},{"search":"\/\u0135\/","replace":"j"},{"search":"\/\u0136|\u039a\/","replace":"K"},{"search":"\/\u0137|\u03ba\/","replace":"k"},{"search":"\/\u0139|\u013b|\u013d|\u013f|\u0141|\u039b\/","replace":"L"},{"search":"\/\u013a|\u013c|\u013e|\u0140|\u0142|\u03bb\/","replace":"l"},{"search":"\/\u00d1|\u0143|\u0145|\u0147|\u039d\/","replace":"N"},{"search":"\/\u00f1|\u0144|\u0146|\u0148|\u0149|\u03bd\/","replace":"n"},{"search":"\/\u00d2|\u00d3|\u00d4|\u00d5|\u014c|\u014e|\u01d1|\u0150|\u01a0|\u00d8|\u01fe|\u039f|\u038c|\u03a9|\u038f\/","replace":"O"},{"search":"\/\u00f2|\u00f3|\u00f4|\u00f5|\u014d|\u014f|\u01d2|\u0151|\u01a1|\u00f8|\u01ff|\u00ba|\u03bf|\u03cc|\u03c9|\u03ce\/","replace":"o"},{"search":"\/\u0154|\u0156|\u0158|\u03a1\/","replace":"R"},{"search":"\/\u0155|\u0157|\u0159|\u03c1\/","replace":"r"},{"search":"\/\u015a|\u015c|\u015e|\u0160|\u03a3\/","replace":"S"},{"search":"\/\u015b|\u015d|\u015f|\u0161|\u017f|\u03c3|\u03c2\/","replace":"s"},{"search":"\/\u0162|\u0164|\u0166\u03a4\/","replace":"T"},{"search":"\/\u0163|\u0165|\u0167|\u03c4\/","replace":"t"},{"search":"\/\u00d9|\u00da|\u00db|\u0168|\u016a|\u016c|\u016e|\u0170|\u0172|\u01af|\u01d3|\u01d5|\u01d7|\u01d9|\u01db\/","replace":"U"},
        {"search":"\/\u00f9|\u00fa|\u00fb|\u0169|\u016b|\u016d|\u016f|\u0171|\u0173|\u01b0|\u01d4|\u01d6|\u01d8|\u01da|\u01dc|\u03c5|\u03cd|\u03cb\/","replace":"u"},{"search":"\/\u00dd|\u0178|\u0176|\u03a5|\u038e|\u03ab\/","replace":"Y"},{"search":"\/\u00fd|\u00ff|\u0177\/","replace":"y"},{"search":"\/\u0174\/","replace":"W"},{"search":"\/\u0175\/","replace":"w"},{"search":"\/\u0179|\u017b|\u017d|\u0396\/","replace":"Z"},{"search":"\/\u017a|\u017c|\u017e|\u03b6\/","replace":"z"},{"search":"\/\u00c6|\u01fc\/","replace":"AE"},{"search":"\/\u00df\/","replace":"ss"},{"search":"\/\u0132\/","replace":"IJ"},{"search":"\/\u0133\/","replace":"ij"},{"search":"\/\u0152\/","replace":"OE"},{"search":"\/\u0192\/","replace":"f"},{"search":"\/\u03b8\/","replace":"th"},{"search":"\/\u03c7\/","replace":"x"},{"search":"\/\u03c6\/","replace":"f"},{"search":"\/\u03be\/","replace":"ks"},{"search":"\/\u03c0\/","replace":"p"},{"search":"\/\u03b2\/","replace":"v"},{"search":"\/\u03bc\/","replace":"m"},{"search":"\/\u03c8\/","replace":"ps"}]',false,false);
        
        // Load required classes
        $this->load->model('page_model','page_m');
        $this->load->model('page_chunk_model', 'page_chunk_m');
        $this->load->helper(array('array','date'));
        // Load language file
        $this->lang->load('content');
        
        Assets::add_js(array('jquery-ui-1.8.13.min.js','jquery.cooki.js','pages/jquery.stickyscroll.js'),'external',true);
        switch($this->router->fetch_method())
        {
            case 'index':
                Assets::add_js('pages/jquery.ui.nestedSortable.js');                
            break;
        }
        Assets::add_js('pages/main.js');   
        
        // Prepare Javascript language array
        M_Assets::add_data('bonfire.lang.dialog_message',lang('content_delete_dialog'),true,false);
        M_Assets::add_data('bonfire.lang.delete',lang('content_delete'),true,false);
             
        // Set module sub-navigation block
        Template::set_block('sub_nav', 'content/_sub_nav');
        Template::set('footer_text','Module based on <a href="http://www.pyrocms.com/" target="_blank">PyroCMS</a> Pages Module');
	}
    
   	public function index()
	{  
        $this->load->helper('tree');
        Assets::add_js('pages/index.js'); 
        Assets::add_css('pages/index.css');
        Template::set('toolbar_title', lang('content_manage'));
        Template::set('pages',$this->page_m->get_page_tree());
        Template::render();
	}
    
    public function duplicate($id, $parent_id = null)
    {
        $this->load->helper('string');
        
        $page = $this->page_m->get($id);
        
        // Steal their children
		$children = $this->page_m->find_all_by('parent_id', $id);
        
        $new_slug = $page['slug'];
        
        // No parent around? Do what you like
		if (is_null($parent_id))
		{
			do
			{
				// Turn "Foo" into "Foo 2"
				$page['title'] = increment_string($page['title'], ' ', 2);

				// Turn "foo" into "foo-2"
				$page['slug'] = increment_string($page['slug'], '-', 2);

				// Find if this already exists in this level
				$dupes = $this->page_m->count_by(array(
					'slug' => $page['slug'],
					'parent_id' => $page['parent_id'],
				));
			}
			while ($dupes > 0);
		}
        // Oop, a parent turned up, work with that
		else
		{
			$page['parent_id'] = $parent_id;
		}

        $page['restricted_to'] = null;
       	//$page['navigation_group_id'] = 0;
	    $page['is_home'] = 0;
        
        foreach($page['chunks'] as $chunk)
        {
           		$page['chunk_slug'][] = $chunk['slug'];
           		$page['chunk_type'][] = $chunk['type'];
           		$page['chunk_body'][] = $chunk['body'];
        }
        
        $new_page = $this->page_m->create($page,true);
        
        if($children !== false) {
            foreach ($children as $child)
    		{
    			$this->duplicate($child->id, $new_page['id']);
    		}
        }
        
        if ($parent_id === NULL)
		{
			redirect('admin/content/pages/edit/'.$new_page['id']);
		}
    }
    
    public function create($parent_id = 0)
    {
        // did they even submit?
		if ($input = $this->input->post())
		{
			// do they have permission to proceed?
			if ($input['status'] == 'live')
			{
                $this->auth->restrict('Pages.Content.PutLive','admin/content/pages');
			}

			// validate and insert
			if ($data = $this->page_m->create($input))
			{
				//Events::trigger('post_page_create', $data);

				Template::set_message(lang('content_success_page_create'),'success');

				// Redirect back to the form or main page
				$input['btnAction'] == 'save_back'
					? redirect('admin/content/pages')
					: redirect('admin/content/pages/edit/'.$data['id']);
			}
			else
			{
                Template::set_message(validation_errors('','<br />'),'error');
				// validation failed, we must repopulate the chunks form
				$chunk_slugs 	= $this->input->post('chunk_slug') ? array_values($this->input->post('chunk_slug')) : array();
				$chunk_bodies 	= $this->input->post('chunk_body') ? array_values($this->input->post('chunk_body')) : array();
				$chunk_types 	= $this->input->post('chunk_type') ? array_values($this->input->post('chunk_type')) : array();

				$page = array();
				$chunk_bodies_count = count($input['chunk_body']);
				for ($i = 0; $i < $chunk_bodies_count; $i++)
				{
					$page['chunks'][] = array(
						'id' 	=> $i,
						'slug' 	=> ! empty($chunk_slugs[$i]) 	? $chunk_slugs[$i] 	: '',
						'type' 	=> ! empty($chunk_types[$i]) 	? $chunk_types[$i] 	: '',
						'body' 	=> ! empty($chunk_bodies[$i]) 	? $chunk_bodies[$i] : '',
					);
				}
			}
		} else {
 			$page = array();
            $page['title'] = "";
            $page['slug'] = "";
            $page['status'] = "draft";
            $page['meta_title'] = "";
            $page['meta_keywords'] = "";
            $page['meta_description'] = "";
            $page['css'] = "";
            $page['js'] = "";
            $page['comments_enabled'] = 0;
            $page['rss_enabled'] = 0;
            $page['is_home'] = 0;
            $page['strict_uri'] = 1;
            $page['page_template'] = 'default';
			$page['chunks'] = array(array(
				'id' => 'NEW',
				'slug' => 'default',
				'body' => '',
				'type' => 'wysiwyg',
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
    
    public function edit($id = 0)
    {        
        // We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/content/pages');
        $this->auth->restrict('Pages.Content.Edit','admin/content/pages');
        
        // Retrieve the page data along with its chunk data as an array.
		$page = $this->page_m->get($id);
        
        // Turn the CSV list back to an array
		$page['restricted_to'] = explode(',', $page['restricted_to']);
        
        // Got page?
		if ( ! $page OR empty($page))
		{
			// Maybe you would like to create one?
			Template::set_message(lang('content_error_page_not_found'),'error');
			redirect('admin/pages/create');
		}
        
        // did they even submit?
		if ($input = $this->input->post())
		{
			// do they have permission to proceed?
			if ($input['status'] == 'live')
			{
				$this->auth->restrict('Pages.Content.PutLive','admin/content/pages');
			}

			// validate and insert
			if ($data = $this->page_m->edit($id, $input))
			{
				Template::set_message(sprintf(lang('content_success_page_edit'), $input['title']),'success');

				//Events::trigger('post_page_edit', $data);

				//$this->pyrocache->delete_all('page_m');
				//$this->pyrocache->delete_all('navigation_m');

				// Mission accomplished!
				$input['btnAction'] == 'save_back'
					? redirect('admin/content/pages')
					: redirect('admin/content/pages/edit/'.$data['id']);
			}
			else
			{
				// validation failed, we must repopulate the chunks form
				$chunk_slugs 	= $this->input->post('chunk_slug') ? array_values($this->input->post('chunk_slug')) : array();
				$chunk_bodies 	= $this->input->post('chunk_body') ? array_values($this->input->post('chunk_body')) : array();
				$chunk_types 	= $this->input->post('chunk_type') ? array_values($this->input->post('chunk_type')) : array();

				$page['chunks'] = array();
				$chunk_bodies_count = count($input['chunk_body']);
				for ($i = 0; $i < $chunk_bodies_count; $i++)
				{
					$page['chunks'][] = array(
						'id' 	=> $i,
						'slug' 	=> ! empty($chunk_slugs[$i]) 	? $chunk_slugs[$i] 	: '',
						'type' 	=> ! empty($chunk_types[$i]) 	? $chunk_types[$i] 	: '',
						'body' 	=> ! empty($chunk_bodies[$i]) 	? $chunk_bodies[$i] : '',
					);
				}
			}
		}
        
        // Loop through each validation rule
		foreach ($this->page_m->fields() as $field)
		{
			// Nothing to do for these two fields.
			if (in_array($field, array(/*'navigation_group_id', */'chunk_body[]')))
			{
				continue;
			}

			// Translate the data of restricted_to to something we can use in the form.
			if ($field === 'restricted_to[]')
			{
				$page['restricted_to'] = set_value($field, $page['restricted_to']);
				$page['restricted_to'][0] = ($page['restricted_to'][0] == '') ? '0' : $page['restricted_to'][0];
				continue;
			}

			// Set all the other fields
			$page[$field] = set_value($field, $page[$field]);
		}
        
        // If this page has a parent.
		if ($page['parent_id'] > 0)
		{
			// Get only the details for the parent, no chunks.
			$parent_page = $this->page_m->get($page['parent_id'], false);
		}
		else
		{
			$parent_page = false;
		}
        
        $this->_form_data();
        
        Template::set('toolbar_title', lang('content_edit_page'));
        Template::set('page', $page);
        Template::set('parent_page',$parent_page);
        Template::set_view('content/form');
        Template::render();        
    }
    
    public function delete($id = 0) 
    {
        // The user needs to be able to delete pages.
		$this->auth->restrict('Pages.Content.Delete','admin/content/pages');

		// @todo Error of no selection not handled yet.
		$ids = ($id) ? array($id) : $this->input->post('action_to');
        
        // Go through the array of slugs to delete
		if ( ! empty($ids))
		{
			foreach ($ids as $id)
			{                
				if ($id != 1 && $id != 2)
				{
					$deleted_ids = $this->page_m->delete($id);

					//$this->comments_m->where('module', 'pages')->delete_by('module_id', $id);

					// Wipe cache for this model, the content has changd
					//$this->pyrocache->delete_all('page_m');
					//$this->pyrocache->delete_all('navigation_m');
				}
				else
				{
					Template::set_message(lang('content_error_home_404_delete'),'error');
				}
			}

			// Some pages have been deleted
			if ( ! empty($deleted_ids))
			{
				//Events::trigger('post_page_delete', $deleted_ids);

				// Only deleting one page
				if ( count($deleted_ids) == 1 )
				{
					Template::set_message(sprintf(lang('content_success_page_delete'), $deleted_ids[0]),'success');
				}
				// Deleting multiple pages
				else
				{
				    Template::set_message(sprintf(lang('content_success_page_mass_delete'), count($deleted_ids)),'success');
				}
			}
			// For some reason, none of them were deleted
			else
			{
                Template::set_message(lang('content_info_delete_none'),'info');
			}
		}
        
        redirect('admin/content/pages');
    }
    
    
    public function order($id = 0)
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
        $this->load->library('M_Template');
        $this->load->language('application');
        
        $page_templates = M_Template::get_page_templates();
        Template::set('templates',$page_templates);
        
        M_Assets::add_data('bonfire.lang.alert_one_chunk',lang('content_one_chunk_dialog'),true,false);
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
        
        Assets::add_js('pages/form.js');
/*      
		$this->template
			->append_js('jquery/jquery.tagsinput.js')
			->append_js('jquery/jquery.cooki.js')
			->append_js('module::form.js')
			->append_css('jquery/jquery.tagsinput.css');*/
	}
}