<?php


class pages extends Front_Controller
{
    /**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
        $this->load->library('boncache');
        $this->load->library('m_template');
		$this->load->model('page_model','page_m');

		// This basically keeps links to /home always pointing to
		// the actual homepage even when the default_controller is
		// changed

		//No page is mentioned and we are not using pages as default
		// (eg blog on homepage)
		if ( ! $this->uri->segment(1) AND $this->router->default_controller != 'pages')
		{
			redirect('');
		}
	}
    
    public function _remap($method)
    {
        // This page has been routed to with pages/view/whatever
		if ($this->uri->rsegment(1, '').'/'.$method == 'pages/view')
		{
			$url_segments = $this->uri->total_rsegments() > 0 ? array_slice($this->uri->rsegment_array(), 2) : null;
		}
        // not routed, so use the actual URI segments
		else
		{
			if (($url_segments = $this->uri->uri_string()) === 'favicon.ico')
			{
				$favicon = Asset::get_filepath_img('theme::favicon.ico');

				if (file_exists(FCPATH.$favicon) && is_file(FCPATH.$favicon))
				{
					header('Content-type: image/x-icon');
					readfile(FCPATH.$favicon);
				}
				else
				{
					set_status_header(404);
				}

				exit;
			}

			$url_segments = $this->uri->total_segments() > 0 ? $this->uri->segment_array() : null;
		}
        
        // If it has .rss on the end then parse the RSS feed
		$url_segments && preg_match('/.rss$/', end($url_segments))
			? $this->_rss($url_segments)
			: $this->_page($url_segments);
    }
    
    /**
	 * Page method
	 *
	 * @param array $url_segments The URL segments.
	 */
    public function _page($url_segments)
    {
        $page = ($url_segments !== NULL)

			// Fetch this page from the database via cache
			? $this->boncache->model('page_m', 'get_by_uri', array($url_segments, TRUE))

			: $this->boncache->model('page_m', 'get_home');

		// If page is missing or not live (and not an admin) show 404
		if ( ! $page OR ($page->status == 'draft' AND ( ! isset($this->current_user->group) OR $this->current_user->group != 'admin')))
		{
			// Load the '404' page. If the actual 404 page is missing (oh the irony) bitch and quit to prevent an infinite loop.
			if ( ! ($page = $this->boncache->model('page_m', 'get_by_uri', array('404'))) )
			{
				show_error('The page you are trying to view does not exist and it also appears as if the 404 page has been deleted.');
			}
		}
        
        // the home page won't have a base uri
		isset($page->base_uri) OR $page->base_uri = $url_segments;
        
        // If this is a homepage, do not show the slug in the URL
		if ($page->is_home and $url_segments)
		{
			redirect('', 'location', 301);
		}
        
        // If the page is missing, set the 404 status header
		if ($page->slug == '404')
		{
			$this->output->set_status_header(404);
		}
        
        // Nope, it is a page, but do they have access?
		elseif ($page->restricted_to)
		{
			$page->restricted_to = (array)explode(',', $page->restricted_to);

			// Are they logged in and an admin or a member of the correct group?
			if ( ! $this->current_user OR (isset($this->current_user->group) AND $this->current_user->group != 'admin' AND ! in_array($this->current_user->group_id, $page->restricted_to)))
			{
				// send them to login but bring them back when they're done
				redirect('users/login/'.(empty($url_segments) ? '' : implode('/', $url_segments)));
			}
		}
        
        // We want to use the valid uri from here on. Don't worry about segments passed by Streams or 
		// similar. Also we don't worry about breadcrumbs for 404
		if ($url_segments = explode('/', $page->base_uri) AND count($url_segments) > 1)
		{
			// we dont care about the last one
			array_pop($url_segments);

			// This array of parents in the cache?
			if ( ! $parents = $this->pyrocache->get('page_m/'.md5(implode('/', $url_segments))))
			{
				$parents = $breadcrumb_segments = array();

				foreach ($url_segments as $segment)
				{
					$breadcrumb_segments[] = $segment;

					$parents[] = $this->pyrocache->model('page_m', 'get_by_uri', array($breadcrumb_segments, TRUE));
				}

				// Cache for next time
				$this->pyrocache->write($parents, 'page_m/'.md5(implode('/', $url_segments)));
			}

			foreach ($parents as $parent_page)
			{
				$this->template->set_breadcrumb($parent_page->title, $parent_page->uri);
			}
		}
        
        // Not got a meta title? Use slogan for homepage or the normal page title for other pages
		if ($page->meta_title == '')
		{
			$page->meta_title = $page->is_home ? $this->settings_lib->item('site_slogan') : $page->title;
		}

		// If this page has an RSS feed, show it
		if ($page->rss_enabled)
		{
            M_Template::append_metadata('<link rel="alternate" type="application/rss+xml" title="'.$page->meta_title.'" href="'.site_url(uri_string().'.rss').'" />');
		}
        
        // Grab all the chunks that make up the body
		$page->chunks = $this->db
			->order_by('sort')
			->get_where('page_chunks', array('page_id' => $page->id))
			->result();

		$chunk_html = '';
		foreach ($page->chunks as $chunk)
		{
			$chunk_html .= '<div class="page-chunk '.$chunk->slug.'">'.
				'<div class="page-chunk-pad">'.
				$chunk->body.
				'</div>'.
				'</div>'.PHP_EOL;
		}
        
        M_Template::set_metadata('keywords', $page->meta_keywords);
		M_Template::set_metadata('description', $page->meta_description);
        
        M_Template::set('content',$chunk_html);
        
        if ($page->css)
		{
			M_Template::append_metadata('
				<style type="text/css">
					'.$page->css.'
				</style>');
		}

		if ($page->js)
		{
			M_Template::append_metadata('
				<script type="text/javascript">
					'.$page->js.'
				</script>');
		}

        M_Template::render($page->page_template);
    }
}