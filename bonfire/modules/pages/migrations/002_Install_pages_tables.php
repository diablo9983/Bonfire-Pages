<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_pages_tables extends Migration {

	public function up()
	{
		$prefix = $this->db->dbprefix;
        
        // Create page chunks table
		$this->dbforge->add_field('`id` INT(11) NOT NULL AUTO_INCREMENT');
        $this->dbforge->add_field('`slug` VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->dbforge->add_field('`page_id` INT(11) NOT NULL');
        $this->dbforge->add_field('`body` TEXT NOT NULL');
        $this->dbforge->add_field('`parsed` TEXT NOT NULL');
        $this->dbforge->add_field('`type` SET(\'html\',\'wysiwyg\') NOT NULL');
        $this->dbforge->add_field('`sort` INT(11) NOT NULL');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('page_chunks',TRUE);
        
        // Create pages table
        $this->dbforge->add_field('`id` INT(11) NOT NULL AUTO_INCREMENT');
        $this->dbforge->add_field('`slug` VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->dbforge->add_field('`title` VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->dbforge->add_field('`uri` TEXT');
        $this->dbforge->add_field('`parent_id` INT(11) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`revision_id` VARCHAR(255) NOT NULL DEFAULT \'1\'');
        $this->dbforge->add_field('`layout_id` VARCHAR(255) NOT NULL DEFAULT \'1\'');
        $this->dbforge->add_field('`css` TEXT');
        $this->dbforge->add_field('`js` TEXT');
        $this->dbforge->add_field('`meta_title` VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->dbforge->add_field('`meta_keywords` VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->dbforge->add_field('`meta_description` TEXT');
        $this->dbforge->add_field('`rss_enabled` INT(1) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`comments_enabled` INT(1) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`status` ENUM(\'draft\',\'live\') NOT NULL DEFAULT \'draft\'');
        $this->dbforge->add_field('`created_on` INT(11) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`modified_on` INT(11) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`restricted_to` VARCHAR(255) DEFAULT NULL');
        $this->dbforge->add_field('`is_home` INT(1) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`strict_uri` TINYINT(1) NOT NULL DEFAULT \'1\'');
        $this->dbforge->add_field('`order` INT(11) NOT NULL DEFAULT \'0\'');
        $this->dbforge->add_field('`page_template` VARCHAR(150) NOT NULL DEFAULT \'default\'');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('pages',TRUE);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$prefix = $this->db->dbprefix;
		$this->dbforge->drop_table('page_chunks');
        $this->dbforge->drop_table('page_layouts');
        $this->dbforge->drop_table('pages');
	}

	//--------------------------------------------------------------------

}