<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2012, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * Array Helpers
 *
 * Provides additional functions for working with arrays.
 *
 * @package    Bonfire
 * @subpackage Helpers
 * @category   Helpers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/array_helpers.html
 *
 */

if ( ! function_exists('array_index_by_key'))
{

	/**
	 * When given an array of arrays (or objects) it will return the index of the
	 * sub-array where $key == $value.
	 *
	 * <code>
	 * $array = array(
	 *	array('value' => 1),
	 *	array('value' => 2),
	 * );
	 *
	 * // Returns 1
	 * array_index_by_key('value', 2, $array);
	 * </code>
	 *
	 * @param $key mixed The key to search on.
	 * @param $value The value the key should be
	 * @param $array array The array to search through
	 * @param $identical boolean Whether to perform a strict type-checked comparison
	 *
	 * @return false|int An INT that is the index of the sub-array, or false.
	 */
	function array_index_by_key($key=null, $value=null, $array=null, $identical=false)
	{
		if (empty($key) || empty($value) || !is_array($array))
		{
			return false;
		}

		foreach ($array as $index => $sub_array)
		{
			$sub_array = (array)$sub_array;

			if (array_key_exists($key, $sub_array))
			{
				if ($identical)
				{
					if ($sub_array[$key] === $value)
					{
						return $index;
					}
				}
				else
				{
					if ($sub_array[$key] == $value)
					{
						return $index;
					}
				}
			}
		}//end foreach
	}//end array_index_by_key()
}

if (!function_exists('array_multi_sort_by_column'))
{
	/**
	 * Sort a multi-dimensional array by a column in the sub array
	 *
	 * @param array  $arr Array to sort
	 * @param string $col The name of the column to sort by
	 * @param int    $dir The sort directtion SORT_ASC or SORT_DESC
	 *
	 * @return void
	 */
	function array_multi_sort_by_column(&$arr, $col, $dir = SORT_ASC)
	{
		if (empty($col) || !is_array($arr))
		{
			return false;
		}

		$sort_col = array();
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);

	}//end array_multi_sort_by_column()
}

/**
 * PyroCMS Array Helpers
 * 
 * This overrides Codeigniter's helpers/array_helper.php file.
 *
 * @author		Philip Sturgeon
 * @package		PyroCMS\Core\Helpers
 */


if (!function_exists('array_object_merge'))
{
	/**
	 * Merge an array or an object into another object
	 *
	 * @param object $object The object to act as host for the merge.
	 * @param object|array $array The object or the array to merge.
	 */
	function array_object_merge(&$object, $array)
	{
		// Make sure we are dealing with an array.
		is_array($array) OR $array = get_object_vars($array);

		foreach ($array as $key => $value)
		{
			$object->{$key} = $value;
		}
	}

}

if (!function_exists('array_for_select'))
{
	/**
	 * @todo Document this please.
	 *
	 * @return boolean 
	 */
	function array_for_select()
	{
		$args = & func_get_args();

		$return = array();

		switch (count($args))
		{
			case 3:
				foreach ($args[0] as $itteration):
					if (is_object($itteration))
						$itteration = (array) $itteration;
					$return[$itteration[$args[1]]] = $itteration[$args[2]];
				endforeach;
				break;

			case 2:
				foreach ($args[0] as $key => $itteration):
					if (is_object($itteration))
						$itteration = (array) $itteration;
					$return[$key] = $itteration[$args[1]];
				endforeach;
				break;

			case 1:
				foreach ($args[0] as $itteration):
					$return[$itteration] = $itteration;
				endforeach;
				break;

			default:
				return FALSE;
		}

		return $return;
	}

}

if (!function_exists('html_to_assoc'))
{
	/**
	 * @todo Document this please.
	 * 
	 * @param array $html_array
	 * @return array 
	 */
	function html_to_assoc($html_array)
	{
		$keys = array_keys($html_array);

		if (!isset($keys[0]))
		{
			return array();
		}

		$total = count(current($html_array));

		$array = array();

		for ($i = 0; $i < $total; $i++)
		{
			foreach ($keys as $key)
			{
				$array[$i][$key] = $html_array[$key][$i];
			}
		}

		return $array;
	}

}

if (!function_exists('html_to_assoc'))
{
	/**
	 * Associative array property
	 *
	 * Reindexes an array using a property of your elements. The elements should 
	 * be a collection of array or objects.
	 *
	 * Note: To give a full result all elements must have the property defined 
	 * in the second parameter of this function.
	 *
	 * @author Marcos Coelho
	 * @param array $arr
	 * @param string $prop Should be a common property with value scalar, as id, slug, order.
	 * @return array 
	 */
	function assoc_array_prop(array &$arr = NULL, $prop = 'id')
	{
		$newarr = array();

		foreach ($arr as $old_index => $element)
		{
			if (is_array($element))
			{
				if (isset($element[$prop]) && is_scalar($element[$prop]))
				{
					$newarr[$element[$prop]] = $element;
				}
			}
			elseif (is_object($element))
			{
				if (isset($element->{$prop}) && is_scalar($element->{$prop}))
				{
					$newarr[$element->{$prop}] = $element;
				}
			}
		}

		return $arr = $newarr;
	}

}
