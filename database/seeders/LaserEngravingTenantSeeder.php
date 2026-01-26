<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Category;
use App\Models\CatalogItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteTemplate;
use App\Models\TermsLibrary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LaserEngravingTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create tenant
        $tenant = Tenant::create([
            'id' => 'laser-engraving-demo',
            'name' => 'Precision Laser Engraving Co.',
            'email' => 'info@precisionlaser.test',
            'plan' => 'professional',
            'trial_ends_at' => now()->addDays(30),
            'company_name' => 'Precision Laser Engraving Co.',
            'company_phone' => '(555) 123-4567',
            'company_address' => '123 Industrial Parkway',
            'company_city' => 'Austin',
            'company_state' => 'TX',
            'company_zip' => '78701',
            'company_country' => 'USA',
        ]);

        // Create domain
        $tenant->domains()->create([
            'domain' => 'laser.estimo.test',
        ]);

        // Run tenant-specific seeding
        tenancy()->initialize($tenant);

        $this->seedUsers();
        $this->seedClients();
        $this->seedCategories();
        $this->seedCatalogItems();
        $this->seedQuotes();
        $this->seedTemplates();
        $this->seedTerms();

        tenancy()->end();

        $this->command->info('Laser engraving tenant seeded successfully!');
        $this->command->info('Domain: precisionlaser.localhost');
        $this->command->info('Email: owner@precisionlaser.test');
        $this->command->info('Password: password');
    }

    private function seedUsers(): void
    {
        User::create([
            'name' => 'Sarah Martinez',
            'email' => 'owner@precisionlaser.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'sales@precisionlaser.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    private function seedClients(): void
    {
        $clients = [
            [
                'name' => 'TechStart Solutions',
                'email' => 'procurement@techstart.com',
                'phone' => '(555) 234-5678',
                'company' => 'TechStart Solutions Inc.',
                'address' => '456 Tech Plaza',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78702',
                'country' => 'USA',
                'notes' => 'Corporate gifts and awards client. Prefers brushed aluminum.',
                'status' => 'active',
            ],
            [
                'name' => 'Brewmaster Craft Beer',
                'email' => 'orders@brewmaster.com',
                'phone' => '(555) 345-6789',
                'company' => 'Brewmaster Craft Beer Co.',
                'address' => '789 Brewery Lane',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78703',
                'country' => 'USA',
                'notes' => 'Custom beer tap handles and glassware. Monthly orders.',
                'status' => 'active',
            ],
            [
                'name' => 'Riverside Wedding Venue',
                'email' => 'events@riversidewedding.com',
                'phone' => '(555) 456-7890',
                'company' => 'Riverside Wedding & Events',
                'address' => '321 River Road',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78704',
                'country' => 'USA',
                'notes' => 'Wedding favors and signage. Peak season: April-October.',
                'status' => 'active',
            ],
            [
                'name' => 'Local Artist Collective',
                'email' => 'info@localartists.com',
                'phone' => '(555) 567-8901',
                'company' => 'Local Artist Collective',
                'address' => '654 Gallery Street',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78705',
                'country' => 'USA',
                'notes' => 'Custom art pieces and installations. Project-based work.',
                'status' => 'active',
            ],
            [
                'name' => 'Pet Paradise Store',
                'email' => 'wholesale@petparadise.com',
                'phone' => '(555) 678-9012',
                'company' => 'Pet Paradise Retail',
                'address' => '987 Pet Lane',
                'city' => 'Round Rock',
                'state' => 'TX',
                'zip' => '78681',
                'country' => 'USA',
                'notes' => 'Custom pet tags and accessories. Wholesale client.',
                'status' => 'active',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Awards & Trophies', 'description' => 'Engraved awards, plaques, and trophies'],
            ['name' => 'Corporate Gifts', 'description' => 'Personalized corporate gifts and promotional items'],
            ['name' => 'Signage', 'description' => 'Custom engraved signs and nameplates'],
            ['name' => 'Jewelry', 'description' => 'Personalized jewelry engraving'],
            ['name' => 'Drinkware', 'description' => 'Custom engraved glasses, mugs, and bottles'],
            ['name' => 'Pet Tags', 'description' => 'Personalized pet identification tags'],
            ['name' => 'Custom Products', 'description' => 'Custom laser engraving on client-provided materials'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }

    private function seedCatalogItems(): void
    {
        $awardsCategory = Category::where('name', 'Awards & Trophies')->first();
        $corporateCategory = Category::where('name', 'Corporate Gifts')->first();
        $signageCategory = Category::where('name', 'Signage')->first();
        $jewelryCategory = Category::where('name', 'Jewelry')->first();
        $drinkwareCategory = Category::where('name', 'Drinkware')->first();
        $petCategory = Category::where('name', 'Pet Tags')->first();
        $customCategory = Category::where('name', 'Custom Products')->first();

        $items = [
            // Awards & Trophies
            [
                'category_id' => $awardsCategory->id,
                'name' => 'Crystal Award - Small',
                'description' => 'Premium crystal award with custom engraving (up to 50 characters)',
                'sku' => 'AWD-CRY-SM',
                'price' => 45.00,
                'cost' => 22.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 25,
                'reorder_level' => 10,
            ],
            [
                'category_id' => $awardsCategory->id,
                'name' => 'Crystal Award - Large',
                'description' => 'Premium crystal award with custom engraving (up to 100 characters)',
                'sku' => 'AWD-CRY-LG',
                'price' => 89.00,
                'cost' => 42.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 15,
                'reorder_level' => 5,
            ],
            [
                'category_id' => $awardsCategory->id,
                'name' => 'Wooden Plaque',
                'description' => 'Walnut wood plaque with brass plate engraving',
                'sku' => 'AWD-PLQ-WD',
                'price' => 35.00,
                'cost' => 18.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 30,
                'reorder_level' => 15,
            ],
            // Corporate Gifts
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Personalized Pen Set',
                'description' => 'Executive pen set with custom name engraving',
                'sku' => 'CORP-PEN-SET',
                'price' => 28.00,
                'cost' => 12.00,
                'unit' => 'set',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 50,
                'reorder_level' => 20,
            ],
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Engraved Business Card Holder',
                'description' => 'Stainless steel card holder with logo/name engraving',
                'sku' => 'CORP-CARD-HLDR',
                'price' => 22.00,
                'cost' => 10.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 40,
                'reorder_level' => 15,
            ],
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Custom Logo USB Drive',
                'description' => 'Wooden USB drive (16GB) with laser-engraved logo',
                'sku' => 'CORP-USB-WD',
                'price' => 18.00,
                'cost' => 8.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 100,
                'reorder_level' => 50,
            ],
            // Signage
            [
                'category_id' => $signageCategory->id,
                'name' => 'Office Door Nameplate',
                'description' => 'Brushed aluminum nameplate (8" x 2")',
                'sku' => 'SIGN-DOOR-AL',
                'price' => 32.00,
                'cost' => 14.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $signageCategory->id,
                'name' => 'Custom Warning Sign',
                'description' => 'Safety/warning sign with custom text (12" x 18")',
                'sku' => 'SIGN-WARN-12X18',
                'price' => 45.00,
                'cost' => 20.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => false,
            ],
            // Jewelry
            [
                'category_id' => $jewelryCategory->id,
                'name' => 'Bracelet Engraving',
                'description' => 'Custom text engraving on bracelet (up to 30 characters)',
                'sku' => 'JWL-BRAC-ENG',
                'price' => 15.00,
                'cost' => 3.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $jewelryCategory->id,
                'name' => 'Pendant Engraving',
                'description' => 'Custom text/date engraving on pendant',
                'sku' => 'JWL-PEND-ENG',
                'price' => 20.00,
                'cost' => 4.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => false,
            ],
            // Drinkware
            [
                'category_id' => $drinkwareCategory->id,
                'name' => 'Engraved Pint Glass',
                'description' => '16oz pint glass with custom design/text',
                'sku' => 'DRK-PINT-16',
                'price' => 12.00,
                'cost' => 5.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 200,
                'reorder_level' => 50,
            ],
            [
                'category_id' => $drinkwareCategory->id,
                'name' => 'Stainless Steel Tumbler',
                'description' => '20oz insulated tumbler with laser engraving',
                'sku' => 'DRK-TUMBLER-20',
                'price' => 25.00,
                'cost' => 11.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 75,
                'reorder_level' => 25,
            ],
            // Pet Tags
            [
                'category_id' => $petCategory->id,
                'name' => 'Stainless Steel Pet Tag',
                'description' => 'Durable pet ID tag with custom engraving (both sides)',
                'sku' => 'PET-TAG-SS',
                'price' => 8.00,
                'cost' => 2.50,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 500,
                'reorder_level' => 100,
            ],
            [
                'category_id' => $petCategory->id,
                'name' => 'Bone-Shaped Pet Tag',
                'description' => 'Bone-shaped aluminum tag with engraving',
                'sku' => 'PET-TAG-BONE',
                'price' => 7.00,
                'cost' => 2.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => true,
                'quantity_on_hand' => 300,
                'reorder_level' => 75,
            ],
            // Custom
            [
                'category_id' => $customCategory->id,
                'name' => 'Custom Engraving - Hourly',
                'description' => 'Custom laser engraving service (per hour)',
                'sku' => 'CUST-ENG-HR',
                'price' => 75.00,
                'cost' => 0.00,
                'unit' => 'hour',
                'taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $customCategory->id,
                'name' => 'Setup Fee - Complex Design',
                'description' => 'One-time setup fee for complex artwork preparation',
                'sku' => 'CUST-SETUP-FEE',
                'price' => 50.00,
                'cost' => 0.00,
                'unit' => 'each',
                'taxable' => true,
                'track_inventory' => false,
            ],
        ];

        foreach ($items as $itemData) {
            CatalogItem::create($itemData);
        }
    }

    private function seedQuotes(): void
    {
        $techStartClient = Client::where('company', 'TechStart Solutions Inc.')->first();
        $brewmasterClient = Client::where('company', 'Brewmaster Craft Beer Co.')->first();

        // Quote 1 - Corporate Awards
        $quote1 = Quote::create([
            'quote_number' => 'Q-2026-0001',
            'client_id' => $techStartClient->id,
            'title' => 'Q1 Employee Recognition Awards',
            'quote_date' => now()->subDays(5),
            'valid_until' => now()->addDays(25),
            'status' => 'sent',
            'sent_at' => now()->subDays(3),
            'notes' => 'Annual employee recognition program. Company logo to be engraved on each award.',
            'terms_conditions' => "## Payment Terms\n\n50% deposit required upon order confirmation. Remaining balance due upon completion.\n\n## Production Timeline\n\n7-10 business days for production after artwork approval.\n\n## Artwork\n\nClient to provide high-resolution logo file (vector format preferred).\n\n## Warranty\n\n30-day warranty on engraving quality.",
            'footer' => 'Thank you for choosing Precision Laser Engraving Co.!',
            'tax_rate' => 8.25,
            'created_by' => User::first()->id,
        ]);

        $crystalSmall = CatalogItem::where('sku', 'AWD-CRY-SM')->first();
        $crystalLarge = CatalogItem::where('sku', 'AWD-CRY-LG')->first();

        QuoteItem::create([
            'quote_id' => $quote1->id,
            'catalog_item_id' => $crystalLarge->id,
            'description' => 'Large Crystal Award - "Excellence in Leadership" with company logo',
            'quantity' => 3,
            'unit_price' => 89.00,
            'tax_rate' => 8.25,
            'sort_order' => 1,
        ]);

        QuoteItem::create([
            'quote_id' => $quote1->id,
            'catalog_item_id' => $crystalSmall->id,
            'description' => 'Small Crystal Award - "Outstanding Performance" with company logo',
            'quantity' => 10,
            'unit_price' => 45.00,
            'tax_rate' => 8.25,
            'sort_order' => 2,
        ]);

        $quote1->calculateTotals();

        // Quote 2 - Custom Drinkware
        $quote2 = Quote::create([
            'quote_number' => 'Q-2026-0002',
            'client_id' => $brewmasterClient->id,
            'title' => 'Custom Tap Handle Production Run',
            'quote_date' => now()->subDays(2),
            'valid_until' => now()->addDays(28),
            'status' => 'draft',
            'notes' => 'Custom walnut tap handles with brewery logo. Requires setup fee for new design.',
            'terms_conditions' => "## Payment Terms\n\n50% deposit, 50% upon completion.\n\n## Production Timeline\n\n3-4 weeks for initial production run. Reorders ship within 1 week.\n\n## Setup Fee\n\nOne-time setup fee applies for new designs. Valid for 12 months for reorders.\n\n## Warranty\n\n90-day warranty on craftsmanship.",
            'footer' => 'We look forward to working with you!',
            'tax_rate' => 8.25,
            'created_by' => User::first()->id,
        ]);

        $customHourly = CatalogItem::where('sku', 'CUST-ENG-HR')->first();
        $setupFee = CatalogItem::where('sku', 'CUST-SETUP-FEE')->first();

        QuoteItem::create([
            'quote_id' => $quote2->id,
            'catalog_item_id' => $setupFee->id,
            'description' => 'Design setup and artwork preparation for tap handle design',
            'quantity' => 1,
            'unit_price' => 50.00,
            'tax_rate' => 8.25,
            'sort_order' => 1,
        ]);

        QuoteItem::create([
            'quote_id' => $quote2->id,
            'catalog_item_id' => $customHourly->id,
            'description' => 'Custom laser engraving on walnut tap handles (estimated 8 hours for 50 pieces)',
            'quantity' => 8,
            'unit_price' => 75.00,
            'tax_rate' => 8.25,
            'sort_order' => 2,
            'notes' => 'Materials (walnut blanks) provided by client',
        ]);

        $quote2->calculateTotals();
    }

    private function seedTemplates(): void
    {
        QuoteTemplate::create([
            'name' => 'Corporate Recognition Awards',
            'description' => 'Standard template for corporate award orders',
            'category' => 'awards',
            'is_default' => true,
            'is_industry_preset' => false,
            'template_data' => [
                'title' => 'Employee Recognition Awards',
                'notes' => 'Please provide company logo in vector format for best results.',
                'valid_until_days' => 30,
                'items' => [],
            ],
            'terms_conditions' => "## Payment Terms\n\n50% deposit required upon order confirmation. Remaining balance due upon completion.\n\n## Production Timeline\n\n7-10 business days for standard orders after artwork approval.\n\n## Artwork Requirements\n\nClient to provide high-resolution artwork (vector format preferred: AI, EPS, or PDF).\n\n## Approval Process\n\nDigital proof will be provided for approval before production begins.\n\n## Warranty\n\n30-day warranty on engraving quality and craftsmanship.",
            'created_by' => User::first()->id,
        ]);

        QuoteTemplate::create([
            'name' => 'Custom Drinkware - Bulk Order',
            'description' => 'Template for bulk drinkware engraving orders',
            'category' => 'drinkware',
            'is_default' => false,
            'is_industry_preset' => false,
            'template_data' => [
                'title' => 'Custom Engraved Drinkware',
                'notes' => 'Volume pricing available for orders over 100 pieces.',
                'valid_until_days' => 30,
                'items' => [],
            ],
            'terms_conditions' => "## Payment Terms\n\n50% deposit, 50% upon completion.\n\n## Volume Discounts\n\n- 100-249 pieces: 10% discount\n- 250-499 pieces: 15% discount\n- 500+ pieces: Contact for custom pricing\n\n## Production Timeline\n\n2-3 weeks for orders up to 100 pieces. Larger orders may require additional time.\n\n## Quality Guarantee\n\nWe inspect every piece before shipment. Any defects will be replaced at no charge.",
            'created_by' => User::first()->id,
        ]);

        QuoteTemplate::create([
            'name' => 'Pet Tag - Retail Wholesale',
            'description' => 'Template for wholesale pet tag orders',
            'category' => 'pet_products',
            'is_default' => false,
            'is_industry_preset' => false,
            'template_data' => [
                'title' => 'Wholesale Pet Tags Order',
                'notes' => 'Minimum order quantity: 100 pieces.',
                'valid_until_days' => 45,
                'items' => [],
            ],
            'terms_conditions' => "## Wholesale Terms\n\nNet 30 payment terms for established accounts. New customers: 50% deposit.\n\n## Minimum Order\n\n100 pieces minimum per order.\n\n## Production Time\n\n5-7 business days for standard orders.\n\n## Returns\n\nDefective products may be returned within 30 days for replacement.",
            'created_by' => User::first()->id,
        ]);
    }

    private function seedTerms(): void
    {
        TermsLibrary::create([
            'title' => 'Laser Engraving - Standard Payment',
            'content' => '50% deposit is required to begin work on your order. The remaining 50% is due upon completion and before shipment or pickup. We accept major credit cards, checks, and bank transfers.',
            'category' => 'payment',
            'is_default' => true,
            'order' => 1,
            'created_by' => User::first()->id,
        ]);

        TermsLibrary::create([
            'title' => 'Artwork Requirements',
            'content' => 'Client must provide artwork in a high-resolution format. Vector files (AI, EPS, PDF) are preferred for best results. Raster images (JPG, PNG) should be at least 300 DPI. We offer artwork preparation services at our hourly rate if needed.',
            'category' => 'general',
            'is_default' => true,
            'order' => 1,
            'created_by' => User::first()->id,
        ]);

        TermsLibrary::create([
            'title' => 'Production Timeline - Standard',
            'content' => 'Standard production time is 7-10 business days after artwork approval and deposit receipt. Rush orders may be available for an additional fee. Production time does not include shipping time.',
            'category' => 'delivery',
            'is_default' => true,
            'order' => 1,
            'created_by' => User::first()->id,
        ]);

        TermsLibrary::create([
            'title' => 'Engraving Quality Guarantee',
            'content' => 'We guarantee the quality of our engraving work for 30 days from delivery. This warranty covers defects in engraving quality and craftsmanship. It does not cover damage from misuse, accidents, or normal wear and tear.',
            'category' => 'warranty',
            'is_default' => true,
            'order' => 1,
            'created_by' => User::first()->id,
        ]);

        TermsLibrary::create([
            'title' => 'Material Specifications',
            'content' => 'All materials are subject to natural variations in grain, color, and texture. We will make every effort to match samples, but exact matches cannot be guaranteed with natural materials like wood and leather.',
            'category' => 'general',
            'is_default' => false,
            'order' => 2,
            'created_by' => User::first()->id,
        ]);

        TermsLibrary::create([
            'title' => 'Rush Order Fee',
            'content' => 'Rush orders requiring completion in less than 7 business days are subject to a 25% rush fee. Rush orders requiring completion in less than 3 business days are subject to a 50% rush fee. Rush service is subject to availability.',
            'category' => 'general',
            'is_default' => false,
            'order' => 3,
            'created_by' => User::first()->id,
        ]);
    }
}
