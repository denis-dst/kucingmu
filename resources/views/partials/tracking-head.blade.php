@php
    $gaId = trim($app_settings['google_analytics_id'] ?? '');
    $gtmId = trim($app_settings['google_tag_manager_id'] ?? '');
    $gtmHead = trim($app_settings['google_tag_manager_head'] ?? '');
    $gaScript = trim($app_settings['google_analytics_script'] ?? '');
    $customHead = trim($app_settings['custom_head_scripts'] ?? '');
@endphp

{{-- Google Tag Manager (<head>) --}}
@if(!empty($gtmHead))
    {!! $gtmHead !!}
@elseif(!empty($gtmId))
    <!-- Google Tag Manager -->
    <script data-cfasync="false">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    <!-- End Google Tag Manager -->
@endif

{{-- Google Analytics 4 (gtag.js) --}}
@if(!empty($gaScript))
    {!! $gaScript !!}
@elseif(!empty($gaId))
    <!-- Google tag (gtag.js) -->
    <script data-cfasync="false" async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script data-cfasync="false">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
@endif

{{-- Custom Head Tracking Scripts --}}
@if(!empty($customHead))
    {!! $customHead !!}
@endif
