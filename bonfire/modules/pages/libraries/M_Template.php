<?php

class M_Template extends Template
{
    /**
     * An instance of the CI super object.
     *
     * @access protected
     * @static
     *
     * @var object
     */
    protected static $ci;

    protected static $_metadata = array();

    public function __construct()
    {
        parent::__construct();
        self::$ci = &get_instance();
    }

    public static function get_metadata($place = 'header')
    {
        return isset(self::$_metadata[$place]) && is_array(self::$_metadata[$place]) ? implode("\n\t\t", self::$_metadata[$place]) : null;
    }

    /**
     * Put extra javascipt, css, meta tags, etc before all other head data
     *
     * @access	public
     * @param	string	$line	The line being added to head
     * @return	object	$this
     */
    public static function prepend_metadata($line, $place = 'header')
    {
        array_unshift(self::$_metadata[$place], $line);
    }

    /**
     * Put extra javascipt, css, meta tags, etc after other head data
     *
     * @access	public
     * @param	string	$line	The line being added to head
     * @return	object	$this
     */
    public function append_metadata($line, $place = 'header')
    {
        self::$_metadata[$place][] = $line;
    }

    /**
     * Set metadata for output later
     *
     * @access	public
     * @param	string	$name		keywords, description, etc
     * @param	string	$content	The content of meta data
     * @param	string	$type		Meta-data comes in a few types, links for example
     * @return	object	$this
     */
    public function set_metadata($name, $content, $type = 'meta')
    {
        $name = htmlspecialchars(strip_tags($name));
        $content = htmlspecialchars(strip_tags($content));

        // Keywords with no comments? ARG! comment them
        if ($name == 'keywords' and !strpos($content, ','))
        {
            $content = preg_replace('/[\s]+/', ', ', trim($content));
        }

        switch ($type)
        {
            case 'meta':
                if ($name == 'http-equiv')
                {
                    $name = 'http-equiv="Content-Type" ';
                } else
                {
                    $name = 'name="' . $name . '" ';
                }
                self::$_metadata['header'][$name] = '<meta ' . $name . ' content="' . $content . '" />';
                break;

            case 'link':
                self::$_metadata['header'][$content] = '<link rel="' . $name . '" href="' . $content . '" />';
                break;
        }

        return $this;
    }

    /**
     * Renders out the specified layout, which starts the process
     * of rendering the page content. Also determines the correct
     * view to use based on the current controller/method.
     *
     * @access public
     * @static
     *
     * @global object $OUT Core CodeIgniter Output object
     * @param  string $layout The name of the a layout to use. This overrides any current or default layouts set.
     *
     * @return void
     */
    public static function render($layout = null)
    {
        $output = '';
        $controller = self::$ci->router->class;

        // We need to know which layout to render
        $layout = empty($layout) ? self::$layout : $layout;
        $layout = ($layout != 'default' ? $layout.'_template' : $layout);
        // Is it in an AJAX call? If so, override the layout
        if (self::$ci->input->is_ajax_request())
        {
            $layout = self::$ci->config->item('template.ajax_layout');
            self::$ci->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
            self::$ci->output->set_header("Cache-Control: post-check=0, pre-check=0");
            self::$ci->output->set_header("Pragma: no-cache");
            self::$ci->output->set_header('Content-Type: text/html');

            $controller = null;
        }

        // Grab our current view name, based on controller/method
        // which routes to views/controller/method.
        if (empty(self::$current_view))
        {
            self::$current_view = self::$ci->router->class . '/' . self::$ci->router->method;
        }

        //
        // Time to render the layout
        //
        self::load_view($layout, self::$data, $controller, true, $output);

        if (empty($output))
        {
            show_error('Unable to find theme layout: ' . $layout);
        }

        Events::trigger('after_layout_render', $output);

        if (file_exists(FCPATH . 'bonfire/themes/' . Template::theme() . 'shortcodes.php'))
        {
            self::$ci->load->library('pages/shortcode');
            include FCPATH . 'bonfire/themes/' . Template::theme() . 'shortcodes.php';
            $short = new Theme_Shortcodes;
            $output = $short->do_shortcode($output);
        }

        global $OUT;
        $OUT->set_output($output);

        // Reset the original view path
        //self::$ci->load->_ci_view_path = self::$orig_view_path;

    } //end render()

    /**
     * Get the Page Templates available in this theme
     *
     *
     * @return array Key is the template name, value is the filename of the template
     */
    public static function get_page_templates()
    {


        $page_templates = array('Default' => 'default');

           
            $base = FCPATH.'bonfire/themes/'.config_item('template.default_theme');
            
            $templates = scandir($base);
            foreach ($templates as $template)
            {
                if($template == '.' || $template == '..' || !strpos($template,'_template')) continue;

                $template_data = implode('', file($base.$template));                
        
                $name = '';
                if (preg_match('|Template Name:(.*)$|mi', $template_data, $name)) $name = $name[1];

                if (!empty($name))
                {
                    $page_templates[trim($name)] = $template;
                }
            }
       
        return $page_templates;
    }
}
