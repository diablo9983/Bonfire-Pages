<?php



class Page_model extends MY_Model {
    
    
    /**
	 * Build a multi-array of parent > children.
	 *
	 * @return array An array representing the page tree.
	 */
	public function get_page_tree()
	{
		$all_pages = $this->db
			->select('id, parent_id, title')
			->order_by('`order`')
			->get('pages')
			->result_array();

		// First, re-index the array.
		foreach ($all_pages as $row)
		{
			$pages[$row['id']] = $row;
		}

		unset($all_pages);

		// Build a multidimensional array of parent > children.
		foreach ($pages as $row)
		{
			if (array_key_exists($row['parent_id'], $pages))
			{
				// Add this page to the children array of the parent page.
				$pages[$row['parent_id']]['children'][] =& $pages[$row['id']];
			}

			// This is a root page.
			if ($row['parent_id'] == 0)
			{
				$page_array[] =& $pages[$row['id']];
			}
		}

		return $page_array;
	}
    
    /**
	 * Set the parent > child relations and child order
	 *
	 * @param array $page
	 */
	public function _set_children($page)
	{
		if (isset($page['children']))
		{
			foreach ($page['children'] as $i => $child)
			{
				$child_id = (int) str_replace('page_', '', $child['id']);
				$page_id = (int) str_replace('page_', '', $page['id']);

				$this->update($child_id, array('parent_id' => $page_id, '`order`' => $i), true);

				//repeat as long as there are children
				if (isset($child['children']))
				{
					$this->_set_children($child);
				}
			}
		}
	}

	/**
	 * Does the page have children?
	 *
	 * @param int $parent_id The ID of the parent page
	 *
	 * @return bool
	 */
	public function has_children($parent_id)
	{
		return parent::count_by(array('parent_id' => $parent_id)) > 0;
	}

	/**
	 * Get the child IDs
	 *
	 * @param int $id The ID of the page?
	 * @param array $id_array ?
	 *
	 * @return array
	 */
	public function get_descendant_ids($id, $id_array = array())
	{
		$id_array[] = $id;

		$children = $this->db->select('id, title')
			->where('parent_id', $id)
			->get('pages')
			->result();

		if ($children)
		{
			// Loop through all of the children and run this function again
			foreach ($children as $child)
			{
				$id_array = $this->get_descendant_ids($child->id, $id_array);
			}
		}

		return $id_array;
	}

	/**
	 * Build a lookup
	 *
	 * @param int $id The id of the page to build the lookup for.
	 *
	 * @return array
	 */
	public function build_lookup($id)
	{
		$current_id = $id;

		$segments = array();
		do
		{
			$page = $this->db
				->select('slug, parent_id')
				->where('id', $current_id)
				->get('pages')
				->row();

			$current_id = $page->parent_id;
			array_unshift($segments, $page->slug);
		}
		while ($page->parent_id > 0);

		return $this->update($id, array('uri' => implode('/', $segments)), true);
	}

	/**
	 * Reindex child items
	 *
	 * @param int $id The ID of the parent item
	 */
	public function reindex_descendants($id)
	{
		$descendants = $this->get_descendant_ids($id);
		foreach ($descendants as $descendant)
		{
			$this->build_lookup($descendant);
		}
	}
  
    /**
	 * Update lookup.
	 *
	 * Updates lookup for entire page tree used to update
	 * page uri after ajax sort.
	 *
	 * @param array $root_pages An array of top level pages
	 */
	public function update_lookup($root_pages)
	{
		// first reset the URI of all root pages
		$this->db
			->where('parent_id', 0)
			->set('uri', 'slug', false)
			->update('pages');

		foreach ($root_pages as $page)
		{
			$this->reindex_descendants($page);
		}
	}
    
    /**
	 * Get a page from the database.
	 *
	 * Also retrieves the chunks for that page.
	 *
	 * @param int $id The page id.
	 * @param bool $get_chunks Whether to retrieve the chunks for this page or not. Defaults to true.
	 *
	 * @return array The page data.
	 */
	public function get($id, $get_chunks = true)
	{
		$page = $this->db
			->where($this->key, $id)
			->get($this->table)
			->row_array();

		if ( ! $page)
		{
			return;
		}

		if ($get_chunks)
		{
			$page['chunks'] = $this->db
				->order_by('sort')
				->get_where('page_chunks', array('page_id' => $id))
				->result_array();
		}

		return $page;
	}
}