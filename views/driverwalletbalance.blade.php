@extends('layouts.master')

@section('title', 'Driver Wallet Balance ')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Driver Wallet Balance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="#">All Users</a></li>
              <li class="breadcrumb-item active">Driver Wallet Balance</li>
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
                <h3 class="card-title">Driver Wallet Balance</h3>
                
              </div>

             <?php if(!empty($details->balance)){ ?>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Wallet Balance</label>
                    <input type="text" name="name" class="form-control" value="{{$details->balance}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Expiry Date</label>
                    <input type="text" name="name" class="form-control" value="{{$details->balance_expiry_date}}" readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Default Payment Method</label>
                    <input type="text" name="name" class="form-control" value="{{$details->default_payment_method}}" readonly>
                  </div>
                 <?php }else{ ?>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Wallet Balance</label>
                    <input type="text" name="name" class="form-control" value="No value available" readonly>
                  </div>
                 
                 <?php } ?>
                 
                    
                   
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