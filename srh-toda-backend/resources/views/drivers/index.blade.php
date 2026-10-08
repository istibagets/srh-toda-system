<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Admin Management Portal') }}"></x-page-header>
    </x-slot>

    <div class="py-8" x-data="{ 
        activeTab: (new URLSearchParams(window.location.search).get('tab')) || '{{ request('tab', 'drivers') }}',
        switchTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState(null, '', url.toString());
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ADMIN NAVIGATION TABS & ACTION HEADER -->
            <div class="bg-white rounded-3xl p-3 sm:p-4 shadow-sm border border-gray-200/80">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <!-- Modern Segmented Tab Bar -->
                    <div class="grid grid-cols-2 gap-1.5 p-1.5 bg-gray-100/80 rounded-2xl flex-1">
                        <!-- Tab 1: All TODA Drivers (Active & Applicants) -->
                        <button type="button" @click="switchTab('drivers')" 
                                :class="activeTab === 'drivers' ? 'bg-blue-600 text-white shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="py-2.5 px-2.5 sm:px-4 rounded-xl text-xs sm:text-sm uppercase tracking-wider transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 border-none">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="truncate">Drivers</span>
                            <span id="applicants-tab-badge" class="px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black shrink-0" :class="activeTab === 'drivers' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-800'">
                                {{ $drivers->where('compliance_status', '!=', 'Removed')->count() }}
                            </span>
                        </button>

                        <!-- Tab 2: Reports Feed & Incident Management -->
                        <button type="button" @click="switchTab('reports')" 
                                :class="activeTab === 'reports' ? 'bg-blue-600 text-white shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="py-2.5 px-2.5 sm:px-4 rounded-xl text-xs sm:text-sm uppercase tracking-wider transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 border-none">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="truncate">Reports</span>
                            <span id="reports-tab-badge"
                                  class="px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black shrink-0"
                                  :class="activeTab === 'reports' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-800'">
                                {{ $reportStats['total'] ?? 0 }}
                            </span>
                        </button>
                    </div>

                    <!-- Header Action Buttons (Exact 1:1 Matching Grid & Insets) -->
                    <div class="grid grid-cols-2 gap-1.5 p-1.5 sm:p-0 flex-1 sm:flex-initial sm:flex sm:items-center">
                        <button type="button" onclick="openAnnouncementModal()"
                                style="background-color: #0f172a !important; color: #ffffff !important;"
                                class="w-full sm:w-auto py-2.5 px-2.5 sm:px-4 rounded-xl font-black text-xs sm:text-sm uppercase tracking-wider shadow-xs transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer border-none text-center">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z" />
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
                            </svg>
                            <span class="truncate">Announce</span>
                        </button>

                        <a href="{{ route('admin.reports.generator') }}"
                           style="background-color: #2563eb !important; color: #ffffff !important;"
                           class="w-full sm:w-auto py-2.5 px-2.5 sm:px-4 rounded-xl font-black text-xs sm:text-sm uppercase tracking-wider shadow-xs transition-all duration-200 flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer no-underline shrink-0 text-decoration-none text-center">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span class="truncate">Print Report</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- TAB 1: ALL FLEET DRIVERS & APPLICANTS -->
            <div x-show="activeTab === 'drivers'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-200">
                    <div class="p-4 sm:p-6 text-gray-900">
                        <div id="admin-table-wrapper">
                            @include('drivers.partials.driver-table')
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PASSENGER INCIDENT & SAFETY REPORTS FEED -->
            <div x-show="activeTab === 'reports'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <!-- Reports Feed Container -->
                <div id="reports-table-container">
                    @include('admin.partials.reports-table')
                </div>
            </div>

        </div>
    </div>

    <!-- UPDATE REPORT STATUS MODAL -->
    <div id="updateReportModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.85); overflow: hidden; align-items: center; justify-content: center;">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
            
            <!-- Modal Header -->
            <div class="p-6 relative flex items-center justify-between shrink-0" style="background-color: #0f172a !important; color: #ffffff !important; flex-shrink: 0;">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(37, 99, 235, 0.3) !important; color: #93c5fd !important; border: 1px solid rgba(147, 197, 253, 0.4) !important;">
                        <span style="color: #93c5fd !important;">ADMIN ACTION</span>
                    </div>
                    <h3 class="text-xl font-black" id="modalReportTitle" style="color: #ffffff !important;">Review Passenger Report</h3>
                    <p class="text-xs mt-0.5" id="modalReportSub" style="color: #cbd5e1 !important;">Update status, write admin notes, or suspend driver</p>
                </div>
                <button type="button" onclick="closeUpdateReportModal()" class="w-8 h-8 rounded-full flex items-center justify-center font-bold border-none cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">✕</button>
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
                    <p class="text-xs text-gray-800 font-medium whitespace-pre-line leading-relaxed" id="modalReportDesc"></p>
                    <div class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row gap-2 text-[11px] text-gray-600">
                        <div><strong>Passenger:</strong> <span id="modalReporterName"></span></div>
                        <div><strong>Reported Driver:</strong> <span id="modalDriverName"></span></div>
                    </div>
                </div>

                <!-- Update Status Select -->
                <div class="space-y-2">
                    <label for="status_select" class="block text-xs font-black text-gray-700 uppercase tracking-wider">Report Status <span class="text-red-500">*</span></label>
                    <select id="status_select" name="status" class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-bold text-gray-900 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending">Pending Review</option>
                        <option value="investigating">Under Investigation</option>
                        <option value="resolved">Resolved</option>
                        <option value="dismissed">Dismissed / Invalid</option>
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
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ANNOUNCEMENT BROADCAST MODAL -->
    <div id="announcementModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.8); overflow: hidden; align-items: center; justify-content: center;">
        <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
            
            <div class="p-5 sm:p-7 relative overflow-hidden shrink-0" 
                 style="background: linear-gradient(135deg, #1e3a8a, #2563eb, #3b82f6) !important; color: #ffffff !important;">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shadow-md shrink-0" 
                             style="background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff;">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z" /><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" /></svg>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(255, 255, 255, 0.2); color: #ffffff;">
                                <span>SYSTEM BROADCAST</span>
                            </div>
                            <h3 class="text-lg sm:text-2xl font-black tracking-tight" style="color: #ffffff !important;">Send Announcement</h3>
                            <p class="text-xs font-medium mt-0.5" style="color: #dbeafe !important;">Broadcast live alert notification to app users</p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="closeAnnouncementModal()" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center font-bold text-base sm:text-lg transition-all cursor-pointer hover:bg-white/20 shrink-0" style="color: #ffffff; background-color: rgba(255, 255, 255, 0.15); border: none;">
                        ✕
                    </button>
                </div>
            </div>

            <form action="{{ route('admin.announcements.store') }}" method="POST" class="p-6 sm:p-8 space-y-6 sm:space-y-7 flex-1 min-h-0" style="overflow-y: auto;">
                @csrf
                
                <div class="space-y-2">
                    <label for="title" class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                        Announcement Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required placeholder="e.g. TODA General Meeting, Fare Adjustment..." 
                           class="w-full rounded-2xl border border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 p-3.5 text-sm font-extrabold text-gray-900 transition">
                </div>

                <div class="space-y-2.5">
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                        Target Audience <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="relative flex items-center justify-center p-3 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="target_audience" value="all" aria-label="Target audience: Everyone" checked class="sr-only">
                            <span class="text-[11px] font-black uppercase text-center text-gray-900">Everyone</span>
                        </label>

                        <label class="relative flex items-center justify-center p-3 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="target_audience" value="drivers" aria-label="Target audience: Drivers" class="sr-only">
                            <span class="text-[11px] font-black uppercase text-center text-orange-700">Drivers</span>
                        </label>

                        <label class="relative flex items-center justify-center p-3 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="target_audience" value="passengers" aria-label="Target audience: Passengers" class="sr-only">
                            <span class="text-[11px] font-black uppercase text-center text-emerald-700">Passengers</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="message" class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                        Message Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="4" required placeholder="Type announcement description or details..." 
                              class="w-full rounded-2xl border border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 p-3.5 text-sm font-medium text-gray-900 transition leading-relaxed"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeAnnouncementModal()" class="px-5 py-3 rounded-2xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-7 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider shadow-lg active:scale-95 transition cursor-pointer">
                        Broadcast Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAnnouncementModal() {
            const area = document.getElementById('spa-content-area');
            let modal = area ? area.querySelector('#announcementModal') : null;
            if (!modal) modal = document.getElementById('announcementModal');
            if (!modal) return;

            // Move to body to ensure viewport-relative fixed positioning (bypasses
            // any container transforms in the #spa-content-area)
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }

            // Remove orphaned duplicate modals (a previous page swap could have
            // left a stuck copy appended to <body> sitting on top of this one)
            document.querySelectorAll('#announcementModal').forEach((el) => {
                if (el !== modal) el.remove();
            });

            // Always start with a clean composer — never carry over the last
            // broadcast's title/message or audience selection
            const form = modal.querySelector('form');
            if (form) form.reset();

            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
        }

        function closeAnnouncementModal() {
            document.body.style.overflow = '';
            document.querySelectorAll('#announcementModal').forEach((el) => el.classList.add('hidden'));
            try {
                window.dispatchEvent(new CustomEvent('srh-close-notifications'));
            } catch (e) {}
        }

        function openUpdateReportModal(report) {
            const area = document.getElementById('spa-content-area');
            let modal = area ? area.querySelector('#updateReportModal') : null;
            if (!modal) modal = document.getElementById('updateReportModal');

            if (modal) {
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

                // Move to body to ensure viewport-relative fixed positioning
                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }

                // Remove orphaned duplicates
                document.querySelectorAll('#updateReportModal').forEach((el) => {
                    if (el !== modal) el.remove();
                });

                document.body.style.overflow = 'hidden';
                modal.classList.remove('hidden');
            }
        }

        function closeUpdateReportModal() {
            document.body.style.overflow = '';
            const modal = document.getElementById('updateReportModal');
            if (modal) modal.classList.add('hidden');
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
                // Seamless in-place refresh — no full page reload.
                window.refreshReportsArea();
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
                // Seamless in-place refresh — no full page reload.
                window.refreshReportsArea();
            });
        }

        function openDirectSuspendModal(driverId = null, driverName = '') {
            const area = document.getElementById('spa-content-area');
            let modal = area ? area.querySelector('#directSuspendModal') : null;
            if (!modal) modal = document.getElementById('directSuspendModal');

            if (modal) {
                // Move to body to ensure viewport-relative fixed positioning
                if (modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }

                // Remove orphaned duplicates
                document.querySelectorAll('#directSuspendModal').forEach((el) => {
                    if (el !== modal) el.remove();
                });

                const nameText = document.getElementById('direct_suspend_driver_name_text');
                if (nameText && driverName) {
                    nameText.textContent = driverName;
                }
                if (driverId) {
                    updateDirectSuspendFormAction(driverId);
                }
                document.body.style.overflow = 'hidden';
                modal.classList.remove('hidden');
            }
        }

        function closeDirectSuspendModal() {
            document.body.style.overflow = '';
            const modal = document.getElementById('directSuspendModal');
            if (modal) modal.classList.add('hidden');
        }

        function updateDirectSuspendFormAction(driverId) {
            const form = document.getElementById('directSuspendForm');
            if (driverId && form) {
                form.action = '/drivers/' + driverId + '/suspend';
            }
        }

        function checkAndHighlightTargetReport() {
            const urlParams = new URLSearchParams(window.location.search);
            const targetReportId = urlParams.get('report_id');
            if (targetReportId) {
                setTimeout(() => {
                    const reportEl = document.getElementById('report-card-' + targetReportId);
                    if (reportEl) {
                        let parent = reportEl.parentElement;
                        while (parent && !parent.getAttribute('x-data')) {
                            parent = parent.parentElement;
                        }
                        if (parent && parent._x_dataStack && parent._x_dataStack[0]) {
                            parent._x_dataStack[0].open = true;
                        }
                        
                        reportEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        reportEl.classList.add('ring-4', 'ring-blue-500', 'bg-amber-100/90', 'shadow-2xl', 'scale-[1.02]');
                        setTimeout(() => {
                            reportEl.classList.remove('scale-[1.02]');
                        }, 350);

                        setTimeout(() => {
                            reportEl.classList.remove('ring-4', 'ring-blue-500', 'bg-amber-100/90', 'shadow-2xl');
                        }, 4500);
                    }
                }, 350);
            }
        }

        document.addEventListener('DOMContentLoaded', checkAndHighlightTargetReport);
        checkAndHighlightTargetReport();

        (function() {
            let previousReportsHtml = '';
            let previousSuspendedHtml = '';
            let isPollingReports = false;

            function applyBadge(stats) {
                const badge = document.getElementById('reports-tab-badge');
                if (!badge || !stats) return;
                const pending = stats.pending || 0;
                if (pending > 0) {
                    badge.textContent = pending + ' New';
                    badge.classList.add('text-red-500', 'animate-pulse');
                } else {
                    badge.textContent = stats.total || 0;
                    badge.classList.remove('text-red-500', 'animate-pulse');
                }
            }

            function swapContainer(containerId, html, prevRef) {
                const container = document.getElementById(containerId);
                if (!container) return prevRef;
                if (html && html.trim() !== (prevRef || '').trim()) {
                    const openItems = [];
                    container.querySelectorAll('[x-data]').forEach(el => {
                        if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].open) {
                            const nameEl = el.querySelector('h4');
                            if (nameEl) openItems.push(nameEl.textContent.trim());
                        }
                    });
                    container.innerHTML = html;
                    prevRef = html;
                    // Re-initialize Alpine on the injected HTML so accordions/toggles stay live.
                    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                        try { window.Alpine.initTree(container); } catch (e) {}
                    }
                    setTimeout(() => {
                        container.querySelectorAll('[x-data]').forEach(el => {
                            const nameEl = el.querySelector('h4');
                            if (nameEl && openItems.includes(nameEl.textContent.trim())) {
                                if (el._x_dataStack && el._x_dataStack[0]) {
                                    el._x_dataStack[0].open = true;
                                }
                            }
                        });
                    }, 50);
                }
                return prevRef;
            }

            function refreshReportsArea() {
                const reportsContainer = document.getElementById('reports-table-container');
                if (!reportsContainer) return;
                if (isPollingReports) return;

                const modal = document.getElementById('updateReportModal');
                if (modal && !modal.classList.contains('hidden')) return;

                // Never rewrite the containers while a submit is in flight
                if (document.querySelector('button[data-srh-submitting="true"]')) return;

                isPollingReports = true;

                fetch("{{ route('admin.fetch-reports-html') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.ok ? res.json() : Promise.reject(new Error('bad response')))
                .then(data => {
                    isPollingReports = false;
                    if (!data) return;
                    previousReportsHtml = swapContainer('reports-table-container', data.reports, previousReportsHtml);
                    if (data.stats) applyBadge(data.stats);
                })
                .catch(err => {
                    isPollingReports = false;
                });
            }

            let isPollingApplicants = false;
            let previousApplicantsHtml = '';

            function refreshApplicantsArea() {
                const container = document.getElementById('admin-table-wrapper');
                if (!container) return;
                if (isPollingApplicants) return;

                // Don't replace table if a modal is open or a form is actively submitting
                const rejectModal = document.getElementById('rejectApplicantModal');
                const removeModal = document.getElementById('removeApplicantModal');
                const docModal = document.getElementById('documentViewerModal');
                const attachModal = document.getElementById('attachmentPreviewModal');
                if (rejectModal && !rejectModal.classList.contains('hidden')) return;
                if (removeModal && !removeModal.classList.contains('hidden')) return;
                if (docModal && !docModal.classList.contains('hidden')) return;
                if (attachModal && (!attachModal.classList.contains('hidden') && attachModal.style.display !== 'none')) return;
                if (document.querySelector('button[data-srh-submitting="true"]')) return;

                isPollingApplicants = true;

                fetch("{{ route('admin.fetch-drivers-list') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json, text/html, application/xhtml+xml'
                    }
                })
                .then(res => {
                    const contentType = res.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        return res.json();
                    }
                    return res.text().then(text => ({ html: text }));
                })
                .then(data => {
                    isPollingApplicants = false;
                    if (!data || !data.html) return;
                    const newHtml = data.html.trim();
                    if (newHtml && newHtml !== (previousApplicantsHtml || '').trim()) {
                        container.innerHTML = newHtml;
                        previousApplicantsHtml = newHtml;
                        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                            try { window.Alpine.initTree(container); } catch (e) {}
                        }
                    }
                    if (typeof data.total_count !== 'undefined') {
                        const badge = document.getElementById('applicants-tab-badge');
                        if (badge) badge.textContent = data.total_count;
                    }
                })
                .catch(err => {
                    isPollingApplicants = false;
                });
            }
            window.refreshApplicantsArea = refreshApplicantsArea;

            // Seamless hook: global ride-action submitter calls this to refresh tables in place
            window.handleDriverRideActionSuccess = function(url, method, data) {
                if (url && /\/(suspend|unsuspend)$/.test(url)) {
                    if (typeof closeDirectSuspendModal === 'function') closeDirectSuspendModal();
                }
                if (typeof closeRejectApplicantModal === 'function') closeRejectApplicantModal();
                if (typeof closeRemoveApplicantModal === 'function') closeRemoveApplicantModal();
                if (typeof closeUpdateReportModal === 'function') closeUpdateReportModal();

                setTimeout(function() { 
                    if (window.refreshReportsArea) window.refreshReportsArea();
                    if (window.refreshApplicantsArea) window.refreshApplicantsArea();
                }, 50);
            };

            const searchInputEl = document.getElementById('searchInput');
            if (searchInputEl && !searchInputEl._searchBound) {
                searchInputEl._searchBound = true;
                searchInputEl.addEventListener('input', applyDriverSearchFilter);
            }

            // ⚡ Real-Time Event-Driven WebSocket Updates (Zero polling, Zero fetches on tab switch)
            if (window._reportsPollInterval) clearInterval(window._reportsPollInterval);

            function attachAdminEchoListeners() {
                if (typeof window.Echo !== 'undefined' && window.Echo) {
                    try {
                        window.Echo.channel('srh-toda-admin')
                            .stopListening('.report.updated')
                            .stopListening('.driver.applicant.updated')
                            .listen('.report.updated', function(e) {
                                refreshReportsArea();
                            })
                            .listen('.driver.applicant.updated', function(e) {
                                refreshApplicantsArea();
                            });
                    } catch (e) {}
                }
            }

            if (window.srhOnEchoReady) {
                window.srhOnEchoReady(attachAdminEchoListeners);
            } else {
                attachAdminEchoListeners();
            }
        })();
    </script>

    <!-- DIRECT DRIVER SUSPENSION MODAL -->
    <div id="directSuspendModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.85); overflow: hidden; align-items: center; justify-content: center;">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
            
            <div class="p-6 relative flex items-center justify-between shrink-0" style="background-color: #dc2626 !important; color: #ffffff !important; flex-shrink: 0;">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important;">
                        <span>DISCIPLINARY ACTION</span>
                    </div>
                    <h3 class="text-xl font-black" style="color: #ffffff !important;">Suspend TODA Driver</h3>
                    <p class="text-xs mt-0.5" style="color: #fee2e2 !important;">Restrict driver from going on duty and receiving ride requests</p>
                </div>
                <button type="button" onclick="closeDirectSuspendModal()" class="w-8 h-8 rounded-full flex items-center justify-center font-bold border-none cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">✕</button>
            </div>

            <form id="directSuspendForm" method="POST" action="" class="p-6 space-y-4 flex-1 overflow-y-auto">
                @csrf
                @method('PATCH')

                <!-- TARGET DRIVER DISPLAY BADGE (Automatically selected from driver card) -->
                <div class="bg-red-50/80 border border-red-200/80 rounded-2xl p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-black uppercase tracking-wider text-red-600">Suspending Driver</p>
                        <h4 id="direct_suspend_driver_name_text" class="text-sm font-black text-slate-900 truncate">Driver</h4>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="driver_index_suspension_reason" class="block text-xs font-black text-gray-700 uppercase">Reason for Suspension <span class="text-red-500">*</span></label>
                    <textarea id="driver_index_suspension_reason" name="suspension_reason" required rows="4" placeholder="Detail official reason for driver suspension..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeDirectSuspendModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Suspend Driver</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>