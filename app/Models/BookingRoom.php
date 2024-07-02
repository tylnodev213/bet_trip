<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRoom extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'booking_rooms';
    protected $guarded = [];
    protected $dates = ['deleted_at'];
}
