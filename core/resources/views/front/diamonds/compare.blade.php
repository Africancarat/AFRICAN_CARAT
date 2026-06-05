@extends('master.front')

@section('title', __('Diamond compare'))

@section('content')
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                        <li class="separator"></li>
                        <li><a href="{{ route('diamonds.index') }}">{{ __('Diamonds') }}</a></li>
                        <li class="separator"></li>
                        <li>{{ __('Compare') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container padding-bottom-3x mb-3">
        @if (session('info'))
            <div class="alert alert-light border mb-3" role="alert">{{ session('info') }}</div>
        @endif
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1 class="h4 mb-0">{{ __('Compare diamonds') }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('diamonds.index') }}" class="btn btn-sm btn-outline-dark">{{ __('Back to search') }}</a>
                @if($items->isNotEmpty())
                    <form action="{{ route('diamonds.compare.clear') }}" method="post" class="d-inline"
                          onsubmit="return confirm(@json(__('Clear all from compare?')));">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('Clear') }}</button>
                    </form>
                @endif
            </div>
        </div>

        @if($items->isEmpty())
            <p class="text-muted">{{ __('Add up to four diamonds from the diamond search grid using the Compare button.') }}</p>
        @else
            <div class="table-responsive diamond-compare-table-wrap">
                <table class="table table-sm table-bordered align-middle diamond-compare-table">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            @foreach($items as $item)
                                <th scope="col" class="text-center diamond-compare-col">
                                    <a href="{{ route('front.product', $item->slug) }}" class="d-block small">{{ $item->name }}</a>
                                    <form action="{{ route('diamonds.compare.remove', $item->id) }}" method="get" class="mt-1">
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-danger">{{ __('Remove') }}</button>
                                    </form>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ __('Image') }}</th>
                            @foreach($items as $item)
                                @php $img = \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo); @endphp
                                <td class="text-center">
                                    <a href="{{ route('front.product', $item->slug) }}"><img src="{{ $img }}" class="diamond-compare-thumb" alt=""></a>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('Price') }}</th>
                            @foreach($items as $item)
                                <td class="text-center">{{ \App\Helpers\PriceHelper::grandCurrencyPrice($item) }}</td>
                            @endforeach
                        </tr>
                        @foreach([
                            'shape' => __('Shape'),
                            'carat_weight' => __('Carat'),
                            'cut_grade' => __('Cut'),
                            'color_grade' => __('Colour'),
                            'clarity_grade' => __('Clarity'),
                            'table_pct' => __('Table %'),
                            'depth_pct' => __('Depth %'),
                            'fluorescence' => __('Fluorescence'),
                            'polish' => __('Polish'),
                            'symmetry' => __('Symmetry'),
                            'lab' => __('Lab'),
                            'certificate_number' => __('Report #'),
                        ] as $field => $label)
                            <tr>
                                <th>{{ $label }}</th>
                                @foreach($items as $item)
                                    @php $da = $item->diamondAttribute; @endphp
                                    @php
                                        $raw = $da ? ($da->{$field} ?? null) : null;
                                        $val = is_array($raw) ? implode(', ', $raw) : $raw;
                                    @endphp
                                    <td class="text-center">{{ $val !== null && $val !== '' ? $val : '—' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <th>{{ __('Certificate') }}</th>
                            @foreach($items as $item)
                                @php $da = $item->diamondAttribute; @endphp
                                <td class="text-center">
                                    @if($da && $da->certificate_url)
                                        <a href="{{ $da->certificate_url }}" target="_blank" rel="noopener">{{ __('Open report') }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('Origin') }}</th>
                            @foreach($items as $item)
                                @php $da = $item->diamondAttribute; @endphp
                                <td class="text-center">{{ $da && $da->is_lab_grown ? __('Lab-grown') : __('Natural') }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
