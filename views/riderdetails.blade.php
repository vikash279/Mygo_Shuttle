@extends('layouts.master')

@section('title', 'Rider Details ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Rider Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="#">All Users</a></li>
              <li class="breadcrumb-item active">Rider Details</li>
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
                <h3 class="card-title">Rider Details</h3>
                
              </div>

             
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Name</label>
                    <input type="text" name="name" class="form-control" value="{{$details->name}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Email</label>
                    <input type="text" name="name" class="form-control" value="{{$details->email}}" readonly>
                  </div>
                   <div class="form-group">
                    <label for="exampleInputEmail1"> Phone</label>
                    <input type="text" name="name" class="form-control" value="{{$details->country_code}}{{$details->phone_number}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> DOB</label>
                    <input type="text" name="name" class="form-control" value="{{$details->dob}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Gender</label>
                    <input type="text" name="name" class="form-control" value="{{$details->gender}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> User Type</label>
                    <input type="text" name="name" class="form-control" value="{{$details->user_type}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Google UID</label>
                    <input type="text" name="name" class="form-control" value="{{$details->google_uid}}" readonly>
                  </div>
                  <?php if(!empty($details->profile_image)){
                     $image = url('images/profile_picture').'/'.$details->profile_image;
                     }else{
                     $image = "";
                     }  
                     ?>
                  <div class="form-group">
                    <label for="exampleInputFile">Profile Image</label>
                    <img src="{{$image}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$image}}" readonly>
                        
                    </div>
                  </div>
                 
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <a href="{{ url('riders')}}"><button type="btn" class="btn btn-primary">Back</button></a>
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