<?php

namespace Zsardarov\ExcelExporter\Tests;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use PHPUnit\Framework\TestCase;
use Zsardarov\ExcelExporter\ExcelResponseFactory;

class FactoryTest extends TestCase
{
    public function testResponseContent()
    {
        $heading = ['id', 'title', 'content'];
        $exportable = [
            [1, 'Lorem', 'ipsum'],
            [2, 'Sample', 'data']
        ];

        $factory = new ExcelResponseFactory();
        $reader = ReaderEntityFactory::createXLSXReader();

        ob_start();

        $factory->setHeadingRow($heading)
            ->createFrom($exportable)
            ->send();

        $content = ob_get_clean();

        $testFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($testFile, $content);

        $rowCount = 0;

        $reader->open($testFile);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($rowCount === 0) {
                    $this->assertSame($heading, $row->toArray());
                } else {
                    $this->assertSame($exportable[$rowCount - 1], $row->toArray());
                }
                ++$rowCount;
            }
        }

        $this->assertSame(3, $rowCount);
    }
}
