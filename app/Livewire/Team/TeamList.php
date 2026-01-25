<?php

namespace App\Livewire\Team;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TeamList extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = '';

    public $showInviteModal = false;

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        return view('livewire.team.team-list', [
            'users' => $users,
            'roles' => $this->getRoles(),
        ]);
    }

    public function deactivateUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'owner') {
            session()->flash('error', 'Cannot deactivate the workspace owner.');

            return;
        }

        $user->update(['is_active' => false]);

        ActivityLog::log(
            'deactivated',
            auth()->user()->name.' deactivated user: '.$user->name,
            $user
        );

        session()->flash('success', 'User deactivated successfully.');
    }

    public function updateRole($userId, $newRole)
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'owner') {
            session()->flash('error', 'Cannot change the owner\'s role.');

            return;
        }

        $oldRole = $user->role;
        $user->update(['role' => $newRole]);

        ActivityLog::log(
            'role_updated',
            auth()->user()->name.' changed '.$user->name.'\'s role from '.$oldRole.' to '.$newRole,
            $user,
            ['old_role' => $oldRole, 'new_role' => $newRole]
        );

        session()->flash('success', 'User role updated successfully.');
    }

    protected function getRoles(): array
    {
        return [
            'owner' => 'Owner',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'sales' => 'Sales',
            'viewer' => 'Viewer',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }
}
