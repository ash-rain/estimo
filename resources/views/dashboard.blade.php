<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome to Estimo!</h3>

                    <div class="space-y-3">
                        <div>
                            <span class="font-medium">Company:</span>
                            <span class="text-gray-700">{{ tenant('name') }}</span>
                        </div>

                        <div>
                            <span class="font-medium">Tenant ID:</span>
                            <span class="text-gray-700">{{ tenant('id') }}</span>
                        </div>

                        <div>
                            <span class="font-medium">Your Role:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>

                        <div>
                            <span class="font-medium">Plan:</span>
                            <span class="text-gray-700">{{ ucfirst(tenant('plan')) }}</span>
                        </div>

                        @if(tenant('trial_ends_at'))
                        <div>
                            <span class="font-medium">Trial Ends:</span>
                            <span class="text-gray-700">{{ \Carbon\Carbon::parse(tenant('trial_ends_at'))->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            🎉 Multi-tenancy is working! You're viewing this from your tenant workspace.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
