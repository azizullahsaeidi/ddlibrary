<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int|string $id)
 */
class ResourceSubjectArea extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = ['resource_id','tid'];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function icon()
    {
        return $this->hasOne(StaticSubjectIcon::class, 'tid', 'tid');
    }
}
