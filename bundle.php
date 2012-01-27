<?php

/**
 * HTML Menu Generator for Laravel
 * 
 * <code>
 *     // Generate a simple navigation menu
 *     echo Menu\Menu::make(array('class' => 'nav'), 'ol')
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

require __DIR__.DIRECTORY_SEPARATOR.'menu'.EXT;