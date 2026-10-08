@props([
    'title' => '',
    'subtitle' => null,
    'iconClass' => null,
])

<div class="flex items-center justify-between min-w-0" style="display: flex; align-items: center; justify-content: space-between; min-width: 0;">
    <h1 class="font-black text-xl sm:text-2xl text-gray-900 tracking-tight" style="font-weight: 900; font-size: 1.5rem; color: #0f172a; letter-spacing: -0.03em; margin: 0;">{!! $title !!}</h1>
    @isset($right)
        <div class="flex items-center gap-2 shrink-0" style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
            {{ $right }}
        </div>
    @endisset
</div>
