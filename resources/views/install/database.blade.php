@extends('install.layout')

@section('title', 'Configuration de la base de données')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Configuration de la base de données</h2>
        <p class="text-gray-600">Veuillez saisir les informations de connexion à votre base de données.</p>
    </div>
    
    @if(session('error'))
    <div class="alert alert-error mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif
    
    <div id="app">
        <form @submit.prevent="testConnection" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="host" class="form-label">Hôte</label>
                    <input type="text" id="host" v-model="formData.host" class="form-input" placeholder="localhost" required>
                    <p class="text-xs text-gray-500">Généralement "localhost" ou "127.0.0.1"</p>
                </div>
                
                <div class="space-y-2">
                    <label for="port" class="form-label">Port</label>
                    <input type="text" id="port" v-model="formData.port" class="form-input" placeholder="3306" required>
                    <p class="text-xs text-gray-500">Généralement "3306" pour MySQL</p>
                </div>
                
                <div class="space-y-2">
                    <label for="database" class="form-label">Nom de la base de données</label>
                    <input type="text" id="database" v-model="formData.database" class="form-input" placeholder="smart_school" required>
                    <p class="text-xs text-gray-500">La base de données sera créée si elle n'existe pas</p>
                </div>
                
                <div class="space-y-2">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" id="username" v-model="formData.username" class="form-input" placeholder="root" required>
                    <p class="text-xs text-gray-500">Généralement "root" pour les installations locales</p>
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" v-model="formData.password" class="form-input" placeholder="Laissez vide si aucun mot de passe">
                    <p class="text-xs text-gray-500">Laissez vide si aucun mot de passe n'est défini</p>
                </div>
            </div>
            
            <div v-if="error" class="alert alert-error">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>@{{ error }}</span>
                </div>
            </div>
            
            <div v-if="success" class="alert alert-success">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>@{{ success }}</span>
                </div>
            </div>
            
            <div class="flex justify-between">
                <a href="{{ route('install.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Retour</span>
                </a>
                
                <button type="submit" class="btn-primary" :disabled="loading">
                    <span v-if="!loading">Tester la connexion</span>
                    <span v-else class="flex items-center">
                        <i class="fas fa-circle-notch fa-spin mr-2"></i>
                        <span>Test en cours...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        new Vue({
            el: '#app',
            data: {
                formData: {
                    host: 'localhost',
                    port: '3306',
                    database: 'smart_school',
                    username: 'root',
                    password: ''
                },
                loading: false,
                error: null,
                success: null,
                databaseExists: false,
                tablesExist: false
            },
            methods: {
                testConnection() {
                    this.loading = true;
                    this.error = null;
                    this.success = null;
                    
                    axios.post('{{ route("install.setup-database") }}', this.formData)
                        .then(response => {
                            this.loading = false;
                            if (response.data.status === 'success') {
                                this.success = response.data.message;
                                this.databaseExists = response.data.database_exists;
                                this.tablesExist = response.data.tables_exist;
                                
                                // Redirect after a short delay
                                setTimeout(() => {
                                    window.location.href = response.data.redirect || '{{ route("install.migration") }}';
                                }, 1500);
                            }
                        })
                        .catch(error => {
                            this.loading = false;
                            if (error.response && error.response.data) {
                                this.error = error.response.data.message || 'Une erreur est survenue lors du test de connexion.';
                            } else {
                                this.error = 'Une erreur est survenue lors du test de connexion.';
                            }
                        });
                }
            }
        });
    </script>
@endsection 