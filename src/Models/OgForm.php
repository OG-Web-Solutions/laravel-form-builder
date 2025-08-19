<?php

namespace Ogwebsolutions\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ogwebsolutions\FormBuilder\Models\OgFormSubmission;
use Ogwebsolutions\FormBuilder\Models\OgFormSetting;

class OgForm extends Model
{
    use SoftDeletes;

    // Fields that can be mass-assigned
    protected $fillable = [
        'title',
        'fields',
        'status'
    ];

    // Automatically cast JSON fields to array
    protected $casts = [
        'fields' => 'array'
    ];

    // Dates for soft deletes
    protected $dates = ['deleted_at'];

    /**
     * A form has many submissions
     */
    public function submissions()
    {
        return $this->hasMany(OgFormSubmission::class, 'og_form_id');
    }
    /**
     * A form has one settings row.
     */
    public function settings()
    {
        return $this->hasOne(OgFormSetting::class, 'og_form_id');
    }
}


