@extends('master.front')

@section('title', 'Interactive 4Cs Explorer')

@section('meta')
    <meta name="description" content="{{ __('Explore how diamond cut, colour, clarity, and carat change what you perceive—side by side.') }}">
@endsection

@section('styleplugins')
<style>
.edu-tool-stone{width:clamp(132px,32vw,200px);height:clamp(132px,32vw,200px);border-radius:50%;margin-inline:auto;background:radial-gradient(circle at 35% 30%,rgba(255,255,255,.95),rgba(237,239,246,.92)42%,rgba(210,218,238,.92)62%,rgba(190,203,229,.94));box-shadow:inset -12px -20px 40px rgba(40,54,94,.08),inset 6px 10px 18px rgba(255,255,255,.72),0 8px 32px rgba(0,0,0,.12);position:relative;transition:filter .35s ease,background .35s ease,transform .35s ease;}
.edu-tool-stone::before{content:'';position:absolute;inset:12%;border-radius:50%;background:linear-gradient(120deg,rgba(255,255,255,.4),transparent 55%);opacity:.85;mix-blend-mode:screen;}
.edu-tool-vs{perspective:800px;}
</style>
@endsection

@section('content')
<div class="page-title">
    <div class="container">
        <ul class="breadcrumbs">
            <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
            <li class="separator"></li>
            <li><a href="{{ route('education.index') }}">{{ __('Education') }}</a></li>
            <li class="separator"></li>
            <li>{{ __('4Cs explorer') }}</li>
        </ul>
    </div>
</div>

<div class="container py-5 mb-5 edu-tool-4cs">
    <h1 class="luxury-headline mb-3">{{ __('Interactive 4Cs explorer') }}</h1>
    <p class="text-muted mb-4">{{ __('Slide each control—the pair of gemstones below reacts with illustrative styling so contrasts are obvious. This is pedagogy—not a grading engine.') }}</p>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="d-flex gap-5 flex-wrap justify-content-around edu-tool-vs">
                <div class="text-center edu-tool-stage">
                    <div class="edu-tool-stone" id="eduStoneA" aria-hidden="true"></div>
                    <p class="small text-uppercase letter-spacing mt-2 mb-0">{{ __('Diamond') }} A</p>
                </div>
                <div class="text-center edu-tool-stage">
                    <div class="edu-tool-stone" id="eduStoneB" aria-hidden="true"></div>
                    <p class="small text-uppercase letter-spacing mt-2 mb-0">{{ __('Diamond') }} B</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mb-4">
                <label class="filter-label d-block mb-1">{{ __('Cut — brightness vs contrast') }}</label>
                <input type="range" min="35" max="100" value="88" id="rngCutA" class="form-range">
                <input type="range" min="35" max="100" value="62" id="rngCutB" class="form-range mt-1">
                <small class="text-muted">A / B</small>
            </div>
            <div class="mb-4">
                <label class="filter-label d-block mb-1">{{ __('Colour — illustrative warmth tint') }}</label>
                <input type="range" min="0" max="75" value="15" id="rngColourA" class="form-range">
                <input type="range" min="0" max="75" value="52" id="rngColourB" class="form-range mt-1">
            </div>
            <div class="mb-4">
                <label class="filter-label d-block mb-1">{{ __('Clarity — visible “noise” halo') }}</label>
                <input type="range" min="5" max="90" value="18" id="rngClarityA" class="form-range">
                <input type="range" min="5" max="90" value="74" id="rngClarityB" class="form-range mt-1">
            </div>
            <div class="mb-2">
                <label class="filter-label d-block mb-1">{{ __('Carat spread — illustrative scale') }}</label>
                <input type="range" min="40" max="120" value="92" id="rngCaratA" class="form-range">
                <input type="range" min="40" max="120" value="74" id="rngCaratB" class="form-range mt-1">
            </div>
        </div>
    </div>
    <a href="{{ route('diamonds.index') }}" class="btn-luxury btn-luxury-gold">{{ __('Search graded diamonds') }}</a>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
  function bind(id,fn){var el=document.getElementById(id);if(el){el.addEventListener('input',fn);el.addEventListener('change',fn);}}
  function apply(){
    [['eduStoneA','rngCutA','rngColourA','rngClarityA','rngCaratA'],
     ['eduStoneB','rngCutB','rngColourB','rngClarityB','rngCaratB']].forEach(function(parts){
       var el=document.getElementById(parts[0]);
       if(!el)return;
       var cut=parseFloat(document.getElementById(parts[1]).value,10)/100;
       var col=parseFloat(document.getElementById(parts[2]).value,10)/100;
       var cla=parseFloat(document.getElementById(parts[3]).value,10)/100;
       var car=parseFloat(document.getElementById(parts[4]).value,10)/100;
       el.style.transform='scale('+(0.75+car*.35)+')';
       el.style.filter='brightness('+(.65+cut*.45)+') contrast('+(.85+cut*.4)+') saturate('+(.45+(.75-col)*.85)+')';
       var tint='rgba('+(186+Math.round(col*55))+','+(198+Math.round(col*42))+','+(226+Math.round(col*26))+',1)';
       el.style.background='radial-gradient(circle at 35% 30%,rgba(255,255,255,.95),rgba(237,239,246,.94)42%,'+tint+' 92%)';
       el.style.boxShadow=['inset 0 0 0 '+cla*12+'px rgba(90,106,148,'+(cla*.085).toFixed(3)+')','inset -12px -20px 40px rgba(40,54,94,.06)','inset 6px 10px 18px rgba(255,255,255,.65)','0 8px 32px rgba(0,0,0,.12)'].join(',');
     });
  }
  ['rngCutA','rngCutB','rngColourA','rngColourB','rngClarityA','rngClarityB','rngCaratA','rngCaratB'].forEach(function(id){bind(id,apply);});
  apply();
});
</script>
@endsection
