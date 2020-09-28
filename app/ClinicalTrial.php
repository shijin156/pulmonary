<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Clinicaltrial extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['trialname', 'category_id', 'coordinator_id'];

    protected $fillable = ['trialname', 'category_id', 'coordinator_id'];

    public function trialfields()
    {
        return $this->belongsToMany('App\Trialfield')->withPivot('value');
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }

    public function coordinator()
    {
        return $this->hasOne('App\User', 'id', 'coordinator_id');
    }
}
