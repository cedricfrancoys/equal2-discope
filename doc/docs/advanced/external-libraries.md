## Using external libraries with eQual 


eQual supports the [PSR-4 standard](https://www.php-fig.org/psr/psr-4/) for autoloading.

[Composer](https://getcomposer.org/) can be used to add dependencies inside the `/vendor` folder located in the root folder of eQual installation.

Libraries that follow the PSR-4 standard can be loaded with a simple `use` statement. 
For libraries that do not provide such support, simply use 'require' or 'include' as stated in the documentation of the library.

The `require` command adds new libraries to the `composer.json`. The `require` need the library names (e.g) `swiftmailer/swiftmailer` and version constraints (e.g) `^6.2`

```json
    "require": {
            "swiftmailer/swiftmailer": "^6.2",
        },
```
Open your CLI at the root of eQual's folder and use this :

```bash
$ ./equal.run --do=init_composer
```

### Examples

Here below are a few examples showing how to embed various popular libraries.

#### Twig - template engine


```bash
$ composer require "twig/twig:^2.0"
```

``` php
<?php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
```

#### PHPOffice - MS Office compatible documents generation library

```bash
$ composer require phpoffice/phpspreadsheet
```

```php
<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
```

#### DOMPDF - HTML to PDF converter

```bash
$ composer require dompdf/dompdf
```

```php
<?php
use Dompdf\Dompdf;
use Dompdf\Options;
```


For libraries that do not support autoloading, it is still a good practice to store them in the `/vendor` folder, and then including them manually by using `include` or `require`:
```php
<?php
require_once '../vendor/swiftmailer/swiftmailer/lib/swift_required.php';
```

However, once included, you can still take advantage of the `use` statement in order to make the syntax smoother and not to bother with namespaces.

```php
<?php
require_once '../vendor/swiftmailer/swiftmailer/lib/swift_required.php';

use \Swift_SmtpTransport as Swift_SmtpTransport;
use \Swift_Message as Swift_Message;
use \Swift_Mailer as Swift_Mailer;
```



