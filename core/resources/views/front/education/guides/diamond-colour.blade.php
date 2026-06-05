<div class="edu-pillar-body mt-4">
    <p class="edu-lead">Colour grading measures body colour on the D→Z continuum; D–F read icy, G–J can still photograph white if cut is commanding.</p>
    <h3 class="edu-subheading">{{ __('Tone strip (conceptual)') }}</h3>
    <div class="edu-colour-strip d-flex rounded overflow-hidden border" style="height:48px;">
        @foreach(range(1,14) as $i)
            <span class="flex-fill" style="background: hsl(48, {{ min(96, 55 + $i * 6) }}%, {{ max(73, 100 - $i * 5) }}%);"></span>
        @endforeach
    </div>
    <ul class="edu-list mt-4">
        <li>{{ __('Yellow gold mountings camouflage faint warmth; platinum demands higher colour tiers for the same “white” subjective read.') }}</li>
        <li>{{ __('Hue vs saturation: fluorescence can cool I–K face-up daylight while leaving the grade unchanged.') }}</li>
        <li>{{ __('Always compare melee side stones when ordering halos—they should not out-whiten the hero diamond.') }}</li>
    </ul>
</div>
