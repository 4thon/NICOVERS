<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceRequestModel extends Model
{
    protected $table = 'service_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'user_id',
        'category_id',
        'customer_name',
        'email',
        'phone',
        'address',
        'preferred_date',
        'details',
        'status',
        'scheduled_at',
        'assigned_employee_id',
    ];
}
