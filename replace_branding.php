<?php
// replace_branding.php - Run once to replace all old DipBan branding with Arup Enterprise
$root = __DIR__;

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
    'woodworking &amp; sheet metal' => 'industrial magnetic separation',
    'Woodworking &amp; Sheet Metal' => 'Industrial Magnetic Separation',
    'woodworking & sheet metal'     => 'industrial magnetic separation',
    'Woodworking & Sheet Metal'     => 'Industrial Magnetic Separation',
    'Advanced industrial machinery crafted for woodworking & sheet metal industries. Engineering excellence that keeps your production running at full power — 24 × 7.' 
                                    => 'Premier manufacturer of industrial magnetic separators — Drum Type, Suspended Magnets, Hopper Magnets & more. Trusted across India for 38+ years.',
    'Where Precision Meets Innovation' => 'Welcome to Arup Enterprise',
    'Since 2022'                    => '38+ Years',
    '200+ Happy Clients'            => '7,000+ Custom Solutions',
    'Pan-India Service Network'     => '28k+ Products Delivered',
    '4.9/5 Customer Rating'         => '100% Satisfied Clients',
    'Why Choose Arup Enterprise'    => 'Why Choose Arup Enterprise',
];

// Get all PHP files (exclude admin, PHPMailer)
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$phpFiles = [];
foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;
    $path = $file->getRealPath();
    if (str_contains($path, DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR)) continue;
    if (str_contains($path, DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR)) continue;
    if ($file->getFilename() === 'replace_branding.php') continue;
    $phpFiles[] = $path;
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

echo "\n✅ Done. Updated " . count($updated) . " files: " . implode(', ', $updated) . "\n";
