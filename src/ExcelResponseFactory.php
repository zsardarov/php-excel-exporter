<?php

declare(strict_types=1);

namespace Zsardarov\ExcelExporter;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Box\Spout\Writer\XLSX\Writer;

final class ExcelResponseFactory extends BaseExcelResponseFactory
{
    private $heading;

    /**
     * @param array|null $heading
     * @return $this
     */
    public function setHeadingRow(?array $heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * @return array
     */
    protected function getHeadingRow(): ?array
    {
        return $this->heading;
    }

    /**
     * @param Writer $writer
     * @param iterable $exportable
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    protected function contentWriter(Writer $writer, iterable $exportable): void
    {
        foreach ($exportable as $cells) {
            if (! is_array($cells)) {
                $cells = (array) $cells;
            }

            $row = WriterEntityFactory::createRowFromArray(array_values($cells));
            $writer->addRow($row);
        }
    }
}
