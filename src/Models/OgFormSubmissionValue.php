<?php

namespace Ogwebsolutions\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class OgFormSubmissionValue extends Model
{
    protected $fillable = [
        'og_form_submission_id',
        'label',
        'key',
        'value',
    ];

    /**
     * Belongs to a submission.
     */
    public function submission()
    {
        return $this->belongsTo(OgFormSubmission::class, 'og_form_submission_id');
    }

    /**
     * May have a large value (if value too big for string column).
     */
    public function largeValue()
    {
        return $this->hasOne(OgFormSubmissionLargeValue::class, 'og_value_id');
    }
}
