@extends('master.front')

@section('title', $meta['title'])

@section('meta')
    <meta name="description" content="{{ Str::limit($meta['description'], 160) }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ Str::limit($meta['description'], 160) }}">
@endsection

@section('content')
<div class="page-title">
    <div class="container">
        <ul class="breadcrumbs">
            <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
            <li class="separator"></li>
            <li><a href="{{ route('education.index') }}">{{ __('Education') }}</a></li>
            <li class="separator"></li>
            <li>{{ Str::limit($meta['title'], 55) }}</li>
        </ul>
    </div>
</div>

<div class="container py-4 edu-pillar-shell mb-5">
    <p class="text-uppercase small text-muted mb-2" style="letter-spacing:0.12em;">{{ $meta['tag'] }}</p>
    <h1 class="luxury-headline">{{ $meta['title'] }}</h1>
    <p class="text-muted edu-pillar-summary">{{ $meta['description'] }}</p>
    @include('front.education.guides.' . $slug)
    <div class="mt-5 pt-4 border-top edu-pillar-actions" style="border-color:#ece8e0;">
        <a href="{{ route('diamonds.index') }}" class="btn-luxury btn-luxury-gold">{{ __('Search certified diamonds') }}</a>
        <a href="{{ route('education.tool.4cs') }}" class="btn btn-outline-dark btn-sm ms-2 mt-2 mt-sm-0">{{ __('Interactive 4Cs explorer') }}</a>
        <a href="{{ route('education.index') }}" class="btn btn-link btn-sm ms-sm-2 mt-2 mt-sm-0">{{ __('Education hub') }}</a>
    </div>
</div>
@endsection
