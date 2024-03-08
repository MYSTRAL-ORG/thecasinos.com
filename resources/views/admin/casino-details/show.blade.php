@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">CasinoDetail {{ $casinodetail->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/casino-details') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/casino-details/' . $casinodetail->id . '/edit') }}" title="Edit CasinoDetail"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('/admin/casinodetails' . '/' . $casinodetail->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete CasinoDetail" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $casinodetail->id }}</td>
                                    </tr>

                                    <tr><th>   Casino </th><td> {{  $casinodetail->name  . ' - '. $casinodetail->country_name . ' - '. $casinodetail->city_name }} </td></tr>
                                    <tr><th>   Actif </th><td>   @if( $casinodetail->actif ==1 ) True @else False @endif   </td></tr>

                                    <tr><th> Id Casino </th><td> {{ $casinodetail->id_casino }} </td></tr><tr><th> Title </th><td> {{ $casinodetail->title }} </td></tr><tr><th> Description </th><td> {{ $casinodetail->description }} </td></tr><tr><th> Sumup </th><td> {{ $casinodetail->sumup }} </td></tr><tr><th> Games </th><td> {{ $casinodetail->games }} </td></tr><tr><th> Fun Facts </th><td> {{ $casinodetail->fun_facts }} </td></tr><tr><th> Resume 1 Line </th><td> {{ $casinodetail->resume_1_line }} </td></tr><tr><th> Resume 2 Words </th><td> {{ $casinodetail->resume_2_words }} </td></tr><tr><th> Id Casino </th><td> {{ $casinodetail->id_casino }} </td></tr><tr><th> Seo Title </th><td> {{ $casinodetail->seo_title }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
