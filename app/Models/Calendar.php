<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'chore_id',
        'chore_day',
    ];

    public function chores()
    {
        return $this->belongsToMany(Chore::class);
    }
}
