<div class="hint-box">
    Web push notifications reach devices even when the app is closed (browser must be installed as an app or granted notification permission).
    The in-app announcement banner is a bonus layer that shows while users are inside the app.
</div>

<div class="card">
    <h2>Send Push Notification</h2>
    <p class="card-sub">Manual broadcast to all devices subscribed by the chosen audience.</p>
    <form method="POST" action="{{ route('superadmin.notifications.send') }}">
        @csrf
        <div class="field">
            <label for="push_title">Title</label>
            <input id="push_title" type="text" name="title" required maxlength="255">
        </div>
        <div class="field">
            <label for="push_message">Body</label>
            <textarea id="push_message" name="message" required></textarea>
        </div>
        <div class="field">
            <label for="push_url">Tap-through URL (leave blank for dashboard)</label>
            <input id="push_url" type="text" name="url" placeholder="/dashboard" maxlength="500">
        </div>
        <div class="row" style="gap:1rem;">
            <div class="field" style="margin-bottom:0;">
                <label for="push_audience">Audience</label>
                <select id="push_audience" name="audience">
                    <option value="all">Everyone</option>
                    <option value="passengers">All passengers</option>
                    <option value="drivers">All drivers</option>
                    <option value="online-drivers">Online drivers only</option>
                </select>
            </div>
            <label style="display:inline-flex;align-items:center;gap:0.45rem;font-size:0.78rem;font-weight:700;color:#334155;cursor:pointer;margin-top:1.5rem;">
                <input type="checkbox" name="also_announce" value="1" checked> Also post as in-app announcement
            </label>
        </div>
        <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Send Push Notification</button>
        </div>
    </form>
</div>