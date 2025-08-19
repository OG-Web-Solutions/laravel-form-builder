<?php
namespace Ogwebsolutions\FormBuilder\Models;

use Illuminate\Database\Eloquent\Model;

class OgFormSetting extends Model
{
    protected $fillable = [
        'og_form_id',
        'admin_email_enabled',
        'admin_email_subject',
        'admin_email_body',
        'admin_emails',
        'admin_csv_enabled',
        'customer_email_enabled',
        'customer_email_subject',
        'customer_email_body',
        'customer_emails',
        'success_message',
        'failure_message',
        'redirect_url',
    ];

    protected $casts = [
        'admin_emails' => 'array',
        'customer_emails' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(OgForm::class, 'og_form_id');
    }
}
