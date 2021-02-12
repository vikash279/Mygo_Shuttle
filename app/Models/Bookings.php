<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;

class Bookings extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
      protected $table = 'bookings';
    protected $fillable = [
        
        'search_id',
        'user_id',
        'ride_id',
        'status',
        'distance',
        'amount',
        'ticket',
        'date',
    ];

  
    
   
    public static function fetchRideDetails($uid,$rid){
      
            $res = DB::table('bookings')
                    ->select('*')
                    ->where('user_id','=',$uid)
                    ->where('bookings.id','=',$rid)
                    ->get()->toArray();
                    
            return $res;        
           
    }
    
    public static function fetchBookingDetails($rideid,$date){
        $bookings = DB::table('bookings as book')
            ->join('search_rides as rides', 'book.search_id', '=', 'rides.id')
              ->where('book.ride_id','=',$rideid)
              ->where('book.date','=',$date)
               ->select('bookings.*,search_rides.*')
            ->get()->toArray();
        return $bookings;    
    }

      

  
}
