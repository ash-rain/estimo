<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteTemplate;
use App\Models\SectionTemplate;
use App\Models\TermsLibrary;

class TemplateService
{
    /**
     * Create a template from an existing quote.
     */
    public function createFromQuote(Quote $quote, array $data): QuoteTemplate
    {
        // Prepare template data from quote
        $templateData = [
            'title' => $data['template_title_pattern'] ?? $quote->title,
            'notes' => $quote->notes,
            'footer' => $quote->footer,
            'valid_until_days' => $data['valid_until_days'] ?? 30,
            'items' => [],
        ];

        // Include items in template
        foreach ($quote->items as $item) {
            $templateData['items'][] = [
                'catalog_item_id' => $item->catalog_item_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'discount_percent' => $item->discount_percent,
                'notes' => $item->notes,
            ];
        }

        // Create the template
        return QuoteTemplate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'is_default' => $data['is_default'] ?? false,
            'is_industry_preset' => false,
            'template_data' => $templateData,
            'sections' => $data['sections'] ?? null,
            'terms_conditions' => $quote->terms_conditions,
            'email_template' => $data['email_template'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Apply a template to a quote.
     */
    public function applyToQuote(QuoteTemplate $template, Quote $quote): void
    {
        $template->applyToQuote($quote);
    }

    /**
     * Merge section templates into a quote template.
     */
    public function mergeSections(QuoteTemplate $template, array $sectionIds): void
    {
        $sections = SectionTemplate::whereIn('id', $sectionIds)
            ->ordered()
            ->get();

        $mergedSections = [];
        foreach ($sections as $section) {
            $mergedSections[] = [
                'id' => $section->id,
                'name' => $section->name,
                'category' => $section->category,
                'content' => $section->content,
            ];
        }

        $template->sections = $mergedSections;
        $template->save();
    }

    /**
     * Apply sections to a quote from section templates.
     */
    public function applySectionsToQuote(Quote $quote, array $sectionIds): array
    {
        $sections = SectionTemplate::whereIn('id', $sectionIds)
            ->ordered()
            ->get();

        $appliedSections = [];
        foreach ($sections as $section) {
            $appliedSections[] = [
                'name' => $section->name,
                'category' => $section->category,
                'content' => $section->applyContent(),
            ];
        }

        return $appliedSections;
    }

    /**
     * Apply terms from library to a quote.
     */
    public function applyTermsToQuote(Quote $quote, array $termsIds): void
    {
        $terms = TermsLibrary::whereIn('id', $termsIds)
            ->ordered()
            ->get();

        $combinedTerms = [];
        foreach ($terms as $term) {
            $combinedTerms[] = "## {$term->title}\n\n{$term->content}";
        }

        $quote->terms_conditions = implode("\n\n", $combinedTerms);
        $quote->save();
    }

    /**
     * Get industry preset templates.
     */
    public function getIndustryPresets(string $industry = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = QuoteTemplate::industryPresets();

        if ($industry) {
            $query->byCategory($industry);
        }

        return $query->get();
    }

    /**
     * Create default industry presets for a new tenant.
     */
    public function createIndustryPresets(): void
    {
        $presets = [
            [
                'name' => 'IT Services - Standard Project',
                'description' => 'Standard template for IT service projects',
                'category' => 'it_services',
                'template_data' => [
                    'title' => 'IT Services Proposal',
                    'notes' => 'This proposal outlines the scope, timeline, and cost for the requested IT services.',
                    'valid_until_days' => 30,
                    'items' => [],
                ],
                'terms_conditions' => $this->getDefaultTerms('it_services'),
            ],
            [
                'name' => 'Construction - Residential',
                'description' => 'Template for residential construction projects',
                'category' => 'construction',
                'template_data' => [
                    'title' => 'Construction Estimate',
                    'notes' => 'This estimate includes labor, materials, and project management for the specified scope of work.',
                    'valid_until_days' => 45,
                    'items' => [],
                ],
                'terms_conditions' => $this->getDefaultTerms('construction'),
            ],
            [
                'name' => 'Consulting - Business Strategy',
                'description' => 'Template for business consulting engagements',
                'category' => 'consulting',
                'template_data' => [
                    'title' => 'Consulting Proposal',
                    'notes' => 'This proposal outlines our approach, deliverables, and investment for the consulting engagement.',
                    'valid_until_days' => 14,
                    'items' => [],
                ],
                'terms_conditions' => $this->getDefaultTerms('consulting'),
            ],
            [
                'name' => 'Marketing - Campaign',
                'description' => 'Template for marketing campaign proposals',
                'category' => 'marketing',
                'template_data' => [
                    'title' => 'Marketing Campaign Proposal',
                    'notes' => 'Comprehensive marketing campaign including strategy, creative, and media placement.',
                    'valid_until_days' => 21,
                    'items' => [],
                ],
                'terms_conditions' => $this->getDefaultTerms('marketing'),
            ],
        ];

        foreach ($presets as $preset) {
            QuoteTemplate::create([
                'name' => $preset['name'],
                'description' => $preset['description'],
                'category' => $preset['category'],
                'is_default' => true,
                'is_industry_preset' => true,
                'template_data' => $preset['template_data'],
                'terms_conditions' => $preset['terms_conditions'],
                'created_by' => null,
            ]);
        }
    }

    /**
     * Get default terms and conditions for an industry.
     */
    private function getDefaultTerms(string $industry): string
    {
        $terms = [
            'it_services' => "## Payment Terms\n\n50% deposit required upon project commencement. Remaining balance due upon completion.\n\n## Project Timeline\n\nTimeline estimates are based on timely client feedback and approval. Delays in feedback may extend the project timeline.\n\n## Scope Changes\n\nAny changes to the original scope will be documented and may result in additional charges.\n\n## Warranty\n\n90-day warranty on all deliverables from project completion date.",

            'construction' => "## Payment Schedule\n\n30% deposit, 40% at project midpoint, 30% upon completion.\n\n## Materials\n\nAll materials subject to availability. Substitutions may be made with client approval if specified materials become unavailable.\n\n## Timeline\n\nEstimated completion date assumes normal weather conditions and timely material delivery.\n\n## Warranty\n\n1-year warranty on workmanship. Material warranties per manufacturer specifications.",

            'consulting' => "## Payment Terms\n\nInvoices issued monthly. Payment due within 15 days of invoice date.\n\n## Confidentiality\n\nAll client information and project details will be kept strictly confidential.\n\n## Deliverables\n\nAll deliverables remain property of consultant until full payment received.\n\n## Termination\n\nEither party may terminate with 30 days written notice. Client responsible for work completed through termination date.",

            'marketing' => "## Payment Terms\n\n40% deposit, 30% at campaign launch, 30% upon completion.\n\n## Creative Rights\n\nClient receives rights to all deliverables upon final payment. Portfolio usage rights retained by agency.\n\n## Media Placement\n\nMedia costs are estimates and subject to change. Final costs will be invoiced with backup documentation.\n\n## Performance\n\nWhile we strive for excellent results, specific performance outcomes cannot be guaranteed.",
        ];

        return $terms[$industry] ?? "## Payment Terms\n\nPayment terms to be agreed upon.\n\n## General Terms\n\nAll work performed subject to our standard terms and conditions.";
    }
}
