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
	@include(soda_cms_view_path('partials.buttons.create'), ['modal' => '#pageTypeModal'])
@stop

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'fa fa-file-o',
    'title'       => 'Pages',
])

@section('content')
	@include(soda_cms_view_path('pages.tree.root'), ['tree' => $pages])

	<div class="modal fade" id="pageTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Select a page type..</h4>
				</div>
				<form method="GET" action="{{ route('soda.pages.create') }}">
					<div class="modal-body">
						<fieldset class="form-group field_page_type page_type  dropdown-field">
							<label for="field_page_type">Page Type</label>

							<input type="hidden" name="parentId" value="" />
							<select name="pageTypeId" class="form-control">
								<option value="">None</option>
								@foreach($page_types as $page_type)
									<option value="{{ $page_type->id }}">{{ $page_type->name }}</option>
								@endforeach
							</select>
						</fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button  class="btn btn-primary">Create Page</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		$('#pageTypeModal').on('show.bs.modal', function (event) {
			var parentId = $(event.relatedTarget).data('page-id');
			$('input[name="parentId"]', this).val(parentId ? parentId : "");
		})
	</script>
@endsection