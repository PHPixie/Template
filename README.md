# Template

[![Build Status](https://travis-ci.org/PHPixie/Template.svg?branch=master)](https://travis-ci.org/PHPixie/Template)
[![Test Coverage](https://codeclimate.com/github/PHPixie/Template/badges/coverage.svg)](https://codeclimate.com/github/PHPixie/Template)
[![Code Climate](https://codeclimate.com/github/PHPixie/Template/badges/gpa.svg)](https://codeclimate.com/github/PHPixie/Template)
[![HHVM Status](https://img.shields.io/hhvm/phpixie/template.svg?style=flat-square)](http://hhvm.h4cc.de/package/phpixie/template)

[![Author](http://img.shields.io/badge/author-@dracony-blue.svg?style=flat-square)](https://twitter.com/dracony)
[![Source Code](http://img.shields.io/badge/source-phpixie/template-blue.svg?style=flat-square)](https://github.com/phpixie/template)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](https://github.com/phpixie/template/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/phpixie/template.svg?style=flat-square)](https://packagist.org/packages/phpixie/template)

PHPixie Template uses PHP as the templating language, but can also handle layouts, content blocks, custom extensions and even custom formats. It’s super easy to use it to parse HAML, Markdown or whatever else you like. All you need to do is provide a compiler that will translate your format into a plain PHP template, the library will take care of caching the result, updating it when the files are modified etc. by itself.


**Inheritance**  
It’s pretty inuitive to understand template inheritance, especially if you used Twig or even Smarty. Here is a quick example:

```php
<!--layout.php-->
<html>
    <title>
        <?php $this->block('title'); ?>
    </title>
    <body>
        <?php $this->childContent(); ?>
        
    </body>
</html>
``````php
<!--fairy.php-->
<?php $this->layout('layout'); ?>
<?php $this->startBlock('title'); ?>
Fairy page
<?php $this->endBlock(); ?>

<h2>Hello <?=$_($name) ?></h2>
```

Now lets render it:

```php
echo $template->render('fairy', array('name' => 'Pixie'));
```

```html
<html>
    <title>Fairy page</title>
    <body>
        <h2>Hello Pixie</h2>
    </body>
</html>
```

You can also include a subtemplate to render a partial:

```php
include $this->resolve('fairy');
```

**Template name resolution**  
Usually templating libraries have some way of providing fallback tempates, that are used if the template you wan’t to use does not exist. And usually they handle it via some naming convention. PHPixie alows you to fine tune name resolutiion using 3 locator types:

- Directory – Maps template name to a folder location, simplest one
- Group – allows you to specify an array of locators. The template will be searched in those locators one by one until it’s found. This is used for providing fallbacks
- Prefix – allows you to route the resolution based on a prefix

This sounds more complex then it actually is, so let’s look at an example, assume the following config:

```php
$config = $slice->arrayData([
    'resolver' => [
        'locator' => [
            'type' => 'prefix',
            'locators' => [
                
                'Site' => [
                    'directory' => __DIR__.'/site/',
                ],
                    
                'Theme' => [
                    'type' => 'group',
                    'locators' => [
                        [
                            'directory' => __DIR__.'/templates/',
                        ],
                        
                        [
                            'directory' => __DIR__.'/fallback/',
                        ],
                    ] 
                ]
            ]
        ]
    ]
]);
```

It means that _Site::layout_ template will be searched for in the _site/_ folder, while the _Theme::home_ one will be searched for in _templates/_ and _fallback/_.

> When using the PHPixie Framework you define your locator in the `templateLocator.php` configuration file. Your locator will be prefixed
> using the name of your bundle.

**Extensions**

Extensions provide additional methods that you may use inside your views, they are helpful for things like formatting and escaping. As an example let’s look at the HTML extension:

```php
class HTML implements \PHPixie\Template\Extensions\Extension
{
    public function name()
    {
        return 'html';
    }
    
    //Methods we would like to expose
    public function methods()
    {
        return array('escape', 'output');
    }
    
    //Also we can assign aliases to some methods, meaning that they will also
    //be assigned to a specified local variable
    //In this case it allows us to use $_($name) instead of $this->escape($name)
    public function aliases()
    {
        return array(
            '_' => 'escape'
        );
    }
    
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public function output($string)
    {
        echo $this->escape($string);
    }
}
```

Your Extensions then have to be injected in the library constructor just like Formats.

## Creating a custom format

So let’s try integrating it with Markdown, we’ll use [mthaml/mthaml](https://packagist.org/packages/mthaml/mthaml) for that:

```php
//composer.json
{
    "require": {
        "phpixie/template": "3.*@dev",
        "phpixie/slice": "3.*@dev",
        "mthaml/mthaml": "1.7.0"
    }
}
```

And here is our compiler:

```php
class HamlFormat implements \PHPixie\Template\Formats\Format
{
    protected $mtHaml;
    
    public function __construct()
    {
        $this->mtHaml = new \MtHaml\Environment('php');
    }
    
    public function handledExtensions()
    {
        return array('haml'); // register which file extension we handle
    }
    
    public function compile($file)
    {
        $contents = file_get_contents($file);
        return $this->mtHaml->compileString($contents, $file);
    }
}
```

And now let’s inject it into Template:

```php
$slice = new \PHPixie\Slice();

$config = $slice->arrayData(array(
    'resolver' => array(
        'locator' => array(
            //template directory
            'directory' => __DIR__.'/templates/',
            'defaultExtension' => 'haml',
        )
    ),
    'compiler' => array(
        'cacheDirectory' => > __DIR__.'/cache/',
    )
));

$template = new \PHPixie\Template($slice, $config, array(), array(
    new HamlCompiler()
));
```

That’s it, we can now use HAML for our templates while retaining all the original features like Extensions and inheritance.
