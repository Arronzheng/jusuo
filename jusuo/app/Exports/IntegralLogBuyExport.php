<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;

class IntegralLogBuyExport implements FromCollection,WithStrictNullComparison,WithEvents
{

    public $data;


    public function __construct(array $data)
    {
        $this->data     = $data;
    }

    /**
     * registerEvents    freeze the first row with headings
     * @return array
     * @author   liuml  <liumenglei0211@163.com>
     * @DateTime 2018/11/1  11:19
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // 合并单元格
                /*$event->sheet->getDelegate()->setMergeCells(['A1:O1', 'A2:C2', 'D2:O2']);
                // 冻结窗格
                $event->sheet->getDelegate()->freezePane('A4');
                // 设置单元格内容居中
                $event->sheet->getDelegate()->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);*/
                // 定义列宽度
                $widths = ['A' => 5, 'B' => 15, 'C' => 10,'D'=>15,'E'=>20,'F'=>10,'G'=>10,'H'=>20,'I'=>15,'J'=>20,'K'=>30,'L'=>10,'M'=>15,'N'=>20,'O'=>20];
                foreach ($widths as $k => $v) {
                    // 设置列宽度
                    $event->sheet->getDelegate()->getColumnDimension($k)->setWidth($v);
                }
                // 其他样式需求（设置边框，背景色等）处理扩展中给出的宏，也可以自定义宏来实现，详情见官网说明
                //...
            },
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = $this->data;
        return collect($data);
    }
}
