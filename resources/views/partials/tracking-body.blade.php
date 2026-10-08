@php
    $gtmId = trim($app_settings['google_tag_manager_id'] ?? '');
    $gtmBody = trim($app_settings['google_tag_manager_body'] ?? '');
    $customBody = trim($app_settings['custom_body_scripts'] ?? '');
@endphp

{{-- Google Tag Manager (noscript for <body>) --}}
@if(!empty($gtmBody))
    {!! $gtmBody !!}
@elseif(!empty($gtmId))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif

{{-- Custom Body Tracking Scripts --}}
@if(!empty($customBody))
    {!! $customBody !!}
@endif
