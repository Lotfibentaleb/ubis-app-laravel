<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullExport implements WithMultipleSheets
{
    use Exportable;

    private $sheetData;

    public function __construct($sheetData)
    {
        $this->sheetData = $sheetData;
    }


    public function sheets(): array
    {
        $sheets = [];
        for($i = 0; $i < count($this->sheetData->pages); $i++){
            $key = $this->sheetData->pages[$i];
            if ($key == 'serial') {
                $sheets[] = new PageSheet($this->sheetData->pages[$i],
                    isset($this->sheetData->columns->$key)?$this->sheetData->columns->$key:[],
                    isset($this->sheetData->values->$key)?$this->sheetData->values->$key:[]);
            }
        }

        for($i = 0; $i < count($this->sheetData->pages); $i++){
            $key = $this->sheetData->pages[$i];
            if ($key != 'serial') {
                $sheets[] = new PageSheet($this->sheetData->pages[$i],
                    isset($this->sheetData->columns->$key)?$this->sheetData->columns->$key:[],
                    isset($this->sheetData->values->$key)?$this->sheetData->values->$key:[]);
            }
        }
        return $sheets;
    }
}
