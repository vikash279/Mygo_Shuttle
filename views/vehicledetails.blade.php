@extends('layouts.master')

@section('title', 'Vehicle Details ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Vehicle Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ url('vehicles')}}">Vehicle Registration Details</a></li>
              <li class="breadcrumb-item active">Vehicle Details</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Vehicle Details</h3>
                
              </div>

             
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Vehicle Name</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_name}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <!--<div class="form-group">-->
                  <!--  <label for="exampleInputEmail1"> User Name</label>-->
                  <!--  <input type="text" name="name" class="form-control" value="#" readonly>-->
                  <!--</div>-->
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Number</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_number}}" readonly>
                  </div>
                   <div class="form-group">
                    <label for="exampleInputEmail1"> Brand</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_brand}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Model</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_model}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Year</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_year}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Color</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_color}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Booking Type</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_booking_type}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Nearby Pickup Address</label>
                    <input type="text" name="name" class="form-control" value="{{$details->nearby_pickup_address}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Timing</label>
                    <input type="text" name="name" class="form-control" value="{{$details->timing}}" readonly>
                  </div>
                  <?php if(!empty($details->vehicle_image)){
                     $image = url('images/vehicle_picture').'/'.$details->vehicle_image;
                     }else{
                     $image = "";
                     }  
                     ?>
                  <div class="form-group">
                    <label for="exampleInputFile">Image</label>
                    <img src="{{$image}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$image}}" readonly>
                        
                    </div>
                  </div>
                 
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <a href="{{ url('vehicles')}}"><button type="btn" class="btn btn-primary">Back</button></a>
                </div>
              
            </div>
            <!-- /.card -->

            
            <!-- /.card -->
          </div>
          <!-- /.col -->
         
          <!-- /.col -->
        </div>
        <!-- /.row -->
        
        <!-- /.row -->
      
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    @endsection