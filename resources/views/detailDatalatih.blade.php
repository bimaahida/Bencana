@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                Detail Data Latih
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
    </div>
</div>
<script>
    $(function() {
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            select: false,
            ajax: '{!! route('bencana.datatableslatih',['id' => $id ]) !!}',
            columns: [
                { data: 'rainfall', name: 'rainfall' },
                { data: 'soil', name: 'soil' },
                { data: 'slope', name: 'slope' },
                { data: 'status', name: 'status' },
            ],
        });
    });
</script>
@endsection
