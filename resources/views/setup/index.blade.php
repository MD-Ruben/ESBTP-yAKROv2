<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - ESBTP Yakro</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .fade-enter-active, .fade-leave-active {
            transition: opacity .5s;
        }
        .fade-enter, .fade-leave-to {
            opacity: 0;
        }
        .slide-fade-enter-active {
            transition: all .3s ease;
        }
        .slide-fade-leave-active {
            transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        }
        .slide-fade-enter, .slide-fade-leave-to {
            transform: translateX(10px);
            opacity: 0;
        }
        .bounce-enter-active {
            animation: bounce-in .5s;
        }
        .bounce-leave-active {
            animation: bounce-in .5s reverse;
        }
        @keyframes bounce-in {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="ESBTP Yakro" class="mx-auto h-24 mb-4">
                <h1 class="text-3xl font-bold text-gray-800">Installation de ESBTP Yakro</h1>
                <p class="text-gray-600 mt-2">Assistant d'installation en quelques étapes simples</p>
        </div>
        
            <!-- Barre de progression -->
            <div class="bg-gray-200 rounded-full h-2 mb-8">
                <div class="progress-bar bg-blue-600 h-2 rounded-full" :style="{ width: progress + '%' }"></div>
            </div>

            <!-- Étapes -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Indicateur d'étapes -->
                <div class="flex justify-between mb-8">
                    <template v-for="(step, index) in steps">
                        <div :key="index" class="flex flex-col items-center">
                            <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold', 
                                currentStep > index ? 'bg-green-500 text-white' : 
                                currentStep === index ? 'bg-blue-600 text-white' : 
                                'bg-gray-200 text-gray-600']">
                                <template v-if="currentStep > index">
                                    <i class="fas fa-check"></i>
                                </template>
                                <template v-else>
                                    @{{ index + 1 }}
                                </template>
                </div>
                            <span class="text-sm mt-2 text-gray-600">@{{ step.title }}</span>
                        </div>
                        <div v-if="index < steps.length - 1" :key="'line-'+index" 
                            :class="['flex-1 h-0.5 mt-4', currentStep > index ? 'bg-green-500' : 'bg-gray-200']">
                        </div>
                    </template>
                </div>

                <!-- Contenu des étapes -->
                <transition name="fade" mode="out-in">
                    <!-- Étape 1: Vérification des prérequis -->
                    <div v-if="currentStep === 0" key="requirements" class="space-y-6">
                        <h2 class="text-xl font-semibold mb-4">Vérification des prérequis</h2>
                        <div v-for="(requirement, index) in requirements" :key="index" 
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i :class="['fas', 'mr-3', requirement.status ? 'fa-check text-green-500' : 'fa-times text-red-500']"></i>
                                <span>@{{ requirement.message }}</span>
                            </div>
                            <transition name="bounce">
                                <i v-if="requirement.status" class="fas fa-check-circle text-green-500"></i>
                                <i v-else class="fas fa-exclamation-circle text-red-500"></i>
                            </transition>
                </div>
            </div>

            <!-- Étape 2: Configuration de la base de données -->
                    <div v-else-if="currentStep === 1" key="database" class="space-y-6">
                        <h2 class="text-xl font-semibold mb-4">Configuration de la base de données</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Type de base de données</label>
                                <select v-model="dbConfig.connection" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="mysql">MySQL</option>
                            <option value="pgsql">PostgreSQL</option>
                            <option value="sqlite">SQLite</option>
                        </select>
                    </div>
                            <template v-if="dbConfig.connection !== 'sqlite'">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Hôte</label>
                                    <input type="text" v-model="dbConfig.host" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Port</label>
                                    <input type="text" v-model="dbConfig.port" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                                    <input type="text" v-model="dbConfig.username" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Mot de passe</label>
                                    <input type="password" v-model="dbConfig.password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                            </template>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Nom de la base de données</label>
                                <input type="text" v-model="dbConfig.database" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        </div>
                    </div>

                    <!-- Étape 3: Migrations -->
                    <div v-else-if="currentStep === 2" key="migrations" class="space-y-6">
                        <h2 class="text-xl font-semibold mb-4">Installation de la base de données</h2>
                        <div class="space-y-4">
                            <!-- Barre de progression avec pourcentage -->
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-2 bg-blue-600 rounded-full transition-all duration-500" 
                             :style="{ width: migrationProgress + '%' }"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-600">@{{ migrationProgress }}%</span>
                    </div>

                            <!-- Console de sortie -->
                            <div class="bg-gray-900 text-gray-100 rounded-lg p-4 font-mono text-sm h-64 overflow-y-auto">
                                <div v-if="migrationOutput" v-html="formattedOutput"></div>
                                <div v-else class="text-gray-500">En attente du démarrage des migrations...</div>
                                <div v-if="loading" class="animate-pulse text-blue-400">
                                    <i class="fas fa-circle-notch fa-spin"></i> Exécution en cours...
                    </div>
                    </div>

                            <!-- État actuel -->
                            <div class="flex items-center space-x-2 text-sm" :class="{
                                'text-yellow-600': loading,
                                'text-green-600': migrationProgress === 100,
                                'text-red-600': error
                            }">
                                <i class="fas" :class="{
                                    'fa-circle-notch fa-spin': loading,
                                    'fa-check-circle': migrationProgress === 100,
                                    'fa-exclamation-circle': error
                                }"></i>
                                <span v-if="loading">Installation en cours...</span>
                                <span v-else-if="migrationProgress === 100">Installation terminée avec succès</span>
                                <span v-else-if="error">Erreur: @{{ error }}</span>
                                <span v-else>Prêt à démarrer l'installation</span>
                    </div>
            </div>
                    </div>

                    <!-- Étape 4: Création de l'administrateur -->
                    <div v-else-if="currentStep === 3" key="admin" class="space-y-6">
                        <h2 class="text-xl font-semibold mb-4">Création du compte administrateur</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Nom complet</label>
                                <input type="text" v-model="adminConfig.name" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Email</label>
                                <input type="email" v-model="adminConfig.email" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Mot de passe</label>
                                <input type="password" v-model="adminConfig.password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                                <input type="password" v-model="adminConfig.password_confirmation" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                        </div>
                    </div>

                    <!-- Étape 5: Finalisation -->
                    <div v-else-if="currentStep === 4" key="finish" class="space-y-6">
                        <div class="text-center">
                            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                            <h2 class="text-2xl font-semibold mb-2">Installation terminée !</h2>
                            <p class="text-gray-600 mb-6">L'application a été installée avec succès.</p>
                            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Accéder à l'application
                            </a>
                        </div>
                    </div>
                </transition>

                <!-- Messages d'erreur -->
                <transition name="slide-fade">
                    <div v-if="error" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center text-red-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>@{{ error }}</span>
                        </div>
                    </div>
                </transition>

                <!-- Boutons de navigation -->
                <div class="mt-8 flex justify-between">
                    <button v-if="currentStep > 0" 
                        @click="previousStep" 
                        :disabled="loading"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Précédent
                    </button>
                    <button v-if="currentStep < steps.length - 1" 
                        @click="nextStep" 
                        :disabled="loading || !canProceed"
                        class="ml-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                        <span v-if="!loading">
                            Suivant
                            <i class="fas fa-arrow-right ml-2"></i>
                        </span>
                        <span v-else class="flex items-center">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i>
                            Traitement...
                        </span>
                    </button>
                    </div>
            </div>
        </div>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                currentStep: 0,
                loading: false,
                error: null,
                progress: 0,
                migrationProgress: 0,
                migrationOutput: '',
                steps: [
                    { title: 'Prérequis' },
                    { title: 'Base de données' },
                    { title: 'Installation' },
                    { title: 'Administrateur' },
                    { title: 'Finalisation' }
                ],
                requirements: [],
                dbConfig: {
                    connection: '{{ env('DB_CONNECTION', 'mysql') }}',
                    host: '{{ env('DB_HOST', 'localhost') }}',
                    port: '{{ env('DB_PORT', '3306') }}',
                    database: '{{ env('DB_DATABASE', '') }}',
                    username: '{{ env('DB_USERNAME', '') }}',
                    password: '{{ env('DB_PASSWORD', '') }}'
                },
                adminConfig: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                dbConnected: {{ $dbConnected ? 'true' : 'false' }}
            },
            computed: {
                canProceed() {
                    switch (this.currentStep) {
                        case 0:
                            return this.requirements.every(req => req.status);
                        case 1:
                            return this.dbConfig.database && 
                                (this.dbConfig.connection === 'sqlite' || 
                                (this.dbConfig.host && this.dbConfig.username));
                        case 2:
                            return this.migrationProgress === 100;
                        case 3:
                            return this.adminConfig.name && 
                                this.adminConfig.email && 
                                this.adminConfig.password && 
                                this.adminConfig.password === this.adminConfig.password_confirmation;
                        default:
                            return true;
                    }
                },
                formattedOutput() {
                    if (!this.migrationOutput) return '';
                    return this.migrationOutput
                        .split('\n')
                        .map(line => {
                            if (line.includes('Migrating:')) {
                                return `<span class="text-yellow-400">${line}</span>`;
                            } else if (line.includes('Migrated:')) {
                                return `<span class="text-green-400">${line}</span>`;
                            } else if (line.includes('error') || line.includes('Error')) {
                                return `<span class="text-red-400">${line}</span>`;
                            }
                            return `<span class="text-gray-300">${line}</span>`;
                        })
                        .join('<br>');
                }
            },
            watch: {
                currentStep(newStep) {
                    if (newStep === 2) {
                        // Démarrer automatiquement les migrations
                        this.runMigrations();
                    }
                }
            },
            methods: {
                async checkRequirements() {
                    try {
                        this.loading = true;
                        const response = await axios.get('{{ route("setup.check-requirements") }}');
                        this.requirements = Object.entries(response.data).map(([key, value]) => ({
                            key,
                            status: value.status,
                            message: value.message
                        }));

                        // Mettre à jour la barre de progression
                        if (this.requirements.every(req => req.status)) {
                            this.progress = (1 / (this.steps.length - 1)) * 100;
                        }
                    } catch (error) {
                        this.error = "Erreur lors de la vérification des prérequis";
                    } finally {
                        this.loading = false;
                    }
                },
                async configureDatabase() {
                    try {
                        this.loading = true;
                        await axios.post('{{ route("setup.setup") }}', this.dbConfig);
                        return true;
                    } catch (error) {
                        this.error = error.response?.data?.message || "Erreur lors de la configuration de la base de données";
                        return false;
                    } finally {
                        this.loading = false;
                    }
                },
                async runMigrations() {
                    try {
                        this.loading = true;
                        this.migrationProgress = 0;
                        this.migrationOutput = 'Démarrage des migrations...\n';
                        
                        const response = await axios.post('{{ route("setup.migrate") }}');
                        
                        if (response.data.success) {
                            // Simuler une progression plus graduelle
                            const lines = response.data.output.split('\n');
                            let progress = 0;
                            const increment = 100 / (lines.length || 1);
                            
                            for (const line of lines) {
                                this.migrationOutput += line + '\n';
                                if (line.includes('Migrated:')) {
                                    progress += increment;
                                    this.migrationProgress = Math.min(Math.round(progress), 100);
                                    // Petit délai pour voir la progression
                                    await new Promise(resolve => setTimeout(resolve, 200));
                                }
                            }
                            
                            this.migrationProgress = 100;
                            return true;
                        } else {
                            this.error = response.data.message || "Erreur lors des migrations";
                            if (response.data.error) {
                                this.migrationOutput += '\nDétails de l\'erreur:\n';
                                this.migrationOutput += `Message: ${response.data.error.message}\n`;
                                this.migrationOutput += `Fichier: ${response.data.error.file}\n`;
                                this.migrationOutput += `Ligne: ${response.data.error.line}\n`;
                                
                                if (response.data.error.existing_tables) {
                                    this.migrationOutput += '\nTables existantes:\n';
                                    response.data.error.existing_tables.forEach(table => {
                                        this.migrationOutput += `- ${table}\n`;
                                    });
                                }
                                
                                if (response.data.error.foreign_keys) {
                                    this.migrationOutput += '\nContraintes de clé étrangère:\n';
                                    response.data.error.foreign_keys.forEach(fk => {
                                        this.migrationOutput += `- ${fk.TABLE_NAME}.${fk.COLUMN_NAME} -> ${fk.REFERENCED_TABLE_NAME}.${fk.REFERENCED_COLUMN_NAME}\n`;
                                    });
                                }
                            }
                            return false;
                        }
                    } catch (error) {
                        this.error = error.response?.data?.message || "Erreur lors des migrations";
                        this.migrationOutput += '\nErreur: ' + this.error + '\n';
                        if (error.response?.data?.error) {
                            const errorData = error.response.data.error;
                            this.migrationOutput += '\nDétails de l\'erreur:\n';
                            this.migrationOutput += JSON.stringify(errorData, null, 2);
                        }
                        return false;
                    } finally {
                        this.loading = false;
                    }
                },
                async createAdmin() {
                    try {
                        this.loading = true;
                        await axios.post('{{ route("setup.create-admin") }}', this.adminConfig);
                        return true;
                    } catch (error) {
                        this.error = error.response?.data?.message || "Erreur lors de la création de l'administrateur";
                        return false;
                    } finally {
                        this.loading = false;
                    }
                },
                async finalize() {
                    try {
                        this.loading = true;
                        await axios.post('{{ route("setup.finalize") }}');
                        return true;
                    } catch (error) {
                        this.error = error.response?.data?.message || "Erreur lors de la finalisation";
                        return false;
                    } finally {
                        this.loading = false;
                    }
                },
                async nextStep() {
                    this.error = null;
                    let success = true;

                    try {
                        this.loading = true;

                        switch (this.currentStep) {
                            case 0: // Prérequis
                                await this.checkRequirements();
                                if (this.requirements.every(req => req.status)) {
                                    success = true;
                                } else {
                                    success = false;
                                    this.error = "Veuillez vérifier que tous les prérequis sont satisfaits";
                                }
                                break;

                            case 1: // Configuration de la base de données
                                if (this.dbConnected) {
                                    success = true;
                    } else {
                                    success = await this.configureDatabase();
                                }
                                break;

                            case 2: // Installation
                                success = await this.runMigrations();
                                break;

                            case 3: // Création de l'administrateur
                                success = await this.createAdmin();
                                break;

                            case 4: // Finalisation
                                success = await this.finalize();
                                break;
                        }

                        if (success) {
                            this.currentStep++;
                            this.progress = (this.currentStep / (this.steps.length - 1)) * 100;
                        }
                    } catch (error) {
                        success = false;
                        this.error = error.message || "Une erreur est survenue";
                    } finally {
                        this.loading = false;
                    }
                },
                previousStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        this.progress = (this.currentStep / (this.steps.length - 1)) * 100;
                    }
                }
            },
            mounted() {
                this.checkRequirements();
            }
        });
    </script>
</body>
</html> 