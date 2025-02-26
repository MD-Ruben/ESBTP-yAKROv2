@extends('layouts.app')

@section('title', 'Configuration du profil étudiant')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Configuration du profil étudiant') }}</div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>Bienvenue, {{ $user->name }} !</h4>
                        <p>Votre compte étudiant a été créé, mais votre profil n'est pas encore configuré.</p>
                        <p>Un administrateur doit compléter votre profil pour que vous puissiez accéder à toutes les fonctionnalités.</p>
                    </div>
                    
                    <div class="mt-4">
                        <h5>En attendant, vous pouvez :</h5>
                        <ul>
                            <li>Vérifier et mettre à jour vos informations personnelles</li>
                            <li>Explorer les fonctionnalités disponibles</li>
                            <li>Contacter l'administrateur pour finaliser votre profil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 