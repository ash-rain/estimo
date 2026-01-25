# Estimo - Requirements & Planning Document

## Project Overview
Estimo is a multi-industry SaaS platform for creating professional quotes and cost estimations. The platform provides flexible, industry-agnostic tools that can be customized for various business sectors including construction, manufacturing, professional services, and more.

## Tech Stack
- **Backend**: Laravel 11.x
- **Frontend**: Livewire 4 + Alpine.js + Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **File Storage**: Laravel Storage (S3 compatible)
- **Queue**: Redis
- **Authentication**: Laravel Breeze/Jetstream with Livewire
- **Payments**: Laravel Cashier (Stripe)

## Core Features

### 1. Multi-Tenancy & Organization Management
- Workspace/Company profiles
- Team member management with role-based access
- Workspace settings and branding customization
- Multi-currency support

### 2. Client Management (CRM Lite)
- Client database with contact information
- Client history and past quotes
- Client-specific pricing rules
- Client portal for viewing quotes

### 3. Product/Service Catalog
- Flexible item catalog (products, services, labor)
- Categories and tags
- Cost vs. selling price tracking
- Unit types (hours, units, sq ft, linear ft, etc.)
- Bulk import/export
- Item variants and options

### 4. Quote/Estimate Builder
- Drag-and-drop line items
- Section/category organization
- Quantity and unit price adjustments
- Markup/margin calculations
- Discount application (percentage or fixed)
- Tax configuration (multiple tax rates)
- Terms and conditions templates
- Optional items (add-ons)
- Valid-until date

### 5. Cost Estimation Engine
- Material cost tracking
- Labor cost calculations
- Overhead allocation
- Profit margin targeting
- Real-time total calculation
- Cost breakdown visibility

### 6. Templates & Industry Presets
- Quote templates by industry
- Customizable sections
- Default terms and conditions
- Branding templates (logo, colors, fonts)
- Email templates

### 7. Pricing Rules & Logic
- Volume discounts
- Client-specific pricing
- Time-based pricing
- Bundle pricing
- Formula-based calculations
- Industry-specific calculation logic

### 8. Quote Workflow
- Draft → Sent → Viewed → Accepted/Rejected
- Quote versioning (revisions)
- Expiration handling
- Quote comparison (multiple versions)
- Approval workflows (internal)
- E-signature integration

### 9. PDF Generation & Delivery
- Professional PDF exports
- Custom branding
- Email delivery with tracking
- Client portal access
- Download analytics

### 10. Reporting & Analytics
- Quote conversion rates
- Average quote value
- Win/loss analysis
- Product/service performance
- Revenue forecasting
- Time-to-quote metrics
- Profit margin analysis

### 11. Integration Capabilities
- Accounting software (QuickBooks, Xero)
- CRM systems
- Project management tools
- Payment processors
- Email marketing platforms
- API for custom integrations

## User Roles & Permissions

### Super Admin (Platform)
- System configuration
- Tenant management
- Global analytics

### Workspace Owner
- Full access to workspace
- Billing and subscription
- Team management
- Settings configuration

### Admin
- Team member management
- Catalog management
- Client management
- Quote creation and approval
- Reports access

### Manager
- Quote creation and approval
- Catalog viewing/editing
- Client management
- Team reports

### Sales/Estimator
- Quote creation
- Client viewing
- Catalog usage
- Own quote management

### Viewer
- Read-only access
- View quotes and reports
- No editing capabilities

### Client (Portal User)
- View assigned quotes
- Accept/reject quotes
- Download PDFs
- Communication thread

## Database Schema (Core Tables)

### Tenants/Workspaces
- id, name, slug, domain, settings, subscription_tier, status

### Users
- id, tenant_id, name, email, role, permissions

### Clients
- id, tenant_id, company_name, contact_name, email, phone, address, currency, tax_exempt

### Catalog Items
- id, tenant_id, name, description, type (product/service/labor), sku, cost_price, selling_price, unit_type, category_id, is_active

### Categories
- id, tenant_id, name, parent_id, industry_type

### Quotes
- id, tenant_id, client_id, quote_number, title, status, subtotal, tax_total, discount, total, currency, valid_until, created_by, approved_by, notes, terms

### Quote Sections
- id, quote_id, name, order, description

### Quote Items
- id, quote_id, section_id, catalog_item_id, description, quantity, unit_price, cost_price, tax_rate, discount, subtotal, is_optional, order

### Quote Versions
- id, quote_id, version_number, data_snapshot, created_by

### Quote Activities
- id, quote_id, user_id, action, metadata, ip_address

### Templates
- id, tenant_id, name, type (quote/email), content, settings

### Pricing Rules
- id, tenant_id, name, conditions, actions, priority, is_active

## Key User Flows

### 1. Creating a Quote
1. Select client (or create new)
2. Choose template (optional)
3. Add sections/line items from catalog
4. Adjust quantities, pricing, discounts
5. Add terms, notes, attachments
6. Preview PDF
7. Save as draft or send immediately
8. Track client views and responses

### 2. Client Views Quote
1. Receive email with secure link
2. Access quote in branded portal
3. Review line items and totals
4. Ask questions (comments)
5. Accept or reject quote
6. Download PDF

### 3. Managing Catalog
1. Import/create items
2. Set cost and selling prices
3. Organize into categories
4. Set default tax rates
5. Configure unit types
6. Enable/disable items

## Industry-Specific Considerations

### Construction & Trades
- Square footage/linear footage calculations
- Material + labor bundling
- Subcontractor costs
- Permit and fee line items
- Change order support

### Manufacturing
- Material specifications
- Setup vs. per-unit costs
- Tooling costs
- Lead time tracking
- MOQ (minimum order quantity)

### Professional Services
- Hourly rate tiers
- Retainer vs. project pricing
- Scope of work sections
- Milestone-based payment terms

### General Requirements
- Flexible custom fields
- Formula builder for calculations
- Industry template library
- Adaptable terminology (quote/estimate/proposal)

## Technical Architecture

### Application Structure
```
app/
├── Models/
│   ├── Tenant
│   ├── User
│   ├── Client
│   ├── Quote
│   ├── QuoteItem
│   ├── CatalogItem
│   └── ...
├── Livewire/
│   ├── Quotes/
│   │   ├── QuoteBuilder.php
│   │   ├── QuoteList.php
│   │   └── QuotePreview.php
│   ├── Catalog/
│   ├── Clients/
│   └── Reports/
├── Services/
│   ├── QuoteService.php
│   ├── PricingEngine.php
│   ├── PdfGenerator.php
│   └── NotificationService.php
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
└── Jobs/
    ├── GenerateQuotePdf.php
    └── SendQuoteEmail.php
```

### Key Design Patterns
- Repository pattern for data access
- Service layer for business logic
- Observer pattern for quote lifecycle events
- Strategy pattern for pricing calculations
- Factory pattern for PDF generation

## Subscription Tiers

### Starter ($29/mo)
- 1 user
- Up to 50 quotes/month
- Basic templates
- PDF export
- Email support

### Professional ($79/mo)
- Up to 5 users
- Unlimited quotes
- Custom branding
- Client portal
- Advanced reporting
- Integration API
- Priority support

### Enterprise ($199/mo)
- Unlimited users
- White-label options
- Advanced permissions
- Custom integrations
- Dedicated support
- SLA guarantee

## Development Phases

### Phase 1: Foundation (MVP)
- Multi-tenancy setup
- Authentication & user management
- Basic client management
- Simple catalog
- Quote builder (basic)
- PDF generation
- Email delivery

### Phase 2: Core Features
- Quote versioning
- Client portal
- Advanced pricing rules
- Template system
- Quote workflow/approvals
- Basic reporting

### Phase 3: Advanced Features
- Industry presets
- Formula builder
- Advanced integrations
- E-signature
- Payment collection
- Mobile optimization

### Phase 4: Scale & Optimize
- API marketplace
- Advanced analytics
- White-label options
- Performance optimization
- Advanced automation

## Non-Functional Requirements

### Performance
- Page load < 2s
- PDF generation < 5s
- Support 1000+ concurrent users per server
- Real-time quote calculations

### Security
- SOC 2 compliance ready
- Data encryption at rest and in transit
- Regular security audits
- GDPR compliance
- Role-based access control
- Audit logging

### Scalability
- Horizontal scaling capability
- Database sharding for large tenants
- CDN for static assets
- Queue-based background processing

### UX/UI Principles
- Mobile-first responsive design
- Intuitive drag-and-drop interfaces
- Inline editing where possible
- Real-time feedback and validation
- Progressive disclosure of complexity
- Accessible (WCAG 2.1 AA)

## Success Metrics
- Time to create first quote < 10 minutes
- Quote conversion rate tracking
- User retention rate > 80%
- NPS score > 50
- Average quote creation time < 5 minutes
- Client portal adoption > 60%

## Next Steps
1. Set up Laravel project with Livewire 4
2. Implement multi-tenancy architecture
3. Design and implement database schema
4. Create authentication and user management
5. Build catalog management system
6. Develop quote builder interface
7. Implement PDF generation
8. Set up email delivery system
9. Create client portal
10. Add reporting dashboard
