@extends('layouts.master')

@section('title', 'Ride Details ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Ride Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="#">All Bookings</a></li>
              <li class="breadcrumb-item active">Ride Details</li>
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
                <h3 class="card-title">Ride Details</h3>
                
              </div>

             
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Pickup Location</label>
                    <input type="text" name="name" class="form-control" value="{{$details->pickup_address}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Dropoff Location</label>
                    <input type="text" name="name" class="form-control" value="{{$details->destination_address}}" readonly>
                  </div>
                 <div class="form-group">
                    <label for="exampleInputEmail1"> Nearby Pickup Location</label>
                    <input type="text" name="name" class="form-control" value="{{$details->nearby_pickup_location}}" readonly>
                  </div>
                 <div class="form-group">
                    <label for="exampleInputEmail1"> Latitude</label>
                    <input type="text" name="name" class="form-control" value="{{$details->latitude}}" readonly>
                  </div>
                <div class="form-group">
                    <label for="exampleInputEmail1"> Longitude</label>
                    <input type="text" name="name" class="form-control" value="{{$details->longitude}}" readonly>
                  </div> 
                  <div class="form-group">
                    <label for="exampleInputEmail1">No Of Seats</label>
                    <input type="text" name="name" class="form-control" value="{{$details->no_of_seats}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Promo Code</label>
                    <input type="text" name="name" class="form-control" value="{{$details->promo_code}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Payment Method</label>
                    <input type="text" name="name" class="form-control" value="{{$details->payment_method}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Date</label>
                    <input type="text" name="name" class="form-control" value="{{$details->date}}" readonly>
                  </div>
                  
                  </div>
                 
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <a href="{{ url('driverbook')}}"><button type="btn" class="btn btn-primary">Back</button></a>
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