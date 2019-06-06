# Enum

[![Build Status](https://travis-ci.org/yar3333/smart-enum.svg?branch=master)](https://travis-ci.org/yar3333/smart-enum)
[![Latest Stable Version](https://poser.pugx.org/yar3333/smart-enum/version.png)](https://packagist.org/packages/yar3333/smart-enum)
[![Total Downloads](https://poser.pugx.org/yar3333/smart-enum/downloads.png)](https://packagist.org/packages/yar3333/smart-enum)

A PHP 7.1+ enumeration library. Based on [php-enum](https://github.com/paillechat/php-enum).

## Installation
```
composer require "smart-enum/smart-enum"
```

## Using

```php
<?php

use SmartEnum\Enum;

/**
 * These docs are used only to help IDE
 * 
 * @method static static ONE
 * @method static static TWO
 */
class IssueType extends Enum 
{
    protected const ONE = 1;
    protected const TWO = 2;
} 

$one = IssueType::ONE(); // $one instanceof IssueType === true

$one1 = IssueType::ONE();
$one2 = IssueType::ONE();
$two = IssueType::TWO();

$one1 === $one2; // true
$one !== $two; // true

function moveIssue(IssueType $type) {}

$name = $one->getName(); # "ONE"
$one = IssueType::fromName($name);

$value = $one->getValue(); # 1
$new = IssueType::fromValue($value);

$names = IssueType::getNames(); // [ "ONE", "TWO" ]
$values = IssueType::getValues(); // [ 1, 2 ]
```
