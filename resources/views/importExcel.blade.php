@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
					Import Excel Curah Hujan
				</div>
                <div class="card-body">
					<form id="uploadForm" action="{{ URL::to('bencana/importAction') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <h5>Maximum Upload <span class="badge badge-secondary">20 File / 15 MB</span></h5>
						<input type="file" name="import_files[]" id="import_files" multiple="multiple" />
                </div>
				<div class="card-footer">
					<button class="btn btn-primary">Import File</button>
					</form>
				</div>
            </div>
        </div>
    </div>
</div>
@endsection