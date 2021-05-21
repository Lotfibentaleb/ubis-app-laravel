<?php

namespace App\Exports;

use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithMappedCells;

use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Events\AfterSheet;



class PageSheet implements FromCollection, WithTitle, ShouldAutoSize, WithEvents

{

    private $sheetName;

    private $column;

    private $data;

    public function __construct($sheetName, $column, $data)

    {

        $this->sheetName  = $sheetName;

        $this->column = $column;

        $this->data = $data;

    }


    public function collection()

    {
        if (count($this->data) != 0){

            $collect = new \stdClass();

            // header data
            $i = 0;
            $collect->$i = $this->column;

            for ($i = 1; $i <= count($this->data); $i++) {

                $collect->$i = $this->data[$i - 1];

            }

            return collect($collect);

        } else {

            return collect([
                0 => $this->column,
            ]);

        }

    }


    public function title(): string

    {

        Return "{$this->sheetName}";

    }

 /**
  * to set every sheet style, for instance: font size, font color...
  */

    public function registerEvents(): array

    {

        $rowCount = count($this->data) + 1;

        $columnObj = $this->column;

        end($columnObj);

        $lastColumn = key($columnObj);

        return [

            AfterSheet::class => function(AfterSheet $event) use ($rowCount, $lastColumn){

                $cellRange = 'A1:'.$lastColumn.$rowCount;

                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');

            },

        ];

    }

}
