<?php

namespace Database\Seeders;

use App\Models\CatalogItem;
use App\Models\Category;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteTemplate;
use App\Models\Tenant;
use App\Models\TermsLibrary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LaserEngravingTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing tenant if it exists
        $existingTenant = Tenant::find('laser-engraving-demo');
        if ($existingTenant) {
            $existingTenant->delete();
            $this->command->info('Deleted existing tenant');
        }

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

        // Run tenant migrations
        $this->command->info('Running tenant migrations...');
        \Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
        ]);
        $this->command->info('Tenant migrations complete');

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
        $this->command->info('Domain: laser.estimo.test');
        $this->command->info('Email: owner@precisionlaser.test');
        $this->command->info('Password: password');
    }

    private function seedUsers(): void
    {
        User::firstOrCreate(
            ['email' => 'owner@precisionlaser.test'],
            [
                'name' => 'Sarah Martinez',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'sales@precisionlaser.test'],
            [
                'name' => 'Mike Johnson',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedClients(): void
    {
        $clients = [
            [
                'company_name' => 'TechStart Solutions Inc.',
                'contact_name' => 'TechStart Solutions',
                'email' => 'procurement@techstart.com',
                'phone' => '(555) 234-5678',
                'address' => '456 Tech Plaza',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78702',
                'country' => 'USA',
                'notes' => 'Corporate gifts and awards client. Prefers brushed aluminum.',
                'status' => 'active',
            ],
            [
                'company_name' => 'Brewmaster Craft Beer Co.',
                'contact_name' => 'Brewmaster Craft Beer',
                'email' => 'orders@brewmaster.com',
                'phone' => '(555) 345-6789',
                'address' => '789 Brewery Lane',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78703',
                'country' => 'USA',
                'notes' => 'Custom beer tap handles and glassware. Monthly orders.',
                'status' => 'active',
            ],
            [
                'company_name' => 'Riverside Wedding & Events',
                'contact_name' => 'Riverside Wedding Venue',
                'email' => 'events@riversidewedding.com',
                'phone' => '(555) 456-7890',
                'address' => '321 River Road',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78704',
                'country' => 'USA',
                'notes' => 'Wedding favors and signage. Peak season: April-October.',
                'status' => 'active',
            ],
            [
                'company_name' => 'Local Artist Collective',
                'contact_name' => 'Local Artist Collective',
                'email' => 'info@localartists.com',
                'phone' => '(555) 567-8901',
                'address' => '654 Gallery Street',
                'city' => 'Austin',
                'state' => 'TX',
                'postal_code' => '78705',
                'country' => 'USA',
                'notes' => 'Custom art pieces and installations. Project-based work.',
                'status' => 'active',
            ],
            [
                'company_name' => 'Pet Paradise Retail',
                'contact_name' => 'Pet Paradise Store',
                'email' => 'wholesale@petparadise.com',
                'phone' => '(555) 678-9012',
                'address' => '987 Pet Lane',
                'city' => 'Round Rock',
                'state' => 'TX',
                'postal_code' => '78681',
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
            ['name' => 'Awards & Trophies', 'slug' => 'awards-trophies', 'description' => 'Engraved awards, plaques, and trophies'],
            ['name' => 'Corporate Gifts', 'slug' => 'corporate-gifts', 'description' => 'Personalized corporate gifts and promotional items'],
            ['name' => 'Signage', 'slug' => 'signage', 'description' => 'Custom engraved signs and nameplates'],
            ['name' => 'Jewelry', 'slug' => 'jewelry', 'description' => 'Personalized jewelry engraving'],
            ['name' => 'Drinkware', 'slug' => 'drinkware', 'description' => 'Custom engraved glasses, mugs, and bottles'],
            ['name' => 'Pet Tags', 'slug' => 'pet-tags', 'description' => 'Personalized pet identification tags'],
            ['name' => 'Custom Products', 'slug' => 'custom-products', 'description' => 'Custom laser engraving on client-provided materials'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
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
                'cost_price' => 22.00,
                'selling_price' => 45.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 25,
                'low_stock_threshold' => 10,
            ],
            [
                'category_id' => $awardsCategory->id,
                'name' => 'Crystal Award - Large',
                'description' => 'Premium crystal award with custom engraving (up to 100 characters)',
                'sku' => 'AWD-CRY-LG',
                'cost_price' => 42.00,
                'selling_price' => 89.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 15,
                'low_stock_threshold' => 5,
            ],
            [
                'category_id' => $awardsCategory->id,
                'name' => 'Wooden Plaque',
                'description' => 'Walnut wood plaque with brass plate engraving',
                'sku' => 'AWD-PLQ-WD',
                'cost_price' => 18.00,
                'selling_price' => 35.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 30,
                'low_stock_threshold' => 15,
            ],
            // Corporate Gifts
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Personalized Pen Set',
                'description' => 'Executive pen set with custom name engraving',
                'sku' => 'CORP-PEN-SET',
                'cost_price' => 12.00,
                'selling_price' => 28.00,
                'unit_type' => 'set',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 50,
                'low_stock_threshold' => 20,
            ],
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Engraved Business Card Holder',
                'description' => 'Stainless steel card holder with logo/name engraving',
                'sku' => 'CORP-CARD-HLDR',
                'cost_price' => 10.00,
                'selling_price' => 22.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 40,
                'low_stock_threshold' => 15,
            ],
            [
                'category_id' => $corporateCategory->id,
                'name' => 'Custom Logo USB Drive',
                'description' => 'Wooden USB drive (16GB) with laser-engraved logo',
                'sku' => 'CORP-USB-WD',
                'cost_price' => 8.00,
                'selling_price' => 18.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 100,
                'low_stock_threshold' => 50,
            ],
            // Signage
            [
                'category_id' => $signageCategory->id,
                'name' => 'Office Door Nameplate',
                'description' => 'Brushed aluminum nameplate (8" x 2")',
                'sku' => 'SIGN-DOOR-AL',
                'cost_price' => 14.00,
                'selling_price' => 32.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $signageCategory->id,
                'name' => 'Custom Warning Sign',
                'description' => 'Safety/warning sign with custom text (12" x 18")',
                'sku' => 'SIGN-WARN-12X18',
                'cost_price' => 20.00,
                'selling_price' => 45.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
            // Jewelry
            [
                'category_id' => $jewelryCategory->id,
                'name' => 'Bracelet Engraving',
                'description' => 'Custom text engraving on bracelet (up to 30 characters)',
                'sku' => 'JWL-BRAC-ENG',
                'cost_price' => 3.00,
                'selling_price' => 15.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $jewelryCategory->id,
                'name' => 'Pendant Engraving',
                'description' => 'Custom text/date engraving on pendant',
                'sku' => 'JWL-PEND-ENG',
                'cost_price' => 4.00,
                'selling_price' => 20.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
            // Drinkware
            [
                'category_id' => $drinkwareCategory->id,
                'name' => 'Engraved Pint Glass',
                'description' => '16oz pint glass with custom design/text',
                'sku' => 'DRK-PINT-16',
                'cost_price' => 5.00,
                'selling_price' => 12.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 200,
                'low_stock_threshold' => 50,
            ],
            [
                'category_id' => $drinkwareCategory->id,
                'name' => 'Stainless Steel Tumbler',
                'description' => '20oz insulated tumbler with laser engraving',
                'sku' => 'DRK-TUMBLER-20',
                'cost_price' => 11.00,
                'selling_price' => 25.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 75,
                'low_stock_threshold' => 25,
            ],
            // Pet Tags
            [
                'category_id' => $petCategory->id,
                'name' => 'Stainless Steel Pet Tag',
                'description' => 'Durable pet ID tag with custom engraving (both sides)',
                'sku' => 'PET-TAG-SS',
                'cost_price' => 2.50,
                'selling_price' => 8.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 500,
                'low_stock_threshold' => 100,
            ],
            [
                'category_id' => $petCategory->id,
                'name' => 'Bone-Shaped Pet Tag',
                'description' => 'Bone-shaped aluminum tag with engraving',
                'sku' => 'PET-TAG-BONE',
                'cost_price' => 2.00,
                'selling_price' => 7.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => true,
                'stock_quantity' => 300,
                'low_stock_threshold' => 75,
            ],
            // Custom
            [
                'category_id' => $customCategory->id,
                'name' => 'Custom Engraving - Hourly',
                'description' => 'Custom laser engraving service (per hour)',
                'sku' => 'CUST-ENG-HR',
                'cost_price' => 0.00,
                'selling_price' => 75.00,
                'unit_type' => 'hour',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
            [
                'category_id' => $customCategory->id,
                'name' => 'Setup Fee - Complex Design',
                'description' => 'One-time setup fee for complex artwork preparation',
                'sku' => 'CUST-SETUP-FEE',
                'cost_price' => 0.00,
                'selling_price' => 50.00,
                'unit_type' => 'each',
                'is_taxable' => true,
                'track_inventory' => false,
            ],
        ];

        foreach ($items as $itemData) {
            CatalogItem::firstOrCreate(
                ['sku' => $itemData['sku']],
                $itemData
            );
        }
    }

    private function seedQuotes(): void
    {
        $techStartClient = Client::where('company_name', 'TechStart Solutions Inc.')->first();
        $brewmasterClient = Client::where('company_name', 'Brewmaster Craft Beer Co.')->first();

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
            'terms' => "## Payment Terms\n\n50% deposit required upon order confirmation. Remaining balance due upon completion.\n\n## Production Timeline\n\n7-10 business days for production after artwork approval.\n\n## Artwork\n\nClient to provide high-resolution logo file (vector format preferred).\n\n## Warranty\n\n30-day warranty on engraving quality.",
            'footer' => 'Thank you for choosing Precision Laser Engraving Co.!',
            'tax_rate' => 8.25,
            'created_by' => User::first()->id,
        ]);

        $crystalSmall = CatalogItem::where('sku', 'AWD-CRY-SM')->first();
        $crystalLarge = CatalogItem::where('sku', 'AWD-CRY-LG')->first();

        QuoteItem::create([
            'quote_id' => $quote1->id,
            'catalog_item_id' => $crystalLarge->id,
            'name' => 'Crystal Award - Large',
            'description' => 'Large Crystal Award - "Excellence in Leadership" with company logo',
            'quantity' => 3,
            'unit_price' => 89.00,
            'subtotal' => 267.00,
            'sort_order' => 1,
        ]);

        QuoteItem::create([
            'quote_id' => $quote1->id,
            'catalog_item_id' => $crystalSmall->id,
            'name' => 'Crystal Award - Small',
            'description' => 'Small Crystal Award - "Outstanding Performance" with company logo',
            'quantity' => 10,
            'unit_price' => 45.00,
            'subtotal' => 450.00,
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
            'terms' => "## Payment Terms\n\n50% deposit, 50% upon completion.\n\n## Production Timeline\n\n3-4 weeks for initial production run. Reorders ship within 1 week.\n\n## Setup Fee\n\nOne-time setup fee applies for new designs. Valid for 12 months for reorders.\n\n## Warranty\n\n90-day warranty on craftsmanship.",
            'footer' => 'We look forward to working with you!',
            'tax_rate' => 8.25,
            'created_by' => User::first()->id,
        ]);

        $customHourly = CatalogItem::where('sku', 'CUST-ENG-HR')->first();
        $setupFee = CatalogItem::where('sku', 'CUST-SETUP-FEE')->first();

        QuoteItem::create([
            'quote_id' => $quote2->id,
            'catalog_item_id' => $setupFee->id,
            'name' => 'Setup Fee - Complex Design',
            'description' => 'Design setup and artwork preparation for tap handle design',
            'quantity' => 1,
            'unit_price' => 50.00,
            'subtotal' => 50.00,
            'sort_order' => 1,
        ]);

        QuoteItem::create([
            'quote_id' => $quote2->id,
            'catalog_item_id' => $customHourly->id,
            'name' => 'Custom Engraving - Hourly',
            'description' => 'Custom laser engraving on walnut tap handles (estimated 8 hours for 50 pieces)',
            'quantity' => 8,
            'unit_price' => 75.00,
            'subtotal' => 600.00,
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
