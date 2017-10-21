# php-hetu

Finnish Social Security number validator.

This simple class validates social security numbers and provides methods for checking birthdate, age and gender based on the 'hetu'.

## Requirements

- PHP >= 5.4

## Installation

The recommended way is to install the lib [through Composer](http://getcomposer.org/).

```
composer require devsmo/php-hetu
```

Or you can add this to your composer.json

```JSON
{
    "require": {
        "devsmo/php-hetu": "^1.0"
    }
}
```

## Usage

```php
<?php

$hetu = new devsmo\Hetu('041281-981T');

if ( $hetu->isValid() ) {
	echo "It's valid";
}
else {
	echo "It's not valid...";
}

$age = $hetu->getAge(); // 35

$date_of_birth = $hetu->getDateStr(); // 1981-12-04

$gender = $hetu->getGender(); // male
```

## Contribution

Feel free to contribute! Just create a new issue or a new pull request.

## License

This library is released under the MIT License.
