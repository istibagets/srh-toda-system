<div class="card">
    <h2>Reports & Disputes</h2>
    <p class="card-sub">Moderation queue — review passenger reports, change status, add notes, or suspend the driver.</p>

    <div class="stat-grid" style="grid-template-columns:repeat(auto-fill,minmax(110px,1fr));">
        @foreach ($reportStats as $label => $count)
            <div class="stat">
                <div class="num" style="font-size:1.2rem;">{{ number_format($count) }}</div>
                <div class="lbl">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('superadmin.reports') }}" class="search-bar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search category, subject, reporter, driver…" aria-label="Search reports">
        <select name="status" aria-label="Filter by report status">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All statuses</option>
            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="investigating" {{ $statusFilter === 'investigating' ? 'selected' : '' }}>Investigating</option>
            <option value="resolved" {{ $statusFilter === 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="dismissed" {{ $statusFilter === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        <a href="{{ route('superadmin.reports') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>

    @forelse ($reports as $report)
        <div class="report-row">
            <div class="report-head">
                <span class="tag tag-blue">#{{ $report->id }}</span>
                <span class="tag {{ $report->status === 'resolved' ? 'tag-green' : ($report->status === 'dismissed' ? 'tag-gray' : ($report->status === 'investigating' ? 'tag-amber' : 'tag-red')) }}">{{ strtoupper($report->status) }}</span>
                <span style="font-weight:800;font-size:0.82rem;">{{ $report->category }}</span>
                <span class="spacer" style="flex:1"></span>
                <span style="font-size:0.7rem;color:#94a3b8;">{{ $report->created_at ? \Carbon\Carbon::parse($report->created_at)->format('M j, Y g:i A') : '' }}</span>
            </div>
            <div class="report-body">
                <div class="report-meta">
                    <span>Reporter: <strong>{{ $report->reporter->name ?? 'Deleted user' }}</strong></span>
                    <span>Driver: <strong>{{ $report->driver->name ?? 'Deleted user' }}</strong></span>
                    <span>Ride: <strong>#{{ $report->ride_id ?? '—' }}</strong></span>
                </div>
                @if ($report->subject)
                    <div class="report-subject">{{ $report->subject }}</div>
                @endif
                <div class="report-desc">{{ $report->description }}</div>
                @if ($report->admin_notes)
                    <div class="report-notes">Admin notes: {{ $report->admin_notes }}</div>
                @endif
                <form method="POST" action="{{ route('superadmin.reports.update-status', $report->id) }}" class="report-form">
                    @csrf
                    <div class="row" style="gap:0.5rem;align-items:flex-start;">
                        <select name="status" aria-label="Update report status" style="padding:0.45rem 0.6rem;border-radius:9px;border:1px solid #e2e8f0;font-size:0.75rem;font-weight:700;background:#f8fafc;color:#334155;">
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="investigating" {{ $report->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                        <input type="text" name="admin_notes" placeholder="Admin notes (optional)" value="" aria-label="Admin notes" style="flex:1;min-width:180px;">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                    <div class="row" style="gap:0.5rem;margin-top:0.5rem;font-size:0.72rem;color:#64748b;font-weight:600;">
                        <label style="display:inline-flex;align-items:center;gap:0.35rem;cursor:pointer;">
                            <input type="checkbox" name="suspend_driver" value="1"> Also suspend this driver
                        </label>
                        <input type="text" name="suspension_reason" placeholder="Suspension reason (optional)" aria-label="Suspension reason" style="flex:1;min-width:160px;">
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="empty">No reports match the filters.</div>
    @endforelse

    <div class="pagination">{{ $reports->links() }}</div>
</div>