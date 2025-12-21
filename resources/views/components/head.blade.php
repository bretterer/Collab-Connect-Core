@cookieAllowed('analytics')
    {{-- Analytics scripts go here --}}
    @if(!app()->environment('local'))
        <script
            defer
            data-website-id="68953b233e0aad41246ad8b4"
            data-domain="collabconnect.app"
            src="https://datafa.st/js/script.js">
        </script>
    @endif
@endCookieAllowed

@cookieAllowed('marketing')
    <x-metapixel-head :userIdAsString="true"/>
@endCookieAllowed