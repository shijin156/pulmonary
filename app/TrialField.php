<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Trialfield extends Model
{
    use LogsActivity;

    protected $fillable = [
        'parent_id', 'name', 'value', 'type', 'description', 'required', 'order', 'status'
    ];

    protected static $logAttributes = ['parent_id', 'name', 'value', 'type', 'description', 'required', 'order', 'status'];

    public function parent()
    {
        return $this->hasOne('App\Trialfield', 'id', 'parent_id');
    }
}
