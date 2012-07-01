<?php

class M_Assets extends Assets {
    
    protected static $data_js = array();
    
    public static function add_data($name=null,$data=null,$quote=true,$var=true) {
        if(empty($name) || empty($data)) return;        
        $quote = $quote ? '"' : '';
        self::$data_js[] = array('name' => ($var ? 'var ' : '').$name, 'data' => $quote.$data.$quote);        
    }
    
    public static function data_js() {
        if(empty(self::$data_js)) return;
        $dat = "<script type=\"text/javascript\">\n";
        foreach(self::$data_js as $data) {
            $dat .= "\t\t".$data['name'].' = '.$data['data'].";\n";           
        }    
        $dat .= "\t</script>\n";
        return $dat;
    }

/**
	 * Renders links to stylesheets, with the $asset_url prepended.
	 * If a single filename is passed, it will only create a single link
	 * for that file, otherwise, it will include any styles that have
	 * been added with add_css below. If no style is passed it will default
	 * to the theme's style.css file.
	 *
	 * When passing a filename, the filepath should be relative to the site
	 * root (where index.php resides).
	 *
	 * @access public
	 * @static
	 *
	 * @param mixed  $style              The style(s) to have links rendered for.
	 * @param string $media              The media to assign to the style(s) being passed in.
	 * @param bool   $bypass_inheritance If TRUE, will skip the check for parent theme styles.
	 *
	 * @return string A string containing all necessary links.
	 */
	public static function css($style=null, $media='screen', $bypass_inheritance=FALSE)
	{
		$styles = array();
		$return = '';

		//Debugging issues with media being set to 1 on module_js
		if ( $media == '1')
		{
			$media = 'screen';
		}

		// If no style(s) has been passed in, use all that have been added.
		if (empty($style) && self::$globals)
		{
			// Make sure to include a file based on media type.
			$styles[] = array(
				'file'	=> $media,
				'media'	=> $media
			);

			$styles = array_merge(self::$styles, $styles);
		}
		// If an array has been passed, merge it with any added styles.
		else if (is_array($style))
		{
			$styles = array_merge($style, self::$styles);
		}
		// If a single style has been passed in, render it only.
		else
		{
			$styles[] = array(
				'file'	=> $style,
				'media'	=> $media
			);
		}

		// Add a style named for the controller so it will be looked for.
		$styles[] = array(
			'file'	=> self::$ci->router->class,
			'media' => $media
		);

        
		$styles     = self::m_find_files($styles, 'css', $bypass_inheritance);
		$mod_styles	= self::m_find_files(self::$module_styles, 'css', $bypass_inheritance);

		$combine = self::$ci->config->item('assets.css_combine');

		// Loop through the styles, spitting out links for each one.
		if (!$combine)
		{
			foreach ($styles as $s)
			{
				if (is_array($s))
				{
					if (substr($s['file'], -4) != '.css')
					{
						$s['file'] .= '.css';
					}
				}
				else
				{
					if (substr($s, -4) != '.css')
					{
						$s .= '.css';
					}
				}

				$attr = array(
					'rel'	=> 'stylesheet',
					'type'	=> 'text/css',
					'href'	=> is_array($s) ? $s['file'] : $s,
					'media'	=> !empty($s['media']) ? $s['media'] : $media
				);

				if (!$combine)
				{
					$return .= '<link'. self::attributes($attr) ." />\n";
				}
			}
		}

		// add the combined css
		else
		{
			$return .= self::combine_css($styles, $media);
		}

		// Make sure we include module styles
		$return .= self::combine_css($mod_styles, $media, 'module');

		return $return;

	}//end css()
    
 
	/**
	 * Renders links to all javascript files including External, Module and Inline
	 * If a single filename is passed, it will only create a single link
	 * for that file, otherwise, it will include any javascript files that have
	 * been added with add_js below.
	 *
	 * When passing a filename, the filepath should be relative to the site
	 * root (where index.php resides).
	 *
	 * @access public
	 * @static
	 *
	 * @param mixed  $script The name of the script to link to (optional)
	 * @param string $type Whether the script should be linked to externally or rendered inline. Acceptable values: 'external' or 'inline'
	 *
	 * @return string Returns all Scripts located in External JS, Module JS and Inline JS in that order.
	 */
	public static function js($script=null, $type='external')
	{
		$type .= '_scripts';
		$output = '';

		// If a string is passed, it's a single script, so override
		// any that are already set
		if (!empty($script))
		{
			self::external_js((string)$script);
			return;
		}
		// If an array was passed, loop through them, adding each as we go.
		else if (is_array($script))
		{
			foreach ($script as $s)
			{
				self::${$type}[] = $s;
			}
		}

		// Render out the scripts/links
		$output  = self::m_external_js();
		$output .= self::module_js();
		$output .= self::inline_js();

		return $output;

	}//end js()
    
	/**
	 * Does the actual work of generating the links to the js files.
	 * It is called by the js() method, but can be used on it's own.
	 *
	 * If no script are passed into the first parameter, links are created for
	 * all scripts within the self::$external_scripts array. If one or
	 * more scripts are passed in the first parameter, only these script files
	 * will be used to create links with, and any stored in self::$external_scripts
	 * will be ignored.
	 *
	 * Note that links will not be rendered for files that cannot be found, though
	 * scripts will full urls are not checked, but are simply included.
	 *
	 * @access public
	 * @static
	 *
	 * @param mixed $new_js  Either a string or an array containing the names of files to link to.
	 * @param bool  $list    If TRUE, will echo out a list of scriptnames, enclosed in quotes and comma separated. Convenient for using with third-party js loaders.
	 * @param bool  $add_ext Automatically add the .js extension when adding files
	 *
	 * @return string
	 */
	public static function m_external_js($new_js=null, $list=FALSE, $add_ext=TRUE)
	{
		$return = '';
		$scripts = array();

		// If scripts were passed, they override all other scripts.
		if (!empty($new_js))
		{
			if (is_string($new_js))
			{
				$scripts[] = $new_js;
			}
			else if (is_array($new_js))
			{
				$scripts = $new_js;
			}
		}
		else
		{
			$scripts = self::$external_scripts;
		}

		// Make sure we check for a 'global.js' file.
		if (self::$globals)
		{
				$scripts[] = 'global';
		}

		// Add a style named for the controller so it will be looked for.
		$scripts[] = self::$ci->router->class;

		// Prep our scripts array with only files
		// that actually can be found.

		$scripts = self::m_find_files($scripts, 'js');

		// We either combine the files into one...
		if ((empty($new_js) || is_array($new_js)) && $list==FALSE && self::$ci->config->item('assets.js_combine'))
		{
			$return = self::combine_js($scripts);
		}
		// Or generate individual links
		else
		{
			//Check for HTTPS or HTTP connection

			if(isset($_SERVER['HTTPS'])){ $http_protocol = "https";} else { $http_protocol = "http";}
            
			foreach ($scripts as $script)
			{
                if(is_array($script)) {
                    $script = str_replace(FCPATH,base_url(),$script['server_path']);
                }
				if (TRUE === $add_ext && substr($script, -3) != '.js')
				{
					$script .= '.js';
				}

				$attr = array(
					'src'	=> (strpos($script, $http_protocol . ':') !== FALSE ||
										strpos($script, 'http:') !== FALSE ||
										strpos($script, 'https:') !== FALSE ) ?

						// It has a full url built in, so leave it alone
						$script :

						// Otherwise, build the full url
						base_url() . self::$asset_base .'/'. self::$asset_folders['js'] .'/'. $script,
							'type'=>'text/javascript'
				);

				if ($list)
				{
					$return .= '"'. $attr['src'] .'", ';
				}
				else
				{
					$return .= '<script'. self::attributes($attr) ." ></script>\n";
				}
			}
		}

		return trim($return, ', ');

	}//end external_js()
    

   	/**
	 * Locates file by looping through the active and default themes, and
	 * then the assets folder (as specified in the config file).
	 *
	 * Files are searched for in this order...
	 *     1 - active_theme/
	 *     2 - active_theme/type/
	 *     3 - default_theme/
	 *     4 - default_theme/type/
	 *     5 - asset_base/type
	 *
	 * Where 'type' is either 'css' or 'js'.
	 *
	 * If the file is not found, it is removed from the array. If the file
	 * is found, a full url is created, using base_path(), unless the path
	 * already includes 'http' at the beginning of the filename, in which case
	 * it is simply included in the return files.
	 *
	 * For CSS files, if a script of the same name is found in both the
	 * default_theme and the active_theme folders (or their type sub-folder),
	 * they are both returned, with the default_theme linked to first, so that
	 * active_theme styles can override those in the default_theme without
	 * having to recreate the entire stylesheet.
	 *
	 * @access private
	 *
	 * @param array  $files              An array of file names to search for.
	 * @param string $type               Either 'css' or 'js'.
	 * @param bool   $bypass_inheritance
	 *
	 * @return array The complete list of files with url paths.
	 */
	private function m_find_files($files=array(), $type='css', $bypass_inheritance=FALSE)
	{
		// Grab the theme paths from the template library.
		$paths = Template::get('theme_paths');
		$site_path = Template::get('site_path');
		$active_theme = Template::get('active_theme');
		$default_theme = Template::get('default_theme');

		$new_files = array();

		$clean_type = $type;
		$type = '.'. $type;

		if (self::$debug)
		{
			echo "Active Theme = {$active_theme}<br/>";
			echo "Default Theme = {$default_theme}<br/>";
			echo "Site Path = {$site_path}<br/>";
			echo 'File(s) to find: '; print_r($files);
		}

		foreach ($files as $file)
		{
			// If it's an array, we're dealing with css and it has both
			// a file and media keys. Store them for later use.
			if ($type == '.css' && is_array($file))
			{
				$media = $file['media'];
				$module	= isset($file['module']) ? $file['module'] : '';
				$file = $file['file'];
			} else if ($type == '.js' && is_array($file))
			{
				$module	= isset($file['module']) ? $file['module'] : '';
				$file = $file['file'];
			}
            
            if(strpos($file,'/')) {
                $tmp = explode('/',$file);
                $module = $tmp[0];
                $file = $tmp[1];
            }

			// Strip out the file type for consistency
			$file     = str_replace($type, '', $file);
			$file_rtl = $file . '-rtl';

			//Check for HTTPS or HTTP connection
			if(isset($_SERVER['HTTPS'])){ $http_protocol = "https";} else { $http_protocol = "http";}

			// If it contains an external URL, we're all done here.
			if (strpos((string)$file, $http_protocol, 0) !== FALSE)
			{
				$new_files[] = !empty($media) ? array('file'=>$file, 'media'=>$media) : $file;
				continue;
			}

			$found = FALSE;

			// Is it a module file?
			if (!empty($module))
			{
				$path = module_file_path($module, 'assets', $file . $type);
				if ( !empty($path) && lang('bf_language_direction') == 'rtl' )
				{
					// looking for RTL Files
					$path_rtl = module_file_path($module, 'assets', $file_rtl . $type);
					if ( !empty($path_rtl) )
						$path = $path_rtl;
				}

				if (empty($path))
				{
					// Try assets/type folder
					$path = module_file_path($module, 'assets', $clean_type .'/'. $file . $type);
					if(!empty($path) && lang('bf_language_direction') == 'rtl')
					{
						$path_rtl = module_file_path($module, 'assets', $clean_type .'/'. $file_rtl . $type);
						if ( !empty($path_rtl) )
							$path = $path_rtl;
					}

				}

				if (self::$debug)
				{
					echo "[Assets] Lookin for MODULE asset at: {$path}<br/>" . PHP_EOL;
				}

				if (!empty($path))
				{
					$file_path = str_replace(FCPATH,base_url(),$path);

					$file = array(
						'file'			=> $file_path,
						'server_path'	=> $path
					);
					if (isset($media))
					{
						$file['media'] = $media;
					}

					$new_files[] = $file;
				}

				continue;
			}
			// Non-module files
			else
			{
				// We need to check all of the possible theme_paths
				foreach ($paths as $path)
				{
					if (self::$debug) {
						echo "[Assets] Looking in: <ul><li>{$site_path}{$path}/{$default_theme}{$file}{$type}</li>" . PHP_EOL;
						echo "<li>{$site_path}{$path}/{$default_theme}{$type}/{$file}{$type}</li>" . PHP_EOL;

						if (!empty($active_theme))
						{
							echo "<li>{$site_path}{$path}/{$active_theme}{$file}{$type}</li>" . PHP_EOL;
							echo "<li>{$site_path}{$path}/{$active_theme}{$type}/{$file}{$type}</li>" . PHP_EOL;
						}

						echo '<li>'. $site_path . self::$asset_base ."/{$type}/{$file}{$type}</li>" . PHP_EOL;

						echo '</ul>' . PHP_EOL;
					}

					if (!$bypass_inheritance)
					{
						/*
							DEFAULT THEME

							First, check the default theme. Add it to the array. We check here first so that it
							will get overwritten by anything in the active theme.
						*/

						if (is_file($site_path . $path .'/'. $default_theme . $file ."{$type}"))
						{
							$file_path		= base_url() . $path .'/'. $default_theme . $file ."{$type}";
							$server_path	= $site_path . $path .'/'. $default_theme . $file ."{$type}";
							if ( lang('bf_language_direction') == 'rtl' )
							{
								//looking for RTL file
								if(is_file($site_path . $path .'/'. $default_theme . $file_rtl ."{$type}"))
								{
									$file_path = base_url() . $path .'/'. $default_theme . $file_rtl . $type;
									$server_path	= $site_path . $path .'/'. $default_theme . $file_rtl .$type;
								}
							}

							$new_files[]	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;
							$found = TRUE;

							if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $default_theme . $file ."{$type}" ."</b><br/>";
						}
						/*
							If it wasn't found in the default theme root folder, look in default_theme/$type/
						*/
						else if (is_file($site_path . $path .'/'. $default_theme . $clean_type .'/'. $file ."{$type}"))
						{
							$file_path 		= base_url() . $path .'/'. $default_theme . $clean_type .'/'. $file ."$type";
							$server_path	= $site_path . $path .'/'. $default_theme . $clean_type .'/'. $file ."{$type}";
							if( lang('bf_language_direction') == 'rtl' )
							{
								//looking for RTL file
								if ( is_file($site_path . $path .'/'. $default_theme . $clean_type .'/'. $file_rtl ."{$type}" ) )
								{
									$file_path 		= base_url() . $path .'/'. $default_theme . $clean_type .'/'. $file_rtl . $type ;
									$server_path	= $site_path . $path .'/'. $default_theme . $clean_type .'/'. $file_rtl . $type;
								}
							}

							$new_files[] 	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;
							$found = TRUE;

							if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $default_theme . $type .'/'. $file ."{$type}" ."</b><br/>";
						}
					}

					/*
						ACTIVE THEME

						By grabbing a copy from both the default theme and the active theme, we can
						handle simple CSS-only overrides for a theme, completely changing it's appearance
						through a simple child css file.
					*/
					if (!empty($active_theme) && is_file($site_path . $path .'/'. $active_theme . $file ."{$type}"))
					{
						$file_path 		= base_url() . $path .'/'. $active_theme . $file . $type ;
						$server_path	= $site_path . $path .'/'. $active_theme . $file . $type ;
						if ( lang('bf_language_direction') == 'rtl' )
						{
							//looking for RTL file
							if(is_file($site_path . $path .'/'. $active_theme . $file_rtl . $type ) )
							{
								$file_path 		= base_url() . $path .'/'. $active_theme . $file_rtl . $type;
								$server_path	= $site_path . $path .'/'. $active_theme . $file_rtl . $type;
							}
						}

						$new_files[] 	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;
						$found = TRUE;

						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $active_theme . $file ."{$type}" ."</b><br/>";
					}
					/*
						If it wasn't found in the active theme root folder, look in active_theme/$type/
					*/
					else if (is_file($site_path . $path .'/'. $active_theme . $clean_type .'/'. $file ."{$type}"))
					{
						$file_path 		= base_url() . $path .'/'. $active_theme . $clean_type .'/'. $file . $type ;
						$server_path	= $site_path . $path .'/'. $active_theme . $clean_type .'/'. $file . $type ;
						if(lang('bf_language_direction') == 'rtl')
						{
							//looking for RTL file
							if(is_file($site_path . self::$asset_base .'/'. $clean_type .'/'. $file_rtl . $type ))
							{
								$file_path 		= base_url() . self::$asset_base .'/'. $clean_type .'/'. $file_rtl . $type;
								$server_path	= $site_path . self::$asset_base .'/'. $clean_type .'/'. $file_rtl . $type;
							}
						}
						$new_files[] 	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;
						$found = TRUE;

						if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . $path .'/'. $active_theme . $type .'/'. $file ."{$type}" ."</b><br/>";
					}

					/*
						ASSET BASE

						If the file hasn't been found, yet, we have one more place to look for it:
						in the folder specified by 'assets.base_folder', and under the $type sub-folder.
					*/
					if (!$found)
					{
						// Assets/type folder
						if (is_file($site_path . self::$asset_base .'/'. $clean_type .'/'. $file ."{$type}"))
						{
							$file_path 		= base_url() . self::$asset_base .'/'. $clean_type .'/'. $file ."{$type}";
							$server_path	= $site_path . self::$asset_base .'/'. $clean_type .'/'. $file ."{$type}";
							if ( lang('bf_language_direction') == 'rtl' )
							{
								//looking for RTL file
								if ( is_file($site_path . $path .'/'. $active_theme . $clean_type .'/'. $file_rtl . $type ) )
								{
								$file_path 		= base_url() . $path .'/'. $active_theme . $clean_type .'/'. $file_rtl . $type;
								$server_path	= $site_path . $path .'/'. $active_theme . $clean_type .'/'. $file_rtl . $type;
								}
							}

							$new_files[] 	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;

							if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . self::$asset_base .'/'. $type .'/'. $file ."{$type}" ."</b><br/>";
						}

						/*
							ASSETS ROOT

							Finally, one last check to see if it is simply under assets/. This is useful for
							keeping collections of scripts (say, TinyMCE or MarkItUp together for easy upgrade.
						*/
						else if (is_file($site_path . self::$asset_base .'/'. $file ."{$type}"))
						{
							$file_path 		= base_url() . self::$asset_base .'/'. $file ."{$type}";
							$server_path	= $site_path . self::$asset_base .'/'. $file ."{$type}";
							if(lang('bf_language_direction') == 'rtl')
							{
								//looking for RTL file
								if(is_file($site_path . self::$asset_base .'/'. $file_rtl . $type ))
								{
									$file_path 		= base_url() . self::$asset_base .'/'. $file_rtl .$type;
									$server_path	= $site_path . self::$asset_base .'/'. $file_rtl . $type;
								}
							}

							$new_files[] 	= isset($media) ? array('file'=>$file_path, 'media'=>$media, 'server_path'=>$server_path) : $file_path;

							if (self::$debug) echo '[Assets] Found file at: <b>'. $site_path . self::$asset_base .'/'. $file ."{$type}" ."</b><br/>";
						}
					}	// if (!$found)
				}	// foreach ($paths as $path)
			}	// else
		}//end foreach
		return $new_files;

	}//end find_files()
    
    
    
}