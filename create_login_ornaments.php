<?php
$base = __DIR__ . '/public/images/login';
if (!is_dir($base)) {
    mkdir($base, 0777, true);
}

function canvas(int $w, int $h)
{
    $img = imagecreatetruecolor($w, $h);
    imagesavealpha($img, true);
    $trans = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $trans);
    return $img;
}

function drawPetal($img, $cx, $cy, $inner, $outer, $angleDeg, $spreadDeg, $color)
{
    $rad = M_PI / 180;
    $a = $angleDeg * $rad;
    $spread = $spreadDeg * $rad;
    $points = [];
    $points[] = $cx + cos($a) * $inner;
    $points[] = $cy + sin($a) * $inner;
    $points[] = $cx + cos($a + $spread) * $outer;
    $points[] = $cy + sin($a + $spread) * $outer;
    $points[] = $cx + cos($a - $spread) * $outer;
    $points[] = $cy + sin($a - $spread) * $outer;
    imagefilledpolygon($img, array_map('intval', $points), 3, $color);
}

function drawLeaf($img, $cx, $cy, $width, $height, $angleDeg, $color)
{
    $rad = M_PI / 180;
    $a = $angleDeg * $rad;
    $cos = cos($a);
    $sin = sin($a);
    $pts = [];
    $pts[] = $cx + (-$width * $cos - 0 * $sin);
    $pts[] = $cy + (-$width * $sin + 0 * $cos);
    $pts[] = $cx + (0 * $cos - $height * $sin);
    $pts[] = $cy + (0 * $sin + $height * $cos);
    $pts[] = $cx + ($width * $cos - 0 * $sin);
    $pts[] = $cy + ($width * $sin + 0 * $cos);
    imagefilledpolygon($img, array_map('intval', $pts), 3, $color);
}

// Flower ornament
$flower = canvas(480, 480);
$gold = imagecolorallocate($flower, 238, 202, 123);
$rose = imagecolorallocate($flower, 243, 155, 192);
$amber = imagecolorallocate($flower, 250, 214, 150);
for ($i = 0; $i < 6; $i++) {
    drawPetal($flower, 240, 240, 40, 160, $i * 60, 22, $i % 2 ? $rose : $amber);
}
imagefilledellipse($flower, 240, 240, 150, 150, $gold);
imagefilledellipse($flower, 240, 240, 60, 60, $rose);
imagepng($flower, $base . '/ornament-flower.png');
imagedestroy($flower);

// Leaf arc ornament
$leaf = canvas(420, 420);
$leafDark = imagecolorallocate($leaf, 120, 182, 143);
$leafMid = imagecolorallocate($leaf, 169, 216, 176);
for ($i = 0; $i < 5; $i++) {
    drawLeaf($leaf, 210, 210, 40 + $i * 12, 160 + $i * 18, -25 + $i * 12, $i % 2 ? $leafMid : $leafDark);
}
imagepng($leaf, $base . '/ornament-leaf.png');
imagedestroy($leaf);

// Gunungan ornament
$gunungan = canvas(420, 520);
$shadow = imagecolorallocatealpha($gunungan, 0, 0, 0, 80);
imagefilledellipse($gunungan, 210, 480, 320, 120, $shadow);
$gunGold = imagecolorallocate($gunungan, 218, 176, 94);
$gunDark = imagecolorallocate($gunungan, 158, 117, 52);
$points = [
    210, 40,
    320, 260,
    300, 420,
    120, 420,
    100, 260,
];
imagefilledpolygon($gunungan, $points, 5, $gunGold);
$inner = [
    210, 110,
    275, 250,
    260, 380,
    160, 380,
    145, 250,
];
imagefilledpolygon($gunungan, $inner, 5, $gunDark);
imagefilledellipse($gunungan, 210, 260, 90, 90, $gunGold);
imagefilledellipse($gunungan, 210, 260, 46, 46, $gunDark);
imagepng($gunungan, $base . '/ornament-gunungan.png');
imagedestroy($gunungan);

?>
