<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = [
        'action_system_record_id',
        'action_category',
        'action_completed_on',
        'action_solicitor_list',
        'action_type',
        'constituent_id',
        'name',
        'record_id',
    ];
}
