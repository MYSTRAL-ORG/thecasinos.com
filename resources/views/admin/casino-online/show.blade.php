@extends('layouts.app')

@section('content')
    <div class="container-fluid">
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
                                    <th>Actif</th>
                                    <td>@if ($casinoonline->actif ==1 ) True @else False @endif  </td>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $casinoonline->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nom Casino</th>
                                    <td>{{ $casinoonline->nom_casino }}</td>
                                </tr>
                                <tr>
                                    <th>Nom Casino Slug</th>
                                    <td>{{ $casinoonline->nom_casino_slug }}</td>
                                </tr>
                                <tr>
                                    <th>Sous Titre</th>
                                    <td>{{ $casinoonline->sous_titre }}</td>
                                </tr>
                                <tr>
                                    <th>Key feature</th>
                                    <td>{{ $casinoonline->key_feature }}</td>
                                </tr>
                                <tr>
                                    <th>Screenshot</th>

                                    <td><img src="{{ $casinoonline->screenshot }}" height="100" width="100">{{ $casinoonline->screenshot }} {{ $casinoonline->screenshot }}</td>
                                </tr>
                                <tr>
                                    <th>Pros</th>
                                    <td>{{ $casinoonline->point_pour }}</td>
                                </tr>
                                <tr>
                                    <th>Cons</th>
                                    <td>{{ $casinoonline->point_contre }}</td>
                                </tr>
                                <tr>
                                    <th>Bonus</th>
                                    <td>{{ $casinoonline->bonus }}</td>
                                </tr>
                                <tr>
                                    <th>Bonus Description</th>
                                    <td>{{ $casinoonline->bonus_description }}</td>
                                </tr>
                                <tr>
                                    <th>Sumup Description/th>
                                    <td>{{ $casinoonline->sumup_description }}</td>
                                </tr>

                                <tr>
                                    <th>Deposit mehods</th>
                                    <td>{{ $casinoonline->deposit_mehods }}</td>
                                </tr>
                                <tr>
                                    <th>Deposit mehods description</th>
                                    <td>{{ $casinoonline->deposit_mehods_description }}</td>
                                </tr>
                                <tr>
                                    <th>Contact information description</th>
                                    <td>{{ $casinoonline->contact_information_description }}</td>
                                </tr>
                                <tr>
                                    <th>Contact information</th>
                                    <td>{{ $casinoonline->contact_information }}</td>
                                </tr>
                                <tr>
                                    <th>Register link</th>
                                    <td>{{ $casinoonline->register_link }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $casinoonline->note }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $casinoonline->description }}</td>
                                </tr>

                                <tr>
                                    <th>Logo</th>
                                    <td><img src="{{ $casinoonline->logo }}" height="100" width="100">{{ $casinoonline->logo }}</td>
                                </tr>
                                <tr>
                                    <th>Icone</th>
                                    <td><img src="{{ $casinoonline->icone }}" height="100" width="100">{{ $casinoonline->icone }}</td>
                                </tr>

                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
