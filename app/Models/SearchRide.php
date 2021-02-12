<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;

class SearchRide extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
      protected $table = 'search_rides';
    protected $fillable = [
        
        'user_id',
        'pickup_address',
        'destination_address',
        'nearby_pickup_address',
        'latitude',
        'longitude',
        'no_of_seat',
        'promo_code',
        'payment_method',
        'date',
    ];

   


      

  
}
