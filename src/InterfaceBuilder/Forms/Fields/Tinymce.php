<?php

namespace Soda\Cms\InterfaceBuilder\Forms\Fields;

use Illuminate\Database\Schema\Blueprint;
use Soda\Cms\InterfaceBuilder\Forms\AbstractFormField;

class Tinymce extends AbstractFormField
{
    protected $view = 'tinymce';

    public function getDefaultParameters()
    {
        return [
            'selector'     => '#field_'.$this->getFieldName(),
            'height'       => 500,
            'plugins'      => [
                'advlist autolink lists link charmap print hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern image imagetools',
            ],
            'toolbar1'     => 'insertfile undo redo | forecolor backcolor styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            'image_advtab' => true,
            'convert_urls' => false, //prevent "http://site.com/my/directory" becoming "/my/directory" - https://www.tinymce.com/docs/configure/url-handling/
        ];
    }

    /**
     * Adds a column for this field to a DynamicModel.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     *
     * @return Blueprint
     */
    public function addToModel(Blueprint $table)
    {
        return $table->mediumText($this->getFieldName());
    }
}
