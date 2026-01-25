<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ActivityLog;
use Livewire\Component;

class ClientForm extends Component
{
    public ?int $clientId = null;

    // Company Information
    public $company_name = '';
    public $contact_name = '';
    public $email = '';
    public $phone = '';
    public $website = '';

    // Address Information
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = 'US';

    // Financial Information
    public $currency = 'USD';
    public $tax_exempt = false;
    public $tax_rate = null;

    // Additional Information
    public $notes = '';
    public $tags = '';
    public $status = 'active';

    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|size:2',
            'currency' => 'required|string|size:3',
            'tax_exempt' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,inactive,archived',
        ];
    }

    public function mount()
    {
        if ($this->clientId) {
            $client = Client::findOrFail($this->clientId);

            $this->company_name = $client->company_name;
            $this->contact_name = $client->contact_name ?? '';
            $this->email = $client->email ?? '';
            $this->phone = $client->phone ?? '';
            $this->website = $client->website ?? '';
            $this->address = $client->address ?? '';
            $this->city = $client->city ?? '';
            $this->state = $client->state ?? '';
            $this->postal_code = $client->postal_code ?? '';
            $this->country = $client->country;
            $this->currency = $client->currency;
            $this->tax_exempt = $client->tax_exempt;
            $this->tax_rate = $client->tax_rate;
            $this->notes = $client->notes ?? '';
            $this->tags = is_array($client->tags) ? implode(', ', $client->tags) : '';
            $this->status = $client->status;
        }
    }

    public function save()
    {
        $validated = $this->validate();

        // Convert tags string to array
        $tagsArray = array_filter(
            array_map('trim', explode(',', $validated['tags'] ?? '')),
            fn($tag) => !empty($tag)
        );
        $validated['tags'] = !empty($tagsArray) ? $tagsArray : null;

        if ($this->clientId) {
            // Update existing client
            $client = Client::findOrFail($this->clientId);
            $client->update($validated);

            ActivityLog::log(
                'client_updated',
                auth()->user()->name . ' updated client: ' . $client->company_name,
                $client,
                ['changes' => $validated]
            );

            session()->flash('success', 'Client updated successfully.');
        } else {
            // Create new client
            $validated['created_by'] = auth()->id();
            $client = Client::create($validated);

            ActivityLog::log(
                'client_created',
                auth()->user()->name . ' created client: ' . $client->company_name,
                $client
            );

            session()->flash('success', 'Client created successfully.');
        }

        $this->dispatch('saved');
    }

    public function cancel()
    {
        $this->dispatch('cancelled');
    }

    public function render()
    {
        return view('livewire.clients.client-form', [
            'currencies' => $this->getCurrencies(),
            'countries' => $this->getCountries(),
            'statuses' => $this->getStatuses(),
        ]);
    }

    protected function getCurrencies(): array
    {
        return [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
            'JPY' => 'JPY - Japanese Yen',
            'CNY' => 'CNY - Chinese Yuan',
            'INR' => 'INR - Indian Rupee',
        ];
    }

    protected function getCountries(): array
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'ES' => 'Spain',
            'IT' => 'Italy',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'IE' => 'Ireland',
            'NZ' => 'New Zealand',
            'SG' => 'Singapore',
            'HK' => 'Hong Kong',
            'JP' => 'Japan',
            'CN' => 'China',
            'IN' => 'India',
            'MX' => 'Mexico',
            'BR' => 'Brazil',
        ];
    }

    protected function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ];
    }
}
