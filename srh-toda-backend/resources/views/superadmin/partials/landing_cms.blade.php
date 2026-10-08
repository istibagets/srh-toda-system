<div class="hint-box" style="margin-bottom: 1.25rem;">
    Manage all public landing page text, FAQs, TODA membership guidelines, and terminal dispatch info. Changes take effect on the landing page immediately upon saving.
</div>

<form method="POST" action="{{ route('superadmin.landing-cms.update') }}">
    @csrf

    <!-- HERO SECTION -->
    <div class="card">
        <h2>Hero & Portal Banner</h2>
        <p class="card-sub">Top headline, badges, and primary call-to-actions displayed to incoming visitors.</p>

        <div class="field">
            <label for="hero_badge">Badge / Pill Text</label>
            <input id="hero_badge" type="text" name="hero_badge" value="{{ old('hero_badge', $landing['hero_badge'] ?? '') }}" maxlength="100">
            <div class="hint">Small pill badge above the main title (e.g. Official Community Transport Portal).</div>
        </div>

        <div class="field">
            <label for="hero_title">Hero Headline</label>
            <input id="hero_title" type="text" name="hero_title" value="{{ old('hero_title', $landing['hero_title'] ?? '') }}" required maxlength="255">
        </div>

        <div class="field">
            <label for="hero_subtitle">Hero Subtitle / Description</label>
            <textarea id="hero_subtitle" name="hero_subtitle" rows="3" required maxlength="500">{{ old('hero_subtitle', $landing['hero_subtitle'] ?? '') }}</textarea>
        </div>

        <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div class="field" style="margin-bottom:0;">
                <label for="hero_cta_primary">Primary Button Text (Passenger)</label>
                <input id="hero_cta_primary" type="text" name="hero_cta_primary" value="{{ old('hero_cta_primary', $landing['hero_cta_primary'] ?? 'Book a Ride') }}" maxlength="50">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="hero_cta_secondary">Secondary Button Text (Driver)</label>
                <input id="hero_cta_secondary" type="text" name="hero_cta_secondary" value="{{ old('hero_cta_secondary', $landing['hero_cta_secondary'] ?? 'Apply as Driver') }}" maxlength="50">
            </div>
        </div>
    </div>

    <!-- ABOUT SECTION -->
    <div class="card">
        <h2>About SRH LINK-TODA & Service Coverage</h2>
        <p class="card-sub">Association background, mission statement, and community cooperation overview.</p>

        <div class="field">
            <label for="about_title">Section Title</label>
            <input id="about_title" type="text" name="about_title" value="{{ old('about_title', $landing['about_title'] ?? '') }}" maxlength="255">
        </div>

        <div class="field">
            <label for="about_text">About Description & Mission</label>
            <textarea id="about_text" name="about_text" rows="4" maxlength="1000">{{ old('about_text', $landing['about_text'] ?? '') }}</textarea>
        </div>
    </div>

    <!-- MEMBERSHIP GUIDE -->
    <div class="card">
        <h2>How to Become a TODA Member / Driver</h2>
        <p class="card-sub">Official 4-step checklist and verification requirements for prospective drivers.</p>

        <div class="field">
            <label for="membership_intro">Membership Introduction</label>
            <input id="membership_intro" type="text" name="membership_intro" value="{{ old('membership_intro', $landing['membership_intro'] ?? '') }}" maxlength="500">
        </div>

        <!-- Step 1 -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1rem; margin-bottom:1rem;">
            <strong style="display:block; font-size:0.82rem; color:#0f172a; margin-bottom:0.5rem;">Step 1 — License</strong>
            <div class="field" style="margin-bottom:0.5rem;">
                <label for="membership_step1_title">Title</label>
                <input id="membership_step1_title" type="text" name="membership_step1_title" value="{{ old('membership_step1_title', $landing['membership_step1_title'] ?? '') }}" maxlength="150">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="membership_step1_desc">Description</label>
                <input id="membership_step1_desc" type="text" name="membership_step1_desc" value="{{ old('membership_step1_desc', $landing['membership_step1_desc'] ?? '') }}" maxlength="300">
            </div>
        </div>

        <!-- Step 2 -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1rem; margin-bottom:1rem;">
            <strong style="display:block; font-size:0.82rem; color:#0f172a; margin-bottom:0.5rem;">Step 2 — Clearances</strong>
            <div class="field" style="margin-bottom:0.5rem;">
                <label for="membership_step2_title">Title</label>
                <input id="membership_step2_title" type="text" name="membership_step2_title" value="{{ old('membership_step2_title', $landing['membership_step2_title'] ?? '') }}" maxlength="150">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="membership_step2_desc">Description</label>
                <input id="membership_step2_desc" type="text" name="membership_step2_desc" value="{{ old('membership_step2_desc', $landing['membership_step2_desc'] ?? '') }}" maxlength="300">
            </div>
        </div>

        <!-- Step 3 -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1rem; margin-bottom:1rem;">
            <strong style="display:block; font-size:0.82rem; color:#0f172a; margin-bottom:0.5rem;">Step 3 — MTOP & Franchise</strong>
            <div class="field" style="margin-bottom:0.5rem;">
                <label for="membership_step3_title">Title</label>
                <input id="membership_step3_title" type="text" name="membership_step3_title" value="{{ old('membership_step3_title', $landing['membership_step3_title'] ?? '') }}" maxlength="150">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="membership_step3_desc">Description</label>
                <input id="membership_step3_desc" type="text" name="membership_step3_desc" value="{{ old('membership_step3_desc', $landing['membership_step3_desc'] ?? '') }}" maxlength="300">
            </div>
        </div>

        <!-- Step 4 -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1rem; margin-bottom:0.5rem;">
            <strong style="display:block; font-size:0.82rem; color:#0f172a; margin-bottom:0.5rem;">Step 4 — Inspection & Terminal Access</strong>
            <div class="field" style="margin-bottom:0.5rem;">
                <label for="membership_step4_title">Title</label>
                <input id="membership_step4_title" type="text" name="membership_step4_title" value="{{ old('membership_step4_title', $landing['membership_step4_title'] ?? '') }}" maxlength="150">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="membership_step4_desc">Description</label>
                <input id="membership_step4_desc" type="text" name="membership_step4_desc" value="{{ old('membership_step4_desc', $landing['membership_step4_desc'] ?? '') }}" maxlength="300">
            </div>
        </div>
    </div>

    <!-- TODA ACTIVITIES -->
    <div class="card">
        <h2>TODA Community Activities & Welfare</h2>
        <p class="card-sub">Information on association workshops, safety programs, clean-up drives, and driver welfare.</p>

        <div class="field">
            <label for="activities_text">Activities Summary</label>
            <textarea id="activities_text" name="activities_text" rows="4" maxlength="1000">{{ old('activities_text', $landing['activities_text'] ?? '') }}</textarea>
        </div>
    </div>

    <!-- FAQS MANAGER -->
    <div class="card">
        <h2>Frequently Asked Questions (FAQs)</h2>
        <p class="card-sub">Questions and answers displayed in the interactive landing page accordion.</p>

        <div id="faqs-container">
            @php $faqList = old('faqs', $landing['faqs'] ?? []); @endphp
            @foreach ($faqList as $idx => $faq)
                <div class="faq-item" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1rem; margin-bottom:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <strong style="font-size:0.8rem; color:#0f172a;">Question #{{ $loop->iteration }}</strong>
                    </div>
                    <div class="field" style="margin-bottom:0.5rem;">
                        <label for="faq_q_{{ $idx }}">Question</label>
                        <input id="faq_q_{{ $idx }}" type="text" name="faqs[{{ $idx }}][question]" value="{{ $faq['question'] ?? '' }}" required maxlength="255">
                    </div>
                    <div class="field" style="margin-bottom:0;">
                        <label for="faq_a_{{ $idx }}">Answer</label>
                        <textarea id="faq_a_{{ $idx }}" name="faqs[{{ $idx }}][answer]" rows="2" required maxlength="1000">{{ $faq['answer'] ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TERMINAL LOCATION & CONTACT -->
    <div class="card">
        <h2>Terminal Location & Dispatch Contacts</h2>
        <p class="card-sub">Operating hours, station location, and emergency hotline numbers.</p>

        <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div class="field" style="margin-bottom:0;">
                <label for="terminal_location">Terminal Station Address</label>
                <input id="terminal_location" type="text" name="terminal_location" value="{{ old('terminal_location', $landing['terminal_location'] ?? '') }}" maxlength="255">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="terminal_hours">Operating Hours</label>
                <input id="terminal_hours" type="text" name="terminal_hours" value="{{ old('terminal_hours', $landing['terminal_hours'] ?? '') }}" maxlength="150">
            </div>
        </div>

        <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div class="field" style="margin-bottom:0;">
                <label for="dispatch_hotline">Dispatch Hotline Phone</label>
                <input id="dispatch_hotline" type="text" name="dispatch_hotline" value="{{ old('dispatch_hotline', $landing['dispatch_hotline'] ?? '') }}" maxlength="100">
            </div>
            <div class="field" style="margin-bottom:0;">
                <label for="dispatch_email">Contact Email</label>
                <input id="dispatch_email" type="email" name="dispatch_email" value="{{ old('dispatch_email', $landing['dispatch_email'] ?? '') }}" maxlength="150">
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; margin-bottom: 2rem;">
        <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.6rem; font-size: 0.88rem;">
            Save Landing Page Content
        </button>
    </div>
</form>
