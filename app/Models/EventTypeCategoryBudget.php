<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTypeCategoryBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type_id',
        'category_id',
        'default_percentage',
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }
}
