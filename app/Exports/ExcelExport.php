<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class ExcelExport
{
    abstract public function headings(): array;

    abstract public function query();

    abstract public function map($row): array;

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$this->headings()], null, 'A1');

        $rowIndex = 2;
        foreach ($this->query()->cursor() as $row) {
            $sheet->fromArray([$this->map($row)], null, "A{$rowIndex}");
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
