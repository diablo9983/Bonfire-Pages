<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_default_pages extends Migration {

	// pages to migrate
	private $pages = array(
		array(
            'id'                => 1,
            'slug'				=> 'home',
			'title'				=> 'Home',
			'uri'				=> 'home',
			'parent_id'			=> 0,
			'layout_id'			=> 0,
			'css'				=> null,
			'js'				=> null,
			'meta_title'    	=> '',
			'meta_keywords' 	=> '',
			'meta_description' 	=> '',
			'rss_enabled'		=> 0,
			'comments_enabled'	=> 0,
			'status'			=> 'live',
			'created_on'		=> '',
			'restricted_to'		=> '0',
			'strict_uri'		=> 1,
			'is_home'			=> 1,
            'order'             => 1
        ),
        array(
            'id'                => 2,
            'slug'				=> '404',
			'title'				=> 'Page Missing',
			'uri'				=> '404',
			'parent_id'			=> 0,
			'layout_id'			=> 0,
			'css'				=> null,
			'js'				=> null,
			'meta_title'    	=> '',
			'meta_keywords' 	=> '',
			'meta_description' 	=> '',
			'rss_enabled'		=> 0,
			'comments_enabled'	=> 0,
			'status'			=> 'live',
			'created_on'		=> '',
			'restricted_to'		=> '0',
			'strict_uri'		=> 1,
			'is_home'			=> 0,
            'order'             => 2
        )
	);
    
    // page chunks to migrate
    private $chunks = array(
        array(
            'slug' 		=> 'default',
			'page_id' 	=> 1,
			'body' 		=> 'Welcome to our homepage. We have not quite finished setting up our website yet, but please add us to your bookmarks and come back soon.',
			'parsed'	=> '',
			'type' 		=> 'wysiwyg',
			'sort' 		=> 1,
        ),
        array(
            'slug' 		=> 'default',
			'page_id' 	=> 2,
			'body' 		=> 'The page you are looking for has been moved or does not exist.',
			'parsed'	=> '',
			'type' 		=> 'wysiwyg',
			'sort' 		=> 2,
        )
    );
    
    public function __construct() {
        $this->load->helper('date');
    }
	//--------------------------------------------------------------------

	public function up()
	{
		$prefix = $this->db->dbprefix;
        
        // Pages
        foreach($this->pages as $page) {
            $page_data = $page;
            $page_data['created_on'] = now();
            $this->db->insert('pages',$page_data);
        }
        
        // Chunks
        foreach($this->chunks as $chunk) {
            $chunk_data = $chunk;
            $this->db->insert('page_chunks',$chunk_data);
        }
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$prefix = $this->db->dbprefix;

        $this->db->delete('pages',array('id' => 1));
        $this->db->delete('pages',array('id' => 2));
        
        $this->db->delete('page_chunks',array('page_id' => 1));
        $this->db->delete('page_chunks',array('page_id' => 2));
	}

	//--------------------------------------------------------------------

}