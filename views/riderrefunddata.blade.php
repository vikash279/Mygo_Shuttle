@extends('layouts.master')

@section('title', 'Rider Refund Data')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Rider Refund Data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="#">All Users</a></li>
              <li class="breadcrumb-item active">Rider Refund Data</li>
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
                <h3 class="card-title">Rider Refund Data</h3>
                
              </div>

             <?php if(!empty($details->refund_amount)){ ?>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Refund Amount</label>
                    <input type="text" name="name" class="form-control" value="{{$details->refund_amount}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Refund Status</label>
                    <input type="text" name="name" class="form-control" value="{{$details->refund_status}}" readonly>
                  </div>
                  
                 <?php }else{ ?>
                  <div class="form-group">
                    <label for="exampleInputEmail1"> Refund Amount</label>
                    <input type="text" name="name" class="form-control" value="No value available" readonly>
                  </div>
                 
                 <?php } ?>
                 
                    
                   
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