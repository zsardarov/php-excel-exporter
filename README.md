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

## Custom Factories

Also, you may extend basic functionality by creating custom factories. For this purpose you must inherit your factory from `BaseExcelFactory class`. Example:

```php
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\XLSX\Writer;
use Zsardarov\ExcelExporter\BaseExcelResponseFactory;

class CustomExcelResponseFactory extends BaseExcelResponseFactory
{
    protected function getHeadingRow(): ?array
    {
        return [
            'id',
            'category',
            'value',
            'date'
        ];
    }

    protected function contentWriter(Writer $writer, iterable $exportable): void
    {
        foreach ($exportable as $item) {
            $cells = [
                $item->id,
                $item->category,
                $item->value,
                $item->date
            ];

            $row = WriterEntityFactory::createRowFromArray($cells);
            $writer->addRow($row);
        }
    }
}
```

```php
use App\Models\Report;
use App\Services\CustomExcelResponseFactory;

class SampleController extends Controller
{
    public function export()
    {
        $exportable = Report::all();
        $factory = new CustomExcelResponseFactory();

        return $factory->createFrom($exportable, 'report.xlsx');
    }
}
```

