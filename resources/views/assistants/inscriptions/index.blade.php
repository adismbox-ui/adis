@extends('assistants.layout')

@section('content')
@include('admin.inscriptions.index', ['apprenantsNonPayes' => $apprenantsNonPayes])
@endsection


