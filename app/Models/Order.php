<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['جديد', 'قيد التجهيز', 'تم الشحن', 'تم التسليم', 'ملغي'];

    protected $fillable = [
        'user_id',
        'customer_id',
        'item',
        'quantity',
        'price',
        'status',
        'notes',
        'cost',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
