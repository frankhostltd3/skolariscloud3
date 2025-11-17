<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'grade',
        'min_amount',
        'max_amount',
        'notes',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
