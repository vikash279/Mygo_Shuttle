@extends('layouts.master')

@section('title', 'Driver DL Details ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Driver DL Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="#">All Users</a></li>
              <li class="breadcrumb-item active">Driver DL Details</li>
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
                <h3 class="card-title">Driver DL Details</h3>
                
              </div>

             
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1"> DL Number</label>
                    <input type="text" name="name" class="form-control" value="{{$details->dl_number}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> DL Expiry</label>
                    <input type="text" name="name" class="form-control" value="{{$details->dl_expiry}}" readonly>
                  </div>
                 
                  <?php if(!empty($details->dl_front_image)){
                     $frontimage = $details->dl_front_image;
                     }else{
                     $frontimage = "";
                     }  
                     ?>
                  <div class="form-group">
                    <label for="exampleInputFile">DL Front Image</label>
                    <img src="{{$frontimage}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$frontimage}}" readonly>
                        
                    </div>
                    
                    <?php if(!empty($details->dl_back_image)){
                     $backimage = $details->dl_back_image;
                     }else{
                     $backimage = "";
                     }  
                     ?>
                     <div class="form-group">
                    <label for="exampleInputFile">DL Bacl Image</label>
                    <img src="{{$backimage}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$backimage}}" readonly>
                        
                    </div>
                  </div>
                 
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <a href="{{ url('drivers')}}"><button type="btn" class="btn btn-primary">Back</button></a>
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