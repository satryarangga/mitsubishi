@extends('layout.main')

@section('title', 'Home')

@section('content')

<section class="content">
	<div class="col-md-12">
		{!! session('displayMessage') !!}
		<div class="box">
            <div class="box-header">
              <a href="{{route($page.'.create')}}" class="btn btn-info">Create {{ucwords(str_replace('-',' ', $page))}}</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                  <th>Customer</th>
                  <th>Sales Price</th>
                  <th>DP (%)</th>
                  <th>DP (IDR)</th>
                  <th>Total Down Payment</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($result as $key => $val)
                <tr>
                <td>{{$val->customer_name}}</td>
                <td>{{moneyFormat($val->price)}}</td>
                <td>{{$val->dp_percentage}}%</td>
                <td>{{moneyFormat($val->dp_amount)}}</td>
                <td>{{moneyFormat($val->total_dp)}}</td>
                <td>
                	<div class="btn-group">
	                  <button type="button" class="btn btn-info">Action</button>
	                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
	                    <span class="caret"></span>
	                    <span class="sr-only">Toggle Dropdown</span>
	                  </button>
	                  <ul class="dropdown-menu" role="menu">
                      <li><a href="{{ route($page.'.edit', ['id' => $val->id]) }}">Edit</a></li>
	                    <li class="divider"></li>
	                    <li>
	                    	<form class="deleteForm" method="post" action="{{route("$page.destroy", ['id' => $val->id])}}">
	                    		{{csrf_field()}}
	                    		<button onclick="return confirm('You will delete this {{$page}}, continue')" type="submit">Delete</button>
	                    		{{ method_field('DELETE') }}
	                    	</form>
	                    </li>
	                  </ul>
                	</div>
                </td>
                </tr>
                @endforeach
              </table>
            </div>
            <!-- /.box-body -->
          </div>
	</div>
</section>

@endsection