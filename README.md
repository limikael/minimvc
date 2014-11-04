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
````

Controllers
-----------

The controller part of the system is used to feed data into the templates, and read and update 
data in the Models. This is known as the "business logic", because this is where the application
"does it's business". In the old days, this was done by having different php scripts for doing
different things. This made it a bit difficult to reuse code and keep a good structure. I came
up with a two level hierarcy, where I use "controllers" and "methods". For a site deployed to
localhost, our urls look somethig like:

    http://localhost/hello/world

Where "hello" is the controller and "world" is the method. In order to get this to work, we use the
[WebDispatcher](http://limikael.altervista.org/minimvcdoc/class-WebDispatcher.html) class. In order
to use it, we first need a very simple `index.php` file that looks something like this:

````php
<?php
    require_once "dispatcher/WebDispatcher.php";

    $dispatcher=new WebDispatcher(_PATH_TO_CONTROLLER_DIR_);
    $dispatcher->dispatch();
````

We put file this in the web root of our project, and alongside with it we put a `.htaccess` file, 
so that the `index.php` file catches all web requests. The 
[dispatch](http://limikael.altervista.org/minimvcdoc/class-WebDispatcher.html#_dispatch)
function will look at the request, and route it to the correct controller and method.

Now, the directory specified by parameter passed to the constructor of the WebDispatcher 
class, is where we put our controller class. We will use the url from above as example:

    http://localhost/hello/world

Here, the `hello` part specifies the controller and the `world` part the method. The way the routing works
in practice is that the `WebDispatcher` will look for a file called `HelloController.php` in the controller
path, and it expects to find a class called `HelloController` inside that file. It also expects that class
to extend the 
[WebController](http://limikael.altervista.org/minimvcdoc/class-WebController.html)
class. 

There is a also a mechanism for this class to delcare which of its methods that should actually be callable
through the web. This is done in the constructor using the 
[method](http://limikael.altervista.org/minimvcdoc/class-WebController.html#_method) method.
An example controller, that would be suitable for handling the call above, could look something like this:

````php
    class HelloController extends WebDispatcher {

        function HelloController() {
            $this->method("world");
        }

        function world() {
            echo "Hello World";
        }
    }
````

The [method](http://limikael.altervista.org/minimvcdoc/class-WebController.html#_method) method returns
an instance of the [ControllerMethod](http://limikael.altervista.org/minimvcdoc/class-ControllerMethod.html)
class. This class provides a number of chainable methods to further specify the behaivour of the declared 
method. To summarize the controller concept so far:

* Each web request has a controller and a method part.
* The controller is class ending with Controller.
* The controller must extend the WebController class.
* The controller needs to declare its callable methods in the constructor.

Declaring controller methods
----------------------------

As mentioned before, the
[method](http://limikael.altervista.org/minimvcdoc/class-WebController.html#_method) method returns
an instance of the [ControllerMethod](http://limikael.altervista.org/minimvcdoc/class-ControllerMethod.html)
class. This class provides a number of chainable methods to further specify the behaivour of the declared 
method. In this section I will describe these methods.

In order to handle requests like this:

    http://localhost/hello/world?a=somevalue&b=someothervalue

We need to tell the system that the `world` method can accept the `a` and `b` parameters. This is done using
the chainable `args` method, like this:

````php
    class HelloController extends WebDispatcher {

        function HelloController() {
            $this->method("world")->args("a","b");
        }

        function world($a, $b) {
            echo "Hello World, the value of a is $a and b's value is $b";
        }
    }
````

We can also handle requests that expects arguments as paths. In this example 2 path variables:

    http://localhost/hello/world/some/parameters

This would be specified like this:

````php
    class HelloController extends WebDispatcher {

        function HelloController() {
            $this->method("world")->paths(2);
        }

        function world($first, $second) {
            echo "Hello World, the path variables are $first and $second";
        }
    }
````

These two methods can also be combined into something that expects arguments both after the ? and 
on the path using the following code in the constructor:

````php
    $this->method("world")->paths(2)->args("a","b");
````

There are a number of other ways to specify how the methods should behave, see the
[ControllerMethod](http://limikael.altervista.org/minimvcdoc/class-ControllerMethod.html) class
for details.
