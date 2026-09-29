<?php
// convert-favicon.php - Run once to create favicon.ico from logo.png
// Visit this file once, then delete it

$logo_path = 'assets/images/logo.png';

if (!file_exists($logo_path)) {
    die('Logo not found at: ' . $logo_path);
}

// Create 16x16 favicon
$source = imagecreatefrompng($logo_path);
if (!$source) {
    die('Could not load PNG. Make sure your logo is PNG format.');
}

// Create 16x16
$favicon_16 = imagecreatetruecolor(16, 16);
imagealphablending($favicon_16, false);
imagesavealpha($favicon_16, true);
$transparent = imagecolorallocatealpha($favicon_16, 0, 0, 0, 127);
imagefill($favicon_16, 0, 0, $transparent);
imagecopyresampled($favicon_16, $source, 0, 0, 0, 0, 16, 16, imagesx($source), imagesy($source));
imagepng($favicon_16, 'assets/images/favicon-16.png');
imagedestroy($favicon_16);

// Create 32x32
$favicon_32 = imagecreatetruecolor(32, 32);
imagealphablending($favicon_32, false);
imagesavealpha($favicon_32, true);
$transparent = imagecolorallocatealpha($favicon_32, 0, 0, 0, 127);
imagefill($favicon_32, 0, 0, $transparent);
imagecopyresampled($favicon_32, $source, 0, 0, 0, 0, 32, 32, imagesx($source), imagesy($source));
imagepng($favicon_32, 'assets/images/favicon-32.png');
imagedestroy($favicon_32);

// Create 64x64
$favicon_64 = imagecreatetruecolor(64, 64);
imagealphablending($favicon_64, false);
imagesavealpha($favicon_64, true);
$transparent = imagecolorallocatealpha($favicon_64, 0, 0, 0, 127);
imagefill($favicon_64, 0, 0, $transparent);
imagecopyresampled($favicon_64, $source, 0, 0, 0, 0, 64, 64, imagesx($source), imagesy($source));
imagepng($favicon_64, 'assets/images/favicon-64.png');
imagedestroy($favicon_64);

// Apple Touch Icon (180x180)
$apple = imagecreatetruecolor(180, 180);
imagealphablending($apple, false);
imagesavealpha($apple, true);
$transparent = imagecolorallocatealpha($apple, 0, 0, 0, 127);
imagefill($apple, 0, 0, $transparent);
imagecopyresampled($apple, $source, 0, 0, 0, 0, 180, 180, imagesx($source), imagesy($source));
imagepng($apple, 'assets/images/apple-touch-icon.png');
imagedestroy($apple);

imagedestroy($source);

echo "✅ Favicon files created successfully!<br><br>";
echo "Files created:<br>";
echo "✅ assets/images/favicon-16.png<br>";
echo "✅ assets/images/favicon-32.png<br>";
echo "✅ assets/images/favicon-64.png<br>";
echo "✅ assets/images/apple-touch-icon.png<br><br>";
echo "Add this to your header.php:<br>";
echo '&lt;link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16.png"&gt;<br>';
echo '&lt;link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32.png"&gt;<br>';
echo '&lt;link rel="icon" type="image/png" sizes="64x64" href="assets/images/favicon-64.png"&gt;<br>';
echo '&lt;link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png"&gt;<br>';