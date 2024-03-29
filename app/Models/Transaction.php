<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'users_id',
        'address',
        'total_price',
        'shoping_price',
        'status',
        'payment'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'users_id','id');
    }
    public function items(){
        return $this->hasMany(TransactionItem::class, 'transactions_id', 'id');
    }
}
