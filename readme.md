# Τόπος Bundle, by Phill Sparks

A HTML Menu Generator for Laravel.  Install by dropping into your **bundles** directory.

    // Generate a simple navigation menu
    echo Topos\Menu::make(array('class' => 'menu'), 'ol')
        ->add('', 'Home')
        ->add('blog', 'Blog')
        ->add('about', 'About')
        ->add('contact', 'Contact')
        ->get();
