<?php

declare(strict_types=1);

namespace Zsardarov\ExcelExporter;

use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Exception\SpoutException;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseExcelResponseFactory
{
    protected const MIME_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * @param Writer $writer
     * @param iterable $exportable
     */
    abstract protected function contentWriter(Writer $writer, iterable $exportable): void;

    /**
     * @return array
     */
    abstract protected function getHeadingRow(): ?array;

    /**
     * @param iterable $exportable
     * @param string $filename
     * @return StreamedResponse
     */
    public function createFrom(iterable $exportable, string $filename = 'export.xlsx'): StreamedResponse
    {
        $writer =  WriterEntityFactory::createXLSXWriter();

            $response = new StreamedResponse(function () use ($writer, $exportable, $filename) {
                try {
                    $writer->openToFile('php://output');

                    $writer->getCurrentSheet()->setName($filename);

                    if (! empty($this->getHeadingRow())) {
                        $border = (new BorderBuilder())
                            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
                            ->build();

                        $style = (new StyleBuilder())
                            ->setBorder($border)
                            ->setFontBold()
                            ->build();

                        $heading = WriterEntityFactory::createRowFromArray($this->getHeadingRow());
                        $heading->setStyle($style);

                        $writer->addRow($heading);
                    }

                    $this->contentWriter($writer, $exportable);

                    $writer->close();
                } catch (SpoutException $exception) {
                    $writer->close();
                }
            });

            $disposition = HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-type', self::MIME_TYPE);

            return $response;
    }
}
