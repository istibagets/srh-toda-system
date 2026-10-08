<div class="card">
    <h2>Rides</h2>
    <p class="card-sub">Full ride history across all passengers and drivers — read-only moderation view.</p>

    <div class="row" style="margin-bottom:1rem;gap:0.5rem;flex-wrap:wrap;">
        @foreach ($rideStatusCounts as $status => $count)
            <span class="tag {{ $status === 'completed' ? 'tag-green' : 'tag-blue' }}">{{ $status }}: {{ number_format($count) }}</span>
        @endforeach
    </div>

    <form method="GET" action="{{ route('superadmin.rides') }}" class="search-bar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search pickup, destination, passenger, driver…" aria-label="Search rides">
        <select name="status" aria-label="Filter by ride status">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All statuses</option>
            @foreach (array_keys($rideStatusCounts->toArray()) as $status)
                <option value="{{ $status }}" {{ $statusFilter === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        <a href="{{ route('superadmin.rides') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Passenger</th>
                    <th>Driver</th>
                    <th>Route</th>
                    <th>Fare</th>
                    <th>Rating</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rides as $ride)
                    <tr>
                        <td>#{{ $ride->id }}</td>
                        <td><span class="tag {{ in_array($ride->status, ['completed', 'cancelled']) ? 'tag-gray' : 'tag-blue' }}">{{ $ride->status }}</span></td>
                        <td>
                            <strong>{{ $ride->passenger->name ?? 'Deleted' }}</strong>
                            <div style="font-size:0.68rem;color:#94a3b8;">{{ $ride->passenger->phone_number ?? '' }}</div>
                        </td>
                        <td>
                            @if ($ride->driver)
                                <strong>{{ $ride->driver->name }}</strong>
                                <div style="font-size:0.68rem;color:#94a3b8;">{{ $ride->driverProfile?->mtop_number ?? '' }}</div>
                            @else
                                <span style="color:#94a3b8;">Deleted</span>
                            @endif
                        </td>
                        <td style="max-width:280px;">
                            <div style="font-weight:600;">{{ $ride->pickup_location }}</div>
                            <div style="font-size:0.68rem;color:#94a3b8;">→ {{ $ride->destination }}</div>
                        </td>
                        <td>{{ $ride->fare ? '₱' . number_format((float) $ride->fare, 0) : '—' }}</td>
                        <td>
                            @if ($ride->rating > 0)
                                <span class="tag tag-amber">★ {{ $ride->rating }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $ride->created_at ? \Carbon\Carbon::parse($ride->created_at)->format('M j, g:i A') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">No rides match the filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $rides->links() }}</div>
</div>