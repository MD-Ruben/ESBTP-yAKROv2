@extends('install.layout')

@section('title', 'Installation terminée')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800">Installation terminée avec succès!</h2>
        <p class="text-gray-600 mt-2">Votre application Smart School est maintenant prête à être utilisée</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="space-y-6">
            <!-- Résumé de l'installation -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Résumé de l'installation</h3>
                
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Base de données configurée</p>
                            <p class="text-xs text-gray-500">Connexion établie avec succès</p>
                        </div>
                    </li>
                    
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Tables créées</p>
                            <p class="text-xs text-gray-500">Migrations exécutées avec succès</p>
                        </div>
                    </li>
                    
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Administrateur créé</p>
                            <p class="text-xs text-gray-500">Compte administrateur configuré</p>
                        </div>
                    </li>
                    
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Fichier d'environnement configuré</p>
                            <p class="text-xs text-gray-500">Paramètres d'application enregistrés</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Informations de connexion -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de connexion</h3>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-700 mb-4">Vous pouvez vous connecter à l'application avec les identifiants suivants:</p>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-2">Identifiants administrateur</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Nom:</p>
                                <p class="text-sm font-medium text-gray-800">{{ session('admin_name') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email:</p>
                                <p class="text-sm font-medium text-blue-800" id="admin-email">{{ session('admin_email') }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500">Mot de passe:</p>
                                <p class="text-sm font-medium text-blue-800">{{ session('admin_password') }}</p>
                                <p class="text-xs text-red-500 mt-1">⚠️ Assurez-vous de conserver ces informations en lieu sûr</p>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Informations de l'école</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Nom de l'école:</p>
                            <p class="text-sm font-medium text-gray-800">{{ session('school_name') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email de contact:</p>
                            <p class="text-sm font-medium text-gray-800">{{ session('school_email') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Adresse:</p>
                            <p class="text-sm font-medium text-gray-800">{{ session('school_address') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Prochaines étapes -->
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-4">Prochaines étapes</h3>
                
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-arrow-right text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Configurer les paramètres de l'école</p>
                            <p class="text-xs text-gray-500">Définir les années scolaires, les classes et les matières</p>
                        </div>
                    </li>
                    
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-arrow-right text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Ajouter des enseignants</p>
                            <p class="text-xs text-gray-500">Créer des comptes pour les enseignants de votre école</p>
                        </div>
                    </li>
                    
                    <li class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-arrow-right text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Ajouter des élèves</p>
                            <p class="text-xs text-gray-500">Enregistrer les élèves et les assigner à leurs classes</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bouton de finalisation -->
        <div class="mt-8 text-center">
            <form id="finalizeForm" @submit.prevent="finalize">
                <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center mx-auto" :disabled="loading">
                    <span v-if="loading" class="mr-2"><i class="fas fa-spinner fa-spin"></i></span>
                    <span v-else class="mr-2"><i class="fas fa-check-double"></i></span>
                    Terminer l'installation et accéder au tableau de bord
                </button>
                
                <div v-if="error" class="mt-4 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">@{{ error }}</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#finalizeForm',
        data: {
            loading: false,
            error: null
        },
        methods: {
            finalize() {
                this.loading = true;
                this.error = null;
                
                axios.post('{{ route("install.finalize") }}')
                    .then(response => {
                        this.loading = false;
                        if (response.data.status === 'success') {
                            // Vérifier le pourcentage de correspondance des migrations
                            const matchPercentage = response.data.match_percentage || 0;
                            
                            // Journaliser le pourcentage de correspondance
                            console.log(`Installation terminée. Correspondance des migrations: ${matchPercentage}%`);
                            
                            // Si la correspondance est de 100%, rediriger vers la page d'accueil
                            // Sinon, rediriger vers la page de migration pour corriger les problèmes
                            if (matchPercentage === 100) {
                                // Rediriger vers la page d'accueil
                                window.location.href = response.data.redirect || '{{ route("welcome") }}';
                            } else {
                                // Afficher un message d'avertissement
                                this.error = `Installation terminée, mais certaines tables sont manquantes (${matchPercentage}% de correspondance). Vous allez être redirigé vers la page de migration.`;
                                
                                // Rediriger vers la page de migration après 3 secondes
                                setTimeout(() => {
                                    window.location.href = '{{ route("install.migration") }}';
                                }, 3000);
                            }
                        } else {
                            this.error = response.data.message || 'Une erreur est survenue lors de la finalisation';
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        this.error = 'Une erreur est survenue lors de la finalisation de l\'installation';
                        console.error(error);
                    });
            }
        }
    });
</script>
@endsection 