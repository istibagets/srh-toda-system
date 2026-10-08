<div class="card">
    <h2>System Health Checks</h2>
    <p class="card-sub">Live diagnostics — database, disk, storage writability, integrations, and limits.</p>

    <table class="data-table">
        <tbody>
            @foreach ($health as $label => [$state, $detail])
                <tr>
                    <td style="width:46%;font-weight:800;color:#334155;">{{ $label }}</td>
                    <td>
                        <span class="tag {{ $state === 'ok' ? 'tag-green' : ($state === 'warn' ? 'tag-amber' : 'tag-red') }}">
                            {{ strtoupper($state) }}
                        </span>
                    </td>
                    <td style="font-size:0.75rem;color:#64748b;">{{ $detail }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Maintenance Mode</h2>
    <p class="card-sub">
        @if ($appMaintenanceDown)
            Maintenance is <strong style="color:#b45309;">ACTIVE</strong> — users currently see the maintenance screen and cannot use the app. The superadmin panel stays accessible so you can bring the app back online.
        @else
            The app is <strong style="color:#047857;">live</strong> — maintenance mode is off.
        @endif
    </p>
    <form method="POST" action="{{ route('superadmin.health.maintenance') }}"
          onsubmit="return confirm('{{ $appMaintenanceDown ? 'Bring the app back online?' : 'Take the app DOWN for all users?' }}');">
        @csrf
        @if ($appMaintenanceDown)
            <button type="submit" class="btn btn-primary">Bring App Online</button>
        @else
            <button type="submit" class="btn btn-danger">Enable Maintenance Mode</button>
        @endif
    </form>
</div>