@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
	<ol class="breadcrumb">
		<li><a href="{{ route('soda.home') }}">Home</a></li>
		<li class="active">Pages</li>
	</ol>
@stop

@section('head.title')
	<title>Soda CMS | Pages</title>
@endsection

@section('content-heading-button')
    @permission('create-pages')
	    @include(soda_cms_view_path('partials.buttons.create'), ['modal' => '#page_type_modal'])
	@endpermission
@stop

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-file-o',
    'title'       => 'Pages',
])

@section('content')
	{!! $tree !!}

    @permission('create-pages')
	<div class="modal fade" id="page_type_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Select a page type..</h4>
				</div>
				<form method="GET" action="{{route('soda.'.$hint.'.create')}}">
					<div class="modal-body">
						<fieldset class="form-group field_page_type page_type  dropdown-field">
							<label for="field_page_type">Page Type</label>

							<select name="page_type_id" class="form-control" id="page_type_id">
							</select>
						</fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button  class="btn btn-primary" >Create Page</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
	    var pageTypes = {!! json_encode($page_types->pluck('name', 'id')->prepend('None', 0), JSON_FORCE_OBJECT) !!};
        var allowedSubpageTypes = {!! json_encode($page_types->keyBy('id')->transform(function($item) {
            return $item->subpageTypes->pluck('id')->toArray();
        })) !!};

		$('[data-tree-add]').on('click', function(e){
			e.preventDefault();
			var modal = $('#page_type_modal');
			var action = $(this).attr('href');
			var pageType = $(this).data('tree-add');

			var allowedSubpages = getAllowedSubpages(pageType);
			var pageTypeSelector = $('select#page_type_id');

            pageTypeSelector.empty();
            $.each(allowedSubpages, function(id, name){
                pageTypeSelector.append('<option value="' + id + '">' + name + '</option>');
            });

			$('form', modal).attr('action', action);
			modal.modal('show');
		});

		function getAllowedSubpages(pageTypeId)
        {
            var allowedSubpages = allowedSubpageTypes[pageTypeId];
            if(allowedSubpages && allowedSubpages.length) {
                return filterByKeys(pageTypes, allowedSubpages);
            }

            return pageTypes;
        }

        // Avoid issues with `{hasOwnProperty: 5}`
        var hasOwnProperty = ({}).hasOwnProperty;
        function filterByKeys(obj, keep) {
            var result = {};
            for (var i = 0, len = keep.length; i < len; i++) {
                var key = keep[i];
                if (hasOwnProperty.call(obj, key)) {
                    result[key] = obj[key];
                }
            }

            return result;
        };
	</script>
	@endpermission
@endsection
