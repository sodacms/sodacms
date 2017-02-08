@extends(soda_cms_view_path('layouts.app'))

@section('main-content')
	<div class="row">
		@include(soda_cms_view_path('layouts.partials.sidebar'))
		<div class="col-xs-offset-2 col-xs-10 main-content">
			@yield('breadcrumb')
			<div class="container-fluid">
				@section('main-content-outer')
				<div class="row">
					<div class="col-xs-12">
						@yield('content-heading')
					</div>
				</div>
                @include(soda_cms_view_path('layouts.partials.alert'))
				<div class="row">
					<div class="col-xs-12">
						<div class="main-content-inner">
							@yield('content')
						</div>
					</div>
				</div>
				@show
			</div>
		</div>
	</div>
@endsection