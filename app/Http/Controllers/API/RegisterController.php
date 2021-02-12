<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Cities;
use App\Models\UserCity;
use App\Models\SearchRide;
use App\Models\Bookings;
use App\Models\WalletBalance;
use App\Models\HireVehicle;
use App\Models\VehicleDetail;
use App\Models\DriverRating;
use App\Models\Notification;
use App\Models\TipToDriver;
use App\Models\LoginStatus;
use App\Models\ShuttleRoute;
use App\Models\DefaultPaymentMethod;
use Illuminate\Support\Facades\Auth;
use Validator;
use Twilio\Rest\Client;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function newRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'name' => 'required',
            'email' => 'required|email|unique:users',
           // 'password' => 'required',
          //  'c_password' => 'required|same:password',
            'country_code' => 'required',
            'phone_number' => 'required',
            'user_type' => 'required',
            'device_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $res = User::where("phone_number",$request->phone_number)->where("user_type",$request->user_type)->first();
        if(!empty($res)){
       // print_r($res);die;
        return $this->sendError('Unauthorised.', ['error'=>'Phone number already exist.']); 
        }else{
        $otp = $this->getRandomString(4);
        //print_r($otp);die;
        $input = $request->all();
        $input['otp'] = $otp;
       // $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //Code for generate and send OTP
        
        $data123 = ['phone' => '+'.$input['country_code'].$input['phone_number'], 'text' => 'Your otp is :'.$otp];
       // $message_sent = $this->sendSMS($data123);
        $message_sent = $this->sendSMSByAPI($data123);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user_name'] =  $user->name;
        $success['user_id'] =  $user->id;
        $success['user_type'] =  $user->user_type;
        $success['otp'] =  $user->otp;
   
        return $this->sendResponse($success, 'OTP has been sent to your phone.');
        }    
    }
   
    /**
     * Login api
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_number' => 'required',
            //'password' => 'required',
            'user_type' => 'required',
            'device_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $user = User::where('phone_number', $request->phone_number)->where("user_type",$request->user_type)->first();
       // print_r($user);die;
        if(!empty($user)){
           // print_r($user->id);die;
        if(Auth::loginUsingId($user->id)){
       // if(Auth::attempt(['country_code' => $request->country_code, 'phone_number' => $request->phone_number, 'password' => $request->get('password'), 'user_type' => $request->user_type, 'device_id' => $request->device_id])){ 
            $user = Auth::user();
            $otp = $this->getRandomString(4);
            $id = $user->id;
            $data = array(
                  'otp' => $otp,
                  'device_id' => $request->device_id
                ); 
            $res = \App\Models\User::updateOTP($data,$id);
            $data123 = ['phone' => '+'.$request->country_code.$request->phone_number, 'text' => 'Your otp is :'.$otp];
            //$message_sent = $this->sendSMS($data123);
            $message_sent = $this->sendSMSByAPI($data123);
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['user_id'] =  $user->id;
            //$success['name'] =  $user->name;
            $success['email'] =  $user->email;
            $success['otp'] =  $otp;
   
            return $this->sendResponse($success, 'OTP has been sent to your phone.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
      }else{
          return $this->sendError('Unauthorised.', ['error'=>'User not found.']); 
      }    
    }

     /**
     * Function to generate OTP
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */ 
     private function getRandomString($length) {
       $characters = '0123456789';
       $string = '';

       for ($i = 0; $i < $length; $i++) {
         $string .= $characters[mt_rand(0, strlen($characters) - 1)];
     }

      return $string;
  }

     
     /**
     * Function for sending sms to user phone using twilio
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */ 
    protected function sendSMS($data123) {
          // Your Account SID and Auth Token from twilio.com/console
            $sid = 'ACf431f8007fdfc98e7fb69d642c52e514';
            $token = '034693224b34e0dfc5ea0033822e5ae8';
           // print_r($data123);die;
            $client = new \Twilio\Rest\Client($sid, $token);
             $message = $client->messages
                  ->create($data123['phone'], // to
                       [
                           "from" => "+17745653560",
                           "body" => $data123['text']
                       ]
                  );    

    }

     /**
     * OTP Verification
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
            'user_type' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user= User::where("id",$request->user_id)->where("user_type",$request->user_type)->first();
       // print_r($user->otp);die;
        if(!empty($user)){
            $otp = $user->otp;
         if($otp == $request->otp){
            $id = $user->id;
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user_id'] =  $id;
            $success['full_name'] =  $user->name;
            $success['email'] =  $user->email;
            $success['country_code'] =  '+'.$user->country_code;
            $success['phone_number'] =  $user->phone_number;
            $success['user_type'] =  $user->user_type;
            $success['dob'] =  $user->dob;
            $success['gender'] =  $user->gender;
            $success['user_type'] =  $user->user_type;
            $success['device_id'] =  $user->device_id;
            $success['referral_code'] =  $user->referral_code;
            $success['profile_image'] =  $user->profile_image;
            if($user->user_type == "driver"){
                $DL_data = User::fetchUserDLDetails($id);
                if(!empty($DL_data[0])){
                  $success['DL_Details'] = "Found";
                }else{
                  $success['DL_Details'] = "Not found";  
                }
                $RC_data = User::fetchUserRCDetails($id);
                if(!empty($RC_data[0])){
                  $success['RC_Details'] = "Found";
                }else{
                  $success['RC_Details'] = "Not found";  
                }
                $Vehicle_Details = User::fetchUserVehicleDetails($id);
                if(!empty($Vehicle_Details[0])){
                  $success['Vehicle_Details'] = "Found";
                }else{
                  $success['Vehicle_Details'] = "Not found";  
                }
                
                $City_Details = User::fetchUserCityDetails($id);
                if(!empty($City_Details[0])){
                  $success['City_Details'] = "Found";
                }else{
                  $success['City_Details'] = "Not found";  
                }
                
            }

            return $this->sendResponse($success, ' OTP matched.');
         }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, OTP does not match']);
         }
        }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, No user found']);
        }
        
       

    }


      /**
     * Cities List API
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function fetchCities(){
        $res= Cities::fetchCities();
       // $res = \App\Models\User::fetchCities();
       // print_r($res);die;
         if(!empty($res)){
            foreach ($res as $val) {
           // $success['token'] =  $val->createToken('MyApp')->accessToken;
            $success[] =  ['city_id' => $val->city_id, 'city_name' => $val->city_name];
           
            }
            return $this->sendResponse($success, ' Cities list fetched successfully.');
        
        }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, No city found']);
        }
    }

    /**
     * API for capturing user city
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function submitCity(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'city_id' => 'required',
            'city_name' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input = $request->all();
        $res = UserCity::create($input);
        //print_r($res);die;
         $success['token'] =  $res->createToken('MyApp')->accessToken;
        $success['city_id'] =  $res->city_id;
        $success['user_id'] =  $res->user_id;
        $success['city_name'] =  $res->city_name;
   
        return $this->sendResponse($success, 'OTP has been sent to your phone.');
    
    }

    /**
     * API for updating user profile
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function updateUserProfile(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            //'referral_code' => 'required',
            //'profile_pic' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        if(!empty($request->profile_picture)){
         $destinationPath = base_path('images/profile_picture');
          $profile_picture = time().'.'.$request->profile_picture->getClientOriginalExtension();
         // print_r($profile_picture);die;
          $request->profile_picture->move($destinationPath, $profile_picture); 
        }else{
            $profile_picture = '';
        }

        $input = $request->all();
        $data = array(
              'name' =>$request->name,
              'dob' =>$request->dob,
              'gender' =>$request->gender,
              'referral_code' =>$request->referral_code,
              'profile_image' =>$profile_picture,
            );
        $id = $request->user_id;
        $res = User::updateUser($id,$data);
        $success['user_id'] =  $id;
        return $this->sendResponse($success, 'User profile has been updated successfully.');
    }


    /**
     * API for uploading user driving licence
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function UploadDrivingLicence(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'dl_front_image' => 'required',
            'dl_back_image' => 'required',
            'dl_number' => 'required',
            'dl_expiry' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $destinationPath = base_path('DL');
       $front = time().'.'.$request->dl_front_image->getClientOriginalExtension();
      // print_r($destinationPath);die;
       $request->dl_front_image->move($destinationPath, $front);
       
       $back = time().'.'.$request->dl_back_image->getClientOriginalExtension();
       $request->dl_back_image->move($destinationPath, $back); 
     
       if(!empty($request->document_id)){
          $document_id = $request->document_id;
          
        $data = array(
          'dl_front_image' => url('DL').'/'.$front,
          'dl_back_image' => url('DL').'/'.$back,
          'dl_number' => $request->dl_number,
          'dl_expiry' => $request->dl_expiry
        );

      // print_r($data);die;
           $res = User::updateUserDL($document_id,$data);
           $success =  $data;
           $success['user_id'] = $request->user_id;
       return $this->sendResponse($success, 'User driving licence details has been updated successfully.');
      }else{
        $data = array(
          'user_id' => $request->user_id,
          'dl_front_image' => url('DL').'/'.$front,
          'dl_back_image' => url('DL').'/'.$back,
          'dl_number' => $request->dl_number,
          'dl_expiry' => $request->dl_expiry
        );

      // print_r($data);die;
       $res = User::captureUserDL($data);
       $success =  $data;
       return $this->sendResponse($success, 'User driving licence details has been saved successfully.');
      } 

    }


    /**
     * API for uploading user rc details
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function uploadRCDetails(Request $request){
    
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'rc_front_image' => 'required',
            'rc_back_image' => 'required',
            'rc_number' => 'required',
            'vehicle_color' => 'required',
            'vehicle_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $destinationPath = base_path('RC');
       $front = time().'.'.$request->rc_front_image->getClientOriginalExtension();
      // print_r($destinationPath);die;
       $request->rc_front_image->move($destinationPath, $front);
       
       $back = time().'.'.$request->rc_back_image->getClientOriginalExtension();
       $request->rc_back_image->move($destinationPath, $back); 

       $data = array(
          'user_id' => $request->user_id,
          'vehicle_id' => $request->vehicle_id,
          'rc_front_image' => url('RC').'/'.$front,
          'rc_back_image' => url('RC').'/'.$back,
          'rc_number' => $request->rc_number,
          'vehicle_color' => $request->vehicle_color
        );

       //print_r($data);die;

        $res = User::captureUserRC($data);
       $success =  $data;
       return $this->sendResponse($success, 'User rc details has been updated successfully.');
         
    } 
    
     /**
     * function to search ride
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function searchRide(Request $request){
          $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'pickup_address' => 'required',
           // 'destination_address' => 'required',
            'nearby_pickup_address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'no_of_seat' => 'required',
            'timing' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $input = $request->all();
        $input1 = array(
             'user_id' => $request->user_id,
             'pickup_address' => $request->pickup_address,
             'destination_address' => $request->destination_address,
             'nearby_pickup_address' => $request->nearby_pickup_address,
             'latitude' => $request->latitude,
             'longitude' => $request->longitude,
             'no_of_seat' => $request->no_of_seat,
             'timing' => $request->timing,
             'promo_code' => $request->promo_code,
             'payment_method' => $request->payment_method,
             'date' => date('Y-m-d')
            );
        $res = SearchRide::create($input1);
       // $vehicle_data = VehicleDetail::where('timing', $request->timing)->where('nearby_pickup_address',$request->nearby_pickup_address)->first();
       $vehdata = ShuttleRoute::where('start_address', 'like', '%' . $request->nearby_pickup_address . '%')->first();
        $vehicle_data1 = VehicleDetail::where('timing', $request->timing)->where('id', $vehdata->shuttle_id)->first();
       // print_r($vehdata->shuttle_id);die;
       
       if(!empty($vehicle_data1)){
           
            $loginstatus = LoginStatus::where('user_id',$vehicle_data1->user_id)->first();
           // print_r($loginstatus->status);die;
            if($loginstatus->status == "login"){
                $vehicle_data = $vehicle_data1;
            }else{
               $vehicle_data =  VehicleDetail::where('timing', $request->timing)->where('nearby_pickup_address', 'like', '%' . $request->nearby_pickup_address . '%')->where('id', '!=' , $vehicle_data1->id)->first();
            }
           
           if(!empty($vehicle_data->vehicle_image)){
               $vehicle_image = url('images/vehicle_picture').'/'.$vehicle_data->vehicle_image;
           }else{
               $vehicle_image = null;
           }
        $vehiclepoints = ShuttleRoute::where('shuttle_id',$vehicle_data->id)->get();   
        $success['search_id'] =  $res->id;
        $success['user_id'] =  $res->user_id;
        $success['ride_id'] =  $vehicle_data->id;
        $success['vehicle_name'] =  $vehicle_data->vehicle_name;
        $success['vehicle_number'] =  $vehicle_data->vehicle_number;
        $success['vehicle_image'] =  $vehicle_image;
        $success['vehicle_brand'] =  $vehicle_data->vehicle_brand;
        $success['vehicle_model'] =  $vehicle_data->vehicle_model;
        $success['vehicle_year'] =  $vehicle_data->vehicle_year;
        $success['vehicle_color'] =  $vehicle_data->vehicle_color;
        $success['vehicle_booking_type'] =  $vehicle_data->vehicle_booking_type;
        $success['nearby_pickup_address'] =  $vehicle_data->nearby_pickup_address;
        $success['timing'] =  $vehicle_data->timing;
        $success['cost'] =  '';
        $success['stoppage'] = $vehiclepoints;
   
        return $this->sendResponse($success, 'Ride searched successfully!!.');
       }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, ride not found!!']); 
       } 
     }
     
     /**
     * API for request ride
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function requestRide(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'ride_id' => 'required',
            'search_id' => 'required',
            'destination_address' => 'required',
            'value' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
       // print_r($request->value);die;
        if($request->value == "Cal"){
            $disdetails = SearchRide::where('id', $request->search_id)->first();
            $shudetails = ShuttleRoute::where('shuttle_id',$request->ride_id)->where('start_address', $disdetails->pickup_address)->orWhere('end_address',$disdetails->destination_address)->get();
            $totaldistance = collect($shudetails)->sum('distance');
            $totalamount = $totaldistance * 5;
            $success['distance'] =  $totaldistance;
            $success['amount'] =  $totalamount;
            $success['promo_code'] =  $request->promo_code;
            $success['payment_method'] =  $request->payment_method;
            return $this->sendResponse($success, 'Distance and amount calculated successfully!!.');
        }else{
        $disdetails = SearchRide::where('id', $request->search_id)->first();
        $shudetails = ShuttleRoute::where('shuttle_id',$request->ride_id)->where('start_address', $disdetails->pickup_address)->orWhere('end_address',$disdetails->destination_address)->get();
        $totaldistance = collect($shudetails)->sum('distance');
        $totalamount = $totaldistance * 5;
        //print_r($totalamount);die;
        $num = $this->getRandomString(4);
        $ticket = "mygo".$num;
        //print_r($ticket);die;
        $input = $request->all();
        $input['ticket'] = $ticket;
        $input['distance'] = $totaldistance;
        $input['amount'] = $totalamount;
        $input['date'] = date('Y-m-d');
        
        $res = Bookings::create($input);
        $updatedata = ['destination_address' => $request->destination_address, 'payment_method' => $request->payment_method, 'promo_code' => $request->promo_code];
        $res1 = SearchRide::where('id',$request->search_id)->update($updatedata);
        $disdetails11 = SearchRide::where('id', $request->search_id)->first();
        if($res){
        $vehicledetails = VehicleDetail::where('id',$request->ride_id)->first();
        $riderdetails= User::where('id', $vehicledetails->user_id)->first();
        // print_r($riderdetails);die;
        if(!empty($riderdetails->profile_image)){
            $profile_pic = url('images/profile_picture').'/'.$riderdetails->profile_image;
        }else{
            $profile_pic = null;
        }
        $success['booking_id'] =  $res->id;
        $success['user_id'] =  $request->user_id;
        $success['rider_id'] =  $riderdetails->id;
        $success['ride_id'] =  $request->ride_id;
        $success['driver_name'] =  $riderdetails->name;
        $success['driver_rating'] =  '';
        $success['vehicle_number'] =  $vehicledetails->vehicle_number;
        $success['ticket_number'] =  $res->ticket;
        $success['seat_number'] =  '';
        $success['distance'] =  $totaldistance;
        $success['amount'] =  $totalamount;
        $success['promo_code'] =  $disdetails11->promo_code;
        $success['payment_method'] =  $disdetails11->payment_method;
        $success['driver_image'] =  $profile_pic;
        $success['duration'] =  '';
        $success['driver_contact_number'] =  $riderdetails->country_code.$riderdetails->phone_number;
   
        return $this->sendResponse($success, 'Ride booked successfully!!.');
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, ride not found!!']); 
        } 
      }  
     }
     
       /**
     * API for fetching wallet balance of user
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function userWalletBalance(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        
        $res = WalletBalance::where('user_id',$request->user_id)->first();

           $success['user_id'] =  $request->user_id;
           if(!empty($res->balance)){
           $success['balance'] =  $res->balance;
           $success['balance_expiry_date'] =  $res->balance_expiry_date;
           $success['default_payment_method'] =  $res->default_payment_method;
           return $this->sendResponse($success, 'Wallet balance fetched successfully!!.'); 
           }else{
             return $this->sendResponse($success, 'User wallet balance not available!!.');  
           }
   
           
     }
     
      /**
     * API for fetching ride booking history of user
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function userRideBookingHistory(Request $request){
          $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $res = Bookings::where('user_id',$request->user_id)->orderBy('id','desc')->get()->toArray();
        if(!empty($res)){
            foreach($res as $val){
               // print_r($val['search_id']);die;
                $userdata = User::where('id',$val['user_id'])->first();
                $addressdata = SearchRide::where('id',$val['search_id'])->first();
                if(!empty($userdata->profile_image)){
                    $profile_pic = url('images/profile_picture').'/'.$userdata->profile_image;
                }else{
                    $profile_pic = null;
                }
                $data[] = array(
                      'booking_id' => $val['id'],
                      'user_id' => $val['user_id'],
                      'full_name' => $userdata->name,
                      'profile_image' => $profile_pic,
                      'booking_number' => $val['ticket'],
                      'ride_amount' => $val['amount'],
                      'distance' => '',
                      'pickup_address' => $addressdata->pickup_address,
                      'dropoff_address' => $addressdata->destination_address,
                      'status' => $val['status'],
                      'date_time' => $val['created_at'],
                    );
            }
            
            $success['user_id'] = $request->user_id;
            $success['total_earnings'] = '';
            $success['total_jobs'] = '';
            $success['booking_history'] = $data;
             return $this->sendResponse($success, 'User ride booking history fetched successfully!!.'); 
        }else{
           return $this->sendError('Unauthorised.', ['error'=>'Sorry, user booking history not found!!']); 
        }
     }
     
          /**
     * API for fetching ride details
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function fetchRideDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'booking_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        $uid = $request->user_id;
        $rid = $request->booking_id;
        $res = Bookings::fetchRideDetails($uid,$rid);
        
        if($res){
            foreach($res as $val){
               
            $driver_details = VehicleDetail::where('id',$val->ride_id)->select('user_id')->first();
            $driver_id = $driver_details->user_id;
            $driver_data = User::where('id', $driver_id)->first();
            
             if(!empty($driver_data->profile_image)){
                     $profile_pic = url('images/profile_picture').'/'.$driver_data->profile_image;
                 }else{
                     $profile_pic = null;
                 } 
                $data[] = array(
                     'user_id' => $val->user_id,
                     'ride_id' => $val->ride_id,  //vehicle id from vehicle details
                     'driver_id' => $driver_id,
                     'driver_name' => $driver_data->name,
                     'driver_image' => $profile_pic,
                     'driver_contact_number' => $driver_data->country_code.$driver_data->phone_number
                    );
            }
       
          $success = $data;
         return $this->sendResponse($success, 'Ride history fetched successfully!!.');
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, ride not found!!']); 
        } 
        
     }
     
        /**
     * API for hire vehicle
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     
     public function hireVehicle(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'trip_type' => 'required',
            'pickup_address' => 'required',
            'destination_address' => 'required',
            //'return_address' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'number_of_passengers' => 'required',
            'vehicle_type' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $input = $request->all();
        $res = HireVehicle::create($input);
        
        if($res){
           $success['trip_id'] =  $res->id;
           $success['trip_type'] =  $res->trip_type;
           $success['from_date'] =  $res->from_date;
           $success['to_date'] =  $res->to_date;
           $success['cost'] =  '';
           $success['vehicle_type'] =  $res->vehicle_type;
   
           return $this->sendResponse($success, 'Fare details fetched successfully!!.');
        }else{
           return $this->sendError('Unauthorised.', ['error'=>'Sorry, no vehicles  found!!']); 
        }
     }
     
     
     /**
     * API for payment of hire vehicle
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function hireVehiclePayment(Request $request){
         $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'amount' => 'required',
            'transaction_id' => 'required',
           
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        
        $data = ['payment_status' => 'Done', 'transaction_id' => $request->transaction_id];
        
         $res = HireVehicle::where('id', '=', $request->trip_id)->update($data);
         
         $result = HireVehicle::where('id', '=', $request->trip_id)->first();
         $success['trip_id'] = $result->id;
         return $this->sendResponse($success, 'Vehicle hired successfully.');
         
     }
     
    /**
     * API for user vehicle registration
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function userVehicleRegistration(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'vehicle_name' => 'required',
            'vehicle_number' => 'required',
            //'vehicle_image' => 'required',
            'vehicle_brand' => 'required',
            'vehicle_model' => 'required',
            'vehicle_year' => 'required',
            'vehicle_color' => 'required',
            'vehicle_booking_type' => 'required',
            'nearby_pickup_address' => 'required',
            'timing' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
         if(empty($request->vehicle_id)){
         if(!empty($request->vehicle_image)){
         $destinationPath = base_path('images/vehicle_picture');
          $vehicle_picture = time().'.'.$request->vehicle_image->getClientOriginalExtension();
         // print_r($vehicle_picture);die;
          $request->vehicle_image->move($destinationPath, $vehicle_picture); 
        }else{
            $vehicle_picture = '';
        }
        
         $input = $request->all();
         $data = array(
              'user_id' =>$request->user_id,
              'vehicle_name' =>$request->vehicle_name,
              'vehicle_number' =>$request->vehicle_number,
              'vehicle_image' =>$vehicle_picture,
              'vehicle_brand' =>$request->vehicle_brand,
              'vehicle_model' =>$request->vehicle_model,
              'vehicle_year' =>$request->vehicle_year,
              'vehicle_color' =>$request->vehicle_color,
              'vehicle_booking_type' =>$request->vehicle_booking_type,
              'nearby_pickup_address' =>$request->nearby_pickup_address,
              'timing' =>$request->timing,
            );
        
         $res = VehicleDetail::create($data);
         $id = $request->user_id;
         $userDLdata = \DB::table('user_driving_licence')->select('*')->where('user_id', $id)->first();
        // print_r($userDLdata);die;
         $success['user_id'] =  $id;
         $success['vehicle_id'] =  $res->id;
        // $success['dl_front_image'] =  base_path('DL').'/'.$userDLdata->dl_front_image;
         //$success['dl_back_image'] =  base_path('DL').'/'.$userDLdata->dl_back_image;
         //$success['dl_number'] =  $userDLdata->dl_number;
        // $success['dl_expiry'] =  $userDLdata->dl_expiry;
        return $this->sendResponse($success, 'Vehicle registered successfully.');
         }else{
            // print_r("hello");die;
            if(!empty($request->vehicle_image)){
                 $destinationPath = base_path('images/vehicle_picture');
                  $vehicle_picture = time().'.'.$request->vehicle_image->getClientOriginalExtension();
                 // print_r($vehicle_picture);die;
                  $request->vehicle_image->move($destinationPath, $vehicle_picture); 
                }else{
                    $vehicle_picture = '';
                }
                
                 $input = $request->all();
                 $data = array(
                      //'user_id' =>$request->user_id,
                      'vehicle_name' =>$request->vehicle_name,
                      'vehicle_number' =>$request->vehicle_number,
                      'vehicle_image' =>$vehicle_picture,
                      'vehicle_brand' =>$request->vehicle_brand,
                      'vehicle_model' =>$request->vehicle_model,
                      'vehicle_year' =>$request->vehicle_year,
                      'vehicle_color' =>$request->vehicle_color,
                      'vehicle_booking_type' =>$request->vehicle_booking_type,
                      'nearby_pickup_address' =>$request->nearby_pickup_address,
                      'timing' =>$request->timing,
                    );  
                $res =  VehicleDetail::where('id', $request->vehicle_id)->update($data); 
                $vehicledetails = VehicleDetail::where('id', $request->vehicle_id)->first();
                $success = $vehicledetails;
                $success['vehicle_id'] =  $vehicledetails->id;
            return $this->sendResponse($success, 'Vehicle details updated successfully.');    
                     
         }
     }
     
     /**
     * API for fetching user profile
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function getUserProfile(Request $request){
       $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $userdata = User::where('id',$request->user_id)->first();
        if(!empty($userdata)){
         if(!empty($userdata->profile_image)){
             $profile_pic = url('images/profile_picture').'/'.$userdata->profile_image;
         }else{
             $profile_pic = null;
         }   
          $paymode = \DB::table('default_payment_methods')->select('*')->where('user_id', $request->user_id)->first();
         // print_r($paymode);die;
          if(!empty($paymode)){
              $paymethod = $paymode->payment_method;
          }else{
              $paymethod = null; 
          }
         $success['user_id'] =  $userdata->id;
         $success['full_name'] =  $userdata->name;
         $success['dob'] =  $userdata->dob;
         $success['gender'] =  $userdata->gender;
         $success['email'] =  $userdata->email;
         $success['country_code'] =  $userdata->country_code;
         $success['phone_number'] =  $userdata->phone_number;
         $success['profile_image'] =  $profile_pic;
         $success['type'] =  $userdata->user_type;
         $success['default_payment_mode'] =  $paymethod;
        return $this->sendResponse($success, 'Profile details fetched successfully!!.');
        }else{
         return $this->sendError('Unauthorised.', ['error'=>'Sorry, no user  found!!']);   
        }    
     }
     
      /**
     * API for fetching nearby shuttle pickup point
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function pickupPoint(Request $request){
         $validator = Validator::make($request->all(), [
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $address = $request->address;
        $res = \DB::table('shuttle_pickup_point')->select('*')->where('address', $address)->first();
        // print_r($res);die;
        if(!empty($res)){
          $success['nearby_pickup_location'] =  $res->pickup_location;
          $success['latitude'] =  $res->latitude;
          $success['longitude'] =  $res->longitude;
          return $this->sendResponse($success, 'Nearby pickup point fetched successfully.');  
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, no nearby pickup point found!!']);    
        }
     }
     
     /**
     * API for fetching users promo code
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function fetchUserPromoCode(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        
        $id = $request->user_id;
        $res = \DB::table('users_promo_code')->select('*')->where('user_id', $id)->get()->toArray();
        if(!empty($res)){
            foreach($res as $val){
            $data[] = array(
                  'promo_code_id' => $val->promo_code_id,
                  'promo_code' => $val->promo_code,
                  'discount' => $val->discount
                );
            }    
        //   $success['promo_code_id'] =  $res->promo_code_id;
        //   $success['promo_code'] =  $res->promo_code;
        //   $success['discount'] =  $res->discount;
        $success = $data;
          return $this->sendResponse($success, 'Promo code fetched successfully.');  
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, no promo code found!!']);    
        }
     }
     
      /**
     * API for cancelling user ride
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     
     public function cancelRide(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        $id = $request->booking_id;
        $res = User::cancelUserRide($id);
        if($res){
            $success['user_id'] = $request->user_id;
            $success['ride_cancellation_amount'] = null;
            return $this->sendResponse($success, 'Ride cancelled successfully.');
        }else{
            return $this->sendError('Unauthorised.', ['error' => 'This ride already cancelled!!']);
        }
     }
     
      /**
     * API for capturing user's default payment method
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     
     public function userDefaultPaymentMethod(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'payment_method' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        $input = $request->all();
        $details = DefaultPaymentMethod::where('user_id',$request->user_id)->first();
       // print_r($details);die;
        if(empty($details->payment_method)){
        $res1 = DefaultPaymentMethod::create($input);
        $res = DefaultPaymentMethod::where('user_id',$request->user_id)->first();
        }else{
            $id = $request->user_id;
            $data = array(
                 'payment_method' => $request->payment_method
                );
          $res1 = DefaultPaymentMethod::updatePaymentMethod($id,$data);
          $res = DefaultPaymentMethod::where('user_id',$request->user_id)->first();
        }
        $success['user_id'] = $res->user_id;
        $success['default_payment_method'] = $res->payment_method;
        return $this->sendResponse($success, 'Your default payment method updated successfully.');
     }
     
      /**
     * API for rating driver
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function rateDriver(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'driver_id' => 'required',
            'rating' => 'required',
            'feedback' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        
        $input = $request->all();
        $res = DriverRating::create($input);
        $driver = User::where('id', $request->driver_id)->first();
      //  print_r($driver);die;
      if(!empty($driver->profile_image)){
          $driverimage = url('images/profile_picture').'/'.$driver->profile_image;
      }else{
          $driverimage = null;
      }
        
        $success['user_id'] = $res->user_id;
        $success['driver_id'] = $res->driver_id;
        $success['driver_name'] = $driver->name;
        $success['driver_image'] = $driverimage;
        return $this->sendResponse($success, 'Rated successfully.');
     }
     
     
     /**
     * API for validating transaction from paystack by reference id
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function validateTransaction(Request $request){
        $validator = Validator::make($request->all(), [
            'reference_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        $reference = $request->reference_id;
        $curl = curl_init();
       $url = 'https://api.paystack.co/transaction/verify/:'.$reference;
          curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer sk_live_b9a832ac9d3c79ae52f56586a506260a1b7e58ee",
              "Cache-Control: no-cache",
            ),
          ));
          
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          
          if ($err) {
           // echo "cURL Error #:" . $err;
            return $this->sendError('Unauthorised.', ['error' => 'Transaction not successfull!!']);
          } else {
              echo $response; 
           // $success = json_decode($response);
            //return $this->sendResponse($success, 'Transaction successfull.');
          }
     }
     
      /**
     * API for giving tip to driver by rider
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function tipDriver(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'driver_id' => 'required',
            'tip_amount' => 'required',
            'tip_from' => 'required',
            //'transaction_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        if($request->tip_from == "online"){
         //  print_r("online");die;
           $input = $request->all();
           $res = TipToDriver::create($input);
        }else{
          // print_r("wallet");die;
          
           $id = $request->user_id;
           $amount = $request->tip_amount;
          $wallet = WalletBalance::where('user_id', $id)->first();
         // print_r($wallet);die;
          if(!empty($wallet)){
              if($wallet->balance >= $amount){
               $input = $request->all();
           $res = TipToDriver::create($input);
           $new_wallet_balance = (int)$wallet->balance - (int)$amount;
           $data = ['balance' => $new_wallet_balance];
           $walletbalance = WalletBalance::where('user_id', '=', $id)->update($data);
           
           $driverwallet = WalletBalance::where('user_id', $request->driver_id)->first();
           if(!empty($driverwallet)){
                $new_wallet_balance11 = (int)$driverwallet->balance + (int)$amount;
                $data111 = ['balance' => $new_wallet_balance11];
                $walletbalance = WalletBalance::where('user_id', '=', $request->driver_id)->update($data111);
              }else{
                  $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+1 month" ) );
                  $insert = ["user_id" => $request->driver_id, "balance" => $amount, "balance_expiry_date" => $myDate, "default_payment_method" => "Cash"];
                  $walletbalance1 =  WalletBalance::create($insert);   
               }
         // print_r($new_wallet_balance);die;
             $success['tip_id'] = $res->id;
            $success['transaction_id'] = $res->transaction_id;
            return $this->sendResponse($success, 'Tip added successfully');
              }else{
                $success['tip_id'] = null;
           return $this->sendResponse($success, 'Not sufficient balance to pay');  
              }
            }else{
                $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+1 month" ) );
               // print_r($myDate);die;
               $insert = ["user_id" => $request->user_id, "balance" => 0, "balance_expiry_date" => $myDate, "default_payment_method" => "Cash"];
               $walletbalance =  WalletBalance::create($insert); 
                $success['tip_id'] = null;
               return $this->sendResponse($success, 'Not sufficient balance to pay');
            }
        }    
       
       
     }
     
     
     /**
     * API for adding balance to user wallet
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function addWalletAmount(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'balance' => 'required',
            'transaction_id' => 'required',
            'balance_expiry_date' => 'required',
            'default_payment_method' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $walletdetails = WalletBalance::where('user_id',$request->user_id)->first();
        //print_r($walletdetails);die;
        if(!empty($walletdetails->balance)){
           // print_r($walletdetails->balance);die;
           $data = array(
                 'user_id' => $request->user_id,
                 'balance' => (int)$request->balance + (int)$walletdetails->balance,
                 'transaction_id' => $request->transaction_id,
                 'balance_expiry_date' => $request->balance_expiry_date,
                 'default_payment_method' => $request->default_payment_method
               );
               
             // print_r($data);die; 
              $addbalance = WalletBalance::where('user_id', '=', $request->user_id)->update($data);
              $res = WalletBalance::where('user_id', $request->user_id)->first();
        }else{
            
        
        $input = $request->all();
         $data = array(
                 'user_id' => $request->user_id,
                 'balance' => (int)$request->balance,
                 'transaction_id' => $request->transaction_id,
                 'balance_expiry_date' => $request->balance_expiry_date,
                 'default_payment_method' => $request->default_payment_method
               );
        $res1 = WalletBalance::create($data);
        $res = WalletBalance::where('user_id', $request->user_id)->first();
     }
        $success['user_id'] = $res->user_id;
        $success['balance'] = $res->balance;
        $success['balance_expiry_date'] = $res->balance_expiry_date;
        $success['default_payment_method'] = $res->default_payment_method;
        return $this->sendResponse($success, 'Amount added to user wallet successfully');
        
     }
     
      /**
     * API for fetching user refund values by user id
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     
     public function userRefund(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
         $id = $request->user_id;
        $res = \DB::table('user_refunds')->select('*')->where('user_id', $id)->get()->toArray();
        if(!empty($res)){
            foreach($res as $val){
            $data[] = array(
                  'user_id' => $val->user_id,
                  'refund_amount' => $val->refund_amount,
                  'refund_status' => $val->refund_status
                );
            }    
       
        $success = $data;
          return $this->sendResponse($success, 'User refunds fetched successfully.');  
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, no user refund found!!']);    
        }
         
     }
     
       
    /**
     * API for send sms by using nigeria bulk sms api
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
        private function sendSMSByAPI($data123){
         $sendername = 'MYGOBUS';
         $mobile = $data123['phone'];
         $message = $data123['text'];
         
         $url = 'https://portal.nigeriabulksms.com/api/?username=timo.mygo@gmail.com&password=TAKE3PANADOL&message='.$message.'&sender='.$sendername.'&mobiles='.$mobile; 

               $curl = curl_init(); 
               curl_setopt_array($curl, array(
               CURLOPT_URL => $url,
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => "",
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => "GET",
               ));

               $response = curl_exec($curl);

               curl_close($curl); 
              // echo $response;die();
             //  $res = json_decode($response,1);
             return $response;
              
       } 
       
     /**
     * API for login via google
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function googleLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'google_uid' => 'required',
            'user_type' => 'required',
            'device_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $details = User::where('email', $request->email)->first();
        //print_r($details);die;
        if(empty($details)){
            $input = $request->all();
            $res = User::create($input);
            if(!empty($res->profile_image)){
                   $profile_image = url('images/profile_picture').'/'.$res->profile_image;
               }else{
                   $profile_image = null;
               }
               $success['token'] =  $res->createToken('MyApp')->accessToken;
               $success['user_id'] = $res->id;
               $success['full_name'] = $res->name;
               $success['email'] = $res->email;
               $success['country_code'] = '+'.$res->country_code;
               $success['phone_number'] = $res->phone_number;
               $success['user_type'] = $res->user_type;
               $success['dob'] = $res->dob;
               $success['gender'] = $res->gender;
               $success['device_id'] = $res->device_id;
               $success['referral_code'] = $res->referral_code;
               $success['profile_image'] = $profile_image;
               $success['google_uid'] = $res->google_uid;
               $id = $res->id;
                $DL_data = User::fetchUserDLDetails($id);
                if(!empty($DL_data[0])){
                  $success['DL_Details'] = "Found";
                }else{
                  $success['DL_Details'] = "Not found";  
                }
                $RC_data = User::fetchUserRCDetails($id);
                if(!empty($RC_data[0])){
                  $success['RC_Details'] = "Found";
                }else{
                  $success['RC_Details'] = "Not found";  
                }
                $Vehicle_Details = User::fetchUserVehicleDetails($id);
                if(!empty($Vehicle_Details[0])){
                  $success['Vehicle_Details'] = "Found";
                }else{
                  $success['Vehicle_Details'] = "Not found";  
                }
                
                $City_Details = User::fetchUserCityDetails($id);
                if(!empty($City_Details[0])){
                  $success['City_Details'] = "Found";
                }else{
                  $success['City_Details'] = "Not found";  
                }
               return $this->sendResponse($success, 'Login successfull.');
        }else{
           if($request->user_type == "rider" and $details->user_type == "rider"){
               $email = $request->email;
               $data = ['google_uid' => $request->google_uid];
               $updateuser = User::where('email', '=', $request->email)->update($data);
               $res = User::where('email', $request->email)->first();
               if(!empty($res->profile_image)){
                   $profile_image = url('images/profile_picture').'/'.$res->profile_image;
               }else{
                   $profile_image = null;
               }
               $success['token'] =  $res->createToken('MyApp')->accessToken;
               $success['user_id'] = $res->id;
               $success['full_name'] = $res->name;
               $success['email'] = $res->email;
               $success['country_code'] = '+'.$res->country_code;
               $success['phone_number'] = $res->phone_number;
               $success['user_type'] = $res->user_type;
               $success['dob'] = $res->dob;
               $success['gender'] = $res->gender;
               $success['device_id'] = $res->device_id;
               $success['referral_code'] = $res->referral_code;
               $success['profile_image'] = $profile_image;
               $success['google_uid'] = $res->google_uid;
               return $this->sendResponse($success, 'Login successfull.');
           }elseif($request->user_type == "driver" and $details->user_type == "driver"){
               $email = $request->email;
               $data = ['google_uid' => $request->google_uid];
               $updateuser = User::where('email', '=', $request->email)->update($data);
               $res = User::where('email', $request->email)->first();
               if(!empty($res->profile_image)){
                   $profile_image = url('images/profile_picture').'/'.$res->profile_image;
               }else{
                   $profile_image = null;
               }
              
               $success['token'] =  $res->createToken('MyApp')->accessToken;
               $success['user_id'] = $res->id;
               $success['full_name'] = $res->name;
               $success['email'] = $res->email;
               $success['country_code'] = '+'.$res->country_code;
               $success['phone_number'] = $res->phone_number;
               $success['user_type'] = $res->user_type;
               $success['dob'] = $res->dob;
               $success['gender'] = $res->gender;
               $success['device_id'] = $res->device_id;
               $success['referral_code'] = $res->referral_code;
               $success['profile_image'] = $profile_image;
               $success['google_uid'] = $res->google_uid;
                $id = $details->id;
                $DL_data = User::fetchUserDLDetails($id);
                if(!empty($DL_data[0])){
                  $success['DL_Details'] = "Found";
                }else{
                  $success['DL_Details'] = "Not found";  
                }
                $RC_data = User::fetchUserRCDetails($id);
                if(!empty($RC_data[0])){
                  $success['RC_Details'] = "Found";
                }else{
                  $success['RC_Details'] = "Not found";  
                }
                $Vehicle_Details = User::fetchUserVehicleDetails($id);
                if(!empty($Vehicle_Details[0])){
                  $success['Vehicle_Details'] = "Found";
                }else{
                  $success['Vehicle_Details'] = "Not found";  
                }
                
                $City_Details = User::fetchUserCityDetails($id);
                if(!empty($City_Details[0])){
                  $success['City_Details'] = "Found";
                }else{
                  $success['City_Details'] = "Not found";  
                }
               return $this->sendResponse($success, 'Login successfull.');
           }
           else{
                return $this->sendError('Unauthorised.', ['error'=>'Sorry, Email id exist for different user.!!']);
           }
        }
     }
     
      /**
     * API for get driver rating list
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function getReviewList(Request $request){
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
       $res = DriverRating::where('driver_id', $request->driver_id)->get();
     //  print_r($res);die;
        if(!empty($res)){
            foreach($res as $val){
                $data = array(
                        'id'=> $val->id,
                        'user_id'=> $val->user_id,
                        'driver_id'=> $val->driver_id,
                        'rating'=> $val->rating,
                        'feedback'=> $val->feedback
                    );
              $userdetails = User::where('id',$val->user_id)->first(); 
              $name = $userdetails->name;
              $image = $userdetails->profile_image;
              if(!empty($image)){
                   $profile = url('images/profile_picture').'/'.$image;
              }else{
                  $profile = "No image found";
              }
              $newdata[] = array_merge($data, ['username' => $name, 'profile_image' => $profile]);
            }
          $success =  $newdata;
          return $this->sendResponse($success, 'Driver ratings fetched successfully.');  
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, no rating found!!']);    
        }
     } 
     
       /**
     * API for fetching document details
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function documentDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            //'document_id' => 'required',
            'value' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        $id = $request->user_id;
        if($request->value == "DL"){
           $res = User::fetchUserDLDetails($id);
          // print_r($res);die;
          if($res){
             $success =  $res;
          return $this->sendResponse($success, 'Document details fetched successfully.');   
          }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, no data found!!']);  
          }
        }elseif($request->value == "RC"){
            $res = User::fetchUserRCDetails($id);
            if($res){
             $success =  $res;
          return $this->sendResponse($success, 'Document details fetched successfully.');   
          }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, no data found!!']);  
          }
        }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, wrong value passed!!']);    
        }    
        
     }
     
     
    /**
     * API for fetching document list
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function documentList(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
         $id = $request->user_id;
        $dl = User::fetchUserDLDetails($id);
        $rc = User::fetchUserRCDetails($id);
        if($dl){
            $dldetails = $dl;
        }else{
            $dldetails = "No DL details found for this user";
        }
         if($rc){
            $rcdetails = $rc;
        }else{
            $rcdetails = "No RC details found for this user";
        }
        
        $success = ['DL' => $dldetails, 'RC' => $rcdetails] ;
       return $this->sendResponse($success, 'Document list fetched successfully.');   
        
     }  
     
     /**
     * API for fetching vehicle details 
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehicleDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'vehicle_id' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $res = VehicleDetail::where('id', $request->vehicle_id)->first();
       // print_r($res);die;
        if($res){
            $data = array(
                    'id' => $res->id,
                    'user_id' => $res->user_id,
                    'vehicle_name' => $res->vehicle_name,
                    'vehicle_number' => $res->vehicle_number,
                    'vehicle_image' => url('images/vehicle_picture').'/'.$res->vehicle_image,
                    'vehicle_brand' => $res->vehicle_brand,
                    'vehicle_model' => $res->vehicle_model,
                    'vehicle_year' => $res->vehicle_year,
                    'vehicle_color' => $res->vehicle_color,
                    'vehicle_booking_type' => $res->vehicle_booking_type,
                    'nearby_pickup_address' => $res->near_pickup_address,
                    'timing' => $res->timing
                );
            $success = $data;
             return $this->sendResponse($success, 'Vehicle details fetched successfully.');
        }else{
             return $this->sendError('Unauthorised.', ['error'=>'Sorry, vehicle details not found!!']);
        }
     }
     
     
     
      /**
     * API for fetching vehicle list 
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehicleList(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $result = VehicleDetail::where('user_id', $request->user_id)->get()->toArray();
       // print_r($result);die;
        if($result){
            foreach($result as $res){
            $data[] = array(
                    'id' => $res['id'],
                    'user_id' => $res['user_id'],
                    'vehicle_name' => $res['vehicle_name'],
                    'vehicle_number' => $res['vehicle_number'],
                    'vehicle_image' => url('images/vehicle_picture').'/'.$res['vehicle_image'],
                    'vehicle_brand' => $res['vehicle_brand'],
                    'vehicle_model' => $res['vehicle_model'],
                    'vehicle_year' => $res['vehicle_year'],
                    'vehicle_color' => $res['vehicle_color'],
                    'vehicle_booking_type' => $res['vehicle_booking_type'],
                    'nearby_pickup_address' => $res['nearby_pickup_address'],
                    'timing' => $res['timing']
                );
            }    
            $success = $data;
             return $this->sendResponse($success, 'Vehicle list fetched successfully.');
        }else{
             return $this->sendError('Unauthorised.', ['error'=>'Sorry, vehicle list not found!!']);
        }
     }
     
     
     /**
     * API for fetch ride booking history list
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function rideBookingHistoryList(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        $uservehicles = VehicleDetail::where('user_id', $request->user_id)->get();
        //print_r($uservehicles);die;
        if(!empty($uservehicles[0])){
            foreach($uservehicles as $value){
                $ride_id = $value->id;
                $details111 = Bookings::where('ride_id', $ride_id)->where('date', $request->date)->get();
                
                $totalearning = Bookings::where('ride_id',$ride_id)->where('date', $request->date)->sum('amount');
                $details = $details111;
            }
            $totaljobs = count($details);
              // print_r($details);die;
              if(!empty($details[0])){
            
            $data = array(
                  'user_id' => $request->user_id,
                  'total_earning' => $totalearning,
                  'total_jobs' => $totaljobs
                );
               // print_r($data);die;
            foreach($details as $val){
               // print_r($val->search_id);die;
                $userdetails = User::where('id', $val->user_id)->first();
                $name = $userdetails->name;
                $address = SearchRide::where('id', $val->search_id)->first();
                $pickup_location = $address->pickup_address;
                $dropoff_location = $address->destination_address;
                if(!empty($userdetails->profile_image)){
                    $profile = url('images/profile_picture').'/'.$userdetails->profile_image;
                }else{
                    $profile = null;
                }
                $booking_data[] = array(
                       'booking_id' => $val->id,
                       'user_id' => $val->user_id,
                       'username' => $name,
                       'profile_image' => $profile,
                       'booking_number' => $val->ticket,
                       'amount' => $val->amount,
                       'distance' => null,
                       'pickup_address' => $pickup_location,
                       'dropoff_location' => $dropoff_location
                    );
            }  
             $newdata[] = array_merge($data, ['booking_history' => $booking_data]);
             $success = $newdata;
             return $this->sendResponse($success, 'Booking history fetched successfully.');    
        }else{
           return $this->sendError('Unauthorised.', ['error'=>'Sorry, booking history not found!!']); 
        }
            
            
        }else{
           return $this->sendError('Unauthorised.', ['error'=>'Sorry, vehicle not found for this user!!']); 
        }
       
        
        
    }
    
    
    /**
     * function for sending push notification
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function pushNotification(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
         $url = 'https://fcm.googleapis.com/fcm/send';
        
         $api_key = 'AAAA25xGx8Q:APA91bHHHC56ua6cTZJ4KkZYOIfNrI7AihnefAOnxtVRU6rX0SDfhZIrajJkj6EhhQo0m7TVB-Mqph7i-jIWVQKzl_UzrT3ILBT2Nuo56z1gjP1EEvtObXdfDeENpVCV0wz3SBBqJ58I';
                        
           $tokendetails = User::where('id',$request->user_id)->first();
          // print_r($tokendetails);die;
           if(empty($tokendetails)){
                return $this->sendError('Unauthorised.', ['error'=>'No fcm token found for this user id.']); 
           }else{
         
            $userfcm = $tokendetails->device_id;
           // print_r($userfcm);die;
            $title = $request->title;
            $body = $request->message;
            $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
            $arrayToSend = array('to' => $userfcm, 'notification' => $notification,'priority'=>'high');
            $json = json_encode($arrayToSend);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $api_key;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
           
           $input = $request->all();
           $notification = Notification::create($input);
            // return $response; 
            
           }
        
     } 
     
    /**
     * function for fetch notifications of user
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function fetchNotification(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }  
        
        $notification = Notification::where('user_id', $request->user_id)->get();
        if(!empty($notification[0])){
            foreach($notification as $val){
                $details = User::where('id', $val->user_id)->first();
                $name = $details->name;
                $data[] = array(
                     'user_id' => $val->user_id,
                     'user_name' => $name,
                     'title' => $val->title,
                     'message' => $val->message
                    );
            }
            $success = $data;
             return $this->sendResponse($success, 'Notifications fetched successfully.'); 
        }else{
          return $this->sendError('Unauthorised.', ['error'=>'Sorry, no notification found for this user!!']);   
        }
     } 
     
     
      /**
     * function for fetching payment history
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function paymentHistory(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $bookings = Bookings::where('user_id',$request->user_id)->get();
        $totaljobs = count($bookings);
        $totalearning = Bookings::where('user_id',$request->user_id)->sum('amount');
        if(!empty($bookings[0])){
               $data = array(
                  'user_id' => $request->user_id,
                  'total_earning' => $totalearning,
                  'total_jobs' => $totaljobs
                );
              //  print_r($data);die;
            foreach($bookings as $val){
                $userdata11 = VehicleDetail::where('id', $val->ride_id)->first();
               // print_r($userdata11->user_id);die;
                $userdata = User::where('id', $userdata11->user_id)->first();
                $name = $userdata->name;
                if(!empty($userdata->profile_image)){
                    $profile = url('images/profile_picture').'/'.$userdata->profile_image;
                }else{
                    $profile = null;
                }
                $paymentdata[] = array(
                      'user_id' => $userdata11->user_id,
                      'name' => $name,
                      'profile_image' => $profile,
                      'booking_number' => $val->ticket,
                      'amount' => $val->amount
                    );
            } 
           
             $newdata[] = array_merge($data, ['payment_history' => $paymentdata]);
             $success = $newdata;
             return $this->sendResponse($success, 'Payment history fetched successfully.');     
        }else{
           return $this->sendError('Unauthorised.', ['error'=>'Sorry, payment history not found for this user!!']);   
        }
        
     }
     
     
     /**
     * function for fetching payment history
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function loginStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'status' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $res = LoginStatus::where('user_id', $request->user_id)->first();
        if(empty($res)){
            $input = $request->all();
            $result = LoginStatus::create($input);
            $success = $result;
            return $this->sendResponse($success, 'Login status created successfully.'); 
        }else{
            $data = ['status' => $request->status];
            $result = LoginStatus::where('user_id', $request->user_id)->update($data);
            if($request->status == "logout"){
            $success = $result;
            return $this->sendResponse($success, 'Logout successfully.'); 
            }else{
               $userdetails = User::where('id', $request->user_id)->first();
             //  print_r($userdetails->user_type);die;
               if($userdetails->user_type == "driver"){
                   $res = VehicleDetail::where('user_id', $request->user_id)->get();
                   foreach($res as $details){
                       $data = array(
                            'user_id' => $details->user_id,
                            'vehicle_name' => $details->vehicle_name,
                            'vehicle_number' => $details->vehicle_number
                           );
                     $booking_details[] = Bookings::where('ride_id', $details->id)->where('date', date('Y-m-d'))->where('status', '!=', "Cancelled")->get()->toArray();
                     
                   }
                    $arraySingle = call_user_func_array('array_merge', $booking_details);
                 //  print_r($arraySingle);die;
                   foreach($arraySingle as $booked){
                     //  print_r($booked);die;
                       $userdetails = User::where('id',$booked['user_id'])->first();
                       $name = $userdetails->name;
                      // $profile = $userdetails->profile_image;
                        if(!empty($userdetails->profile_image)){
                            $profile = url('images/profile_picture').'/'.$userdetails->profile_image;
                        }else{
                            $profile = null;
                        }
                    $address = SearchRide::where('id',$booked['search_id'])->first();    
                     $pickup_address = $address->pickup_address;
                     $dropoff_location = $address->destination_address;
                     $totalseats = $address->no_of_seat;
                     $newdata11[] = array_merge($booked, ['username' => $name, 'profile_picture' => $profile, 'pickup_address' => $pickup_address, 'dropoff_location' => $dropoff_location, 'no_of_seat' => $totalseats]);
                   }
                 // print_r($newdata11);die;
                 if(!empty($arraySingle)){
                   $newdata[] = array_merge($data, ['booking_details' => $newdata11]);
                   $success = $newdata;
                   return $this->sendResponse($success, 'Booking list for today fetched successfully.'); 
                 }else{
                     return $this->sendError('Unauthorised.', ['error'=>'Sorry, no booking found for today!!']);
                 }
               }else{
                 return $this->sendError('Unauthorised.', ['error'=>'Sorry, user id does not exist for user type driver!!']);  
               }
            }
        }
        
        
     }
     
     
     
      /**
     * function for fetching users last login status
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function fetchLoginStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $res = LoginStatus::where('user_id', $request->user_id)->first();
        if(!empty($res)){
           if($res->status == "login"){
                $userdetails = User::where('id', $request->user_id)->first();
              // print_r($userdetails->user_type);die;
               if($userdetails->user_type == "driver"){
                   $res = VehicleDetail::where('user_id', $request->user_id)->get();
                   foreach($res as $details){
                      
                       $data = array(
                            'user_id' => $details->user_id,
                            'vehicle_name' => $details->vehicle_name,
                            'vehicle_number' => $details->vehicle_number
                           );
                           // print_r($details->id);die;
                           $rideid = $details->id;
                           $date = date('Y-m-d');
                     $booking_details[] = Bookings::where('ride_id', $details->id)->where('date', date('Y-m-d'))->where('status', '!=', "Cancelled")->get()->toArray();
                   }
                  // print_r($booking_details);die;
                  $arraySingle = call_user_func_array('array_merge', $booking_details);
                   //print_r($arraySingle);die;
                   foreach($arraySingle as $booked){
                      // print_r($booked);die;
                       $userdetails = User::where('id',$booked['user_id'])->first();
                       $name = $userdetails->name;
                      // $profile = $userdetails->profile_image;
                        if(!empty($userdetails->profile_image)){
                            $profile = url('images/profile_picture').'/'.$userdetails->profile_image;
                        }else{
                            $profile = null;
                        }
                    $address = SearchRide::where('id',$booked['search_id'])->first();    
                     $pickup_address = $address->pickup_address;
                     $dropoff_location = $address->destination_address;
                     $totalseats = $address->no_of_seat;
                     $newdata11[] = array_merge($booked, ['username' => $name, 'profile_picture' => $profile, 'pickup_address' => $pickup_address, 'dropoff_location' => $dropoff_location, 'no_of_seat' => $totalseats]);
                   }
                 // print_r($newdata11);die;
                  if(!empty($arraySingle)){
                   $newdata[] = array_merge($data, ['booking_details' => $newdata11]);
                   $success = $newdata;
                   return $this->sendResponse($success, 'Booking list for today fetched successfully.'); 
               }else{
                     return $this->sendError('Unauthorised.', ['error'=>'Sorry, no booking found for today!!']);
                 }
               }else{
                 return $this->sendError('Unauthorised.', ['error'=>'Sorry, user id does not exist for user type driver!!']);  
               }
           }else{
               $result1[] = $res;
               $success = $result1;
               return $this->sendResponse($success, 'Login status fetched successfully.');
           }
        }else{
            return $this->sendError('Unauthorised.', ['error'=>'Sorry, login status not found for this user, please login!!']); 
        }
        
     } 
     
     
     
      /**
     * function for updating users boarding status
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function updateBoardingStatus(Request $request){
          $validator = Validator::make($request->all(), [
           // 'user_id' => 'required',
            'booking_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $data = ['status' => 'Completed'];
        $res = Bookings::where('id', $request->booking_id)->update($data);
        $success = $res;
         return $this->sendResponse($success, 'Boarding status updated successfully');
     }
     
     
     public function userLastBookingDetails(Request $request){
          $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        } 
        
        $res = Bookings::where('user_id',$request->user_id)->orderBy('id','DESC')->take(1)->get();
        //print_r($res);die;
        if(!empty($res[0])){
            foreach($res as $val){
                $rideid = $val['ride_id'];
                $vehicledetails = VehicleDetail::where('id', $rideid)->first();
                $driverdetails = User::where('id',$vehicledetails->user_id)->first();
               // print_r($driverdetails->name);
                if(!empty($driverdetails->profile_image)){
                    $profile = url('images/profile_picture').'/'.$driverdetails->profile_image;
                }else{
                    $profile = null;
                }
                $data11[] = array(
                        'booking_id' => $val['id'],
                        'search_id' => $val['search_id'],
                        'user_id' => $val['user_id'],
                        'ride_id' => $val['ride_id'],
                        'ticket' => $val['ticket'],
                        'status' => $val['status'],
                        'distance' =>$val['distance'],
                        'amount' =>$val['amount'],
                        'date' => $val['date'],
                        'vehicle_number' => $vehicledetails->vehicle_number,
                        'driver_name' => $driverdetails->name,
                        'driver_image' => $profile,
                        'driver_contact_number' => $driverdetails->country_code.$driverdetails->phone_number,
                    );
            }
        $success = $data11;
        return $this->sendResponse($success, 'User last booking status fetched successfully');
        }else{
         return $this->sendError('Unauthorised.', ['error'=>'Sorry, No booking found for this user!!']);
        }
     }
}