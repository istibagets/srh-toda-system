<!-- REAL-TIME RIDE CHAT MODAL (Clean Light Theme with Ultra-Smooth Spring Animations) -->
<style>
    /* Hardware-Accelerated Smooth Entrance & Exit Animations */
    #rideChatModal {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity;
    }
    #rideChatModal.srh-chat-active {
        opacity: 1;
        pointer-events: auto;
    }

    #rideChatModal .srh-chat-card {
        transform: translate3d(0, 100%, 0) scale(0.98);
        transition: transform 0.36s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    #rideChatModal.srh-chat-active .srh-chat-card {
        transform: translate3d(0, 0, 0) scale(1);
    }
</style>

<div id="rideChatModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden flex items-end sm:items-center justify-center p-0 sm:p-4 z-[2147483640]" style="display: none; position: fixed; inset: 0; width: 100vw; height: 100vh; height: 100dvh; z-index: 2147483640;" onclick="if(event.target === this) closeRideChatModal();">
    <div id="rideChatCard" class="srh-chat-card relative w-full sm:max-w-md h-[92dvh] sm:h-[620px] max-h-[92dvh] sm:max-h-[620px] bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden text-left border border-slate-200/90" onclick="event.stopPropagation();">

        <!-- Header -->
        <div class="px-4 py-3 sm:py-3.5 bg-white border-b border-slate-200 flex items-center justify-between gap-3 shrink-0 shadow-xs select-none">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="relative w-10 h-10 rounded-2xl overflow-hidden shrink-0 border border-slate-200 bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-xs">
                    <img id="chat_modal_avatar" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    <span id="chat_modal_initial" class="uppercase">D</span>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h4 id="chat_modal_name" class="text-sm font-black text-slate-900 truncate">TODA Driver</h4>
                        <span id="chat_modal_badge" class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 shrink-0">Driver</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Trip Chat
                    </p>
                </div>
            </div>

            <!-- Close button -->
            <button type="button" onclick="closeRideChatModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-90 text-slate-600 hover:text-slate-900 font-black text-sm flex items-center justify-center transition cursor-pointer border border-slate-200" title="Close Chat">✕</button>
        </div>

        <!-- Message History Stream -->
        <div id="rideChatMessagesList" class="flex-1 w-full bg-slate-50/80 overflow-y-auto p-4 space-y-3 overscroll-contain">
            <!-- Loading Indicator -->
            <div id="chat_modal_loading" class="flex flex-col items-center justify-center gap-2 py-12 text-slate-400">
                <div class="w-7 h-7 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-bold text-slate-500">Connecting chat...</span>
            </div>

            <!-- Empty State -->
            <div id="chat_modal_empty" class="hidden flex flex-col items-center justify-center gap-2 py-14 text-slate-400 text-center px-4">
                <div class="w-12 h-12 rounded-2xl bg-white text-blue-600 flex items-center justify-center text-xl shadow-xs border border-slate-200">💬</div>
                <p class="text-xs font-black text-slate-800">Direct message your driver/passenger</p>
                <p class="text-[11px] text-slate-500 max-w-xs">Messages are instant, private, and visible only to you and your driver during this trip.</p>
            </div>

            <!-- Dynamic Messages injected here -->
        </div>

        <!-- Quick Preset Reply Chips -->
        <div class="px-3 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
            <button type="button" onclick="sendQuickPresetChat('I am at the pickup point')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">📍 At pickup point</button>
            <button type="button" onclick="sendQuickPresetChat('On my way!')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">⚡ On my way!</button>
            <button type="button" onclick="sendQuickPresetChat('Where are you now?')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">❓ Where are you?</button>
            <button type="button" onclick="sendQuickPresetChat('I have arrived outside')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">🛵 Arrived outside</button>
            <button type="button" onclick="sendQuickPresetChat('Okay, copy that!')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">👍 Okay, copy!</button>
            <button type="button" onclick="sendQuickPresetChat('Thank you!')" class="px-2.5 py-1 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-[11px] font-bold border border-slate-200/90 whitespace-nowrap active:scale-95 transition cursor-pointer shadow-2xs">🙏 Thank you</button>
        </div>

        <!-- Message Input Footer -->
        <form id="rideChatForm" onsubmit="handleRideChatSubmit(event)" class="p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom,0px))] bg-white border-t border-slate-200 flex items-center gap-2 shrink-0">
            <input type="text" id="rideChatInput" name="message" maxlength="1000" required autocomplete="off" aria-label="Type a message" placeholder="Type a message..." class="flex-1 bg-slate-100 hover:bg-slate-100/90 focus:bg-white text-slate-900 placeholder-slate-400 border border-slate-200 rounded-full px-4 py-2.5 text-xs font-medium focus:outline-hidden focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
            <button type="submit" id="rideChatSendBtn" class="w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 active:scale-90 text-white flex items-center justify-center shrink-0 shadow-md border-none cursor-pointer transition disabled:opacity-50" title="Send Message">
                <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
            </button>
        </form>

    </div>
</div>

<script>
(function() {
    let currentChatRideId = null;
    let chatPollingTimer = null;
    let isChatSubmitting = false;
    let knownMessageIds = new Set();
    let unreadChatCount = 0;

    function escapeHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function scrollChatToBottom(smooth = false) {
        const container = document.getElementById('rideChatMessagesList');
        if (container) {
            if (smooth) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            } else {
                container.scrollTop = container.scrollHeight;
            }
        }
    }

    function renderChatMessage(msg, animate = false) {
        const list = document.getElementById('rideChatMessagesList');
        if (!list || knownMessageIds.has(msg.id)) return;
        knownMessageIds.add(msg.id);

        const empty = document.getElementById('chat_modal_empty');
        if (empty) empty.classList.add('hidden');

        const isMe = !!msg.is_me;
        const bubbleWrap = document.createElement('div');
        bubbleWrap.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} ${animate ? 'animate-in fade-in-50 slide-in-from-bottom-2 duration-150' : ''}`;
        bubbleWrap.dataset.messageId = msg.id;

        const senderBadge = isMe ? '' : `<span class="text-[9px] font-black text-slate-500 mb-0.5 px-1">${escapeHtml(msg.sender_name)}</span>`;

        bubbleWrap.innerHTML = `
            ${senderBadge}
            <div class="max-w-[82%] px-3.5 py-2 rounded-2xl text-xs ${isMe ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-br-xs shadow-sm font-medium' : 'bg-white text-slate-800 border border-slate-200/90 rounded-bl-xs shadow-2xs font-medium'} leading-relaxed break-words">
                ${escapeHtml(msg.message)}
            </div>
            <span class="text-[9px] font-bold text-slate-400 mt-0.5 px-1">${escapeHtml(msg.time || '')}</span>
        `;

        list.appendChild(bubbleWrap);
    }

    window.openRideChatModal = function(rideId, targetName, targetAvatar, targetRole) {
        const modal = document.getElementById('rideChatModal');
        const card = document.getElementById('rideChatCard');
        if (!modal || !rideId) return;

        currentChatRideId = parseInt(rideId);
        unreadChatCount = 0;
        updateUnreadBadges(0);
        window.srhSubscribeRideChat(rideId);

        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }

        const nameEl = document.getElementById('chat_modal_name');
        const badgeEl = document.getElementById('chat_modal_badge');
        const avatarImg = document.getElementById('chat_modal_avatar');
        const initialEl = document.getElementById('chat_modal_initial');
        const loadingEl = document.getElementById('chat_modal_loading');
        const emptyEl = document.getElementById('chat_modal_empty');
        const list = document.getElementById('rideChatMessagesList');

        if (nameEl) nameEl.textContent = targetName || 'Ride Participant';
        if (badgeEl) badgeEl.textContent = targetRole || (targetName && targetName.toLowerCase().includes('driver') ? 'Driver' : 'Passenger');

        if (avatarImg && targetAvatar) {
            avatarImg.src = targetAvatar;
            avatarImg.classList.remove('hidden');
            if (initialEl) initialEl.classList.add('hidden');
        } else {
            if (avatarImg) { avatarImg.src = ''; avatarImg.classList.add('hidden'); }
            if (initialEl) {
                initialEl.textContent = (targetName ? targetName.charAt(0) : 'D').toUpperCase();
                initialEl.classList.remove('hidden');
            }
        }

        // Clean out previous messages and reset set
        knownMessageIds.clear();
        if (list) {
            const children = Array.from(list.children);
            children.forEach(c => {
                if (c.id !== 'chat_modal_loading' && c.id !== 'chat_modal_empty') {
                    c.remove();
                }
            });
        }

        if (loadingEl) loadingEl.classList.remove('hidden');
        if (emptyEl) emptyEl.classList.add('hidden');

        document.body.style.overflow = 'hidden';

        if (card) {
            card.style.transition = '';
            card.style.transform = '';
        }
        modal.style.opacity = '';
        modal.style.transition = '';
        modal.style.display = 'flex';
        modal.classList.remove('hidden');

        // Smooth GPU Spring Entrance
        requestAnimationFrame(() => {
            modal.classList.add('srh-chat-active');
        });

        // Fetch messages immediately
        loadRideMessages(currentChatRideId, true);

        // Start 2.5s polling loop while modal is open
        if (chatPollingTimer) clearInterval(chatPollingTimer);
        chatPollingTimer = setInterval(() => {
            if (currentChatRideId && modal.style.display !== 'none' && !modal.classList.contains('hidden')) {
                loadRideMessages(currentChatRideId, false);
            }
        }, 2500);

        // Autofocus input
        setTimeout(() => {
            const inp = document.getElementById('rideChatInput');
            if (inp) inp.focus();
        }, 250);
    };

    window.closeRideChatModal = function() {
        const modal = document.getElementById('rideChatModal');
        const card = document.getElementById('rideChatCard');
        if (!modal) return;

        modal.classList.remove('srh-chat-active');
        document.body.style.overflow = '';

        if (chatPollingTimer) {
            clearInterval(chatPollingTimer);
            chatPollingTimer = null;
        }

        setTimeout(() => {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            if (card) {
                card.style.transition = '';
                card.style.transform = '';
            }
            modal.style.opacity = '';
            modal.style.transition = '';
        }, 280);
    };

    // Universal Swipe-Down-To-Dismiss Engine (Real-time 1:1 finger tracking & momentum release)
    (function() {
        const card = document.getElementById('rideChatCard');
        const modal = document.getElementById('rideChatModal');
        const list = document.getElementById('rideChatMessagesList');
        if (!card || !modal) return;

        let startY = 0, startX = 0, currentY = 0, lastY = 0, lastTime = 0, velocityY = 0;
        let isDragging = false, canDragCard = false;

        card.addEventListener('touchstart', function(e) {
            if (!e.touches || !e.touches[0]) return;
            const target = e.target;
            
            // Ignore drag if touching input, buttons, or form controls
            if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'BUTTON' || target.closest('button') || target.closest('form'))) {
                canDragCard = false;
                isDragging = false;
                return;
            }

            startY = e.touches[0].clientY;
            startX = e.touches[0].clientX;
            currentY = startY;
            lastY = startY;
            lastTime = performance.now();
            velocityY = 0;
            
            // If touching inside message list, only allow card drag when at the very top (scrollTop <= 2)
            if (list && list.contains(target)) {
                canDragCard = (list.scrollTop <= 2);
            } else {
                canDragCard = true;
            }
            isDragging = false;
        }, { passive: true });

        card.addEventListener('touchmove', function(e) {
            if (!canDragCard || !e.touches || !e.touches[0]) return;
            currentY = e.touches[0].clientY;
            const deltaY = currentY - startY;
            const deltaX = e.touches[0].clientX - startX;

            const now = performance.now();
            const dt = Math.max(1, now - lastTime);
            velocityY = (currentY - lastY) / dt;
            lastY = currentY;
            lastTime = now;

            // Only activate downward swipe when dragging downward more than horizontal drift
            if (deltaY > 6 && Math.abs(deltaY) > Math.abs(deltaX)) {
                isDragging = true;
                e.stopPropagation(); // Prevents touch from leaking to background map
                if (list && list.scrollTop > 0) {
                    list.scrollTop = 0;
                }
                const scale = Math.max(0.96, 1 - (deltaY / 2400));
                card.style.transition = 'none';
                card.style.transform = `translate3d(0, ${deltaY}px, 0) scale(${scale})`;
                
                modal.style.transition = 'none';
                modal.style.opacity = Math.max(0.2, 1 - (deltaY / 600));
            }
        }, { passive: false });

        card.addEventListener('touchend', function(e) {
            if (!isDragging) {
                canDragCard = false;
                return;
            }
            isDragging = false;
            canDragCard = false;
            e.stopPropagation();

            const deltaY = currentY - startY;
            // Dismiss if pulled down > 65px OR flicked downward with velocity
            if (deltaY > 65 || velocityY > 0.35) {
                card.style.transition = 'transform 0.26s cubic-bezier(0.25, 1, 0.5, 1)';
                card.style.transform = 'translate3d(0, 100%, 0) scale(0.96)';
                modal.style.transition = 'opacity 0.22s ease-out';
                modal.style.opacity = '0';
                
                setTimeout(() => {
                    closeRideChatModal();
                }, 240);
            } else {
                card.style.transition = 'transform 0.32s cubic-bezier(0.16, 1, 0.3, 1)';
                card.style.transform = 'translate3d(0, 0, 0) scale(1)';
                modal.style.transition = 'opacity 0.28s ease-out';
                modal.style.opacity = '1';
            }
        }, { passive: true });
    })();

    function loadRideMessages(rideId, isInitial = false) {
        if (!rideId) return;
        fetch(`/rides/${rideId}/messages`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const loadingEl = document.getElementById('chat_modal_loading');
            const emptyEl = document.getElementById('chat_modal_empty');
            if (loadingEl) loadingEl.classList.add('hidden');

            if (data && data.messages) {
                if (data.messages.length === 0 && knownMessageIds.size === 0) {
                    if (emptyEl) emptyEl.classList.remove('hidden');
                } else {
                    if (emptyEl) emptyEl.classList.add('hidden');
                    let hasNew = false;
                    data.messages.forEach(msg => {
                        if (!knownMessageIds.has(msg.id)) {
                            renderChatMessage(msg, !isInitial);
                            hasNew = true;
                        }
                    });
                    if (hasNew || isInitial) {
                        scrollChatToBottom(!isInitial);
                    }
                }
            }
        })
        .catch(() => {
            const loadingEl = document.getElementById('chat_modal_loading');
            if (loadingEl) loadingEl.classList.add('hidden');
        });
    }

    window.handleRideChatSubmit = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const input = document.getElementById('rideChatInput');
        const sendBtn = document.getElementById('rideChatSendBtn');
        if (!input || !currentChatRideId || isChatSubmitting) return;

        const text = input.value.trim();
        if (!text) return;

        isChatSubmitting = true;
        if (sendBtn) sendBtn.disabled = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(`/rides/${currentChatRideId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(r => r.json())
        .then(data => {
            isChatSubmitting = false;
            if (sendBtn) sendBtn.disabled = false;
            if (data && data.message) {
                input.value = '';
                renderChatMessage(data.message, true);
                scrollChatToBottom(true);
            }
        })
        .catch(() => {
            isChatSubmitting = false;
            if (sendBtn) sendBtn.disabled = false;
            if (window.createSlidingToast) window.createSlidingToast('Failed to send message. Please retry.', 'danger');
        });
    };

    window.sendQuickPresetChat = function(presetText) {
        const input = document.getElementById('rideChatInput');
        if (input) {
            input.value = presetText;
            window.handleRideChatSubmit();
        }
    };

    function updateUnreadBadges(count) {
        document.querySelectorAll('.srh-chat-unread-badge').forEach(el => {
            if (count > 0) {
                el.textContent = count > 9 ? '9+' : count;
                el.style.display = 'inline-flex';
                el.classList.remove('hidden');
            } else {
                el.style.display = 'none';
                el.classList.add('hidden');
            }
        });
    }

    // Per-ride Reverb subscriptions: each user only ever subscribes to chat events
    // for the rides they are actually part of (their own active ride, or a ride
    // whose chat they open). The old global 'srh-toda-rides' listener leaked every
    // chat message to every open dashboard (toast + unread badge on other drivers'
    // UIs) - that channel is no longer used for chat at all.
    const CHAT_CURRENT_USER_ID = {{ auth()->check() ? auth()->id() : 0 }};
    const _subscribedRideChats = {};

    function handleIncomingChatMessage(e) {
        if (!e || !e.rideId) return;

        // Hard safety net (client-side): never render / toast / badge a message
        // for a ride this user is not part of.
        if (CHAT_CURRENT_USER_ID && e.passengerId && e.driverId
            && e.passengerId !== CHAT_CURRENT_USER_ID && e.driverId !== CHAT_CURRENT_USER_ID) {
            return;
        }

        const modal = document.getElementById('rideChatModal');
        const isModalOpen = modal && modal.style.display !== 'none' && !modal.classList.contains('hidden') && currentChatRideId === e.rideId;

        if (isModalOpen) {
            const currentUserId = CHAT_CURRENT_USER_ID;
            const isMe = (currentUserId !== null && e.senderId === currentUserId);
            renderChatMessage({
                id: e.id,
                ride_id: e.rideId,
                sender_id: e.senderId,
                sender_name: e.senderName,
                sender_avatar: e.senderAvatar,
                sender_role: e.senderRole,
                message: e.message,
                time: e.time,
                created_at: e.createdAt,
                is_me: isMe
            }, true);
            scrollChatToBottom(true);
        } else {
            const currentUserId = CHAT_CURRENT_USER_ID;
            if (currentUserId !== null && e.senderId !== currentUserId) {
                unreadChatCount++;
                updateUnreadBadges(unreadChatCount);
                if (window.createSlidingToast) {
                    window.createSlidingToast(`💬 ${e.senderName}: "${e.message.substring(0, 45)}"`, 'info');
                }
            }
        }
    }

    window.srhSubscribeRideChat = function(rideId) {
        if (!rideId || typeof window.Echo === 'undefined' || !window.Echo) return;
        rideId = parseInt(rideId);
        if (!rideId || _subscribedRideChats[rideId]) return;
        try {
            window.Echo.channel('srh-ride-chat.' + rideId).listen('.chat.message.sent', handleIncomingChatMessage);
            _subscribedRideChats[rideId] = true;
        } catch(e) {}
    };

    // Subscribe to the current user's active ride chat (ride id exposed per page)
    function initReverbChatListener() {
        if (window._srhActiveChatRideId) window.srhSubscribeRideChat(window._srhActiveChatRideId);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReverbChatListener);
    } else {
        initReverbChatListener();
    }
    window.addEventListener('spa:page-loaded', initReverbChatListener);
})();
</script>
