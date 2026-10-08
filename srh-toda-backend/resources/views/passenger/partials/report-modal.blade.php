<!-- PASSENGER INCIDENT REPORT MODAL -->
<div id="passengerReportModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-6" style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 999999; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.85); overflow: hidden; align-items: center; justify-content: center; display: none;">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in zoom-in-95 duration-200 flex flex-col" style="max-height: 85vh; max-height: 85dvh; margin: auto; position: relative; z-index: 1000000;">
        
        <!-- Modal Header -->
        <div class="p-6 relative flex items-center justify-between shrink-0" style="background-color: #dc2626 !important; color: #ffffff !important; flex-shrink: 0;">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1" style="background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important;">
                    <span style="color: #ffffff !important;">PASSENGER SAFETY REPORT</span>
                </div>
                <h3 class="text-xl font-black" style="color: #ffffff !important;">Report an Incident</h3>
                <p class="text-xs mt-0.5" style="color: #fee2e2 !important;">Report reckless driving, overcharging, or conduct to TODA Admin</p>
            </div>
            <button type="button" onclick="closePassengerReportModal()" class="w-8 h-8 rounded-full flex items-center justify-center font-bold border-none cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;">✕</button>
        </div>

        <form id="passengerReportForm" onsubmit="handlePassengerReportSubmit(event)" class="p-6 space-y-4 flex-1 overflow-y-auto">
            <input type="hidden" name="driver_id" id="passenger_report_driver_id">
            <input type="hidden" name="ride_id" id="passenger_report_ride_id">

            <!-- Pre-filled Ride Info Summary Card -->
            <div id="passenger_report_info_card" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Target Driver & Ride</span>
                <div class="text-xs font-black text-slate-900" id="passenger_report_driver_display">TODA Driver (Ride Incident)</div>
            </div>

            <!-- Incident Category -->
            <div class="space-y-1.5">
                <label for="passenger_report_category" class="block text-xs font-black text-gray-700 uppercase">Incident Type <span class="text-red-500">*</span></label>
                <select name="category" id="passenger_report_category" required class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-bold text-gray-900 focus:ring-red-500 focus:border-red-500">
                    <option value="Overcharging">Overcharging / Fare Dispute</option>
                    <option value="Reckless Driving">Reckless / Overspeeding Driving</option>
                    <option value="Unprofessional Behavior">Unprofessional / Rude Behavior</option>
                    <option value="Lost Item">Lost / Forgotten Item</option>
                    <option value="Vehicle Condition">Poor Tricycle Condition</option>
                    <option value="Other">Other Incident</option>
                </select>
            </div>

            <!-- Description / Details -->
            <div class="space-y-1.5">
                <label for="passenger_report_description" class="block text-xs font-black text-gray-700 uppercase">Description / Details <span class="text-red-500">*</span></label>
                <textarea name="description" id="passenger_report_description" required rows="4" placeholder="Describe what happened during your ride..." class="w-full rounded-2xl border border-gray-300 p-3 text-xs font-medium text-gray-900 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closePassengerReportModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold text-xs uppercase hover:bg-gray-100 cursor-pointer">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-md cursor-pointer">Submit to Admin</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.openPassengerReportModalFromRide = function(rideId, driverId, driverName) {
        const ratingModal = document.getElementById('trip-completed-modal');
        if (ratingModal) ratingModal.style.display = 'none';

        const modal = document.getElementById('passengerReportModal');
        if (modal) {
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
            document.getElementById('passenger_report_ride_id').value = rideId || '';
            document.getElementById('passenger_report_driver_id').value = driverId || '';
            document.getElementById('passenger_report_driver_display').textContent = driverName ? ('Driver: ' + driverName + (rideId ? (' (Ride #' + rideId + ')') : '')) : (rideId ? ('Ride #' + rideId) : 'Unspecified Driver');
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    };

    window.closePassengerReportModal = function() {
        const modal = document.getElementById('passengerReportModal');
        if (modal) {
            document.body.style.overflow = '';
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        const ratingModal = document.getElementById('trip-completed-modal');
        if (ratingModal) {
            ratingModal.style.display = 'flex';
        }
    };

    window.handlePassengerReportSubmit = function(e) {
        e.preventDefault();
        const category = document.getElementById('passenger_report_category').value;
        const driver_id = document.getElementById('passenger_report_driver_id').value;
        const ride_id = document.getElementById('passenger_report_ride_id').value;
        const description = document.getElementById('passenger_report_description').value;

        fetch('{{ route("reports.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-SPA-Request': 'true'
            },
            body: JSON.stringify({
                category: category,
                driver_id: driver_id || null,
                ride_id: ride_id || null,
                description: description
            })
        })
        .then(res => res.json())
        .then(data => {
            closePassengerReportModal();
            if (window.createSlidingToast) {
                window.createSlidingToast('Your report has been submitted to TODA Admin for review.', 'success');
            } else {
                alert('Your report has been submitted to TODA Admin for review.');
            }
            document.getElementById('passengerReportForm').reset();
        })
        .catch(err => {
            alert('Error submitting report. Please try again.');
        });
    };
</script>
