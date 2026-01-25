<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'active';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showFormModal = false;
    public $showImportModal = false;
    public $editingClientId = null;

    public function render()
    {
        $clients = Client::query()
            ->with('creator')
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.clients.client-list', [
            'clients' => $clients,
            'statuses' => $this->getStatuses(),
        ]);
    }

    public function createClient()
    {
        $this->editingClientId = null;
        $this->showFormModal = true;
    }

    public function editClient($clientId)
    {
        $this->editingClientId = $clientId;
        $this->showFormModal = true;
    }

    public function deleteClient($clientId)
    {
        $client = Client::findOrFail($clientId);

        ActivityLog::log(
            'client_deleted',
            auth()->user()->name . ' deleted client: ' . $client->company_name,
            $client
        );

        $client->delete();

        session()->flash('success', 'Client deleted successfully.');
    }

    public function archiveClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        $client->archive();

        ActivityLog::log(
            'client_archived',
            auth()->user()->name . ' archived client: ' . $client->company_name,
            $client
        );

        session()->flash('success', 'Client archived successfully.');
    }

    public function exportClients()
    {
        $clients = Client::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->get();

        $filename = 'clients_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Company Name',
                'Contact Name',
                'Email',
                'Phone',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Currency',
                'Tax Exempt',
                'Notes',
                'Status',
            ]);

            // Data rows
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->company_name,
                    $client->contact_name,
                    $client->email,
                    $client->phone,
                    $client->address,
                    $client->city,
                    $client->state,
                    $client->postal_code,
                    $client->country,
                    $client->currency,
                    $client->tax_exempt ? 'Yes' : 'No',
                    $client->notes,
                    $client->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    protected function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
}
