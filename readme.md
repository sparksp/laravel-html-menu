# HTML Menu Generator for Laravel

    // Generate a simple navigation menu
    echo Menu\Menu::make(array('class' => 'nav'), 'ol')
        ->add('', 'Home')
        ->add('blog', 'Blog')
        ->add('about', 'About')
        ->add('contact', 'Contact')
        ->get();
