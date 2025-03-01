@extends('install.layout')

@section('title', 'Création de l\'administrateur')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <i class="fas fa-user-shield text-5xl text-blue-600 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800">Création du compte administrateur</h2>
        <p class="text-gray-600 mt-2">Créez le compte administrateur principal pour gérer votre école</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="adminForm" @submit.prevent="createAdmin" class="space-y-6">
            <!-- Nom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                    <input type="text" id="name" v-model="form.name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Jean Dupont">
                    <p class="text-xs text-gray-500 mt-1">Entrez votre nom complet</p>
                </div>

                <!-- Nom d'utilisateur -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                    <input type="text" id="username" v-model="form.username" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="admin">
                    <p class="text-xs text-gray-500 mt-1">Ce nom sera utilisé pour la connexion</p>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                <input type="email" id="email" v-model="form.email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="admin@ecole.fr">
                <p class="text-xs text-gray-500 mt-1">Cette adresse sera utilisée pour les notifications</p>
            </div>

            <!-- Mot de passe -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" id="password" v-model="form.password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="••••••••">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" v-model="form.password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="••••••••">
                    <p class="text-xs text-gray-500 mt-1">Répétez le même mot de passe</p>
                </div>
            </div>

            <!-- Informations école -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Informations de l'école</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'école</label>
                        <input type="text" id="school_name" v-model="form.school_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="École ESBTP-Yakro">
                    </div>
                    
                    <div>
                        <label for="school_email" class="block text-sm font-medium text-gray-700 mb-1">Email de contact</label>
                        <input type="email" id="school_email" v-model="form.school_email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="contact@ecole.fr">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="school_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" id="school_address" v-model="form.school_address" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="123 Rue de l'École, 75000 Paris">
                </div>
            </div>

            <!-- Messages d'erreur et de succès -->
            <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">@{{ error }}</p>
                    </div>
                </div>
            </div>

            <div v-if="success" class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">@{{ success }}</p>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-between pt-4">
                <a href="{{ route('install.migration') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center" :disabled="loading">
                    <span v-if="loading" class="mr-2"><i class="fas fa-spinner fa-spin"></i></span>
                    <span v-else class="mr-2"><i class="fas fa-user-plus"></i></span>
                    Créer l'administrateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#adminForm',
        data: {
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                school_name: '',
                school_email: '',
                school_address: ''
            },
            loading: false,
            error: null,
            success: null
        },
        methods: {
            createAdmin() {
                // Vérifier que les mots de passe correspondent
                if (this.form.password !== this.form.password_confirmation) {
                    this.error = 'Les mots de passe ne correspondent pas';
                    return;
                }
                
                // Réinitialiser les messages
                this.error = null;
                this.success = null;
                this.loading = true;
                
                // Envoyer la requête au serveur
                axios.post('{{ route("install.setup-admin") }}', this.form)
                    .then(response => {
                        this.loading = false;
                        if (response.data.status === 'success') {
                            this.success = response.data.message || 'Administrateur créé avec succès!';
                            // Rediriger vers la page suivante après 2 secondes
                            setTimeout(() => {
                                window.location.href = response.data.redirect || '{{ route("install.complete") }}';
                            }, 2000);
                        } else {
                            this.error = response.data.message || 'Une erreur est survenue';
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        if (error.response && error.response.data && error.response.data.errors) {
                            // Récupérer le premier message d'erreur
                            const errorMessages = Object.values(error.response.data.errors);
                            this.error = errorMessages.length > 0 ? errorMessages[0][0] : 'Une erreur est survenue';
                        } else {
                            this.error = 'Une erreur est survenue lors de la création de l\'administrateur';
                        }
                    });
            }
        }
    });
</script>
@endsection 