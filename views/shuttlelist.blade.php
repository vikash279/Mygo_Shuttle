@extends('layouts.master')

@section('title', 'Shuttle Routes')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Shuttle Routes</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Shuttle Routes</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
                  	@if ($message = Session::get('success'))

                <div class="alert alert-success alert-block">
                
                    <button type="button" class="close" data-dismiss="alert">×</button>    
                
                    <strong>{{ $message }}</strong>
                
                </div>
                
                @endif
                
                  
                
                @if ($message = Session::get('error'))
                
                <div class="alert alert-danger alert-block">
                
                    <button type="button" class="close" data-dismiss="alert">×</button>    
                
                    <strong>{{ $message }}</strong>
                
                </div>
                
                @endif

  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All Shuttle's Details</h3>
                <!--<a href="#" style="float:right">Add Category</a>-->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered" id="cat">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Shuttle Name</th>
                      <th>Shuttle Number</th>
                      <th>Shuttle Brand</th>
                      <th>Shuttle Model</th>
                      <th>Shuttle Year</th>
                      <th style="width: 40px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <?php $x = 1; ?> 
                     @foreach($details as $val) 
                    
                    <tr>
                      <td>{{$x++}}</td>
                      <td>{{$val->vehicle_name}}</td>
                      <td>{{$val->vehicle_number}}</td>
                      <td>{{$val->vehicle_brand}}</td>
                      <td>{{$val->vehicle_model}}</td>
                      <td>{{$val->vehicle_year}}</td>
                      <td><form action="#" method="POST">
                       <a class="btn btn-info btn-sm" href="{{ url('setshuttleroutes',$val->id)}}">Set Routes</a>
                       <a class="btn btn-info btn-sm" href="{{ url('fetchshuttleroutes',$val->id)}}">Check Routes</a>
                      
                       @csrf
                      <!--<button type="submit" class="btn btn-danger btn-sm">Deactivate</button>-->
                      </form></td>
                      
                    </tr>
                     @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <!--<div class="card-footer clearfix">-->
              <!--  <ul class="pagination pagination-sm m-0 float-right">-->
              <!--    <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>-->
              <!--    <li class="page-item"><a class="page-link" href="#">1</a></li>-->
              <!--    <li class="page-item"><a class="page-link" href="#">2</a></li>-->
              <!--    <li class="page-item"><a class="page-link" href="#">3</a></li>-->
              <!--    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>-->
              <!--  </ul>-->
              <!--</div>-->
            </div>
            <!-- /.card -->

            
            <!-- /.card -->
          </div>
          <!-- /.col -->
         
          <!-- /.col -->
        </div>
       {!! $details->links() !!}
        <!-- /.row -->
        
        <!-- /.row -->
      
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    
    </section>
    @endsection