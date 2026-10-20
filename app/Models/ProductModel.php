<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['category_id', 'name', 'slug', 'description', 'price', 'stock_qty', 'warranty_years', 'image_path', 'model_url', 'model_scale', 'is_published'];
}
