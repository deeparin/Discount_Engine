<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table      = 'campaign';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'type',
        'subtype',
        'amount',
        'percentage',
        'category_id',
        'threshold_amount',
        'max_discount_percentage',
        'points_per_currency'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';
}
