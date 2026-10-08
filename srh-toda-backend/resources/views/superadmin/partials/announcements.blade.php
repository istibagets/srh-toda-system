<div class="card">
    <h2>Broadcast Announcement</h2>
    <p class="card-sub">Creates an in-app notification banner for the chosen audience. Optionally also fires a web-push even when the app is closed.</p>
    <form method="POST" action="{{ route('superadmin.announcements.store') }}">
        @csrf
        <div class="field">
            <label for="ann_title">Title</label>
            <input id="ann_title" type="text" name="title" required maxlength="255">
        </div>
        <div class="field">
            <label for="ann_message">Message</label>
            <textarea id="ann_message" name="message" required></textarea>
        </div>
        <div class="row" style="gap:1rem;">
            <div class="field" style="margin-bottom:0;">
                <label for="ann_audience">Audience</label>
                <select id="ann_audience" name="target_audience">
                    <option value="all">Everyone</option>
                    <option value="passengers">Passengers</option>
                    <option value="drivers">Drivers</option>
                </select>
            </div>
            <label style="display:inline-flex;align-items:center;gap:0.45rem;font-size:0.78rem;font-weight:700;color:#334155;cursor:pointer;margin-top:1.5rem;">
                <input type="checkbox" name="send_push" value="1" checked> Also send web push
            </label>
        </div>
        <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Publish Announcement</button>
        </div>
    </form>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.8rem;">
        <div>
            <h2>All Announcements</h2>
            <p class="card-sub" style="margin-bottom: 0;">Published broadcasts, their read reach, and removal.</p>
        </div>
        @if ($announcements->total() > 0)
            <form method="POST" action="{{ route('superadmin.announcements.clear-all') }}" onsubmit="return confirm('Are you sure you want to clear ALL announcements and notification records? This cannot be undone.');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear All Announcements & Notifications
                </button>
            </form>
        @endif
    </div>
    <div style="max-height:520px;overflow:auto;">
        <table class="data-table">
            <thead style="position:sticky;top:0;background:#fff;">
                <tr>
                    <th>Title</th>
                    <th>Audience</th>
                    <th>Sent by</th>
                    <th>Reads</th>
                    <th>When</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($announcements as $announcement)
                    <tr>
                        <td>
                            <strong>{{ $announcement->title }}</strong>
                            <div style="font-size:0.7rem;color:#94a3b8;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ \Illuminate\Support\Str::limit($announcement->message, 90) }}</div>
                        </td>
                        <td><span class="tag tag-gray">{{ $announcement->target_audience }}</span></td>
                        <td>{{ $announcement->creator->name ?? 'Superadmin' }}</td>
                        <td>{{ number_format($announcement->reads_count) }}</td>
                        <td style="white-space:nowrap;">{{ $announcement->created_at ? \Carbon\Carbon::parse($announcement->created_at)->format('M j, g:i A') : '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('superadmin.announcements.delete', $announcement->id) }}" class="inline-form"
                                  onsubmit="return confirm('Delete announcement \'{{ addslashes($announcement->title) }}\'?');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No announcements published yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $announcements->links() }}</div>
</div>