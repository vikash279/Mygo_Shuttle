<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_code',
        'phone_number',
        'dob',
        'gender',
        'user_type',
        'device_id',
        'referral_code',
        'otp',
        'profile_image',
        'google_uid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     public static function updateOTP($data,$id){
          $value = DB::table('users')->where('id',$id)->update($data);
          return $value; 
      }

     public static function fetchCities(){
        $value = DB::table('cities')->get();
          return $value;
     }

      public static function updateUser($id,$data){
         // print_r($id);die;
          $value = DB::table('users')->where('id', $id)->update($data);
           if($value){
            return TRUE;
        }else{
            return FALSE;
        }
      }

      public static function captureUserDL($data){
         $value = DB::table('user_driving_licence')->insert($data);
        if($value){
            return TRUE;
        }else{
            return FALSE;
        } 
      }
      
       public static function updateUserDL($document_id,$data){
         $value = DB::table('user_driving_licence')->where('id',$document_id)->update($data);
        if($value){
            return TRUE;
        }else{
            return FALSE;
        } 
      }

      public static function captureUserRC($data){
         $value = DB::table('user_rc_details')->insert($data);
        if($value){
            return TRUE;
        }else{
            return FALSE;
        } 
      }
      
      public static function fetchUserDLDetails($id){
         $value = DB::table('user_driving_licence')->where('user_id', $id)->get();
          return $value; 
      }
      
       public static function fetchUserRCDetails($id){
         $value = DB::table('user_rc_details')->where('user_id', $id)->get();
          return $value; 
      }
      
      public static function fetchUserVehicleDetails($id){
        $value = DB::table('vehicle_details')->where('user_id', $id)->get();
          return $value;  
      }
      
      public static function fetchUserCityDetails($id){
         $value = DB::table('user_cities')->where('user_id', $id)->get();
          return $value;   
      }  
      
      public static function cancelUserRide($id){
          $data = ['status' => 'Cancelled'];
          $value = DB::table('bookings')->where('id',$id)->update($data);
          return $value;  
      }
      
     
}
