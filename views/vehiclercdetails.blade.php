@extends('layouts.master')

@section('title', 'Vehicle RC Details ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Vehicle RC Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ url('vehicles')}}">Vehicle Registration Detail</a></li>
              <li class="breadcrumb-item active">Vehicle RC Details</li>
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
                <h3 class="card-title">Vehicle RC Details</h3>
                
              </div>

             
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1"> RC Number</label>
                    <input type="text" name="name" class="form-control" value="{{$details->rc_number}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  
                 
                  <?php if(!empty($details->rc_front_image)){
                     $frontimage = $details->rc_front_image;
                     }else{
                     $frontimage = "";
                     }  
                     ?>
                  <div class="form-group">
                    <label for="exampleInputFile">RC Front Image</label>
                    <img src="{{$frontimage}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$frontimage}}" readonly>
                        
                    </div>
                    
                    <?php if(!empty($details->rc_back_image)){
                     $backimage = $details->rc_back_image;
                     }else{
                     $backimage = "";
                     }  
                     ?>
                     <div class="form-group">
                    <label for="exampleInputFile">RC Back Image</label>
                    <img src="{{$backimage}}" id="img1" class="img1" height="100px" width="100px">
                    <input type="text" name="profile_image" class="form-control" value="{{$backimage}}" readonly>
                        
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