{{--
  $returnUrl — primary: login for this flow
  $secondaryHref / $secondaryLabel — optional (e.g. different email, re-enter code)
--}}
@php
    $secondaryHref = $secondaryHref ?? null;
    $secondaryLabel = $secondaryLabel ?? null;
@endphp
<div class="mt-8 border-t border-slate-200 pt-6 space-y-3">
    <a href="{{ $returnUrl }}" class="block text-center text-sm text-slate-500 hover:text-slate-800 transition">
        Back to login
    </a>
    @if($secondaryHref && $secondaryLabel)
        <a href="{{ $secondaryHref }}" class="rr-link-accent block text-center text-sm font-medium transition">
            {{ $secondaryLabel }}
        </a>
    @endif
</div>
