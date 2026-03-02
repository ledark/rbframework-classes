@php
$title = $title ?? '';
$icon = $icon ?? 'fas fa-tachometer-alt';
$url = $url ?? '';

if(strpos($url, '{httpSite}') !== false){
    $url = str_replace('{httpSite}', '', $url);
}

@endphp

    

    <a class="nav-link" href="{httpSite}/{{$url}}">
        <div class="sb-nav-link-icon"><i class="{{$icon}}"></i></div>
        {{$title}}
    </a>
