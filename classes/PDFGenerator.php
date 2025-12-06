<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Fpdf\Fpdf;

class PDFGenerator
{
    public function __construct() {}

    public function generateInvoice(array $orderData, array $userDetails, array $cartItems): string|bool
    {
        $invoicesDir = __DIR__ . '/../invoices';
        if (!is_dir($invoicesDir)) {
            mkdir($invoicesDir, 0777, true);
        }

        $orderId = $orderData['orderID'] ?? '0';
        $invoiceFileName = 'invoice_' . $orderId . '_' . time() . '.pdf';
        $savePathFull = $invoicesDir . '/' . $invoiceFileName;

        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Invoice #' . $orderId, 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Date: ' . date('Y-m-d H:i:s'), 0, 1);
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Bill To:', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, trim(($userDetails['firstName'] ?? '') . ' ' . ($userDetails['lastName'] ?? '')), 0, 1);
        if (!empty($userDetails['street'])) $pdf->Cell(0, 6, $userDetails['street'], 0, 1);
        $addr = trim(($userDetails['city'] ?? '') . ' ' . ($userDetails['provinceState'] ?? '') . ' ' . ($userDetails['postalCode'] ?? ''));
        if ($addr) $pdf->Cell(0, 6, $addr, 0, 1);
        if (!empty($userDetails['country'])) $pdf->Cell(0, 6, $userDetails['country'], 0, 1);
        $pdf->Ln(6);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(90, 7, 'Item', 1);
        $pdf->Cell(20, 7, 'Qty', 1, 0, 'R');
        $pdf->Cell(30, 7, 'Unit', 1, 0, 'R');
        $pdf->Cell(40, 7, 'Total', 1, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $grandTotal = 0.0;
        foreach ($cartItems as $it) {
            $name = substr($it['name'] ?? ($it['productName'] ?? 'Item'), 0, 90);
            $qty = (int)($it['quantity'] ?? 1);
            $unit = (float)($it['price'] ?? $it['unitPrice'] ?? 0.0);
            $total = $qty * $unit;
            $grandTotal += $total;

            $pdf->Cell(90, 6, $name, 1);
            $pdf->Cell(20, 6, (string)$qty, 1, 0, 'R');
            $pdf->Cell(30, 6, number_format($unit, 2), 1, 0, 'R');
            $pdf->Cell(40, 6, number_format($total, 2), 1, 1, 'R');
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(140, 7, 'Grand Total', 1);
        $pdf->Cell(40, 7, number_format($grandTotal, 2), 1, 1, 'R');

        $pdf->Output('F', $savePathFull);

        return 'invoices/' . $invoiceFileName;
    }
}
