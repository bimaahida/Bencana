@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-12">
            @if(Session::has('alert-success'))
            <div class="alert alert-success alert-dismissible fade show">  
                <button type="button" class="close" data-dismiss="alert">×</button>  
                <h4 class="alert-heading">Succuss!</h4>
                {{ \Illuminate\Support\Facades\Session::get('alert-success') }}
            </div>    
            @endif
            @if(!empty($errors->all()))
            <div class="alert alert-danger alert-dismissible fade show">  
                <button type="button" class="close" data-dismiss="alert">×</button>  
                <h4 class="alert-heading">ERROR!</h4>
                {!! $errors->first() !!}
            </div>    
            @endif
            <div class="card">
                <div class="card-header">
					Detail Area
				</div>
                <div class="card-body">
                    <div style="height: 600px;">
                        {!! Mapper::render(0) !!}
                    </div>
                    <a href="#" id="add-btn" class="btn btn-success" role="button" aria-pressed="true" style="margin-bottom: 25px;margin-top: 20px;">Add Area</a>
                    <a href="#" id="cancel-btn" class="btn btn-danger" role="button" aria-pressed="true" style="margin-bottom: 25px;margin-top: 20px;">Cancel</a>
                    <form class="form-inline" action="{!! route('position.store') !!}" method ="post" id="form-add">
                        <div class="form-group mb-2">
                            <label for="staticEmail2" class="sr-only">Latitude</label>
                            <input type="text" class="form-control" id="latitude" value="Latitude">
                        </div>
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="inputPassword2" class="sr-only">Longitude</label>
                            <input type="text" class="form-control" id="longitude" placeholder="Longitude">
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <button type="submit" class="btn btn-primary mb-2">Add</button>
                    </form>
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#form-add').hide();
        $('#cancel-btn').hide();
        $('#add-btn').click(function(){
            $('#form-add').show(1000);
            $('#cancel-btn').show();
            $('#add-btn').hide();
        })
        $('#cancel-btn').click(function(){
            $('#form-add').hide(1000);
            $('#cancel-btn').hide();
            $('#add-btn').show();
        })

        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            select: false,
            ajax: '{!! route('wilayah.datatablesDetail',['id' => $id ]) !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'latitude', name: 'latitude' },
                { data: 'longitude', name: 'longitude' },
                { data: null, render: function(data){
                    var delete_button = '<form action="' + data.delete_url + '" method="POST" style="display: contents;"><input type="hidden" name="_method" value="delete">{{csrf_field()}}<button type="submit" class="btn btn-danger" style="margin-right: 10px;">Delete</button>';
                    return delete_button;
                }},
            ],
        });
    });
</script>
@endsection