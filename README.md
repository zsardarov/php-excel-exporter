# PHP Excel Exporter

A package for exporting your reports from Database (or any other source) to Excel (.xlsx). 

## Installation

Install this package via Composer:
```bash
$ composer require zsardarov/php-excel-exporter
```

## Basic Usage

Firstly create the factory. Pass the heading row and exportable data to it. 
After all the response can be sent to client. Example:

```php
use Zsardarov\ExcelExporter\ExcelResponseFactory;


$heading = ['id', 'title', 'content'];
$exportable = [
    [1, 'Lorem', 'ipsum'],
    [2, 'Sample', 'data']
];

$factory = new ExcelResponseFactory();

return $factory->setHeadingRow($heading)
     ->createFrom($exportable)
     ->send();
```
