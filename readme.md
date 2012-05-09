# Τόπος Bundle

A HTML Menu Generator for Laravel

## Installation

Install via the Artisan CLI:

```sh
php artisan bundle:install topos
```

Or download the zip and unpack into your **bundles** directory.

## Bundle Registration

You need to register topos with your application before you can use it.  Simply edit **application/bundles.php** and add the following to the array:

```php
'topos' => array(
    'autoloads' => array(
        'map' => array(
            'Topos\\Menu' => '(:bundle)/menu.php',
        ),
    ),
),
```

Alternatively you can add just `'topos'` and use `Bundle::start('topos')` each time before you want to use it.

## Guide

Generate a simple navigation menu ('ul' is default):

```php
echo Topos\Menu::make(array('class' => 'menu'), 'ol')
    ->link('', 'Home')
    ->link('blog', 'Blog')
    ->link('about', 'About')
    ->link('contact', 'Contact')
    ->render();
```

### Optional menu items

You can also use `->link_if($test, $url, $label)` to conditionally add items, test can be any callback or boolean.

```php
echo Topos\Menu::make()
    ->link('', 'Home')
    ->link('blog', 'Blog')
    // Only show the admin item if we're in admin area
    ->link_if(URI::is('admin(/*)?'), 'admin', 'Admin')
    // Only show the logout link if we're logged in
    ->link_if(Auth::check(), 'logout', 'Logout')
    ->render();
```

### Dividers

You can use `->divider($title, $attributes)` to add dividers (non-link items); useful for sidebar menus.

```php
echo Topos\Menu::make()
    ->divider('Section 1')
    ->link('', 'Home')
    ->link('blog', 'Blog')
    // Only show Section 2 if we're logged in
    ->divider_if(Auth::check(), 'Section 2')
    ->link_if(Auth::check(), 'me', 'Profile')
    ->link_if(Auth::check(), 'logout', 'Logout')
    ->render();
```
