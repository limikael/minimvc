minimvc
=======

Minimalistic Web Framewok.

Half my career in the making, it is Invended Here&trade; and Exactly The Way
I Want It&trade;.

It may or may not be what _you_ want, but if it is, feel free to use it. In
an effort mainly to untangle my crumpled thoughts I will try to explain some 
concepts here.

MVC
---

This is a Model-View-Controller framework. Actually, it doesn't deal with the
Model part at all, so maybe it can be said to be a View-Controller framework,
if such a term exists. Also, I am fond of the
[unix philosophy](http://en.wikipedia.org/wiki/Unix_philosophy) in the sense
that I like software that does one thing good, and one thing only. Therefore
I have kept the View and Controller parts apart, they have no dependencies at
all between them and you can certanly use one without the other. Maybe I
should make two separate projects? Nooo that would be going too far, it is
just a couple of PHP files anyway.

There is reference documentation here:
http://limikael.altervista.org/minimvcdoc/

Templates
---------

The templates constitute the view part of this framework. There are templating engines such as Smarty and PHPTAL. Why not use them? Why does Drupal not use them? Well I don't know exatcly what Drupal's motivation is, but personally I find them a bit bloated. Actually, PHP _is_ in itself a kind of template system. Why not just use it the way it is? This is what I did at first, but I found that things became a bit messy. I like to keep things as simple as possible but not simpler. When creating template files, I wanted easy access to the data that should be displayed, as if they were just global PHP variables. But when creating business logic, there was a benefit in being able be a little bit more structured and put things in classes and so.

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

Say that this template is saved in a file called `my_template.php`. Now, to use this template, we can use the [Template](http://limikael.altervista.org/minimvcdoc/class-Template.html) class like this:

````php
<?php
    require_once "template/Template.php";

    $template=new Template("my_template.php");
    $template->set("title", "The title of the page");
    $template->set("somevar", "The value of some variable");
    $template->show();
````

The [show](http://limikael.altervista.org/minimvcdoc/class-Template.html#_show) function will set up the variables as global, but only for the template to use, and it will render the template and output it to the browser.

In many situations, for example if we have a page that has a header and a footer and some content inside of it, we would like to use the renderation of a template as a variable for another template. The [render](http://limikael.altervista.org/minimvcdoc/class-Template.html#_render) function comes in handy in these situations:

````php
<?php
    require_once "template/Template.php";

    $contentTemplate=new Template("my_content_template.php");
    $contentTemplate->set("title", "The title of the page");
    $contentTemplate->set("somevar", "The value of some variable");
    $content=$contentTemplate->render();

    $pageTemplate=new Template("my_header_and_footer_template.php");
    $pageTemplate->set("content", $content);
    $pageTemplate->show();
`````

Controllers
-----------

The controller part of the system is used to feed data into the templates, and read and update data in the Models. This is known as the "business logic", because this is where the application "does it's business". In the old days, this was done by having different php scripts for doing different things. This made it a bit difficult to reuse code and keep a good structure. I came up with a two level hierarcy, where I use "controllers" and "methods". For a site deployed to localhost, our urls look somethig like:

    http://localhost/hello/world

Where "hello" is the controller and "world" is the method. In order to get this to work, we use the [WebDispatcher](http://limikael.altervista.org/minimvcdoc/class-WebDispatcher.html) class. In order to use it, we first need a very simple `index.php` file that looks something like this:

````php
<?php
    require_once "dispatcher/WebDispatcher.php";

    $dispatcher=new WebDispatcher(_PATH_TO_CONTROLLER_DIR_);
    $dispatcher->dispatch();
`````

We should put this in the web root of our project, and alongside with it we put a `.htaccess` file, so that the `index.php` file catches all web requests. The [dispatch](http://limikael.altervista.org/minimvcdoc/class-WebDispatcher.html#_dispatch) function will look at the request, and route it to the correct controller and method.

