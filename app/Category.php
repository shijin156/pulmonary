<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['name', 'order'];

    public function trials()
    {
        return $this->hasMany('App\Clinicaltrial');
    }

    protected $fillable = [
        'name', 'order'
    ];

}
