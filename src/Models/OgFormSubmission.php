<?php

namespace Ogwebsolutions\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class OgFormSubmission extends Model
{
    protected $fillable = [
        'og_form_id',
        'token',
        'ip',
    ];

    /**
     * Get the form this submission belongs to.
     */
    public function form()
    {
        return $this->belongsTo(OgForm::class, 'og_form_id');
    }

    /**
     * Get all field values submitted in this form submission.
     */
    public function values()
    {
        return $this->hasMany(OgFormSubmissionValue::class, 'og_form_submission_id');
    }
}
