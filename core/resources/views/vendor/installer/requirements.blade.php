@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.requirements.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-list-ul fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.requirements.title') }}
@endsection

@section('container')

    @foreach ($requirements['requirements'] as $type => $requirement)
        <ul class="list mb-2">
            <li class="list__item list__title {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">
                <strong>{{ ucfirst($type) }}</strong>
                @if ($type == 'php')
                    <strong>
                        <small>
                            (version {{ $phpSupportInfo['minimum'] }} required)
                        </small>
                    </strong>
                    <span class="float-right">
                        <strong>
                            {{ $phpSupportInfo['current'] }}
                        </strong>
                        <i class="fa fa-fw fa-{{ $phpSupportInfo['supported'] ? 'check-circle-o' : 'exclamation-circle' }} row-icon"
                            aria-hidden="true"></i>
                    </span>
                @endif
            </li>



            @foreach ($requirements['requirements'][$type] as $extention => $enabled)
                <li class="list__item {{ $enabled ? 'success' : 'error' }}">
                    {{ $extention }}
                    <i class="fa fa-fw fa-{{ $enabled ? 'check-circle-o' : 'exclamation-circle' }} row-icon"
                        aria-hidden="true"></i>
                </li>
            @endforeach
        </ul>
    @endforeach

    @php
        function isFunctionDisabled($funcName)
        {
            $disabled = explode(',', ini_get('disable_functions'));
            return in_array($funcName, array_map('trim', $disabled));
        }
    @endphp

    <div>
        <ul class="list">
            <li class="list__item list__title {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">
                <strong>Symlink</strong> 
                {{-- link icon for article redirect --}}
                <a href="https://www.hostens.com/knowledgebase/how-to-enable-php-disabled_functions-on-cpanel/?utm_source=chatgpt.com" target="_blank">
                    <span>
                        (click to read more)
                    </span>
                    <i class="fa fa-fw fa-external-link row-icon"
                    aria-hidden="true"></i>
                </a>
            </li>
            <li class="list__item {{ $enabled ? 'success' : 'error' }}">
                @if (isFunctionDisabled('link') || isFunctionDisabled('symlink'))
                    'link' or 'symlink' is disabled.
                    <i class="fa fa-fw fa-exclamation-circle row-icon"
                    aria-hidden="true"></i>
                @else
                    Symlink functions are available.
                    <i class="fa fa-fw fa-check-circle-o row-icon"
                    aria-hidden="true"></i>
                @endif
            </li>
        </ul>
    </div>

    @if ((!isset($requirements['errors']) && $phpSupportInfo['supported']) && !(isFunctionDisabled('link') || isFunctionDisabled('symlink')))
        <div class="buttons">
            <a class="button" href="{{ route('LaravelInstaller::permissions') }}">
                {{ trans('installer_messages.requirements.next') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </a>
        </div>
    @endif

@endsection
