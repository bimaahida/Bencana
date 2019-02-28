@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
        @if(!empty($errors->all()))
        <div class="alert alert-danger alert-dismissible fade show">  
            <button type="button" class="close" data-dismiss="alert">×</button>  
            <h4 class="alert-heading">ERROR!</h4>
            @foreach($errors->all() as $message)
                {!! $message."<br>" !!}
            @endforeach
        </div>    
        @endif
        <div class="card">
            <div class="card-header">
                {!! $title !!}
            </div>
            <div class="card-body">
                <form @if(empty($param)) action="{!! action('WilayahController@store') !!}" @else action="{!! action('WilayahController@update',['id'=>$data->id]) !!}" @endif method="{!! $metod !!}">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="@if(!empty($data)){!! $data->name; !!}@endif" required>
                    </div>
                    @if(!empty($param))
                    <input name="_method" type="hidden" value="PUT">
                    @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-primary">{!! $button !!}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection