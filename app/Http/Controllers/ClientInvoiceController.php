<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientInvoiceController extends Controller
{
    public function show(Request $request, Invoice $invoice): View
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        return view('client.invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    public function file(Request $request, Invoice $invoice): StreamedResponse
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        return Storage::disk('local')->response($invoice->pdf_path, basename($invoice->pdf_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($invoice->pdf_path).'"',
        ]);
    }
}
