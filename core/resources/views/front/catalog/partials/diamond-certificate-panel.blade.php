{{-- Certificate block for diamond PDPs — uses diamond_attributes lab / URLs / scans --}}
@php
    $d = $item->diamondAttribute;
    $verifyHref = \App\Helpers\CertificationHelper::certificateLink(
        $d->certificate_url ?? null,
        $d->lab ?? null,
        $d->certificate_number ?? null,
    );
    $scanImg = \App\Helpers\CertificationHelper::storageImageUrl($d->certificate_report_image ?? null);
    $scanPdfRel = trim((string) ($d->certificate_report_pdf ?? ''));
    $scanPdf = $scanPdfRel !== ''
        ? (str_starts_with($scanPdfRel, 'http')
            ? $scanPdfRel
            : url('/core/public/storage/images/' . ltrim($scanPdfRel, '/')))
        : '';
@endphp
<div class="diamond-cert-panel border p-3 mb-3 bg-white" style="border-color:#D4C5A9!important;">
    <h3 class="h6 luxury-headline mb-2">{{ __('Certificate & authenticity') }}</h3>
    <div class="row g-3 align-items-center">
        @if ($scanImg)
            <div class="col-sm-5 col-lg-4 text-center">
                @if ($verifyHref)
                    <a href="{{ $verifyHref }}" target="_blank" rel="noopener noreferrer" class="d-block">
                        <img src="{{ $scanImg }}" alt="{{ __('Grading certificate') }}" class="img-fluid border" loading="lazy" style="max-height:220px;object-fit:contain;">
                        <span class="small text-muted d-block mt-1">{{ __('Open lab verification') }}</span>
                    </a>
                @else
                    <img src="{{ $scanImg }}" alt="{{ __('Grading certificate') }}" class="img-fluid border" loading="lazy" style="max-height:220px;object-fit:contain;">
                    <span class="small text-muted d-block mt-1">{{ __('Lab scan / report image') }}</span>
                @endif
            </div>
            <div class="col-sm-7 col-lg-8">
        @else
            <div class="col-12">
        @endif
            @if ($d->lab)
                <p class="mb-1 small"><strong>{{ __('Laboratory') }}:</strong> {{ $d->lab }}</p>
            @endif
            @if ($d->certificate_number)
                <p class="mb-1 small"><strong>{{ __('Certificate number') }}:</strong>
                    @if ($verifyHref)
                        <a href="{{ $verifyHref }}" target="_blank" rel="noopener noreferrer">{{ $d->certificate_number }}</a>
                    @else
                        {{ $d->certificate_number }}
                    @endif
                </p>
            @endif
            @if ($scanPdf)
                <p class="mb-1"><a href="{{ $scanPdf }}" class="small" target="_blank" rel="noopener">{{ __('Download certificate PDF') }}</a></p>
            @endif
            @if (!$verifyHref && ! $d->certificate_number)
                <p class="small text-muted mb-0">{{ __('Certificate verification link will appear when lab reference data is supplied for this SKU.') }}</p>
            @endif
            @if (($d->is_lab_grown ?? false))
                <p class="small text-muted mb-0 mt-2">{{ __('Laboratory-grown diamond — chemically identical crystal with disclosed origin.') }}</p>
            @else
                <p class="small mb-0 mt-2">{{ __("Monte Luca sources natural diamonds documented with independent grading reports. We support the Kimberley Process and reject conflict stones; each listing discloses laboratory and verification where available.") }}</p>
            @endif
        </div>
    </div>
    <div class="mt-2 pt-2 border-top" style="border-color:#ece8e0!important;">
        <a href="{{ route('education.guides.show', ['slug' => 'certification-explained']) }}" class="small">{{ __('How certificates protect you') }} &rsaquo;</a>
    </div>
</div>
