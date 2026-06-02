<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['order_number', 'order_type', 'table_number', 'total_price', 'status', 'notes'];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}