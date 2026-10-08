{{--
    Shared maintenance screen visuals — used by BOTH the in-app overlay
    (layouts/app.blade.php) and the standalone 503 page (errors/503.blade.php)
    so the UI never changes between a live toggle and a page reload.
--}}
<style>
    #srh-maintenance-overlay .srh-mo-card { text-align:center; max-width:400px; width:100%; }
    #srh-maintenance-overlay .srh-mo-logo { width:80px; height:80px; margin:0 auto 1.2rem; background:#fff; border-radius:20px; padding:12px; display:flex; align-items:center; justify-content:center; box-shadow:0 16px 32px -12px rgba(59,130,246,0.55); }
    #srh-maintenance-overlay .srh-mo-logo img { width:100%; height:100%; object-fit:contain; }
    #srh-maintenance-overlay .srh-mo-name { font-size:0.68rem; font-weight:800; letter-spacing:0.14em; text-transform:uppercase; color:#93c5fd; margin-bottom:0.7rem; }
    #srh-maintenance-overlay h2 { font-size:1.2rem; font-weight:900; color:#f1f5f9; line-height:1.35; margin-bottom:0.6rem; letter-spacing:-0.01em; }
    #srh-maintenance-overlay p { font-size:0.84rem; line-height:1.6; color:#94a3b8; font-weight:500; margin-bottom:1.4rem; }
    #srh-maintenance-overlay .srh-mo-pill { display:inline-flex; align-items:center; gap:0.5rem; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.35); color:#fbbf24; font-size:0.7rem; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; padding:0.5rem 1rem; border-radius:999px; }
    #srh-maintenance-overlay .srh-mo-pulse { width:8px; height:8px; border-radius:50%; background:#f59e0b; animation:srh-mo-pulse 1.4s ease-in-out infinite; }
    @keyframes srh-mo-pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.35; transform:scale(0.72); } }
</style>
<div class="srh-mo-card">
    <div class="srh-mo-logo">
        <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }} logo">
    </div>
    <div class="srh-mo-name">{{ \App\Support\SystemSettings::brandName() }}</div>
    <h2>System Under Maintenance</h2>
    <p id="srh-maint-message">The system is temporarily under maintenance. Please check back shortly.</p>
    <span class="srh-mo-pill"><span class="srh-mo-pulse"></span> Temporarily Unavailable</span>
</div>
