@section('head.main')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
@stop

@section('head.title')
    <title>Soda CMS</title>
@stop

@section('head.meta')
    <link rel="shortcut icon" type="image/ico" href="/soda/cms/img/favicon.ico">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('head.css')
    <!-- Styles -->
    <link href="/soda/cms/css/application.css" rel="stylesheet">
@stop

@section('head.js')
    <!-- JavaScripts -->
    <script>
        WebFontConfig = {
            google: {
                families: ['Lato:400,700,i400:latin']
            }
        };

        (function(d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js';
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>
    <script src="/soda/cms/js/core.js"></script>
    <script src="/soda/cms/js/application.js"></script>
    <script>
        (function (Soda) {
            Soda.queryString = {!! json_encode(Request::query(), JSON_HEX_TAG) !!}
            Soda.urls = {
                sort: '{{ route('soda.sort') }}',
            };
            return Soda;
        })(Soda || {});
    </script>
@stop

@section('head.extra')
@stop

@section('footer.js')
    <script src="/soda/cms/js/forms/slugs.js"></script>
    <script src="/soda/cms/js/forms/dates.js"></script>
    <script src="/soda/cms/js/forms/tinymce.js"></script>
    <script src="/soda/cms/js/forms/upload.js"></script>
    <script src="/soda/cms/js/forms/json.js"></script>
    <script src="/soda/cms/js/forms/multiselect.js"></script>
@stop

@section('head')
    @yield('head.main')
    @yield('head.title')
    @yield('head.meta')
    @yield('head.css')
    @yield('head.js')
    @yield('head.extra')
@show