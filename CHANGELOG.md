# General Information

GitHub Release: 
[![GitHub release](https://img.shields.io/github/release/sinri/enoch.svg)](https://github.com/sinri/enoch/releases)

Stable Release Version on Packagist:
[![Packagist](https://img.shields.io/packagist/v/sinri/enoch.svg)](https://packagist.org/packages/sinri/enoch)

# Revolution to next generation

NOTE: `^2.0` NOT COMPATIBLE WITH `^1.0`!

## 2.2.5

Make PDO component more safe when SQL is not correct. 

## 2.2.4

Support more json header such as `application/json;charset=UTF-8` in LibRequest!

## 2.2.3

Refine HTTP header handle codes with RFC 7230 

> Each header field consists of a case-insensitive field name followed by a colon (":"), optional leading whitespace, the field value, and optional trailing whitespace.

## 2.2.2

Move unnecessary Exceptions thrown in the PDO and Model.
New `DataModelCondition` for complex operators.

## 2.2.1

Add method `getPhpMailerInstance` to `LibMail`.

## 2.2.0 

Make new respond design for Lamech
Composer and Packagist do not load 2.1.19.

## 2.1.19

Fewer Exceptions;
Add parameter `options` to method `setConnection` of `LibPDO`. 

## 2.1.18

Make `forceUseStandardOutputInCLI` settable for LibLog.

## 2.1.17

Refine: Make http status code 404 as default for Lamech Route Not Matchable situation. 

## 2.1.16

Support library for LibSqlite3.
Add http status code and configurable `application/json` header in `_sayOK` and `_sayFail` methods of SethController.
Make http status code 404 as default for Lamech Route Not Matchable situation.

## 2.1.15

Fix Issue [#9](https://github.com/sinri/enoch/issues/9) .

## 2.1.14

In AbstractDataModel, use an array as condition map value would lead to en IN clause for SQL.

## 2.1.13

Now by default under CLI would not print to file but to standard output directly. 
But you can configure it.

## 2.1.12

Fix #8 : Ignore Level for LibLog.

## 2.1.11

Add `safeReadObject` to `CommonHelper`.
Add `dryQuote` and `safeBuildSQL` to `LibPDO`.

## 2.1.10

Add `loadConfig` method for initialization in `Enos`.
Add `setFtpUsePassive` method in `LibFTP`.

## 2.1.9

Add `LibFTP` for standard FTP and SSL-FTP. 

## 2.1.8

Add `hasPrefixAmong` in `MiddlewareInterface`.

## 2.1.7

Add `AbstractDataModel`.

## 2.1.6

Fix Issue #7.
Better display on Lamech Error.

## 2.1.5

According to [StackOverflow](https://stackoverflow.com/questions/41118475/segmentation-fault-on-fopen-using-sftp-and-ssh2).
It seems since this PHP update(PHP 5.6.28), you have to surround your host part (result of `ssh2_sftp()`) with `intval()`.

## 2.1.4

Move Tree Route Support to new Router Zillah and clean code of Adah.

## 2.1.3

Tree Route and its benchmark, with not accept conclusion.
Refine on surround kept.

## 2.1.3-alpha

Adah Tree Route.

## 2.1.2

Redis Queue Support.

## 2.1.1

Redis Cache Support.

## 2.1.0

Add CLI handler in Lamech.

## 2.0.2

Remove embedded PHPMailer-based SmallPHPMail Component.
Use `sinri\smallphpmailer` instead, 
which is a derivative work of original PHPMailer library licensed under LGPLv2.1 as well.
This upgrade might make this library light, and lessen risk on law.

## 2.0.1

Fix override bug in SQL-Related library.

## 2.0.0

Add BaseCodedException::ASSERT_FAILED

## 2.0.0-beta

Less new instances by make sharable method static. 
Drop out the markdown parser and let it be set by user.

# Changing Note

## 1.5.4

Added `jsonPost` to LibRequest to hand content type `application/json`.
Make `getRequest` compatible with it.

## 1.5.3

Enos Framework for Yii-like cron job worker. 

## 1.5.2

Add method `hasWeChatUserAgent` to LibRequest.

## 1.5.1 

Fix Lamech Session Storage Directory settings bug.

## 1.5.0

Adah add chained middleware support and SethInterface for easier controller construction.

## 1.4.9

Markdown Baruch preview version.

## 1.4.8

Baruch Auto Index Lower API.

## 1.4.7

Baruch Auto Index API.

## 1.4.6

Baruch auto-complete mode changes to Redirect Mode.

## 1.4.5

Make directory auto index page function to Baruch.

## 1.4.4

Baruch, the simple wiki system framework.

## 1.4.3

Refine LibMySQL

## 1.4.2

Add root support in CI-Controller-Style Auto Router to `Adah`.

## 1.4.1

Add support of Non-Required Parameters of Method in CI-Controller-Style Auto Router to `Adah`.
Note, required parameter should not appear after non-required parameter.
Also, the parameters received from URL query string would be decoded from URL-ENCODING.

## 1.4.0

Composer China Mirror not refresh.

## 1.3.9

`LibRequest` add IP address checker.

## 1.3.8

`LibRequest` can parse JSON body now.

## 1.3.7

Add `safeReadNDArray` and `turnListToMapping` to `CommonHelper`.

## 1.3.6

Lamech add function to load controller directory recycly.

## 1.3.5 

Event Service.

## 1.3.4

Composer Require "ralouphie/getallheaders";
Better LibRequest.

## 1.3.3

Add debug mode for Lamech.

## 1.3.2

Use ReflectionClass to auto build CI-style routers.

## 1.3.1

Add group to router.

## 1.3.0

Stop use of Spirit in Package Codes.
Add LibRequest and LibResponse.

## 1.2.9

Autoloader;
LibLog instead of full Spirit.

## 1.2.8

Add MiddleWare Support to Adah and Lamech.

## 1.2.7

Add fetch style of PDO in LibMySQL safe query all.

## 1.2.6

Make libraries for walker system constructable without parameters;
And predefined them in Walker class.

## 1.2.5

Fix issue Adah cannot match url with query string (https://github.com/sinri/enoch/issues/2)

## 1.2.4

Add support to non-static method calling to Lamech-Adah Pair. 

## 1.2.3

Cache Interface and File Cache.

## 1.2.2

Add Queue Interface.

## 1.2.1

Refine RouterInterface, marked lumen style methods (get, post, etc.) as abstract.

## 1.2.0

A new router Adah instead of Naamah for Lamech.
Refine method names of Lamech.

## 1.1.1

Lamech Path Analyzer could use customized gateway now instead of fixed 'index_route.php'.

## 1.1.0

Router added support for Method and Pure Text judgement.

## 1.0.2

Fix https://github.com/sinri/enoch/issues/1

## 1.0.0

Make classes (`Spirit`, `LibSession` etc.) able to be constructed outside protected area.
Make method `sessionStart` of `LibSession` as deprecated; this could be called within `Lamech`.
Add a new linked-style call design to `LibMail` to send mail; and make method `sendMail` deprecated.
Make method parameter names as camelCase-style.
Add method `safeReadArray` to `Spirit`.
Add property `spirit` as instance of `Spirit` to `Lamech` and `Nammah`.

## 0.9.9

It is the base and the last Framework Research Version.
