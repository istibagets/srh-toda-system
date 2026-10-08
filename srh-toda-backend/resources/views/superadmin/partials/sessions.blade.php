<div class="stat-grid">
    <div class="stat">
        <div class="num">{{ number_format($sessionTotals['total']) }}</div>
        <div class="lbl">Total Sessions</div>
    </div>
    <div class="stat">
        <div class="num">{{ number_format($sessionTotals['online']) }}</div>
        <div class="lbl">Online Now (10 min)</div>
    </div>
    <div class="stat">
        <div class="num">{{ number_format($sessionTotals['signedIn']) }}</div>
        <div class="lbl">Signed In</div>
    </div>
    <div class="stat">
        <div class="num">{{ number_format($sessionTotals['guests']) }}</div>
        <div class="lbl">Guest Visits</div>
    </div>
    <div class="stat">
        <div class="num {{ $sessionTotals['expired'] > 0 ? 'na' : '' }}">{{ number_format($sessionTotals['expired']) }}</div>
        <div class="lbl">Expired</div>
    </div>
</div>

<div class="hint-box">
    One row = one browser or device that visited within the last {{ $sessionLifetime }} minutes. Guest visits and
    background bots also create rows, so the total is not the number of people online. Sessions expire automatically
    after {{ $sessionLifetime }} minutes of inactivity and are swept daily.
</div>

<div class="log-toolbar">
    <form class="search-bar" style="margin:0;flex:1;" method="GET" action="{{ route('superadmin.sessions') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search by user, email or IP address…" aria-label="Search sessions">
        <button type="submit" class="btn btn-ghost btn-sm">Search</button>
        @if ($q !== '')
            <a href="{{ route('superadmin.sessions') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
    <form method="POST" action="{{ route('superadmin.sessions.clear-expired') }}">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm">Clear Expired</button>
    </form>
</div>

<div class="card" style="padding:0.4rem 1rem; overflow-x:auto;">
    <table class="data-table" style="min-width: 780px;">
        <thead>
            <tr>
                <th>User</th>
                <th>Device</th>
                <th>IP Address</th>
                <th>Last Activity</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sessions as $s)
                <tr>
                    <td>
                        @if ($s->user_id)
                            <div style="display:flex;align-items:center;gap:0.55rem;">
                                <span class="avatar-inline">{{ strtoupper(substr($s->name ?? '?', 0, 1)) }}</span>
                                <div>
                                    <div style="font-weight:800;color:#0f172a;font-size:0.78rem;">{{ $s->name }}</div>
                                    <div style="font-size:0.68rem;color:#94a3b8;">{{ $s->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="tag tag-gray">Guest</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:0.75rem;color:#334155;">{{ $s->browser }}</div>
                        <div style="font-size:0.68rem;color:#94a3b8;margin-top:0.15rem;">
                            {{ $s->os }}
                            <span class="tag {{ $s->device === 'Desktop' ? 'tag-gray' : 'tag-blue' }}" style="padding:1px 6px;margin-left:0.25rem;">{{ $s->device }}</span>
                        </div>
                    </td>
                    <td style="font-family:'JetBrains Mono',ui-monospace,monospace;font-size:0.72rem;color:#475569;">{{ $s->ip_address ?: '—' }}</td>
                    <td title="{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->format('Y-m-d H:i:s') }}">{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans() }}</td>
                    <td>
                        @if ($s->last_activity >= time() - 600)
                            <span class="tag tag-green">Online now</span>
                        @elseif ($s->last_activity >= time() - ($sessionLifetime * 60))
                            <span class="tag tag-blue">Active</span>
                        @else
                            <span class="tag tag-amber">Expired</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if ($s->id === $currentSessionId)
                            <span class="tag tag-blue">This session</span>
                        @else
                            <form class="inline-form" method="POST" action="{{ route('superadmin.sessions.revoke', $s->id) }}"
                                  onsubmit="return confirm('Sign this device out immediately?');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"><div class="empty">No sessions found.</div></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">{{ $sessions->links() }}</div>