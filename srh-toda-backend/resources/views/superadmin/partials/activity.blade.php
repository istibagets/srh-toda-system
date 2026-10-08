<div class="card">
    <h2>Activity Log</h2>
    <p class="card-sub">Audit trail — every login, logout, registration, failed attempt, and superadmin action.</p>

    <form method="GET" action="{{ route('superadmin.activity') }}" class="search-bar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search name, IP, action…" aria-label="Search activity log">
        <select name="action" aria-label="Filter by action">
            <option value="all" {{ $actionFilter === 'all' ? 'selected' : '' }}>All actions</option>
            @foreach ($actionCounts as $action => $count)
                <option value="{{ $action }}" {{ $actionFilter === $action ? 'selected' : '' }}>{{ $action }} ({{ $count }})</option>
            @endforeach
        </select>
        <select name="role" aria-label="Filter by role">
            <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Everyone</option>
            <option value="superadmin" {{ $roleFilter === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
            <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admins</option>
            <option value="driver" {{ $roleFilter === 'driver' ? 'selected' : '' }}>Drivers</option>
            <option value="passenger" {{ $roleFilter === 'passenger' ? 'selected' : '' }}>Passengers</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        <a href="{{ route('superadmin.activity') }}" class="btn btn-ghost btn-sm">Reset</a>
        <div style="flex:1"></div>
        <form method="POST" action="{{ route('superadmin.activity.clear') }}" style="display:inline;"
              onsubmit="return confirm('Permanently clear the ENTIRE activity log? Type DELETE confirm is required.');">
            @csrf
            <input type="hidden" name="confirm" value="DELETE">
            <button type="submit" class="btn btn-danger btn-sm">Clear All</button>
        </form>
    </form>

    <div style="max-height:640px;overflow:auto;">
        <table class="data-table">
            <thead style="position:sticky;top:0;background:#fff;">
                <tr>
                    <th>When</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $entry)
                    <tr>
                        <td style="white-space:nowrap;">{{ $entry->created_at ? \Carbon\Carbon::parse($entry->created_at)->format('M j, H:i:s') : '—' }}</td>
                        <td>
                            <strong>{{ $entry->user_name ?? ($entry->user_role === 'superadmin' ? 'Superadmin' : 'Guest') }}</strong>
                            @if ($entry->user_role)
                                <span class="tag {{ $entry->user_role === 'superadmin' ? 'tag-blue' : 'tag-gray' }}">{{ $entry->user_role }}</span>
                            @endif
                        </td>
                        <td><span class="tag {{ in_array($entry->action, ['login_failed', 'account_deleted', 'login_blocked', 'account_deactivated']) ? 'tag-red' : (in_array($entry->action, ['login', 'logout', 'register']) ? 'tag-gray' : 'tag-blue') }}">{{ $entry->action }}</span></td>
                        <td style="font-family:'JetBrains Mono',monospace;">{{ $entry->ip_address ?? '—' }}</td>
                        <td style="font-size:0.7rem;color:#64748b;max-width:340px;">
                            @php
                                $meta = $entry->metadata ? json_decode($entry->metadata, true) : [];
                            @endphp
                            @if (!empty($meta))
                                @foreach (array_slice($meta, 0, 4) as $k => $v)
                                    <div><strong>{{ $k }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</div>
                                @endforeach
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $logs->links() }}</div>
</div>