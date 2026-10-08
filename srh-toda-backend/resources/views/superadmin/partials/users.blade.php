<div class="card">
    <h2>Users</h2>
    <p class="card-sub">Account management — role changes, activation, and deletion. All actions are recorded in the Activity Log.</p>

    <form method="GET" action="{{ route('superadmin.users') }}" class="search-bar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, phone…" aria-label="Search users">
        <select name="role" aria-label="Filter by role">
            <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>All roles</option>
            <option value="passenger" {{ $roleFilter === 'passenger' ? 'selected' : '' }}>Passengers</option>
            <option value="driver" {{ $roleFilter === 'driver' ? 'selected' : '' }}>Drivers</option>
            <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admins</option>
        </select>
        <select name="status" aria-label="Filter by status">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Any status</option>
            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Deactivated</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        <a href="{{ route('superadmin.users') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Driver profile</th>
                    <th>Joined</th>
                    <th style="width:250px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            <div style="font-size:0.7rem;color:#94a3b8;">{{ $user->email }}<br>{{ $user->phone_number ?? '—' }} · #{{ $user->id }}</div>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('superadmin.users.update-role', $user->id) }}" class="inline-form">
                                @csrf
                                <select name="role" aria-label="Change user role for {{ $user->name }}" onchange="this.form.submit()" style="padding:0.3rem 0.5rem;border-radius:8px;border:1px solid #e2e8f0;font-size:0.72rem;font-weight:700;background:#f8fafc;color:#334155;">
                                    <option value="passenger" {{ $user->role === 'passenger' || $user->role === 'user' ? 'selected' : '' }}>passenger</option>
                                    <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>driver</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            @if ($user->is_active === false)
                                <span class="tag tag-red">DEACTIVATED</span>
                            @else
                                <span class="tag tag-green">ACTIVE</span>
                            @endif
                        </td>
                        <td>
                            @if ($user->driverProfile)
                                @php $dp = $user->driverProfile; @endphp
                                <span class="tag {{ strtolower((string) $dp->compliance_status) === 'approved' ? 'tag-green' : (strtolower((string) $dp->compliance_status) === 'suspended' ? 'tag-red' : 'tag-amber') }}">{{ strtoupper((string) $dp->compliance_status) }}</span>
                                <div style="font-size:0.68rem;color:#94a3b8;margin-top:0.2rem;">{{ $dp->is_online ? 'Online · Q' . ($dp->queue_position ?? '-') : 'Offline' }}</div>
                            @else
                                <span style="color:#94a3b8;font-size:0.72rem;">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M j, Y') : '—' }}</td>
                        <td>
                            <div class="row" style="gap:0.4rem;">
                                @if ($user->is_active === false)
                                    <form method="POST" action="{{ route('superadmin.users.toggle-active', $user->id) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">Reactivate</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('superadmin.users.toggle-active', $user->id) }}" class="inline-form"
                                          onsubmit="return confirm('Deactivate {{ $user->name }}? They will no longer be able to log in.');">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm">Deactivate</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('superadmin.users.delete', $user->id) }}" class="inline-form"
                                      onsubmit="return confirm('PERMANENTLY delete {{ $user->name }} (#{{ $user->id }})?\n\nTheir driver profile, push subscriptions, reports and announcements will be removed too.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No users match the filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $users->links() }}</div>
</div>