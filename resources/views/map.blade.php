@extends('layouts.app')
@section('content')
<!-- <button class="btn-lg btn-info" style="z-index: 5;position: absolute;left: 90%;">Hitung Naive Bayes</button> -->
<div style="height: 1000px;">
	{!! Mapper::render(0) !!}
</div>
<!-- Modal -->

<div style="position: fixed;top: 40px;left: 0;z-index: 5;width: 100%;height: 100%;" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Naive Bayes Form</h5>
        <button type="button" class="close btnclose" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form class="form" action="{!! route('position.store') !!}" method ="post" id="form-add">
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="staticEmail2" class="sr-only">Soil</label>
					<select name="soil" id="soil" class="form-control">
						<option value="AN">AN</option>
						<option value="AR">AR</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="staticEmail2" class="sr-only">Slope</label>
					<input type="text" class="form-control" id="slope" name="slope" placeholder="Slope">
				</div>
				<div class="form-group col-md-4">
					<label for="staticEmail2" class="sr-only">Rain Fall</label>
					<input type="text" class="form-control" id="rainFall" name="rainFall" placeholder="Rain Fall">
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="staticEmail2" class="sr-only">Latitude</label>
					<input type="text" class="form-control" id="lat" name="lat" placeholder="Latitude">
				</div>
				<div class="form-group col-md-6">
					<label for="inputPassword2" class="sr-only">Longitude</label>
					<input type="text" class="form-control" id="long" name="long" placeholder="Longitude">
				</div>
			</div>
			<meta name="csrf-token" content="{{ csrf_token() }}">
      	</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary btnclose" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary" id="submit" name="submit">Naive Bayes </button>
		</div>
	  </form>
    </div>
  </div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#exampleModal').hide();
	});
	dynamicallyCreatedMarkers = [];

	function addEventListener(map)
	{
		google.maps.event.addListener(map, 'click', function (e) {
			// var marker = new google.maps.Marker({
			// 	position: e.latLng,
			// 	map: map
			// });

			map.panTo(e.latLng);
			dynamicallyCreatedMarkers.push({
				position: e.latLng,
			});
			$('#lat').val(e.latLng.lat);
			$('#long').val(e.latLng.lng);
			$('#exampleModal').show();
			console.log();
		});
		
	}

	$('#submit').click(function() {
		var param = {
			soil : $('#soil').val(),
			slope : $('#slope').val(),
			rainFall : $('#rainFall').val(),
			latitude : $('#lat').val(),
			longitude : $('#long').val(),
			row : "235",
			colum:"190",
			area_id:"0",
		}
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: '/bencana/naiveBayesManual',
			data: param,
			success: function(response)
			{
				console.log(response)
				location.reload();
			}
		});
	});
	$('.btnclose').click(function () {
		$('#exampleModal').hide();
		$('#soil').val("");
		$('#slope').val("");
		$('#rainFall').val("");
		$('#lat').val("");
		$('#long').val("");
	})

</script>
@endsection