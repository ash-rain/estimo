<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\QuoteAcceptance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteAcceptedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Quote $quote,
        public QuoteAcceptance $acceptance
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Quote {$this->quote->quote_number} has been Accepted!")
            ->greeting('Great News!')
            ->line("Your quote **{$this->quote->quote_number}** has been accepted by {$this->acceptance->client_name}.")
            ->line('**Client Details:**')
            ->line("Name: {$this->acceptance->client_name}")
            ->line("Email: {$this->acceptance->client_email}")
            ->line("Accepted: {$this->acceptance->accepted_at->format('M d, Y \a\t g:i A')}")
            ->line("**Quote Total:** {$this->quote->currency}".number_format($this->quote->total, 2))
            ->action('View Quote', route('quotes.edit', $this->quote->id))
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'quote_number' => $this->quote->quote_number,
            'client_name' => $this->acceptance->client_name,
            'client_email' => $this->acceptance->client_email,
            'total' => $this->quote->total,
            'currency' => $this->quote->currency,
            'accepted_at' => $this->acceptance->accepted_at,
        ];
    }
}
