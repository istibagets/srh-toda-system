<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Passenger Incident & Driver Reports') }}"></x-page-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Header & Stats Grid -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-black uppercase tracking-wider mb-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <span>ADMIN SAFETY & COMPLIANCE HUB</span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white">Passenger Incident Reports</h1>
                        <p class="text-sm text-blue-200 mt-1 max-w-xl">Review safety complaints, fare disputes, lost items, and driver conduct reports submitted by TODA passengers.</p>
                    </div>

                    <!-- Action Button to Submit Test / Manual Report -->
                    <button type="button" onclick="openAdminCreateReportModal()" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg active:scale-95 transition flex items-center gap-2 cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>New Report Record</span>
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-700/60">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                        <span class="text-[11px] font-extrabold uppercase text-slate-300 tracking-wider">Total Reports</span>
                        <div class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-amber-500/20 backdrop-blur-md rounded-2xl p-4 border border-amber-400/30">
                        <span class="text-[11px] font-extrabold uppercase text-amber-200 tracking-wider">Pending Review</span>
                        <div class="text-2xl sm:text-3xl font-black text-amber-300 mt-1">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="bg-blue-500/20 backdrop-blur-md rounded-2xl p-4 border border-blue-400/30">
                        <span class="text-[11px] font-extrabold uppercase text-blue-200 tracking-wider">Investigating</span>
                        <div class="text-2xl sm:text-3xl font-black text-blue-300 mt-1">{{ $stats['investigating'] }}</div>
                    </div>
                    <div class="bg-emerald-500/20 backdrop-blur-md rounded-2xl p-4 border border-emerald-400/30">
                        <span class="text-[11px] font-extrabold uppercase text-emerald-200 tracking-wider">Resolved</span>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-300 mt-1">{{ $stats['resolved'] }}</div>
                    </div>
                    <div class="bg-slate-500/20 backdrop-blur-md rounded-2xl p-4 border border-slate-400/30">
                        <span class="text-[11px] font-extrabold uppercase text-slate-300 tracking-wider">Dismissed</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-300 mt-1">{{ $stats['dismissed'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Filter Controls & Search -->
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
                
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <a href="{{ route('admin.reports.index', ['status' => 'all', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap {{ request('status', 'all') === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        All ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.reports.index', ['status' => 'pending', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Pending ({{ $stats['pending'] }})
                    </a>
                    <a href="{{ route('admin.reports.index', ['status' => 'investigating', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap {{ request('status') === 'investigating' ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                        Investigating ({{ $stats['investigating'] }})
                    </a>
                    <a href="{{ route('admin.reports.index', ['status' => 'resolved', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white shadow-md' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Resolved ({{ $stats['resolved'] }})
                    </a>
                    <a href="{{ route('admin.reports.index', ['status' => 'dismissed', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition whitespace-nowrap {{ request('status') === 'dismissed' ? 'bg-slate-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Dismissed ({{ $stats['dismissed'] }})
                    </a>
                </div>

                <!-- Search Input -->
                <form action="{{ route('admin.reports.index') }}" method="GET" class="relative w-full sm:w-72">
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" aria-label="Search reports"
                           class="block w-full pl-9 pr-4 py-2.5 text-xs font-bold text-gray-900 border border-gray-200 rounded-2xl bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Search category, passenger, driver...">
                </form>
            </div>

            <!-- Reports List Table Container -->
            <div id="reports-table-container">
                @include('admin.partials.reports-table')
            </div>

        </div>
    </div>

    <!-- UPDATE REPORT STATUS MODAL -->
    <div id="updateReportModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; z-index: 999999; backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.8);">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh;">
            
            <!-- Modal Header -->
            <div class="p-6 relative bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1 bg-blue-500/20 text-blue-300 border border-blue-400/30">
                        <span>ADMIN ACTION</span>
                    </div>
                    <h3 class="text-xl font-black text-white" id="modalReportTitle">Review Passenger Report</h3>
                    <p class="text-xs text-slate-300 mt-0.5" id="modalReportSub">Update status, write admin notes, or suspend driver</p>
                </div>
                <button type="button" onclick="closeUpdateReportModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center border-none cursor-pointer">✕</button>
            </div>

            <!-- Modal Form Body -->
            <form id="updateReportForm" onsubmit="handleReportUpdateSubmit(event)" class="p-6 space-y-5 flex-1 overflow-y-auto">
                <input type="hidden" id="report_id">
                
                <!-- Report Summary Card -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
                        <span class="text-xs font-black uppercase text-blue-700 bg-blue-100 px-3 py-1 rounded-xl whitespace-nowrap shrink-0" id="modalReportCategory">Category</span>
                        <span class="text-xs font-bold text-gray-500 whitespace-nowrap shrink-0 ml-auto" id="modalReportDate">Date</span>
                    </div>
                    <p class="text-xs text-gray-700 font-medium whitespace-pre-line leading-relaxed" id="modalReportDesc"></p>
                    <div class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row gap-2 text-[11px] text-gray-600">
                        <div><strong>Passenger:</strong> <span id="modalReporterName"></span></div>
                        <div><strong>Reported Driver:</strong> <span id="modalDriverName"></span></div>
                    </div>
                </div>

                <!-- Update Status Select -->
                <div class="space-y-2">
                    <label for="status_select" class="block text-xs font-black text-gray-700 uppercase tracking-wider">Report Status <span class="text-red-500">*</span></label>
                    <select id="status_select" name="status" class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-bold text-gray-900 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending">⏳ Pending Review</option>
                        <option value="investigating">🔍 Under Investigation</option>
                        <option value="resolved">✅ Resolved</option>
                        <option value="dismissed">❌ Dismissed / Invalid</option>
                    </select>
                </div>

                <!-- Admin Resolution Notes -->
                <div class="space-y-2">
                    <label for="admin_notes_input" class="block text-xs font-black text-gray-700 uppercase tracking-wider">Admin Resolution Notes</label>
                    <textarea id="admin_notes_input" name="admin_notes" rows="3" placeholder="Write internal resolution notes or steps taken..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Direct Driver Suspension Option -->
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="suspend_driver_check" name="suspend_driver" value="1" class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                        <span class="text-xs font-extrabold text-red-800">Suspend Reported Driver Immediately</span>
                    </label>
                    <input type="text" id="suspension_reason_input" name="suspension_reason" aria-label="Reason for driver suspension" placeholder="Reason for driver suspension (optional)..." class="w-full rounded-xl border border-red-300 p-2.5 text-xs text-red-900 focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeUpdateReportModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider shadow-md active:scale-95 cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADMIN MANUAL RECORD REPORT MODAL -->
    <div id="adminCreateReportModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; z-index: 999999; backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.8);">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh;">
            
            <div class="p-6 relative bg-blue-900 text-white flex items-center justify-between">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1 bg-white/20 text-white">
                        <span>NEW ENTRY</span>
                    </div>
                    <h3 class="text-xl font-black text-white">Record Incident Report</h3>
                    <p class="text-xs text-blue-200 mt-0.5">Record a passenger complaint or incident manually</p>
                </div>
                <button type="button" onclick="closeAdminCreateReportModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center border-none cursor-pointer">✕</button>
            </div>

            <form action="{{ route('reports.store') }}" method="POST" class="p-6 space-y-4 flex-1 overflow-y-auto">
                @csrf
                <div class="space-y-1.5">
                    <label for="admin_create_category" class="block text-xs font-black text-gray-700 uppercase">Category <span class="text-red-500">*</span></label>
                    <select id="admin_create_category" name="category" required class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-bold text-gray-900">
                        <option value="Overcharging">Overcharging / Fare Dispute</option>
                        <option value="Reckless Driving">Reckless / Overspeeding Driving</option>
                        <option value="Unprofessional Behavior">Unprofessional / Rude Behavior</option>
                        <option value="Lost Item">Lost / Forgotten Item</option>
                        <option value="Vehicle Condition">Poor Tricycle Condition</option>
                        <option value="Other">Other Incident</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="admin_create_driver_id" class="block text-xs font-black text-gray-700 uppercase">Reported Driver (Optional)</label>
                    <select id="admin_create_driver_id" name="driver_id" class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-bold text-gray-900">
                        <option value="">-- Select Driver --</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="admin_create_description" class="block text-xs font-black text-gray-700 uppercase">Description / Details <span class="text-red-500">*</span></label>
                    <textarea id="admin_create_description" name="description" required rows="4" placeholder="Describe the incident details..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeAdminCreateReportModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUpdateReportModal(report) {
            document.getElementById('report_id').value = report.id;
            document.getElementById('modalReportTitle').textContent = 'Report #' + report.id + ': ' + report.category;
            document.getElementById('modalReportCategory').textContent = report.category;
            document.getElementById('modalReportDate').textContent = report.created_at || '';
            document.getElementById('modalReportDesc').textContent = report.description || 'No details provided.';
            
            document.getElementById('modalReporterName').textContent = report.reporter ? (report.reporter.name + ' (' + (report.reporter.email || 'No email') + ')') : 'Anonymous Passenger';
            document.getElementById('modalDriverName').textContent = report.driver ? report.driver.name : 'N/A';
            
            document.getElementById('status_select').value = report.status || 'pending';
            document.getElementById('admin_notes_input').value = report.admin_notes || '';
            document.getElementById('suspend_driver_check').checked = false;
            document.getElementById('suspension_reason_input').value = '';

            document.getElementById('updateReportModal').classList.remove('hidden');
        }

        function closeUpdateReportModal() {
            document.getElementById('updateReportModal').classList.add('hidden');
        }

        function openAdminCreateReportModal() {
            document.getElementById('adminCreateReportModal').classList.remove('hidden');
        }

        function closeAdminCreateReportModal() {
            document.getElementById('adminCreateReportModal').classList.add('hidden');
        }

        function handleReportUpdateSubmit(e) {
            e.preventDefault();
            const reportId = document.getElementById('report_id').value;
            const status = document.getElementById('status_select').value;
            const admin_notes = document.getElementById('admin_notes_input').value;
            const suspend_driver = document.getElementById('suspend_driver_check').checked ? 1 : 0;
            const suspension_reason = document.getElementById('suspension_reason_input').value;

            fetch('/admin/reports/' + reportId + '/status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-SPA-Request': 'true'
                },
                body: JSON.stringify({
                    status: status,
                    admin_notes: admin_notes,
                    suspend_driver: suspend_driver,
                    suspension_reason: suspension_reason
                })
            })
            .then(res => res.json())
            .then(data => {
                closeUpdateReportModal();
                if (window.createSlidingToast) {
                    window.createSlidingToast('Report status updated successfully!', 'success');
                }
                if (window.clearPageCache) window.clearPageCache();
                if (window.navigateTo) {
                    window.navigateTo(window.location.href, false, false, true);
                } else {
                    window.location.reload();
                }
            })
            .catch(err => {
                alert('Error updating report status.');
            });
        }

        function deleteReport(id) {
            if (!confirm('Are you sure you want to delete this report record permanently?')) return;
            fetch('/admin/reports/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-SPA-Request': 'true'
                }
            })
            .then(res => res.json())
            .then(() => {
                if (window.createSlidingToast) {
                    window.createSlidingToast('Report deleted successfully.', 'success');
                }
                if (window.clearPageCache) window.clearPageCache();
                if (window.navigateTo) {
                    window.navigateTo(window.location.href, false, false, true);
                } else {
                    window.location.reload();
                }
            });
        }
    </script>
</x-app-layout>
