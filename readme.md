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
    ->add('', 'Home')
    ->add('blog', 'Blog')
    ->add('about', 'About')
    ->add('contact', 'Contact')
    ->get();
```
