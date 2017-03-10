@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <li><a href="{{ route('soda.home') }}">Home</a></li>
    <li class="active">Roles</li>
@stop

@section('head.title')
    <title>Roles :: Soda CMS</title>
@endsection

@section('content-heading-button')
    @include(soda_cms_view_path('partials.buttons.create'), ['url' => route('soda.roles.create')])
@stop

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'mdi mdi-account-card-details',
    'title'       => 'Roles',
])

@section('content')
    <div class="content-top">
        {!! $filter !!}
    </div>

    <div class="content-block">
        {!! $grid !!}
    </div>
@endsection
