<?php namespace Topos;

use URI, URL, Request, HTML;

/**
 * Τόπος - HTML Menu Generator for Laravel
 * 
 * <code>
 *     // Generate a simple navigation menu
 *     echo Topos\Menu::make(array('class' => 'nav'), 'ol')
 *         ->link('', 'Home')
 *         ->link('blog', 'Blog')
 *         ->divider('', array('class' => 'divider'))
 *         ->link('about', 'About')
 *         ->link('contact', 'Contact')
 *         ->render();
 * </code>
 *
 * @category    Bundle
 * @package     Topos
 * @author      Phill Sparks <me@phills.me.uk>
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 * @copyright   2012 Phill Sparks
 * 
 * @see  https://github.com/sparksp/laravel-html-menu
 */
class Menu {

	/**
	 * Array of HTML attributes added to the list element
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Contains the menu items
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Type of list, currently one of 'ul' or 'ol'
	 *
	 * @var string
	 */
	protected $type  = 'ul';

	/**
	 * Whether to link to the 'active' item or not.
	 *
	 * @var bool
	 */
	protected $linkActive = true;

	/**
	 * Create a new Menu
	 *
	 * @param  array   $attributes
	 * @param  string  $type  One of 'ul' or 'ol'
	 * @param  bool    $linkActive
	 */
	protected function __construct(array $attributes = array(), $type = 'ul', $linkActive = true)
	{
		$this->attributes = $attributes;
		$this->type($type);
		$this->linkActive = ($linkActive == true);
	}

	/**
	 * Create a new Menu
	 *
	 * @param  mixed   $attributes  (optional) can be skipped
	 * @param  string  $type
	 * @param  bool    $linkActive
	 * @return Menu
	 */
	static function make($attributes = array(), $type = 'ul', $linkActive = true)
	{
		if (is_string($attributes))
		{
			if (is_bool($type))
			{
				$linkActive = $type;
			}
			$type = $attributes;
			$attributes = array();
		}
		return new Menu($attributes, $type, $linkActive);
	}

	/**
	 * Add an link to the menu
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return Menu
	 */
	function link($url, $title, array $attributes = array(), $https = false)
	{
		$this->items[] = new Menu_Link($url, $title, $attributes, $https);
		return $this;
	}

    /**
     * Add an divider to the menu
     *
     * @param  string  $title
     * @param  array   $attributes
     * @return Menu
     */
    function divider($title, array $attributes = array())
    {
        $this->items[] = new Menu_Divider($title, $attributes);
        return $this;
    }

	/**
	 * Add an link if the test is true
	 *
	 * @param  bool|callback  $test
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array  $attributes
	 * @param  bool  $https
	 * @return Menu
	 */
	function link_if($test, $url, $title, $attributes = array(), $https = false)
	{
		if (value($test))
		{
			$this->link($url, $title, $attributes, $https);
		}
		return $this;
	}

    /**
     * Add an divider if the test is true
     *
     * @param  bool|callback  $test
     * @param  string  $url
     * @param  string  $title
     * @param  array  $attributes
     * @param  bool  $https
     * @return Menu
     */
    function divider_if($test, $title, array $attributes = array())
    {
        if (value($test))
        {
            $this->divider($title, $attributes);
        }
        return $this;
    }

	/**
	 * If no arguments are provided returns the type of this menu, otherwise sets the type of the menu and returns the menu.
	 *
	 * @param  string  $type
	 * @return string|Menu
	 */
	function type($type = null)
	{
		if (is_null($type))
		{
			return $this->type;
		}
		else
		{
			$type = strtolower($type);
			if ($type == 'ol' or $type == 'ul')
			{
				$this->type = $type;
			}
			return $this;
		}
	}
	
	/**
	 * If no arguments are provided returns whether to link active items, otherwise sets linkActive and returns the menu.
	 *
	 * @param  bool  $value
	 * @return bool|Menu
	 */
	function linkActive($value = null)
	{
		if (is_null($value))
		{
			return $this->linkActive;
		}
		else
		{
			$this->linkActive = ($value == true);
			return $this;
		}
	}

	/**
	 * If no value is provided returns the attributes array, otherwise replaces the attributes and returns the menu.
	 *
	 * @param  array  $attributes
	 * @return array|Menu
	 */
	function attributes(array $attributes = null)
	{
		if (is_null($attributes))
		{
			return $this->attributes;
		}
		else
		{
			$this->attributes = $attributes;
			return $this;
		}
	}

	/**
	 * If no value is provided returns the value of the named attribute, otherwise sets the attribute and returns the menu.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @return string|Menu
	 */
	function attribute($name, $value = null)
	{
		if (is_null($value))
		{
			return $this->attributes[$name];
		}
		else
		{
			$this->attributes[$name] = $value;
			return $this;
		}
	}

	/**
	 * Returns the number of items in the menu.
	 *
	 * @return int
	 */
	function count()
	{
		return count($this->items);
	}

	/**
	 * Removes all items from the menu and returns the menu
	 *
	 * @return Menu
	 */
	function clear()
	{
		$this->items = array();
		return $this;
	}

	/**
	 * Renders the menu
	 *
	 * @param  string  $type  One of 'ol' or 'ul'.  If not provided then render using the menu's type.
	 * @return string
	 */
	function render($type = null)
	{
		if (!in_array($type, array('ol', 'ul'))) $type = $this->type;

		$html = '';

        $items_no = count($this->items);
        $link_pointer = 0;
        $link_pointer_max = $items_no - 1;

        for ($i = 0; $i < $items_no; $i++)
        {
            if ($this->items[$i] instanceof Menu_Link)
            {
                $class = array(); $link = true;

                if ($link_pointer === 0) $class[] = 'first';
                if ($link_pointer === $link_pointer_max
                    or (isset($this->items[$i + 1])
                        and $this->items[$i + 1] instanceof Menu_Divider)
                    or (! isset($this->items[$i + 1])))
                {
                    $class[] = 'last';
                }

                if (URI::is($this->items[$i]->url.'(/*)?'))
                {
                    $class[] = 'active';
                    $link = $this->linkActive;
                }

                $link_pointer++;

                $html .= '<li'.HTML::attributes(array('class' => implode(' ', $class))).'>'.($link ? $this->items[$i]->get_link() : $this->items[$i]->get_span()).'</li>';
            }
            elseif ($this->items[$i] instanceof Menu_Divider)
            {
                $link_pointer = 0;

                $html .= $this->items[$i]->get_divider();
            }
        }
		
		if ($html != '')
		{
			return '<'.$type.HTML::attributes($this->attributes).'>'.$html.'</'.$type.'>';
		}
		else
		{
			return '';
		}
	}

	/**
	 * Renders the menu (deprecated)
	 * 
	 * @deprecated
	 * @param  string  $type
	 * @return  string
	 */
	function get($type = null)
	{
		trigger_error('Deprecated: $menu->get() is deprecated, please use $menu->render() instead.', E_USER_DEPRECATED);
		return $this->render($type);
	}

	/**
	 * Add an link to the menu
	 *
	 * @deprecated
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return Menu
	 */
	function add($url, $title, array $attributes = array(), $https = false)
	{
		trigger_error('Deprecated: $menu->add() is deprecated, please use $menu->link() instead.', E_USER_DEPRECATED);
		return $this->add($url, $title, $attributes, $https);
	}

	/**
	 * Add an link if the test is true
	 *
	 * @deprecated
	 * @param  bool|callback  $test
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array  $attributes
	 * @param  bool  $https
	 * @return Menu
	 */
	function add_if($test, $url, $title, $attributes = array(), $https = false)
	{
		trigger_error('Deprecated: $menu->add_id() is deprecated, please use $menu->link_if() instead.', E_USER_DEPRECATED);
		return $this->link_if($test, $url, $title, $attributes, $https);
	}

}

/**
 * HTML Menu Link
 *
 * @see Menu
 * @internal
 */
class Menu_Link {

	/**
	 * @var string
	 */
	public $url = '';

	/**
	 * @var string
	 */
	public $title = '';

	/**
	 * Array of HTML attributes added to link and span.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * @var bool
	 */
	public $https = false;

	/**
	 * Create a new Menu_Link
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 */
	function __construct($url, $title, array $attributes = array(), $https = false)
	{
		$this->url = $url;
		$this->title = $title;
		$this->attributes = $attributes;
		$this->https = $https;
	}

	/**
	 * Returns a HTML link of this menu link
	 *
	 * @uses Laravel\HTML::link()  Generates the HTML link
	 */
	function get_link()
	{
		return HTML::link($this->url, $this->title, $this->attributes, $this->https);
	}

	/**
	 * Returns a HTML span of this menu link
	 *
	 * @uses Laravel\HTML::span()  Generates the HTML span
	 */
	function get_span()
	{
		return HTML::span($this->title, $this->attributes);
	}

}

/**
 * HTML Menu Divider
 *
 * @see Menu
 * @internal
 */
class Menu_Divider {

    /**
     * @var string
     */
    public $title = '';

    /**
     * Array of HTML attributes added to link and span.
     *
     * @var array
     */
    public $attributes = array();

    /**
     * Create a new Menu_Divider
     *
     * @param  string  $title
     * @param  array   $attributes
     */
    function __construct($title, array $attributes = array())
    {
        $this->title = $title;
        $this->attributes = $attributes;
    }

    /**
     * Returns a divider menu item
     *
     * @uses Laravel\HTML::attributes()  Generates the HTML attributes
     */
    function get_divider()
    {
        return '<li'.HTML::attributes($this->attributes).'>'.$this->title.'</li>';
    }

}
