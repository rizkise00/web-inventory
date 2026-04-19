<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
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
        static::created(function ($stockIn) {
            if ($stockIn->item) {
                $stockIn->item->increment('stock', $stockIn->quantity);
            }
        });

        static::updated(function ($stockIn) {
            if ($stockIn->isDirty('item_id')) {
                $oldItem = Item::find($stockIn->getOriginal('item_id'));
                if ($oldItem) {
                    $oldItem->decrement('stock', $stockIn->getOriginal('quantity'));
                }
                if ($stockIn->item) {
                    $stockIn->item->increment('stock', $stockIn->quantity);
                }
            } else {
                $difference = $stockIn->quantity - $stockIn->getOriginal('quantity');
                if ($difference != 0 && $stockIn->item) {
                    if ($difference > 0) {
                        $stockIn->item->increment('stock', $difference);
                    } else {
                        $stockIn->item->decrement('stock', abs($difference));
                    }
                }
            }
        });

        static::deleted(function ($stockIn) {
            if ($stockIn->item) {
                $stockIn->item->decrement('stock', $stockIn->quantity);
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
