<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($stockOut) {
            if ($stockOut->item) {
                $stockOut->item->decrement('stock', $stockOut->quantity);
            }
        });

        static::updated(function ($stockOut) {
            if ($stockOut->isDirty('item_id')) {
                $oldItem = Item::find($stockOut->getOriginal('item_id'));
                if ($oldItem) {
                    $oldItem->increment('stock', $stockOut->getOriginal('quantity'));
                }
                if ($stockOut->item) {
                    $stockOut->item->decrement('stock', $stockOut->quantity);
                }
            } else {
                $difference = $stockOut->quantity - $stockOut->getOriginal('quantity');
                if ($difference != 0 && $stockOut->item) {
                    if ($difference > 0) {
                        $stockOut->item->decrement('stock', $difference);
                    } else {
                        $stockOut->item->increment('stock', abs($difference));
                    }
                }
            }
        });

        static::deleted(function ($stockOut) {
            if ($stockOut->item) {
                $stockOut->item->increment('stock', $stockOut->quantity);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
