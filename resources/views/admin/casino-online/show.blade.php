@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">CasinoOnline {{ $casinoonline->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/casino-online') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/casino-online/' . $casinoonline->id . '/edit') }}" title="Edit CasinoOnline"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('/admincasinoonline' . '/' . $casinoonline->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete CasinoOnline" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $casinoonline->id }}</td>
                                    </tr>
                                    <tr><th> Nom Casino </th><td> {{ $casinoonline->nom_casino }} </td></tr><tr><th> Nom Casino Slug </th><td> {{ $casinoonline->nom_casino_slug }} </td></tr><tr><th> Sous Titre </th><td> {{ $casinoonline->sous_titre }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
