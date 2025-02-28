<?php
// Fix PHP Version Check Script

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Function to check if a file is writable
function is_really_writable($file)
{
    // If we're on a Unix server with safe_mode off, check using is_writable
    if (DIRECTORY_SEPARATOR === '/' && @ini_get('safe_mode') == false) {
        return is_writable($file);
    }

    // For Windows servers and safe_mode on, check if the file is writable by attempting to open it
    if (is_file($file)) {
        if (($fp = @fopen($file, 'r+')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }

    if (is_dir($file)) {
        if (($fp = @fopen($file . '/' . uniqid(mt_rand()) . '.tmp', 'w')) === false) {
            return false;
        }
        fclose($fp);
        @unlink($file . '/' . uniqid(mt_rand()) . '.tmp');
        return true;
    }

    return false;
}

// Function to modify the SetupController.php file
function fix_setup_controller()
{
    $file = __DIR__ . '/../app/Http/Controllers/SetupController.php';
    
    if (!file_exists($file)) {
        return [
            'success' => false,
            'message' => 'Le fichier SetupController.php n\'existe pas à l\'emplacement attendu.'
        ];
    }
    
    if (!is_really_writable($file)) {
        return [
            'success' => false,
            'message' => 'Le fichier SetupController.php n\'est pas accessible en écriture. Vérifiez les permissions.'
        ];
    }
    
    $content = file_get_contents($file);
    
    // Check if the file has already been modified
    if (strpos($content, '$forcePhpCompatible = true;') !== false) {
        return [
            'success' => true,
            'message' => 'Le fichier SetupController.php a déjà été modifié pour ignorer la vérification de version PHP.'
        ];
    }
    
    // Replace the checkRequirements method
    $pattern = '/public function checkRequirements\(\)\s*\{[^{]*\$requirements = \[/s';
    $replacement = 'public function checkRequirements()
    {
        // Force PHP version to be considered compatible
        $forcePhpCompatible = true; // Set to true to bypass PHP version check
        
        $requirements = [';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    // Replace the php_version check
    $pattern = /'php_version' => \[\s*\'status\' => version_compare\(PHP_VERSION, \'8\.0\.0\', \'\>=\'\),\s*\'message\' => \'PHP version \' \. PHP_VERSION \. \' \(requis: 8\.0\+\)\'/s';
    $replacement = '\'php_version\' => [
                \'status\' => $forcePhpCompatible || version_compare(PHP_VERSION, \'8.0.0\', \'>=\'),
                \'message\' => \'PHP version \' . PHP_VERSION . \' (requis: 8.0+)\' . ($forcePhpCompatible ? \' [Compatibilité forcée]\' : \'\')';
    
    $newContent = preg_replace($pattern, $replacement, $newContent);
    
    // Save the modified file
    if (file_put_contents($file, $newContent)) {
        return [
            'success' => true,
            'message' => 'Le fichier SetupController.php a été modifié avec succès pour ignorer la vérification de version PHP.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Impossible d\'écrire dans le fichier SetupController.php.'
        ];
    }
}

// Function to clear Laravel cache
function clear_laravel_cache()
{
    $basePath = __DIR__ . '/..';
    $artisanPath = $basePath . '/artisan';
    
    if (!file_exists($artisanPath)) {
        return [
            'success' => false,
            'message' => 'Le fichier artisan n\'existe pas à l\'emplacement attendu.'
        ];
    }
    
    $commands = [
        'php ' . escapeshellarg($artisanPath) . ' config:clear',
        'php ' . escapeshellarg($artisanPath) . ' cache:clear',
        'php ' . escapeshellarg($artisanPath) . ' view:clear',
        'php ' . escapeshellarg($artisanPath) . ' route:clear'
    ];
    
    $output = [];
    $success = true;
    
    foreach ($commands as $command) {
        exec($command . ' 2>&1', $commandOutput, $returnCode);
        $output[] = $command . ': ' . ($returnCode === 0 ? 'Succès' : 'Échec');
        if ($returnCode !== 0) {
            $success = false;
        }
    }
    
    return [
        'success' => $success,
        'message' => 'Résultat du nettoyage du cache Laravel:',
        'details' => $output
    ];
}

// Process form submission
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'fix_controller':
                $result = fix_setup_controller();
                break;
            case 'clear_cache':
                $result = clear_laravel_cache();
                break;
        }
    }
}

// Get PHP information
$phpVersion = PHP_VERSION;
$phpSapi = php_sapi_name();
$phpIniPath = php_ini_loaded_file();
$isCompatible = version_compare($phpVersion, '8.0.0', '>=');

// Check WAMP PHP versions
$wampPhpVersions = [];
if (file_exists('C:/wamp64/bin/php')) {
    $phpDirs = glob('C:/wamp64/bin/php/*', GLOB_ONLYDIR);
    foreach ($phpDirs as $phpDir) {
        $wampPhpVersions[] = basename($phpDir);
    }
}

// Get current WAMP PHP version
$currentWampPhp = '';
if (file_exists('C:/wamp64/bin/php/php.exe')) {
    $output = [];
    exec('C:/wamp64/bin/php/php.exe -v', $output);
    if (!empty($output[0])) {
        $currentWampPhp = $output[0];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correction de la vérification de version PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Correction de la vérification de version PHP</h1>
            
            <!-- PHP Info -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informations PHP</h2>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="font-medium">Version PHP:</span>
                        <span class="<?php echo $isCompatible ? 'text-green-600' : 'text-red-600'; ?> font-semibold">
                            <?php echo htmlspecialchars($phpVersion); ?>
                            <?php echo $isCompatible ? '✓' : '✗'; ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="font-medium">SAPI PHP:</span>
                        <span><?php echo htmlspecialchars($phpSapi); ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="font-medium">Fichier de configuration PHP:</span>
                        <span class="text-sm"><?php echo htmlspecialchars($phpIniPath); ?></span>
                    </div>
                    <?php if ($currentWampPhp): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <span class="font-medium">PHP WAMP actuel:</span>
                        <span><?php echo htmlspecialchars($currentWampPhp); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($wampPhpVersions)): ?>
                <h3 class="text-lg font-semibold mt-6 mb-2">Versions PHP disponibles dans WAMP</h3>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($wampPhpVersions as $version): ?>
                    <div class="p-2 bg-gray-50 rounded">
                        <?php echo htmlspecialchars($version); ?>
                        <?php if (version_compare($version, '8.0.0', '>=')): ?>
                            <span class="text-green-600 ml-2">✓</span>
                        <?php else: ?>
                            <span class="text-red-600 ml-2">✗</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Actions</h2>
                
                <div class="space-y-4">
                    <!-- Fix Controller -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-medium mb-2">1. Modifier le contrôleur pour ignorer la vérification de version PHP</h3>
                        <p class="text-gray-600 mb-4">Cette action modifiera le fichier SetupController.php pour ignorer la vérification de version PHP.</p>
                        <form method="post" class="mt-2">
                            <input type="hidden" name="action" value="fix_controller">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Modifier le contrôleur
                            </button>
                        </form>
                    </div>
                    
                    <!-- Clear Cache -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-medium mb-2">2. Vider le cache Laravel</h3>
                        <p class="text-gray-600 mb-4">Cette action videra tous les caches de Laravel pour appliquer les modifications.</p>
                        <form method="post" class="mt-2">
                            <input type="hidden" name="action" value="clear_cache">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Vider le cache
                            </button>
                        </form>
                    </div>
                    
                    <!-- Switch PHP Version -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-medium mb-2">3. Changer la version PHP dans WAMP</h3>
                        <p class="text-gray-600 mb-4">Pour changer la version PHP utilisée par WAMP:</p>
                        <ol class="list-decimal list-inside space-y-1 text-gray-700">
                            <li>Cliquez sur l'icône WAMP dans la barre des tâches</li>
                            <li>Allez dans PHP → Version</li>
                            <li>Sélectionnez une version PHP 8.0.0 ou supérieure</li>
                            <li>Redémarrez les services WAMP</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <!-- Result -->
            <?php if ($result): ?>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Résultat</h2>
                <div class="p-4 rounded-lg <?php echo $result['success'] ? 'bg-green-100 border border-green-200' : 'bg-red-100 border border-red-200'; ?>">
                    <p class="<?php echo $result['success'] ? 'text-green-800' : 'text-red-800'; ?> font-medium">
                        <?php echo htmlspecialchars($result['message']); ?>
                    </p>
                    <?php if (isset($result['details'])): ?>
                    <div class="mt-2 text-sm">
                        <ul class="list-disc list-inside">
                            <?php foreach ($result['details'] as $detail): ?>
                            <li><?php echo htmlspecialchars($detail); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Next Steps -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Étapes suivantes</h2>
                <p class="text-gray-700 mb-4">Après avoir effectué les actions ci-dessus:</p>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Retournez à la <a href="/" class="text-blue-600 hover:underline">page d'installation</a> et essayez à nouveau</li>
                    <li>Si vous rencontrez toujours des problèmes, consultez les <a href="/phpinfo.php" class="text-blue-600 hover:underline">informations PHP détaillées</a></li>
                    <li>Assurez-vous que votre serveur web utilise la bonne version de PHP</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html> 