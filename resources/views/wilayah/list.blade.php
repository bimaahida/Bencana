@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <a href="{!! route('wilayah.create') !!}" class="btn btn-success" role="button" aria-pressed="true" style="margin-bottom: 25px;">Add Area</a>
            <br>
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
					Table WIlayah
				</div>
                <div class="card-body">
                <table class="table table-bordered" id="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
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
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            select: false,
            ajax: '{!! route('wilayah.datatables') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: null, render: function(data){
                    var detail_button = '<a href="' + data.detail_url + '" class="btn btn-warning" role="button" aria-pressed="true" style="margin-right: 10px;">Detail</a>';
                    var edit_button = '<a href="' + data.edit_url + '" class="btn btn-primary" role="button" aria-pressed="true" style="margin-right: 10px;">Edit</a>';
                    var delete_button = '<form action="' + data.delete_url + '" method="POST" style="display: contents;"><input type="hidden" name="_method" value="delete">{{csrf_field()}}<button type="submit" class="btn btn-danger" style="margin-right: 10px;" >Delete</button>';
                    return detail_button+edit_button + delete_button;
                }},
            ],
        });
    });
</script>
@endsection