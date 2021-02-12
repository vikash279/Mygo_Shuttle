<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Session;
use Crypt;
use Validator;
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
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;

class UserController extends Controller
{
    
    /**
     * function for loading login page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('login');
    }
    
    /**
     * function for loading dashboard page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
    public function dashBoard(){
        return view('dashboard');
    }
    
    
    /**
     * function for loading login page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function login(Request $request){
        // dd($request);die;
    
        $userdata = \DB::table('admins')->select('*')->where('email', $request->email)->where('status', 1)->first();
       // print_r($userdata->email);die;
        if(!empty($userdata->email)){
       if($userdata->password == $request->password){
          // print_r("login done");die;
           
            $request->session()->put('email',$userdata->email);
			$request->session()->put('admin_id',$userdata->id);
		
		//	return redirect('dashBoard');
			return redirect()->route('dashboard');
       }else{
          // print_r("wrong");die;
           return back()->with('error','wrong credentials!');
       }
        }else{
           return back()->with('error','email does not exist!'); 
        }
     }
     
     /**
     * function for loading driver details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverDetails(){
         $details = User::where('user_type','driver')->latest()->paginate(10);
        // print_r($details);die;
         return view('drivers',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);
       
     }
     
     
     /**
     * function for loading rider details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function riderDetails(){
         $details = User::where('user_type','rider')->latest()->paginate(10);
         return view('riders',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);  
     }
     
     
     /**
     * function for loading driver details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverData(Request $request,$id){
        // print_r($id);die;
         $details = User::where('id', $id)->first();
         return view('driverdetails', compact('details'));
     }
     
      /**
     * function for loading driver DL details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverDLData(Request $request,$id){
        $details = \DB::table('user_driving_licence')->select('*')->where('user_id', $id)->first(); 
        return view('driversdldata', compact('details'));
     }
     
     
      /**
     * function for loading driver wallet balance details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverWalletBalance(Request $request,$id){
         $details = WalletBalance::where('user_id', $id)->first();
        // print_r($details);die;
         return view('driverwalletbalance', compact('details'));
     }
     
     /**
     * function for loading rider details page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function riderData(Request $request,$id){
         $details = User::where('id', $id)->first();
         return view('riderdetails', compact('details')); 
     }
     
     
      /**
     * function for loading rider refunds detailed page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function riderRefundData(Request $request,$id){
         $details = \DB::table('user_refunds')->select('*')->where('user_id', $id)->first(); 
        return view('riderrefunddata', compact('details')); 
     }
     
     /**
     * function for loading vehicle details table page
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehiclesDetails(){
         $details = VehicleDetail::latest()->paginate(10);
         return view('vehicles',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);   
     }
     
     
      /**
     * function for loading vehicle details page by vehicle id
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehicleDetails(Request $request,$id){
       $details =  VehicleDetail::where('id',$id)->first(); 
      // print_r($details);die;
        // foreach($details as $val){
        //   // print_r($val);die;
        //      $userdetails = User::where('id', $val->user_id)->first();
        //      $username = $userdetails->name;
        //  }
        // print_r($username);die;
       return view('vehicledetails',compact('details'));
     }
     
     
      /**
     * function for loading vehicle rc details page by vehicle id
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehicleRCDetails(Request $request,$id){
          $details = \DB::table('user_rc_details')->select('*')->where('vehicle_id', $id)->first(); 
          return view('vehiclercdetails', compact('details')); 
     }
     
     
     /**
     * function for loading driver details page for showing bookings
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverBook(){
         $details = User::where('user_type','driver')->latest()->paginate(10);
        // print_r($details);die;
         return view('driversbook',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);
       
     }
     
     /**
     * function for loading vehicles details page for showing bookings
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function driverVehicles(Request $request,$id){
         $details = VehicleDetail::where('user_id',$id)->get();
        // print_r($details);die;
         return view('drivervehicleslist',compact('details'));
     }
     
     
      /**
     * function for loading bookings details page for vehicles
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function vehicleBookings(Request $request,$id){
         $details = Bookings::where('ride_id',$id)->latest()->paginate(10);
        // print_r($details);die;
         return view('vehiclebookings',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);
     }
     
     
    /**
     * function for loading bookings details page with address details
     * Vikash Rai
     * @return \Illuminate\Http\Response
     */
     public function bookingDetails(Request $request,$id){
         $details = SearchRide::where('id',$id)->first();
         return view('driverbookingdetails',compact('details'));
     }
     
     
     public function riderBooking(){
          $details = User::where('user_type','rider')->latest()->paginate(10);
        // print_r($details);die;
         return view('ridersbook',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);
     }
     
     public function riderBookingDetails(Request $request,$id){
         $details = Bookings::where('user_id',$id)->get();
         return view('riderbookingdetails',compact('details'));
     }
     
     
     public function bookingRideDetails(Request $request,$id){
         $details = SearchRide::where('id',$id)->first();
         return view('bookingridedetails',compact('details'));
     }
     
     
     public function shuttleList(){
         $details = VehicleDetail::latest()->paginate(10);
         return view('shuttlelist',compact('details'))->with('i', (request()->input('page', 1) - 1) * 10);   
     }
     
     
     public function setShuttleRoutes(Request $request,$id){
        // print_r($id);die;
          $details = VehicleDetail::where('id',$id)->first();
        // dd($details);die;
         return view('setshuttleroutes',compact('details'));
     }
     
     public function saveShuttleRoutes(Request $request){
         $id = $request->id;
         $start_address = $request->start_point;
         $end_address = $request->end_point;
         $distance = $request->distance;
         $cost = null;
         
         if(empty($start_address) and empty($end_address) and empty($distance)){
             return back()->with('error','All Fields Are Mandatory!');
         }else{
             for ($i = 0; $i < count($start_address); $i++) {

             $data = array(
                  'shuttle_id' => $id,
                  'start_address' => $start_address[$i],
                  'end_address' => $end_address[$i],
                  'distance' => $distance[$i],
                  'cost' => $cost
                 );
              $res = ShuttleRoute::create($data);     
             }
           // print_r($data);die;
          // $res = ShuttleRoute::create($data); 
           return back()->with('success','Shuttle Route Saved Successfully!');
         }
        
     }
     
     
     public function fetchShuttleRoutes(Request $request,$id){
         $details = ShuttleRoute::where('shuttle_id',$id)->get();
        // dd($details);die;
         return view('shuttleroutelist',compact('details'));
     }
     
}    