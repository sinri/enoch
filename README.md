# Enoch 

[WIKI OF ENOCH PROJECT](https://sinri.cc/wiki/index.php/enoch/index)

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

# Instruction

## Build a Web Site for API or Content

You might need `Lamech` and `Adah` to handle requests and work as a router.
`MiddlewareInterface` might also be needed to filter requests.
A controller fundamental `SethController` implements `SethInterface` is also provided for who is familiar with CodeIgniter.

### I just want to build a Wiki Site

There are `MarkdownBaruch` and `Baruch`, you can take one as base.

## Build a CLI Task Worker

### Yii Style

If you are familiar with Yii Action, you can use `Enos`.

### EbIntegrator Style

It is used by my company for a long time, might be designed by the pioneers.
Pair of `Enoch` and `Walker` is designed after this library.

## Use as Toolkit

A lot of helper libraries are provided. 
You can use them to deal with your various requirements, including

* Database (uses PDO)
* SFTP
* SMTP (uses sinri/smallphpmailer)
* Logger
* Web IO
* Session (based on file system)
* Cache (File and Redis)
* Queue (Redis)