<?php

namespace App\Services;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    /**
     * Generate a PDF for the given quote.
     */
    public function generateQuotePdf(Quote $quote, array $options = []): \Barryvdh\DomPDF\PDF
    {
        $quote->load(['client', 'items.catalogItem', 'user']);

        $data = [
            'quote' => $quote,
            'company' => $this->getCompanySettings(),
            'showPrices' => $options['showPrices'] ?? true,
            'showNotes' => $options['showNotes'] ?? true,
        ];

        $pdf = Pdf::loadView('pdf.quote', $data);

        // Set paper size and orientation
        $pdf->setPaper($options['paper'] ?? 'a4', $options['orientation'] ?? 'portrait');

        return $pdf;
    }

    /**
     * Generate and save PDF to storage.
     */
    public function saveQuotePdf(Quote $quote, array $options = []): string
    {
        $pdf = $this->generateQuotePdf($quote, $options);

        $filename = "quotes/quote-{$quote->quote_number}.pdf";
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate and download PDF.
     */
    public function downloadQuotePdf(Quote $quote, array $options = [])
    {
        $pdf = $this->generateQuotePdf($quote, $options);

        $filename = "Quote-{$quote->quote_number}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate and stream PDF for preview.
     */
    public function streamQuotePdf(Quote $quote, array $options = [])
    {
        $pdf = $this->generateQuotePdf($quote, $options);

        return $pdf->stream();
    }

    /**
     * Get company settings for PDF branding.
     */
    protected function getCompanySettings(): array
    {
        $tenant = tenant();

        return [
            'name' => $tenant->name ?? config('app.name'),
            'email' => $tenant->email ?? config('mail.from.address'),
            'phone' => $tenant->phone ?? '',
            'address' => $tenant->address ?? '',
            'city' => $tenant->city ?? '',
            'state' => $tenant->state ?? '',
            'postal_code' => $tenant->postal_code ?? '',
            'country' => $tenant->country ?? '',
            'tax_id' => $tenant->tax_id ?? '',
            'logo' => $tenant->logo_url ?? null,
            'primary_color' => $tenant->primary_color ?? '#4F46E5',
            'website' => $tenant->website ?? '',
        ];
    }

    /**
     * Get the full company address formatted.
     */
    protected function getFormattedAddress(array $company): string
    {
        $parts = array_filter([
            $company['address'],
            $company['city'],
            $company['state'] . ' ' . $company['postal_code'],
            $company['country'],
        ]);

        return implode("\n", $parts);
    }
}
