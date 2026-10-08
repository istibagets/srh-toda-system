<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Live Terminal Queue') }}"></x-page-header>
    </x-slot>

    <!-- SortableJS Library for iOS, Android, and Desktop Touch Drag & Drop (Local Bundle) -->
    <script src="{{ asset('vendor/sortable/Sortable.min.js') }}"></script>

    <style>
        /* 🌟 Smooth Card Transition for Squeezing & Popping Sibling Elements */
        .draggable-queue-item {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
            will-change: transform;
        }

        /* 🌟 Clean Empty Drop Slot Placeholder (Preserves Height Math, No Text Overlap) */
        .sortable-ghost {
            opacity: 0.25 !important;
            background: #eff6ff !important;
            border: 2px dashed #2563eb !important;
            border-radius: 1rem !important;
            box-shadow: inset 0 2px 8px rgba(37, 99, 235, 0.1) !important;
        }

        /* Hide inner content of ghost element */
        .sortable-ghost * {
            opacity: 0 !important;
            visibility: hidden !important;
        }

        /* 🚀 Active Dragged Fallback Card (Transition: NONE for Instant 1:1 Cursor Tracking) */
        .sortable-drag, .sortable-fallback {
            transition: none !important;
            opacity: 0.98 !important;
            background: #ffffff !important;
            border: 2px solid #2563eb !important;
            border-radius: 1rem !important;
            box-shadow: 0 16px 36px -6px rgba(37, 99, 235, 0.35) !important;
            z-index: 999999 !important;
        }

        /* 🎯 Chosen Card */
        .sortable-chosen {
            background-color: #f0f9ff !important;
        }

        /* 🖐 Touch & Mouse Drag Handle */
        .drag-handle {
            touch-action: none;
            cursor: grab;
            -webkit-user-select: none;
            user-select: none;
        }
        .drag-handle:active {
            cursor: grabbing;
        }

        /* ⚡ Spring Pop Animation for Queue Badges */
        .badge-pop {
            animation: badgePopAnim 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes badgePopAnim {
            0% { transform: scale(0.85); }
            50% { transform: scale(1.12); }
            100% { transform: scale(1); }
        }

        /* 🚀 Ultra-Sleek Fixed Action Bar (Teleported to document.body root) */
        #queue-action-bar {
            position: fixed !important;
            bottom: 4.75rem !important; /* 76px from viewport bottom */
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 999999 !important;
            width: 90% !important;
            max-width: 23.5rem !important;
        }

        @media (min-width: 640px) {
            #queue-action-bar {
                bottom: 1.25rem !important;
                max-width: 24rem !important;
            }
        }
    </style>

    <!-- Main scrollable queue container (with padding for fixed bottom elements) -->
    <div class="py-6 px-4 w-full max-w-lg mx-auto pb-28 sm:pb-16">
        
        <!-- Clean Glassmorphic Toast Notification (Teleported to document.body root) -->
        <div id="queueToast" 
             class="fixed left-1/2 -translate-x-1/2 px-4 py-2 rounded-full shadow-xl transition-all duration-300 opacity-0 -translate-y-12 pointer-events-none flex items-center justify-center whitespace-nowrap z-[999999]"
             style="position: fixed; top: calc(1rem + env(safe-area-inset-top, 0px)); left: 50%; transform: translateX(-50%); z-index: 999999; background-color: #0f172a !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.6) !important;">
            <span id="queueToastMessage" class="text-xs font-extrabold tracking-wide whitespace-nowrap" style="color: #ffffff !important;">Queue order saved</span>
        </div>

        <!-- The partial ships its own #live-queue-wrapper root; nesting an extra
         wrapper here broke the DOM-diff merge (children[1] = undefined),
         leaving the "On Trip" section stale until a manual reload. -->
        @include('drivers.partials.queue-list')

    </div>

    <!-- ⚡ ACTION BAR (Ultra-sleek, compact floating pill bar) -->
    @if(auth()->check() && auth()->user()->role === 'admin')
        <div id="queue-action-bar" 
             class="fixed left-1/2 rounded-xl shadow-2xl transition-all duration-300 pointer-events-none flex items-center justify-between gap-2 z-[999999] bottom-[calc(4.75rem+env(safe-area-inset-bottom,0px))] sm:bottom-6"
             style="position: fixed; left: 50%; transform: translateX(-50%) translateY(8rem); opacity: 0; pointer-events: none; z-index: 999999; width: calc(100% - 2rem); max-width: 23.5rem; background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.75) !important; padding: 0.5rem 0.75rem !important;">
            
            <div class="min-w-0 flex-1 pl-0.5">
                <p class="text-[11px] font-black tracking-tight leading-tight whitespace-nowrap" style="color: #ffffff !important; font-weight: 800;">Unsaved Changes</p>
                <p class="text-[9px] font-semibold truncate leading-tight" style="color: #94a3b8 !important;">Save to apply new order</p>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="cancelQueueChanges()" 
                        class="px-2.5 py-1 rounded-lg font-bold text-[10px] transition active:scale-95 cursor-pointer whitespace-nowrap"
                        style="background-color: #1e293b !important; color: #cbd5e1 !important; border: 1px solid #334155 !important; padding: 0.25rem 0.6rem !important;">
                    Cancel
                </button>
                <button type="button" onclick="confirmSaveQueueOrder()" id="save-queue-btn"
                        class="px-3 py-1 rounded-lg font-black text-[10px] transition active:scale-95 shadow-xs flex items-center gap-1 cursor-pointer whitespace-nowrap"
                        style="background-color: #2563eb !important; color: #ffffff !important; border: 1px solid #3b82f6 !important; padding: 0.25rem 0.75rem !important;">
                    <span>Save</span>
                </button>
            </div>
        </div>
    @endif

    <script>
    (function() {
        window.isAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
        window.isUserDragging = false;
        window.hasUnsavedChanges = false;
        window.originalDriverOrder = null;

        if (window.sortableInstance) {
            try { window.sortableInstance.destroy(); } catch(e){}
            window.sortableInstance = null;
        }

        function teleportElements() {
            let bar = document.getElementById('queue-action-bar');
            if (!bar && window.isAdmin) {
                bar = document.createElement('div');
                bar.id = 'queue-action-bar';
                bar.className = 'fixed left-1/2 rounded-xl shadow-2xl transition-all duration-300 pointer-events-none flex items-center justify-between gap-2 z-[999999]';
                bar.style.cssText = 'position: fixed !important; bottom: calc(4.75rem + env(safe-area-inset-bottom, 0px)) !important; left: 50% !important; transform: translateX(-50%) translateY(8rem); opacity: 0; pointer-events: none; z-index: 999999 !important; width: calc(100% - 2rem) !important; max-width: 23.5rem !important; background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.75) !important; padding: 0.5rem 0.75rem !important;';
                bar.innerHTML = `
                    <div class="min-w-0 flex-1 pl-0.5">
                        <p class="text-[11px] font-black tracking-tight leading-tight whitespace-nowrap" style="color: #ffffff !important; font-weight: 800;">Unsaved Changes</p>
                        <p class="text-[9px] font-semibold truncate leading-tight" style="color: #94a3b8 !important;">Save to apply new order</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="cancelQueueChanges()" 
                                class="px-2.5 py-1 rounded-lg font-bold text-[10px] transition active:scale-95 cursor-pointer whitespace-nowrap"
                                style="background-color: #1e293b !important; color: #cbd5e1 !important; border: 1px solid #334155 !important; padding: 0.25rem 0.6rem !important;">
                            Cancel
                        </button>
                        <button type="button" onclick="confirmSaveQueueOrder()" id="save-queue-btn"
                                class="px-3 py-1 rounded-lg font-black text-[10px] transition active:scale-95 shadow-xs flex items-center gap-1 cursor-pointer whitespace-nowrap"
                                style="background-color: #2563eb !important; color: #ffffff !important; border: 1px solid #3b82f6 !important; padding: 0.25rem 0.75rem !important;">
                            <span>Save</span>
                        </button>
                    </div>
                `;
                document.body.appendChild(bar);
            } else if (bar && bar.parentElement !== document.body) {
                document.body.appendChild(bar);
            }

            let toast = document.getElementById('queueToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'queueToast';
                toast.className = 'fixed left-1/2 -translate-x-1/2 px-4 py-2 rounded-full shadow-xl transition-all duration-300 opacity-0 -translate-y-12 pointer-events-none flex items-center justify-center whitespace-nowrap z-[999999]';
                toast.style.cssText = 'position: fixed; top: calc(1rem + env(safe-area-inset-top, 0px)); left: 50%; transform: translateX(-50%); z-index: 999999; background-color: #0f172a !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.6) !important;';
                toast.innerHTML = '<span id="queueToastMessage" class="text-xs font-extrabold tracking-wide whitespace-nowrap" style="color: #ffffff !important;">Queue order saved</span>';
                document.body.appendChild(toast);
            } else if (toast && toast.parentElement !== document.body) {
                document.body.appendChild(toast);
            }
        }
        teleportElements();

        function showQueueToast(message = 'Queue order saved') {
            teleportElements();
            const toast = document.getElementById('queueToast');
            if (!toast) return;
            const msgEl = document.getElementById('queueToastMessage');
            if (msgEl) msgEl.innerText = message;

            toast.classList.remove('opacity-0', '-translate-y-12');
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-12');
            }, 2800);
        }
        window.showQueueToast = showQueueToast;

        function captureOriginalOrder(force = false) {
            if (!force && (window.hasUnsavedChanges || window.isUserDragging)) return;
            const container = document.getElementById('queue-items-container');
            if (!container) return;
            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const ids = Array.from(items).map(item => String(item.getAttribute('data-driver-id'))).filter(Boolean);
            if (ids.length > 0) {
                window.originalDriverOrder = ids;
            }
        }
        window.captureOriginalOrder = captureOriginalOrder;

        function checkUnsavedChanges() {
            if (!window.isAdmin) return;
            teleportElements();
            const container = document.getElementById('queue-items-container');
            const actionBar = document.getElementById('queue-action-bar');
            if (!container || !actionBar) return;

            const currentItems = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const currentOrder = Array.from(currentItems).map(item => String(item.getAttribute('data-driver-id'))).filter(Boolean);

            if (!window.originalDriverOrder || window.originalDriverOrder.length === 0) {
                window.originalDriverOrder = currentOrder.slice();
                return;
            }

            const isDifferent = currentOrder.length > 0 && window.originalDriverOrder.length > 0 && (
                currentOrder.length !== window.originalDriverOrder.length ||
                currentOrder.some((id, idx) => String(id) !== String(window.originalDriverOrder[idx]))
            );

            window.hasUnsavedChanges = isDifferent;

            if (window.hasUnsavedChanges) {
                actionBar.style.setProperty('transform', 'translateX(-50%) translateY(0)', 'important');
                actionBar.style.setProperty('opacity', '1', 'important');
                actionBar.style.setProperty('pointer-events', 'auto', 'important');
                actionBar.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                actionBar.style.setProperty('transform', 'translateX(-50%) translateY(8rem)', 'important');
                actionBar.style.setProperty('opacity', '0', 'important');
                actionBar.style.setProperty('pointer-events', 'none', 'important');
                actionBar.classList.add('opacity-0', 'pointer-events-none');
            }
        }
        window.checkUnsavedChanges = checkUnsavedChanges;

        function initSortableQueue() {
            teleportElements();
            const container = document.getElementById('queue-items-container');
            if (!container || !window.isAdmin) return;

            if (typeof Sortable === 'undefined') {
                if (!window.loadingSortableScript) {
                    window.loadingSortableScript = true;
                    const script = document.createElement('script');
                    script.src = "{{ asset('vendor/sortable/Sortable.min.js') }}";
                    script.onload = function() {
                        window.loadingSortableScript = false;
                        initSortableQueue();
                    };
                    document.head.appendChild(script);
                } else {
                    setTimeout(initSortableQueue, 100);
                }
                return;
            }

            if (window.sortableInstance) {
                try { window.sortableInstance.destroy(); } catch(e){}
            }

            window.sortableInstance = new Sortable(container, {
                animation: 200,
                easing: "cubic-bezier(0.16, 1, 0.3, 1)",
                handle: '.drag-handle, .queue-number-badge',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                fallbackClass: 'sortable-fallback',
                chosenClass: 'sortable-chosen',
                forceFallback: true,
                fallbackOnBody: true,
                fallbackTolerance: 0,
                touchStartThreshold: 0,
                swapThreshold: 0.5,
                onStart: function () {
                    window.isUserDragging = true;
                    if (!window.originalDriverOrder || window.originalDriverOrder.length === 0) {
                        captureOriginalOrder(true);
                    }
                    if (navigator.vibrate) {
                        try { navigator.vibrate(20); } catch(e){}
                    }
                },
                onChange: function() {
                    updateVisualQueueNumbers();
                },
                onEnd: function () {
                    window.isUserDragging = false;
                    if (navigator.vibrate) {
                        try { navigator.vibrate([15, 15]); } catch(e){}
                    }
                    updateVisualQueueNumbers();
                    checkUnsavedChanges();
                }
            });

            // Capture initial baseline
            captureOriginalOrder(true);
        }
        window.initSortableQueue = initSortableQueue;

        initSortableQueue();

        function updateVisualQueueNumbers() {
            const container = document.getElementById('queue-items-container');
            if (!container) return;

            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            items.forEach((item, index) => {
                const numSpan = item.querySelector('.queue-number-badge');
                if (numSpan) {
                    const newText = '#' + (index + 1);
                    if (numSpan.innerText !== newText) {
                        numSpan.innerText = newText;
                        numSpan.classList.remove('badge-pop');
                        void numSpan.offsetWidth;
                        numSpan.classList.add('badge-pop');
                    }
                    if (index === 0) {
                        numSpan.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs transition-all bg-blue-600 text-white shrink-0';
                    } else {
                        numSpan.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs transition-all bg-blue-50 text-blue-700 border border-blue-200 shrink-0';
                    }
                }
            });

            const fallback = document.querySelector('.sortable-fallback');
            const ghost = container.querySelector('.sortable-ghost');
            if (fallback && ghost) {
                const realSiblings = Array.from(container.children).filter(child => !child.classList.contains('sortable-fallback'));
                const ghostIndex = realSiblings.indexOf(ghost);
                const fallbackBadge = fallback.querySelector('.queue-number-badge');
                if (fallbackBadge && ghostIndex !== -1) {
                    const newText = '#' + (ghostIndex + 1);
                    fallbackBadge.innerText = newText;
                    if (ghostIndex === 0) {
                        fallbackBadge.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs bg-blue-600 text-white shrink-0';
                    } else {
                        fallbackBadge.className = 'queue-number-badge font-black text-sm sm:text-base px-2.5 py-1 rounded-xl shadow-xs bg-blue-50 text-blue-700 border border-blue-200 shrink-0';
                    }
                }
            }
        }
        window.updateVisualQueueNumbers = updateVisualQueueNumbers;

        function confirmSaveQueueOrder() {
            const container = document.getElementById('queue-items-container');
            const saveBtn = document.getElementById('save-queue-btn');
            if (!container) return;

            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            const driverIds = Array.from(items).map(item => item.getAttribute('data-driver-id')).filter(Boolean);

            if (driverIds.length === 0) return;

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span>Saving...</span>';
            }

            fetch("{{ route('admin.reorder-queue') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ driver_ids: driverIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.originalDriverOrder = [...driverIds];
                    checkUnsavedChanges();
                    showQueueToast('Queue order saved');
                }
            })
            .catch(err => console.error('Error saving queue order:', err))
            .finally(() => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<span>Save</span>';
                }
            });
        }
        window.confirmSaveQueueOrder = confirmSaveQueueOrder;

        function cancelQueueChanges() {
            const container = document.getElementById('queue-items-container');
            if (!container || !window.originalDriverOrder || window.originalDriverOrder.length === 0) return;

            const itemMap = new Map();
            const items = container.querySelectorAll('.draggable-queue-item:not(.sortable-fallback)');
            items.forEach(item => {
                itemMap.set(item.getAttribute('data-driver-id'), item);
            });

            window.originalDriverOrder.forEach(id => {
                const item = itemMap.get(id);
                if (item) {
                    container.appendChild(item);
                }
            });

            updateVisualQueueNumbers();
            checkUnsavedChanges();
        }
        window.cancelQueueChanges = cancelQueueChanges;

        function fetchLiveQueue() {
            if (window.isUserDragging || window.hasUnsavedChanges) return;
            if (window._srhQueueFetchInFlight) return;
            window._srhQueueFetchInFlight = true;

            const wrapper = document.getElementById('live-queue-wrapper');
            if (!wrapper) {
                window._srhQueueFetchInFlight = false;
                if (window.liveQueueInterval) {
                    clearInterval(window.liveQueueInterval);
                    window.liveQueueInterval = null;
                }
                return;
            }

            const activeEl = document.activeElement;
            if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT')) {
                window._srhQueueFetchInFlight = false;
                return;
            }

            fetch("{{ route('driver.fetch-queue') }}", {
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) return null;
                    return response.text();
                })
                .then(html => {
                    if (!html || window.isUserDragging || window.hasUnsavedChanges) return;

                    // Seamless DOM-diff merge: preserves rows, transitions and scroll
                    const structureChanged = window.srhMergeLiveQueue ? window.srhMergeLiveQueue(wrapper, html) : false;
                    if (structureChanged) {
                        initSortableQueue();
                    }

                    // Re-baseline the drag state so external changes (a driver joining
                    // or leaving, another admin reordering) never leave a stale
                    // "Unsaved Changes" state or a mismatched original order.
                    captureOriginalOrder();
                })
                .catch(error => console.error('Error fetching queue:', error))
                .finally(() => {
                    window._srhQueueFetchInFlight = false;
                });
        }

        // Rerverb "queue.changed" events hook into this for instant updates.
        window.srhRefreshQueueList = function() {
            fetchLiveQueue();
        };

        initSortableQueue();
        setTimeout(initSortableQueue, 100);
    })();
    </script>
</x-app-layout>