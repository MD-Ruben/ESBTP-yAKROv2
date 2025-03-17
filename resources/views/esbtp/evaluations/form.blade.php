@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($evaluation) ? 'Modifier l\'évaluation' : 'Nouvelle évaluation' }}</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ isset($evaluation) ? route('esbtp.evaluations.update', $evaluation) : route('esbtp.evaluations.store') }}" method="POST">
                        @csrf
                        @if(isset($evaluation))
                            @method('PUT')
                        @endif

                        @yield('content_form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
