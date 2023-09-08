<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $appends = ['orderTotal', ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'purchaser_id', 'id');
    }

    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchaser_id', 'id');
    }

    public function getOrderTotalAttribute()
    {   
        $total = 0;
        $all = $this->orderItems()->with('product')->get();
        foreach($all as $item){
            $total += $item->qantity * $item->product?->price;
        }
        return $total;
    }

}
