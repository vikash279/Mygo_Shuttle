@extends('layouts.master')

@section('title', 'Set Shuttle Route')

@section('content')


 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Set Shuttle Route</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ url('shuttlelist')}}">Shuttle Routes</a></li>
              <li class="breadcrumb-item active">Set Shuttle Route</li>
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
                <h3 class="card-title">Set Shuttle Route</h3>
                
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

             <form action="{{ url('saveshuttleroutes') }}" method="post" enctype="multipart/form-data">
                  @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Shuttle Name</label>
                    <input type="text" name="name" class="form-control" value="{{$details->vehicle_name}}" readonly>
                    <input type="hidden" name="id" class="form-control" value="{{$details->id}}">
                  </div>
                  
                   <label for="exampleInputEmail1">Routes</label>
                    <div class="multi-field-wrapper">
                      <div class="multi-fields">
                        <div class="multi-field">
                          <input type="text" name="start_point[]" placeholder="start address">
                          <input type="text" name="end_point[]" placeholder="end address">
                          <input type="number" name="distance[]" placeholder="distance">
                          <button type="button" class="remove-field btn btn-danger btn-sm">Remove</button>
                        </div>
                      </div></br>
                    <button type="button" class="add-field btn btn-primary btn-sm">Add More Routes</button>
                  </div>
                 
                
                 
                    
                   
                  </div>
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                 <button type="submit" class="btn btn-primary">Submit</button>
                
                </div>
               </form>
               
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
      <script>
        $('.multi-field-wrapper').each(function() {
            var $wrapper = $('.multi-fields', this);
            $(".add-field", $(this)).click(function(e) {
                $('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
            });
            $('.multi-field .remove-field', $wrapper).click(function() {
                if ($('.multi-field', $wrapper).length > 1)
                    $(this).parent('.multi-field').remove();
            });
        });
      </script>
    </section>
    @endsection