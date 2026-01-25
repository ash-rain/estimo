<?php

namespace App\Livewire\Team;

use App\Models\Invitation;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class InviteMember extends Component
{
    public $email = '';
    public $role = 'sales';

    protected $rules = [
        'email' => 'required|email',
        'role' => 'required|in:admin,manager,sales,viewer',
    ];

    public function render()
    {
        return view('livewire.team.invite-member', [
            'roles' => $this->getRoles(),
        ]);
    }

    public function sendInvitation()
    {
        $this->validate();

        // Check if user already exists
        $existingUser = User::where('email', $this->email)->first();
        if ($existingUser) {
            $this->addError('email', 'A user with this email already exists in your workspace.');
            return;
        }

        // Check if invitation already exists
        $existingInvitation = Invitation::where('email', $this->email)
            ->where('accepted_at', null)
            ->first();

        if ($existingInvitation) {
            if ($existingInvitation->isExpired()) {
                // Delete old expired invitation
                $existingInvitation->delete();
            } else {
                $this->addError('email', 'An invitation has already been sent to this email.');
                return;
            }
        }

        // Create invitation
        $invitation = Invitation::create([
            'email' => $this->email,
            'role' => $this->role,
            'invited_by' => auth()->id(),
        ]);

        // Send invitation email (placeholder - will be implemented with mail system)
        // For now, just log it
        ActivityLog::log(
            'invitation_sent',
            auth()->user()->name . ' invited ' . $this->email . ' as ' . $this->role,
            $invitation
        );

        // TODO: Send actual email
        // Mail::to($this->email)->send(new TeamInvitation($invitation));

        session()->flash('success', 'Invitation sent successfully!');

        $this->dispatch('saved');
        $this->reset(['email', 'role']);
    }

    protected function getRoles(): array
    {
        return [
            'admin' => 'Admin - Full access except billing',
            'manager' => 'Manager - Quotes, clients, team reports',
            'sales' => 'Sales - Create and manage own quotes',
            'viewer' => 'Viewer - Read-only access',
        ];
    }
}
