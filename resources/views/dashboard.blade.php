<x-layouts::app title="Dashboard">
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-on-surface">Dashboard</h2>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card mb-5">
            <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">analytics</span>
                System Overview
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">people</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Total Users</span>
                    </div>
                    <span class="text-2xl font-bold text-primary">{{ $stats['total_users'] }}</span>
                </div>
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-info">notifications</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Notifications</span>
                    </div>
                    <span class="text-2xl font-bold text-info">{{ $stats['total_notifications'] }}</span>
                </div>
                <div class="bg-surface-container rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-warning">mark_email_unread</span>
                        </div>
                        <span class="text-xs font-semibold text-on-surface-variant uppercase">Unread</span>
                    </div>
                    <span class="text-2xl font-bold text-warning">{{ $stats['unread_notifications'] }}</span>
                </div>
            </div>
        </div>
        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
            <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card min-w-0 overflow-hidden">
                <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">trending_up</span>
                    User Registrations
                </h3>
                <div class="min-w-0">
                    {!! $userChart->container() !!}
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card min-w-0 overflow-hidden">
                <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">pie_chart</span>
                    Notifications
                </h3>
                <div class="bg-surface-container rounded-lg p-4 min-w-0 overflow-hidden">
                    {!! $notifChart->container() !!}
                </div>
            </div>
        </div>


        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
            <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">person</span>
                Recent Users
            </h3>
            @if($recentUsers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-on-surface-variant uppercase border-b border-outline-variant">
                            <th class="pb-3 pr-4">Name</th>
                            <th class="pb-3 pr-4">Email</th>
                            <th class="pb-3 pr-4">Role</th>
                            <th class="pb-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr class="border-b border-outline-variant/50">
                            <td class="py-3 pr-4 font-medium">{{ $user->name }}</td>
                            <td class="py-3 pr-4 text-on-surface-variant">{{ $user->email }}</td>
                            <td class="py-3 pr-4">
                                <span class="bg-surface-container text-xs px-2 py-1 rounded-full">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="py-3 text-on-surface-variant">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl mb-2 block">person_off</span>
                <p class="text-sm">No users found</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
        {!! $userChart->script() !!}
        {!! $notifChart->script() !!}
    @endpush
</x-layouts::app>
