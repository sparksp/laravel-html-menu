<?php namespace Topos;

use URL, Request, HTML;

/**
 * Τόπος - HTML Menu Generator for Laravel
 * 
 * <code>
 *     // Generate a simple navigation menu
 *     echo Topos\Menu::make(array('class' => 'nav'), 'ol')
 *         ->add('', 'Home')
 *         ->add('blog', 'Blog')
 *         ->add('about', 'About')
 *         ->add('contact', 'Contact')
 *         ->get();
 * </code>
 *
 * @package     Bundles
 * @subpackage  HTML-Menu
 * @author      Phill Sparks <me@phills.me.uk>
 * @version     1.0
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
	 * Add an item to the menu
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return Menu
	 */
	function add($url, $title, array $attributes = array(), $https = false)
	{
		$this->items[] = new Menu_Item($url, $title, $attributes, $https);
		return $this;
	}

	/**
	 * Add an item if the test is true
	 *
	 * @param  bool    $test
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return Menu
	 */
	function add_if($test, $url, $title, $attributes = array(), $https = false)
	{
		if ($test)
		{
			$this->add($url, $title, $attributes, $https);
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
	function get($type = null)
	{
		if (!in_array($type, array('ol', 'ul'))) $type = $this->type;
		$html = '';
		
		$n = 0; $c = count($this->items);
		foreach ($this->items as $item)
		{
			$class = array(); $link = true;
			if ($n === 0) $class[] = 'first';
			if ($n === $c - 1) $class[] = 'last';
			if (self::isActiveURL($item->url, $item->https))
			{
				$class[] = 'active';
				$link = $this->linkActive;
			}
			$html .= '<li'.HTML::attributes(array('class' => implode(' ', $class))).'>'.($link ? $item->get_link() : $item->get_span()).'</li>';
			
			$n++;
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
	 * Determine whether the given URL is handled by the current request
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return bool
	 */
	static function isActiveURL($url, $https = false)
	{
		$here  = URL::to(Request::uri(), $https);
		$there = URL::to(preg_replace('/#.*$/', '', $url), $https);

		if ($url == '/')
		{
			return ($here == $there);
		}
		elseif (false !== $p = strpos($here, '/', min(strlen($here), strlen($there))))
		{
			return (strncmp($here, $there, strlen($there)) === 0);
		}
		else
		{
			return (strcmp($here, $there) === 0);
		}
	}
}

/**
 * HTML Menu Item
 *
 * @see Menu
 * @internal
 */
class Menu_Item {

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
	 * Create a new Menu_Item
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
	 * Returns a HTML link of this menu item
	 *
	 * @uses Laravel\HTML::link()  Generates the HTML link
	 */
	function get_link()
	{
		return HTML::link($this->url, $this->title, $this->attributes, $this->https);
	}

	/**
	 * Returns a HTML span of this menu item
	 *
	 * @uses Laravel\HTML::span()  Generates the HTML span
	 */
	function get_span()
	{
		return HTML::span($this->title, $this->attributes);
	}

}
