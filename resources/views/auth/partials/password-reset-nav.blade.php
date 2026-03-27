{{--
  $returnUrl — primary: login for this flow
  $secondaryHref / $secondaryLabel — optional (e.g. different email, re-enter code)
--}}
@php
    $secondaryHref = $secondaryHref ?? null;
    $secondaryLabel = $secondaryLabel ?? null;
@endphp
<div class="mt-8 border-t border-slate-700/60 pt-6 space-y-3">
    <a href="{{ $returnUrl }}" class="block text-center text-sm text-slate-400 hover:text-slate-200 transition">
        Back to login
    </a>
    @if($secondaryHref && $secondaryLabel)
        <a href="{{ $secondaryHref }}" class="block text-center text-sm text-slate-500 hover:text-emerald-400 transition">
            {{ $secondaryLabel }}
        </a>
    @endif
</div>
