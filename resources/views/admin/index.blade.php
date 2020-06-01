@extends('admin.app')

@section('section')

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Dashboard</h1>
			</div>
		</div>

		<div class="panel panel-container">
			<div class="row">
				<div class="col-xs-6 col-md-3 col-lg-4 no-padding">
					<div class="panel panel-teal panel-widget border-right">
						<div class="row no-padding"><em class="fa fa-xl fa-user color-blue"></em>
							<div class="large">{{\App\User::all()->count()}}</div>
							<div class="text-muted">All users</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-4 no-padding">
					<div class="panel panel-blue panel-widget border-right">
						<div class="row no-padding"><em class="fa fa-xl fa-users color-orange"></em>
							<div class="large">{{\App\Party::all()->count()}}</div>
							<div class="text-muted">All parties</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3 col-lg-4 no-padding">
					<div class="panel panel-orange panel-widget border-right">
						<div class="row no-padding"><em class="fa fa-xl fa-plus-circle color-teal"></em>
							<div class="large">{{\App\User::where('created_at', '>=', date('Y-m-d'))->count()}}</div>
							<div class="text-muted">New Users</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <!--
	<script>
		window.onload = function () {
	var chart1 = document.getElementById("line-chart").getContext("2d");
	window.myLine = new Chart(chart1).Line(lineChartData, {
	responsive: true,
	scaleLineColor: "rgba(0,0,0,.2)",
	scaleGridLineColor: "rgba(0,0,0,.05)",
	scaleFontColor: "#c5c7cc"
	});
};
	</script> -->
    @endsection
