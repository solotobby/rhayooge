<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderReceiptController extends Controller
{
    /**
     * Display the web receipt page for an order.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'shippingLocation', 'businessExecutive']);

        return view('orders.receipt', [
            'order' => $order,
        ]);
    }

    /**
     * Generate and stream/download a PDF receipt for the order.
     */
    public function downloadPdf(Order $order)
    {
        $order->load(['items.product', 'shippingLocation', 'businessExecutive']);

        $pdf = Pdf::loadView('orders.receipt-pdf', [
            'order' => $order,
        ])->setPaper('a4', 'portrait');

        $fileName = 'RHAYOOGE-Receipt-Order-#' . $order->id . '.pdf';

        return $pdf->download($fileName);
    }
}
