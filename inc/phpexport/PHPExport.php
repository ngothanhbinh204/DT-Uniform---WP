<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require 'vendor/autoload.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Tiêu đề cột
$sheet->setCellValue('A1', 'Tên sản phẩm');
$sheet->setCellValue('B1', 'Số lượng');
$sheet->setCellValue('C1', 'Tổng tiền');

// Dữ liệu
$sheet->setCellValue('A2', 'Áo thun');
$sheet->setCellValue('B2', 5);
$sheet->setCellValue('C2', '500,000đ');

$writer = new Xlsx($spreadsheet);

// Xuất file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="bao-cao.xlsx"');
$writer->save('php://output');
exit;
