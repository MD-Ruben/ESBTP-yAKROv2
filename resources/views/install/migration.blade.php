@extends('install.layout')

@section('title', 'Migration de la base de données')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Migration de la base de données</h2>
        <p class="text-gray-600">Nous allons maintenant créer les tables nécessaires dans votre base de données.</p>
    </div>
    
    <div id="app">
        <div class="space-y-6">
            <!-- Database Status -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-2">État de la base de données</h3>
                
                <div class="space-y-2">
                    <div class="flex items-center">
                        <i class="fas fa-database mr-2 text-blue-500"></i>
                        <span class="text-gray-700">Base de données : <span class="font-medium">{{ $dbStatus['connected'] ? 'Connectée' : 'Non connectée' }}</span></span>
                    </div>
                    
                    @if($dbStatus['connected'])
                        <div class="flex items-center">
                            <i class="fas fa-table mr-2 text-blue-500"></i>
                            <span class="text-gray-700">Tables existantes : <span class="font-medium">{{ $dbStatus['existing_tables_count'] ?? 0 }}</span></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-code-branch mr-2 text-blue-500"></i>
                            <span class="text-gray-700">Tables de migration : <span class="font-medium">{{ $dbStatus['migration_tables_count'] ?? 0 }}</span></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-file-code mr-2 text-blue-500"></i>
                            <span class="text-gray-700">Fichiers de migration : <span class="font-medium">{{ $dbStatus['migration_files_count'] ?? 0 }}</span></span>
                        </div>
                        
                        @if(isset($dbStatus['multi_table_migrations_count']) && $dbStatus['multi_table_migrations_count'] > 0)
                        <div class="flex items-center">
                            <i class="fas fa-layer-group mr-2 text-purple-500"></i>
                            <span class="text-gray-700">Migrations avec plusieurs tables : <span class="font-medium">{{ $dbStatus['multi_table_migrations_count'] }}</span></span>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <i class="fas fa-percentage mr-2 {{ $dbStatus['match_percentage'] == 100 ? 'text-green-500' : ($dbStatus['match_percentage'] >= 70 ? 'text-yellow-500' : 'text-red-500') }}"></i>
                            <span class="text-gray-700">Correspondance : <span class="font-medium">{{ $dbStatus['match_percentage'] }}%</span></span>
                        </div>
                        
                        <!-- Barre de progression visuelle pour le pourcentage de correspondance -->
                        <div class="mt-1 flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="h-2 rounded-full {{ $dbStatus['match_percentage'] == 100 ? 'bg-green-500' : ($dbStatus['match_percentage'] >= 70 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ $dbStatus['match_percentage'] }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Messages basés sur le pourcentage de correspondance -->
                        @if($dbStatus['match_percentage'] == 100)
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-green-700 font-semibold">Correspondance parfaite (100%) !</p>
                                        <p class="text-sm text-green-700 mt-1">Toutes les tables requises existent déjà dans la base de données. Vous pouvez passer la migration.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($dbStatus['match_percentage'] >= 70)
                            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-yellow-700 font-semibold">Correspondance partielle ({{ $dbStatus['match_percentage'] }}%)</p>
                                        <p class="text-sm text-yellow-700 mt-1">Certaines tables requises sont présentes, mais d'autres sont manquantes. Il est recommandé d'exécuter la migration.</p>
                                        
                                        @if(isset($dbStatus['missing_tables']) && count($dbStatus['missing_tables']) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs font-semibold text-yellow-700">Tables manquantes ({{ count($dbStatus['missing_tables']) }}) :</p>
                                            <p class="text-xs text-yellow-600">{{ implode(', ', array_slice($dbStatus['missing_tables'], 0, 10)) }}{{ count($dbStatus['missing_tables']) > 10 ? '...' : '' }}</p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($dbStatus['extra_tables']) && count($dbStatus['extra_tables']) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs font-semibold text-yellow-700">Tables supplémentaires ({{ count($dbStatus['extra_tables']) }}) :</p>
                                            <p class="text-xs text-yellow-600">{{ implode(', ', array_slice($dbStatus['extra_tables'], 0, 10)) }}{{ count($dbStatus['extra_tables']) > 10 ? '...' : '' }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-times-circle text-red-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-red-700 font-semibold">Correspondance insuffisante ({{ $dbStatus['match_percentage'] }}%)</p>
                                        <p class="text-sm text-red-700 mt-1">La plupart des tables requises sont manquantes. Vous devez exécuter la migration complète.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($dbStatus['existing_tables_count'] > 0 && !$dbStatus['all_tables_exist'])
                            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-yellow-700">Des tables existent déjà dans la base de données, mais certaines tables requises sont manquantes.</p>
                                        <p class="text-sm text-yellow-700 mt-1">Toutes les tables existantes seront préservées et les tables manquantes seront créées.</p>
                                        
                                        @if(isset($dbStatus['missing_tables']) && count($dbStatus['missing_tables']) > 0)
                                        <div class="mt-2">
                                            <p class="text-xs font-semibold text-yellow-700">Tables manquantes ({{ count($dbStatus['missing_tables']) }}) :</p>
                                            <p class="text-xs text-yellow-600">{{ implode(', ', array_slice($dbStatus['missing_tables'], 0, 10)) }}{{ count($dbStatus['missing_tables']) > 10 ? '...' : '' }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($dbStatus['existing_tables_count'] > 0 && $dbStatus['all_tables_exist'])
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-green-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-green-700 font-semibold">Toutes les tables requises existent déjà dans la base de données.</p>
                                        <p class="text-sm text-green-700 mt-1">Vous pouvez passer directement à l'étape suivante sans exécuter les migrations.</p>
                                        
                                        @if($dbStatus['match_percentage'] >= 90)
                                        <div class="mt-2 p-2 bg-green-100 rounded">
                                            <p class="text-xs text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Excellente correspondance ({{ $dbStatus['match_percentage'] }}%) entre les tables de migration et les tables existantes.
                                            </p>
                                        </div>
                                        @endif
                                        
                                        @if(isset($dbStatus['extra_tables']) && count($dbStatus['extra_tables']) > 0)
                                        <div class="mt-2 p-2 bg-blue-100 rounded">
                                            <p class="text-xs text-blue-800">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ count($dbStatus['extra_tables']) }} tables supplémentaires trouvées dans la base de données.
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif(!$dbStatus['database_exists'])
                            <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-blue-700">La base de données n'existe pas encore.</p>
                                        <p class="text-sm text-blue-700 mt-1">Elle sera créée automatiquement lors de la migration.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Aide sur le pourcentage de correspondance -->
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-question-circle text-blue-500 mr-2"></i>
                    <h3 class="font-medium text-gray-800">Comprendre le pourcentage de correspondance</h3>
                </div>
                
                <div class="text-sm text-gray-700 space-y-2">
                    <p>Le <strong>pourcentage de correspondance</strong> indique à quel point votre base de données actuelle correspond à la structure attendue par l'application.</p>
                    
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                        <p><strong>100%</strong> : Toutes les tables requises existent. Vous pouvez passer la migration.</p>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                        <p><strong>70-99%</strong> : La plupart des tables existent, mais certaines sont manquantes. Il est recommandé d'exécuter la migration.</p>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                        <p><strong>0-69%</strong> : Trop de tables manquantes. Vous devez exécuter la migration complète.</p>
                    </div>
                </div>
            </div>
            
            <!-- Migration Console -->
            <div>
                <h3 class="font-medium text-gray-800 mb-2">Console de migration</h3>
                
                <div class="console" ref="console">
                    <div v-if="output" v-html="formattedOutput"></div>
                    <div v-else class="text-gray-400">En attente du démarrage des migrations...</div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4 flex items-center">
                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                        <div class="bg-blue-500 h-2 rounded-full" :style="{ width: progress + '%' }"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-600">@{{ progress }}%</span>
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
                <a href="{{ route('install.database') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Retour</span>
                </a>
                
                <div class="flex space-x-4">
                    @if($dbStatus['match_percentage'] == 100)
                    <button @click="skipMigration" class="btn-success">
                        <i class="fas fa-forward mr-2"></i>
                        <span>Passer la migration</span>
                    </button>
                    @elseif($dbStatus['existing_tables_count'] > 0 && $dbStatus['all_tables_exist'])
                    <a href="{{ route('install.admin') }}" class="btn-success">
                        <i class="fas fa-forward mr-2"></i>
                        <span>Passer à l'étape suivante</span>
                    </a>
                    @endif
                    
                    <button v-if="!started" @click="runMigration" class="btn-primary" :disabled="loading">
                        <span v-if="!loading">Démarrer la migration</span>
                        <span v-else class="flex items-center">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i>
                            <span>Migration en cours...</span>
                        </span>
                    </button>
                    
                    <button v-else-if="completed" @click="goToNextStep" class="btn-primary">
                        <span>Continuer</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        new Vue({
            el: '#app',
            data: {
                loading: false,
                started: false,
                completed: false,
                error: null,
                success: null,
                output: '',
                progress: 0,
                nextUrl: ''
            },
            computed: {
                formattedOutput() {
                    if (!this.output) return '';
                    
                    return this.output
                        .split('\n')
                        .map(line => {
                            if (line.includes('Migrating:')) {
                                return `<span class="text-yellow-400">${line}</span>`;
                            } else if (line.includes('Migrated:')) {
                                return `<span class="text-green-400">${line}</span>`;
                            } else if (line.includes('error') || line.includes('Error')) {
                                return `<span class="text-red-400">${line}</span>`;
                            } else if (line.includes('Seeding:')) {
                                return `<span class="text-blue-400">${line}</span>`;
                            } else if (line.includes('Seeded:')) {
                                return `<span class="text-green-400">${line}</span>`;
                            } else if (line.includes('Creating table:')) {
                                return `<span class="text-purple-400">${line}</span>`;
                            }
                            return `<span class="text-gray-300">${line}</span>`;
                        })
                        .join('<br>');
                }
            },
            methods: {
                runMigration() {
                    this.loading = true;
                    this.started = true;
                    this.error = null;
                    this.success = null;
                    this.output = 'Démarrage des migrations...\n';
                    this.progress = 5;
                    
                    // Scroll to bottom of console
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    
                    axios.post('{{ route("install.run-migration") }}')
                        .then(response => {
                            this.loading = false;
                            
                            if (response.data.status === 'success') {
                                this.success = response.data.message;
                                this.output += response.data.output;
                                this.progress = 100;
                                this.completed = true;
                                this.nextUrl = response.data.redirect;
                            } else {
                                this.error = response.data.message;
                                this.output += '\nErreur: ' + response.data.message;
                                this.progress = 0;
                            }
                            
                            // Scroll to bottom of console
                            this.$nextTick(() => {
                                this.scrollToBottom();
                            });
                        })
                        .catch(error => {
                            this.loading = false;
                            
                            if (error.response && error.response.data) {
                                this.error = error.response.data.message || 'Une erreur est survenue lors de la migration.';
                                this.output += '\nErreur: ' + this.error;
                                
                                if (error.response.data.output) {
                                    this.output += '\n' + error.response.data.output;
                                }
                            } else {
                                this.error = 'Une erreur est survenue lors de la migration.';
                                this.output += '\nErreur: ' + this.error;
                            }
                            
                            this.progress = 0;
                            
                            // Scroll to bottom of console
                            this.$nextTick(() => {
                                this.scrollToBottom();
                            });
                        });
                },
                goToNextStep() {
                    window.location.href = this.nextUrl || '{{ route("install.admin") }}';
                },
                skipMigration() {
                    // Afficher un message dans la console
                    this.output = 'Migration ignorée - Toutes les tables requises existent déjà.\n';
                    this.output += 'Pourcentage de correspondance: {{ $dbStatus["match_percentage"] }}%\n';
                    this.output += 'Redirection vers l\'étape suivante...\n';
                    
                    this.progress = 100;
                    this.completed = true;
                    this.success = 'Migration ignorée avec succès. Toutes les tables requises existent déjà.';
                    
                    // Rediriger après un court délai
                    setTimeout(() => {
                        window.location.href = '{{ route("install.admin") }}';
                    }, 2000);
                },
                scrollToBottom() {
                    const console = this.$refs.console;
                    console.scrollTop = console.scrollHeight;
                }
            }
        });
    </script>
@endsection 