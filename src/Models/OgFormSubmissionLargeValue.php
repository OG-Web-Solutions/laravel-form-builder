<?php

namespace Ogwebsolutions\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class OgFormSubmissionLargeValue extends Model
{
    protected $fillable = [
        'og_value_id',
        'value',
    ];

    /**
     * Belongs to a submission value.
     */
    public function submissionValue()
    {
        return $this->belongsTo(OgFormSubmissionValue::class, 'og_value_id');
    }
}
