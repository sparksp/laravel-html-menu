# Τόπος Bundle, by Phill Sparks

A HTML Menu Generator for Laravel, installable via the Artisan CLI:

    php artisan bundle:install topos
    
Generate a simple navigation menu ('ul' is default):

    echo Topos\Menu::make(array('class' => 'menu'), 'ol')
        ->add('', 'Home')
        ->add('blog', 'Blog')
        ->add('about', 'About')
        ->add('contact', 'Contact')
        ->get();
