<?php
$smallView = false;
if ($page->type === null || $page->type->fields === null || !count($page->type->fields->where('pivot.show_in_table', 1))) {
    $smallView = true;
}

$blockTypes = $page->blockTypes->keyBy('id');
if ($page->type && $page->type->blockTypes) {
    $blockTypes = $blockTypes->merge($page->type->blockTypes->keyBy('id'));
}
?>

@extends(soda_cms_view_path('layouts.inner'))

@section('breadcrumb')
    <li><a href="{{ route('soda.home') }}">Home</a></li>
    <li><a href="{{ route('soda.pages.index') }}">Pages</a></li>
    <li class="active">{{ $page->name ? $page->name : 'New ' . ($page->type ? $page->type->name . " Page" : "Page") }}</li>
@stop

@section('head.title')
    <title>{{ $page->id ? 'Edit' : 'New' }} Page :: Soda CMS</title>
@endsection

@section('content-heading-button')
    @include(soda_cms_view_path('partials.buttons.save'), ['submits' => '#page-form'])
@stop

@include(soda_cms_view_path('partials.heading'), [
    'icon'        => 'mdi mdi-file-outline',
    'title'       => $page->name ? ' Page: ' . $page->name : 'New Page',
    'description' => $page->description,
])

@section('settings.basic')
    <div class="content-block">
        {!! SodaForm::text([
            "name"        => "Name",
            "description" => "The name of this page",
            "field_name"  => 'name',
        ])->setLayout($smallView ? soda_cms_view_path('partials.inputs.layouts.inline') : soda_cms_view_path('partials.inputs.layouts.stacked'))->setModel($page) !!}

        {!! SodaForm::slug([
            'name'        => 'Slug',
            'description' => 'The url of this page',
            'field_name'  => 'slug',
            'field_params' => [
                'prefix' => ($page->parent_id !== null && $parent = $page->getParent()) ? $parent->slug : '',
            ],
        ])->setLayout($smallView ? soda_cms_view_path('partials.inputs.layouts.inline') : soda_cms_view_path('partials.inputs.layouts.stacked'))->setModel($page) !!}

        {!! SodaForm::toggle([
            'name'         => 'Published',
            'field_name'   => 'status',
            'value'        => Soda\Cms\Foundation\Constants::STATUS_LIVE,
            'field_params' => [
                'checked-value'   => Soda\Cms\Foundation\Constants::STATUS_LIVE,
                'unchecked-value' => Soda\Cms\Foundation\Constants::STATUS_DRAFT],
        ])->setLayout($smallView ? soda_cms_view_path('partials.inputs.layouts.inline') : soda_cms_view_path('partials.inputs.layouts.stacked'))->setModel($page) !!}
    </div>
@stop

@section('tab.advanced')
    <div class="content-block">
        <p>Advanced page settings</p>
        <hr/>

        {!! SodaForm::toggle([
            'name'         => 'Prevent delete',
            'field_name'   => 'can_delete',
            'value'        => 1,
            'field_params' => ['checked-value' => 0, 'unchecked-value' => 1],
            'description'  => 'If enabled, this page can not be deleted'
        ])->setModel($page) !!}

        {!! SodaForm::toggle([
            'name'         => 'Allowed Children',
            'field_name'   => 'allowed_children',
            'value'        => 1,
            'field_params' => ['checked-value' => 1, 'unchecked-value' => 0],
            'description'  => 'If enabled, this page can have child pages'
        ])->setModel($page) !!}

        <div class="row fieldset-group">
            <div class="col-sm-6 col-xs-12">
                {!! SodaForm::dropdown([
                    'name'        => 'View Action',
                    'field_name'  => 'view_action_type',
                    'field_params' => ['options' => Soda\Cms\Foundation\Constants::PAGE_ACTION_TYPES],
                    'description'  => 'Specifies the interface supplied when viewing this page.',
                    'value'        => $page->type && $page->type->view_action_type ? $page->type->view_action_type : 'view',
                ])->setModel($page)->setLayout(soda_cms_view_path('partials.inputs.layouts.inline-group')) !!}
            </div>
            <div class="col-sm-6 col-xs-12">
                {!! SodaForm::text([
                    'name'        => null,
                    'field_name'  => 'view_action',
                    'value'       => $page->type && $page->type->view_action,
                ])->setModel($page)->setLayout(soda_cms_view_path('partials.inputs.layouts.inline-group')) !!}
            </div>
        </div>

        <div class="row fieldset-group">
            <div class="col-sm-6 col-xs-12">
                {!! SodaForm::dropdown([
                    'name'        => 'Edit Action',
                    'field_name'  => 'edit_action_type',
                    'field_params' => ['options' => Soda\Cms\Foundation\Constants::PAGE_ACTION_TYPES],
                    'description'  => 'Specifies the interface supplied when editing this page.',
                    'value'        => $page->type && $page->type->edit_action_type ? $page->type->edit_action_type : 'view',
                ])->setModel($page)->setLayout(soda_cms_view_path('partials.inputs.layouts.inline-group')) !!}
            </div>
            <div class="col-sm-6 col-xs-12">
                {!! SodaForm::text([
                    'name'        => null,
                    'field_name'  => 'edit_action',
                    'value'       => $page->type && $page->type->edit_action,
                ])->setModel($page)->setLayout(soda_cms_view_path('partials.inputs.layouts.inline-group')) !!}
            </div>
        </div>
    </div>
@stop

@section('content')
    <form method="POST" id="page-form" action="{{ route('soda.pages.' . ($page->id ? 'update' : 'store'), $page->id) }}"
          enctype="multipart/form-data">
        {!! csrf_field() !!}
        {!! method_field($page->id ? 'PUT' : 'POST') !!}
        @if($page->type)
            <input type="hidden" name="page_type_id" value="{{ $page->type->id }}"/>
        @endif
        @if(!$page->id)
            <input type="hidden" name="parent_id" value="{{ $page->parent_id }}"/>
        @endif
        <div class="row">
            <div class="{{ !$smallView ? 'col-md-9' : 'col-md-12' }} col-xs-12">

                <ul class="nav nav-tabs" role="tablist">
                    <li role='presentation' aria-controls="tab_settings">
                        <a role="tab" data-toggle="tab" href="#tab_settings">Settings</a>
                    </li>

                    @foreach($blockTypes as $blockType)
                        @if($blockType->list_action_type == 'view')
                            <li role='presentation' aria-controls="tab_{{ strtolower($blockType->identifier) }}">
                                <a role="tab" data-toggle="tab"
                                   href="#tab_{{ strtolower($blockType->identifier) }}">{{ $blockType->name }}</a>
                            </li>
                        @endif
                    @endforeach

                    @permission("advanced-pages")
                    <li role='presentation' aria-controls="tab_advanced">
                        <a role="tab" data-toggle="tab" href="#tab_advanced">Advanced</a>
                    </li>
                    @endpermission

                    @if($page->id)
                        @permission("attach-blocks")
                        <li role='presentation'>
                            <a role="tab" href="#tab_new-block">+</a>
                        </li>
                        @endpermission
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab_settings" role="tabpanel">
                        @if(!$smallView)
                        <div class="content-block">
                            @if($page->type && $page->type->description)
                                <p>{{ $page->type->description }}</p>
                                <hr/>
                            @endif
                            @if($page->type && $page->type->fields)
                                @foreach($page->type->fields->where('pivot.show_in_table', 1) as $field)
                                    {!! SodaForm::field($field)->setModel($page->pageAttributes())->setPrefix('settings') !!}
                                @endforeach
                            @endif
                        </div>
                        @else
                            @yield('settings.basic')
                        @endif
                    </div>
                    @foreach($blockTypes as $blockType)
                        @if($blockType->list_action_type == 'view')
                            <div class="tab-pane" id="tab_{{ strtolower($blockType->identifier) }}" role="tabpanel">
                                <div class="content-block">
                                    @include($blockType->list_action, [
                                        'blockType'  => $blockType,
                                        'page'       => $page,
                                        'blocks'     => $page->block($blockType)->paginate(null, ['*'], $blockType->identifier .'-page')
                                    ])
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @permission("live-preview")
                    <div class="tab-pane" id="tab_live" role="tabpanel">
                        @yield('tab.live-preview')
                    </div>
                    @endpermission

                    @permission("advanced-pages")
                    <div class="tab-pane" id="tab_advanced" role="tabpanel">
                        @yield('tab.advanced')
                    </div>
                    @endpermission
                </div>
            </div>
            @if(!$smallView)
            <div class="col-md-3 col-xs-12 pull-right" style="margin-top: 42px;">
                @yield('settings.basic')
            </div>
            @endif
        </div>
    </form>

    <div class="content-bottom">
        @include(soda_cms_view_path('partials.buttons.save'), ['submits' => '#page-form'])
    </div>
@stop

@section('modals')
    @parent
    @if($page->id)
        @permission("attach-blocks")
        <div class="modal fade" id="newBlockModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Select a block to add...</h4>
                    </div>
                    <form method="POST" action="{{ route('soda.pages.blocks.attach', $page->id) }}">
                        {!! csrf_field() !!}
                        <div class="modal-body">
                            {!! SodaForm::dropdown([
                                "name"         => "Blocks",
                                "field_name"   => 'block_id',
                                "field_params" => ['options' => $blockTypes->pluck('name', 'id')]
                            ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

                            @permission("manage-block-types")
                            {!! SodaForm::text([
                                "name"        => "Max Blocks",
                                "field_name"  => "max_blocks",
                                "description" => "Defines the maximum number of rows this block can hold. Default: unlimited"
                            ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}

                            {!! SodaForm::text([
                                "name"        => "Min Blocks",
                                "field_name"  => "min_blocks",
                                "description" => "Defines the minimum number of rows this block can hold. Default: unlimited"
                            ])->setLayout(soda_cms_view_path('partials.inputs.layouts.stacked')) !!}
                            @endpermission
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary">Add Block</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endpermission
    @endif
@stop

@section('footer.js')
    @parent
    <script>
        $(function () {
            $('a[href="#tab_new-block"]').on('click', function (e) {
                e.preventDefault();
                $('#newBlockModal').modal('show')
            })
        });
    </script>
@stop
