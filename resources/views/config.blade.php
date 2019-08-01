@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Data Latih
                </div>
                <div class="card-body">
                <table class="table table-bordered" id="data-table">
                    <thead>
                        <tr>
                            <th>RainFall</th>
                            <th>Soil</th>
                            <th>Slope</th>
                            <!-- <th>Latitude</th>
                            <th>Longitude</th> -->
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
					Import Excel Data Latih
				</div>
                <div class="card-body">
					<form id="uploadForm" action="{{ URL::to('bencana/importdatalatih') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <h5>Maximum Upload <span class="badge badge-secondary">1 File / 15 MB</span></h5>
						<input type="file" name="import_files[]" id="import_files" multiple="multiple" />
                </div>
				<div class="card-footer">
					<button class="btn btn-primary">Import File</button>
					</form>
				</div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Data Model
                        </div>
                        <div class="card-body">
                        <table class="table table-bordered" id="data-table-model">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                    </div>
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
            ajax: '{!! route('bencana.datatableslatih') !!}',
            columns: [
                { data: 'rainfall', name: 'rainfall' },
                { data: 'soil', name: 'soil' },
                { data: 'slope', name: 'slope' },
                { data: 'status', name: 'status' },
                
            ],
        });
    });

    $(function() {
        $('#data-table-model').DataTable({
            processing: true,
            serverSide: true,
            select: false,
            ajax: '{!! route('bencana.dataTablesmodel') !!}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'date', name: 'date' },
                { data: null, render: function(data){
                    var detail_button = '<a href="' + data.detail_url + '" class="btn btn-warning" role="button" aria-pressed="true" style="margin-right: 10px;">Detail</a>';
                    return detail_button;
                }},
            ],
        });
    });
</script>
@endsection
