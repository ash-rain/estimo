<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteAcceptance;
use App\Models\ActivityLog;
use App\Notifications\QuoteAcceptedNotification;
use App\Notifications\QuoteRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientPortalController extends Controller
{
    /**
     * Show the quote to the client.
     */
    public function show($token)
    {
        $quote = Quote::with(['client', 'items.catalogItem', 'user', 'acceptance'])
            ->where('portal_token', $token)
            ->firstOrFail();

        // Track portal view
        $quote->increment('portal_view_count');
        
        if (!$quote->portal_viewed_at) {
            $quote->update(['portal_viewed_at' => now()]);
        }

        // Also update the main viewed_at if not set
        if (!$quote->viewed_at) {
            $quote->update(['viewed_at' => now()]);
        }

        return view('portal.quote-view', [
            'quote' => $quote,
            'company' => $this->getCompanySettings(),
        ]);
    }

    /**
     * Accept the quote.
     */
    public function accept(Request $request, $token)
    {
        $quote = Quote::where('portal_token', $token)->firstOrFail();

        // Check if already accepted or rejected
        if ($quote->acceptance) {
            return back()->with('error', 'This quote has already been ' . $quote->acceptance->action . '.');
        }

        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'signature' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create acceptance record
        $acceptance = QuoteAcceptance::create([
            'quote_id' => $quote->id,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'action' => 'accepted',
            'signature_data' => $request->signature,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => $request->notes,
        ]);

        // Update quote status
        $quote->markAsAccepted();

        // Log activity
        ActivityLog::log(
            'quote_accepted',
            $request->client_name . ' accepted quote: ' . $quote->quote_number,
            $quote
        );

        // Send notification to quote owner
        try {
            $quote->user->notify(new QuoteAcceptedNotification($quote, $acceptance));
        } catch (\Exception $e) {
            // Silently fail if notification fails
        }

        return redirect()->route('portal.quote.show', $token)
            ->with('success', 'Thank you! Your acceptance has been recorded.');
    }

    /**
     * Reject the quote.
     */
    public function reject(Request $request, $token)
    {
        $quote = Quote::where('portal_token', $token)->firstOrFail();

        // Check if already accepted or rejected
        if ($quote->acceptance) {
            return back()->with('error', 'This quote has already been ' . $quote->acceptance->action . '.');
        }

        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create rejection record
        $acceptance = QuoteAcceptance::create([
            'quote_id' => $quote->id,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'action' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Update quote status
        $quote->markAsRejected();

        // Log activity
        ActivityLog::log(
            'quote_rejected',
            $request->client_name . ' rejected quote: ' . $quote->quote_number,
            $quote
        );

        // Send notification to quote owner
        try {
            $quote->user->notify(new QuoteRejectedNotification($quote, $acceptance));
        } catch (\Exception $e) {
            // Silently fail if notification fails
        }

        return redirect()->route('portal.quote.show', $token)
            ->with('info', 'Your response has been recorded. Thank you for your time.');
    }

    /**
     * Get company settings.
     */
    protected function getCompanySettings(): array
    {
        $tenant = tenant();
        
        return [
            'name' => $tenant->name ?? config('app.name'),
            'email' => $tenant->email ?? config('mail.from.address'),
            'phone' => $tenant->phone ?? '',
            'website' => $tenant->website ?? '',
            'address' => $tenant->address ?? '',
            'city' => $tenant->city ?? '',
            'state' => $tenant->state ?? '',
            'postal_code' => $tenant->postal_code ?? '',
            'country' => $tenant->country ?? '',
            'logo' => $tenant->logo_url ?? null,
            'primary_color' => $tenant->primary_color ?? '#4F46E5',
        ];
    }
}
