<div class="d-flex text-center justify-content-center">
    @foreach($available_locales as $locale_name => $available_locale)
        @if($available_locale != $current_locale)
            <a class="ml-1 underline ml-2 mr-2" href="language/{{ $available_locale }}">
                <span>{{ $locale_name }}</span>
            </a>
        @endif
    @endforeach
</div>