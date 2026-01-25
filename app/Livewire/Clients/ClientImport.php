<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Validator;

class ClientImport extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $importResults = null;
    public $isProcessing = false;

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:10240',
    ];

    protected $messages = [
        'csvFile.required' => 'Please select a CSV file to import.',
        'csvFile.mimes' => 'The file must be a CSV file.',
        'csvFile.max' => 'The file size must not exceed 10MB.',
    ];

    public function importClients()
    {
        $this->validate();

        $this->isProcessing = true;
        $this->importResults = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $path = $this->csvFile->getRealPath();
            $file = fopen($path, 'r');

            // Read and validate header row
            $header = fgetcsv($file);
            $expectedHeaders = [
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
            ];

            if ($header !== $expectedHeaders) {
                $this->addError('csvFile', 'Invalid CSV format. Please use the export template.');
                fclose($file);
                $this->isProcessing = false;
                return;
            }

            $row = 1;
            while (($data = fgetcsv($file)) !== false) {
                $row++;

                try {
                    // Map CSV data to model attributes
                    $clientData = [
                        'company_name' => $data[0] ?? '',
                        'contact_name' => $data[1] ?? null,
                        'email' => $data[2] ?? null,
                        'phone' => $data[3] ?? null,
                        'address' => $data[4] ?? null,
                        'city' => $data[5] ?? null,
                        'state' => $data[6] ?? null,
                        'postal_code' => $data[7] ?? null,
                        'country' => $data[8] ?? 'US',
                        'currency' => $data[9] ?? 'USD',
                        'tax_exempt' => isset($data[10]) && strtolower($data[10]) === 'yes',
                        'notes' => $data[11] ?? null,
                        'status' => $data[12] ?? 'active',
                        'created_by' => auth()->id(),
                    ];

                    // Validate the data
                    $validator = Validator::make($clientData, [
                        'company_name' => 'required|string|max:255',
                        'contact_name' => 'nullable|string|max:255',
                        'email' => 'nullable|email|max:255',
                        'phone' => 'nullable|string|max:50',
                        'address' => 'nullable|string',
                        'city' => 'nullable|string|max:255',
                        'state' => 'nullable|string|max:255',
                        'postal_code' => 'nullable|string|max:20',
                        'country' => 'required|string|size:2',
                        'currency' => 'required|string|size:3',
                        'tax_exempt' => 'boolean',
                        'notes' => 'nullable|string',
                        'status' => 'required|in:active,inactive,archived',
                    ]);

                    if ($validator->fails()) {
                        $this->importResults['failed']++;
                        $this->importResults['errors'][] = [
                            'row' => $row,
                            'company' => $clientData['company_name'],
                            'errors' => $validator->errors()->all(),
                        ];
                        continue;
                    }

                    // Create the client
                    Client::create($clientData);
                    $this->importResults['success']++;

                } catch (\Exception $e) {
                    $this->importResults['failed']++;
                    $this->importResults['errors'][] = [
                        'row' => $row,
                        'company' => $data[0] ?? 'Unknown',
                        'errors' => [$e->getMessage()],
                    ];
                }
            }

            fclose($file);

            ActivityLog::log(
                'clients_imported',
                auth()->user()->name . ' imported ' . $this->importResults['success'] . ' clients from CSV',
                null,
                $this->importResults
            );

            if ($this->importResults['success'] > 0 && $this->importResults['failed'] === 0) {
                session()->flash('success', "Successfully imported {$this->importResults['success']} clients.");
                $this->dispatch('imported');
            }

        } catch (\Exception $e) {
            $this->addError('csvFile', 'Error processing CSV file: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function downloadTemplate()
    {
        $filename = 'client_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
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

            // Sample row
            fputcsv($file, [
                'Acme Corporation',
                'John Doe',
                'john@acme.com',
                '+1-555-0100',
                '123 Main Street',
                'New York',
                'NY',
                '10001',
                'US',
                'USD',
                'No',
                'Sample client for reference',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cancel()
    {
        $this->dispatch('cancelled');
    }

    public function render()
    {
        return view('livewire.clients.client-import');
    }
}
