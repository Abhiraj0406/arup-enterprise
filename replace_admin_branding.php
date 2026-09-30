<?php
$root = __DIR__ . '/admin';

$replacements = [
    'DipBan Technical Services'     => 'Arup Enterprise',
    'DipBan'                        => 'Arup Enterprise',
    'dipbantechnicalservices.in'    => 'arup-enterprise.com',
    'sudip@arup-enterprise.com'     => 'enterprisearup@gmail.com',
    'sudip@dipbantechnicalservices' => 'enterprisearup@gmail.com',
    '9903126940'                    => '8013635806',
    '9903 12 6940'                  => '8013 635 806',
    '33-45081559'                   => '8839019950',
    '33 45081559'                   => '8839019950',
    'Knowledge of Machine'          => 'Magnetic Separator',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$phpFiles = [];
foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;
    $phpFiles[] = $file->getRealPath();
}

$updated = [];
foreach ($phpFiles as $path) {
    $content = file_get_contents($path);
    $original = $content;
    foreach ($replacements as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated[] = basename($path);
        echo "Updated: $path\n";
    }
}

echo "\nDone. Updated " . count($updated) . " files in admin folder.\n";
