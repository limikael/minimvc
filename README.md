minimvc
=======

Minimalistic Web Framewok.

Half my career in the making, it is Invended Here&trade; and Exactly The Way I Want It&trade;.

It may or may not be what _you_ want, but if it is, feel free to use it. In an effort mainly to untangle my crumpled thoughts I will try to explain some concepts here.

MVC
---

This is a Model-View-Controller framework. Actually, it doesn't deal with the Model part at all, so maybe it can be said to be a View-Controller framework, if such a term exists. Also, I am fond of the [unix philosophy](http://en.wikipedia.org/wiki/Unix_philosophy) in the sense that I like software that does one thing good, and one thing only. Therefore I have kept the View and Controller parts apart, they have no dependencies at all between them and you can certanly use one without the other. Maybe I should make two separate projects? Nooo that would be going too far, it is just a couple of PHP files anyway.

There is reference documentation here: http://limikael.altervista.org/minimvcdoc/

Templates
---------

The templates constitute the view part of this framework. There are templating engines such as Smarty and PHPTAL. Why not use them? Why does Drupal not use them? Well I don't know for sure what Drupal's motivation is, but personally I find them a bit bloated. Actually, PHP _is_ in itself a kind of template system. Why not just use it the way it is? This is what I did at first, but I found that things became a bit messy. When creating template files, I wanted easy access to the data that should be displayed, as if they were just global PHP variables. But when creating business logic, there was a benefit in being able be a little bit more structured and put things in classes and so.

So I invented my [Template](http://limikael.altervista.org/minimvcdoc/class-Template.html) class. Using this, I can create a template in pure PHP with global variables:

````html
<hmtl>
  <head>
    <title><?php echo $title; ?></title>
  </head>
  <body>
    Hello, the value of the variable is: <?php echo $somevar; ?>
  </body>
</html>
````

Say that this template is saved in a file called `my_template.php`. Now, to use this template, we can use the Template class like this:

````php
<?php

    require_once "template/Template.php";

    $template=new Template("my_template.php");
    $template->set("title", "The title of the page");
    $template->set("somevar", "The value of some variable");
    $template->show();
?>
`````
