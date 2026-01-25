<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\QuoteAcceptance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteRejectedNotification extends Notification
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
        $message = (new MailMessage)
            ->subject("Quote {$this->quote->quote_number} was Declined")
            ->line("Your quote **{$this->quote->quote_number}** has been declined by {$this->acceptance->client_name}.")
            ->line("**Client Details:**")
            ->line("Name: {$this->acceptance->client_name}")
            ->line("Email: {$this->acceptance->client_email}")
            ->line("Declined: {$this->acceptance->rejected_at->format('M d, Y \a\t g:i A')}");

        if ($this->acceptance->rejection_reason) {
            $message->line("**Reason:**")
                ->line($this->acceptance->rejection_reason);
        }

        $message->action('View Quote', route('quotes.edit', $this->quote->id))
            ->line('Consider following up with the client to address their concerns.');

        return $message;
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
            'rejected_at' => $this->acceptance->rejected_at,
            'rejection_reason' => $this->acceptance->rejection_reason,
        ];
    }
}
