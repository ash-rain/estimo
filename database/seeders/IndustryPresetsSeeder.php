<?php

namespace Database\Seeders;

use App\Models\QuoteTemplate;
use App\Models\TermsLibrary;
use Illuminate\Database\Seeder;

class IndustryPresetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create industry preset templates
        $this->createTemplates();
        
        // Create default terms library entries
        $this->createDefaultTerms();
    }

    private function createTemplates(): void
    {
        $templates = [
            [
                'name' => 'IT Services - Standard Project',
                'description' => 'Standard template for IT service projects including development, implementation, and support',
                'category' => 'it_services',
                'template_data' => [
                    'title' => 'IT Services Proposal',
                    'notes' => 'This proposal outlines the scope, timeline, and cost for the requested IT services.',
                    'valid_until_days' => 30,
                    'items' => [],
                ],
                'terms_conditions' => "## Payment Terms\n\n50% deposit required upon project commencement. Remaining balance due upon completion.\n\n## Project Timeline\n\nTimeline estimates are based on timely client feedback and approval. Delays in feedback may extend the project timeline.\n\n## Scope Changes\n\nAny changes to the original scope will be documented and may result in additional charges.\n\n## Warranty\n\n90-day warranty on all deliverables from project completion date.",
            ],
            [
                'name' => 'Construction - Residential',
                'description' => 'Template for residential construction projects and renovations',
                'category' => 'construction',
                'template_data' => [
                    'title' => 'Construction Estimate',
                    'notes' => 'This estimate includes labor, materials, and project management for the specified scope of work.',
                    'valid_until_days' => 45,
                    'items' => [],
                ],
                'terms_conditions' => "## Payment Schedule\n\n30% deposit, 40% at project midpoint, 30% upon completion.\n\n## Materials\n\nAll materials subject to availability. Substitutions may be made with client approval if specified materials become unavailable.\n\n## Timeline\n\nEstimated completion date assumes normal weather conditions and timely material delivery.\n\n## Warranty\n\n1-year warranty on workmanship. Material warranties per manufacturer specifications.",
            ],
            [
                'name' => 'Consulting - Business Strategy',
                'description' => 'Template for business consulting engagements and strategic advisory services',
                'category' => 'consulting',
                'template_data' => [
                    'title' => 'Consulting Proposal',
                    'notes' => 'This proposal outlines our approach, deliverables, and investment for the consulting engagement.',
                    'valid_until_days' => 14,
                    'items' => [],
                ],
                'terms_conditions' => "## Payment Terms\n\nInvoices issued monthly. Payment due within 15 days of invoice date.\n\n## Confidentiality\n\nAll client information and project details will be kept strictly confidential.\n\n## Deliverables\n\nAll deliverables remain property of consultant until full payment received.\n\n## Termination\n\nEither party may terminate with 30 days written notice. Client responsible for work completed through termination date.",
            ],
            [
                'name' => 'Marketing - Campaign',
                'description' => 'Template for marketing campaign proposals and advertising services',
                'category' => 'marketing',
                'template_data' => [
                    'title' => 'Marketing Campaign Proposal',
                    'notes' => 'Comprehensive marketing campaign including strategy, creative, and media placement.',
                    'valid_until_days' => 21,
                    'items' => [],
                ],
                'terms_conditions' => "## Payment Terms\n\n40% deposit, 30% at campaign launch, 30% upon completion.\n\n## Creative Rights\n\nClient receives rights to all deliverables upon final payment. Portfolio usage rights retained by agency.\n\n## Media Placement\n\nMedia costs are estimates and subject to change. Final costs will be invoiced with backup documentation.\n\n## Performance\n\nWhile we strive for excellent results, specific performance outcomes cannot be guaranteed.",
            ],
            [
                'name' => 'Design - Branding Package',
                'description' => 'Template for branding and design projects',
                'category' => 'design',
                'template_data' => [
                    'title' => 'Branding Design Proposal',
                    'notes' => 'Complete branding package including logo design, brand guidelines, and supporting materials.',
                    'valid_until_days' => 30,
                    'items' => [],
                ],
                'terms_conditions' => "## Payment Terms\n\n50% deposit to begin, 25% at concept approval, 25% upon final delivery.\n\n## Revisions\n\nUp to 3 rounds of revisions included. Additional revisions billed hourly.\n\n## Rights & Ownership\n\nAll rights transfer to client upon final payment. Designer retains portfolio rights.\n\n## Timeline\n\nProject timeline begins upon receipt of deposit and all required materials from client.",
            ],
        ];

        foreach ($templates as $template) {
            QuoteTemplate::create([
                'name' => $template['name'],
                'description' => $template['description'],
                'category' => $template['category'],
                'is_default' => true,
                'is_industry_preset' => true,
                'template_data' => $template['template_data'],
                'terms_conditions' => $template['terms_conditions'],
                'created_by' => null,
            ]);
        }
    }

    private function createDefaultTerms(): void
    {
        $terms = [
            [
                'title' => 'Payment Terms - 50% Deposit',
                'content' => '50% of the total project cost is due as a deposit before work begins. The remaining 50% is due upon project completion and final delivery.',
                'category' => 'payment',
                'order' => 1,
            ],
            [
                'title' => 'Payment Terms - Net 30',
                'content' => 'Payment is due within 30 days of invoice date. Late payments may be subject to a 1.5% monthly interest charge.',
                'category' => 'payment',
                'order' => 2,
            ],
            [
                'title' => 'Standard Warranty - 90 Days',
                'content' => 'We provide a 90-day warranty on all work performed. This warranty covers defects in workmanship and materials. It does not cover damage caused by misuse, neglect, or normal wear and tear.',
                'category' => 'warranty',
                'order' => 1,
            ],
            [
                'title' => 'Liability Limitation',
                'content' => 'Our liability is limited to the total amount paid for services. We are not liable for indirect, incidental, or consequential damages.',
                'category' => 'liability',
                'order' => 1,
            ],
            [
                'title' => 'Scope Change Policy',
                'content' => 'Any changes to the original scope of work must be documented in writing and may result in additional charges. Change requests will be evaluated and priced separately.',
                'category' => 'general',
                'order' => 1,
            ],
            [
                'title' => 'Delivery Timeline',
                'content' => 'Estimated delivery dates are provided in good faith but are not guaranteed. Timelines are dependent on timely client feedback and approval at designated milestones.',
                'category' => 'delivery',
                'order' => 1,
            ],
            [
                'title' => 'Confidentiality Agreement',
                'content' => 'All information shared during this engagement will be kept strictly confidential. We will not disclose any proprietary information to third parties without written consent.',
                'category' => 'general',
                'order' => 2,
            ],
            [
                'title' => 'Cancellation Policy',
                'content' => 'Either party may cancel this agreement with 30 days written notice. Client is responsible for payment of all work completed through the cancellation date.',
                'category' => 'general',
                'order' => 3,
            ],
        ];

        foreach ($terms as $term) {
            TermsLibrary::create([
                'title' => $term['title'],
                'content' => $term['content'],
                'category' => $term['category'],
                'is_default' => true,
                'order' => $term['order'],
                'created_by' => null,
            ]);
        }
    }
}
