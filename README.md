# Enoch 

http://enoch.sinri.cc/ 

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/sinri/enoch/master/LICENSE) 
[![GitHub release](https://img.shields.io/github/release/sinri/enoch.svg)](https://github.com/sinri/enoch/releases)
[![Packagist](https://img.shields.io/packagist/v/sinri/enoch.svg)](https://packagist.org/packages/sinri/enoch) 
[![Code Climate](https://codeclimate.com/github/sinri/enoch/badges/gpa.svg)](https://codeclimate.com/github/sinri/enoch)

PHP Framework for integrating. 
Mainly for regular work run under CLI. 
Also support web pages and api.

INSTALL: `composer require sinri/enoch`

> And Enoch lived sixty and five years, and begat Methuselah: 
> And Enoch walked with God after he begat Methuselah three hundred years, and begat sons and daughters: 
> And all the days of Enoch were three hundred sixty and five years: 
> And Enoch walked with God: and he [was] not; for God took him.
>
>  Genesis 5:21-24 KJV

## Walking System

Walker is the first component of Enoch Project, which allow users to run certain integrate duties.
Enoch would run defined walker implements to complete targets.

### Usage

Define your Enoch class by overriding Enoch class.
In constructor method, execute `initialize` method with parameters of `project name` and `project base`.
A common example given in test directory as 
```php
$this->initialize('SampleEnoch', __DIR__);
```
If you need using database to store config, write your own `readConfig` method. 
By default, read local file: `[projectBase]/config.php`, 
an array `$config` should be defined in this file, containing walkers and optional information.

To finally run Enoch, you should create an instance of your overrode Enoch class, and call `start` method.

### Config

Now let us see what `$config` looks like.
```php
// the sample config.php file for project sample
// non-optional fields:
// walkers
// optional fields:
// mail_list
// others: ...

$config = [
    // walkers contain: WALKER_NAME => BOOLEAN
    'walkers' => [
        "GetOrder" => true,// this orders GetOrderWalker class to be called
        "SendOrder" => false,// this orders SendOrderWalker class not to be called
    ],
    "mail_list" => [
        "tester"=>"erp@leqee.com",
    ],
];

```
Customized config read method should build `$config` in the same format, and pass it to `$this->config`.

### Walker

Walker is a standard class abstracted for a single task in the whole integrate job.
According to the requirement, you can realize the `install` method family members, for SQL, SFTP, and Mail.
After all, you must implement `walk` method.

## MVC System

Sometimes you might need to build some pages for human use.
Now with Lamech class, you can quickly build up an site with MVC structure.

### Definition

`Controller` is specified as an API class with several methods, each responses with certain content, page or JSON.
`View` is a page, could be pure HTML or PHP-mixed HTML with injected parameters.
 
### Usage

The sample for Lamech is very simple to understand.

```php
$lamech = new  \sinri\enoch\mvc\Lamech();
```

If you want to use customized error page, set this line:

```php
$lamech->setErrorPage(__DIR__.'/sample/error.php');
```

If you want to use file-based session management, you should run these two lines:

```php
$lamech->setSessionDir('S_DIR');
$lamech->startSession();
```

For example, you want handle URL as:
 
lamech.php?act=A&method=M&param=P

If you want to use controller as api:
```php
$lamech->setControllerDir(__DIR__.'/sample');
$lamech->apiFromRequest("\\api\\namespace\\");
```
This would load `\api\namespace\A` and generate an instance of it, then call its `M` method.
Within the method, you can use `getRequest` of `Spirit` to get `P` by the field name `param`.

Or, if you want to use controller as page router

```php
$lamech->setViewDir(__DIR__.'/sample');
$lamech->viewFromRequest();
```
This would load `sample/A.php` as HTML and output.

### CodeIgniter Style RESTful Framework

```php
$lamech = new  \sinri\enoch\mvc\Lamech();
// If you want to use controller as api
$lamech->setControllerDir(__DIR__ . '/controller');
// the following two could be ignored as default, call only when you want to use a customized value.
$lamech->setDefaultControllerName("Welcome");
$lamech->setDefaultMethodName("index");
// use restful handler
$lamech->restfullyHandleRequest("\\sinri\\enoch\\test\\requesting\\controller\\");
```

For example, the following URL 

`http://localhost/leqee/fundament/enoch/test/requesting/index.php/ExampleAPI/index/1/3`

would call

`\sinri\enoch\test\requesting\controller\ExampleAPI::index(1,3)`

For index.php-free Config, use `.htaccess` file with

apache version

```apacheconfig
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

nginx version

```nginxconfix
server {
    location / {
        try_files $uri $uri/ /index.php;
    }
}
```