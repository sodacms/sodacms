<?php

namespace Soda\Cms\Foundation\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Soda\Cms\Foundation\Application\Interfaces\ApplicationInterface;
use Soda\Cms\Foundation\Application\Interfaces\ApplicationUrlInterface;
use Soda\Cms\Foundation\Pages\Interfaces\PageInterface;

class Application extends Model implements ApplicationInterface
{
    protected $table = 'applications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(resolve_class(PageInterface::class));
    }

    public function urls()
    {
        return $this->hasMany(resolve_class(ApplicationUrlInterface::class));
    }
}