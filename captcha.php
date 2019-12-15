<?php
/*
                      &
                   %&&&                               (&&
                    &&&                                &&
                    &&&
                    &&&    *                             %
                    &&&&&/ *&&&&                     &&&
                    &&&      &&&                      ,&&.
                    &&&      /&&                      .&&
    &&,       &&&                   &&&        &&&
    &&,      /&&,                   &&&        &&&
    &&&     .&&                      .&&      &&&
    &&,&&&&&                            *&&&
    &&,
    &&,
   ,&&&

   PHOI Phonothèque Historique de l'Océan - CAPTCHA

   Créé par idéesculture (G. Michelin) - 2019
   Modifié par :
        (ajouter vos NOMS - DATES ici, 1 ligne par contributeur)

   This project is GNU GPL v3. https://www.gnu.org/licenses/gpl-3.0.html

   -----------------------------------------------------------------------------
   Images and icons in this project are CC BY NC SA if not elsewise documented.
   If you are using this project inside a commercial project, please do your own
   graphism and publish the sources, mandatory with GNU GPL v3 license. Thanks.
   -----------------------------------------------------------------------------
   captcha.php
*/

session_start();

function imagepatternedline($image, $xstart, $ystart, $xend, $yend, $color, $thickness=1, $pattern="11000011") {
    $pattern=(!strlen($pattern)) ? "1" : $pattern;
    $x=$xend-$xstart;
    $y=$yend-$ystart;
    $length=floor(sqrt(pow(($x),2)+pow(($y),2)));
    $fullpattern=$pattern;
    while (strlen($fullpattern)<$length) $fullpattern.=$pattern;
    if (!$length) {
        if ($fullpattern[0]) imagefilledellipse($image, $xstart, $ystart, $thickness, $thickness, $color);
        return;
    }
    $x1=$xstart;
    $y1=$ystart;
    $x2=$x1;
    $y2=$y1;
    $mx=$x/$length;
    $my=$y/$length;
    $line="";
    for($i=0;$i<$length;$i++){
        if (strlen($line)==0 or $fullpattern[$i]==$line[0]) {
            $line.=$fullpattern[$i];
        }else{
            $x2+=strlen($line)*$mx;
            $y2+=strlen($line)*$my;
            if ($line[0]) imageline($image, round($x1), round($y1), round($x2-$mx), round($y2-$my), $color);
            $k=1;
            for($j=0;$j<$thickness-1;$j++) {
                $k1=-(($k-0.5)*$my)*(floor($j*0.5)+1)*2;
                $k2= (($k-0.5)*$mx)*(floor($j*0.5)+1)*2;
                $k=1-$k;
                if ($line[0]) {
                    imageline($image, round($x1)+$k1, round($y1)+$k2, round($x2-$mx)+$k1, round($y2-$my)+$k2, $color);
                    if ($y) imageline($image, round($x1)+$k1+1, round($y1)+$k2, round($x2-$mx)+$k1+1, round($y2-$my)+$k2, $color);
                    if ($x) imageline($image, round($x1)+$k1, round($y1)+$k2+1, round($x2-$mx)+$k1, round($y2-$my)+$k2+1, $color);
                }
            }
            $x1=$x2;
            $y1=$y2;
            $line=$fullpattern[$i];
        }
    }
    $x2+=strlen($line)*$mx;
    $y2+=strlen($line)*$my;
    if ($line[0]) imageline($image, round($x1), round($y1), round($xend), round($yend), $color);
    $k=1;
    for($j=0;$j<$thickness-1;$j++) {
        $k1=-(($k-0.5)*$my)*(floor($j*0.5)+1)*2;
        $k2= (($k-0.5)*$mx)*(floor($j*0.5)+1)*2;
        $k=1-$k;
        if ($line[0]) {
            imageline($image, round($x1)+$k1, round($y1)+$k2, round($xend)+$k1, round($yend)+$k2, $color);
            if ($y) imageline($image, round($x1)+$k1+1, round($y1)+$k2, round($xend)+$k1+1, round($yend)+$k2, $color);
            if ($x) imageline($image, round($x1)+$k1, round($y1)+$k2+1, round($xend)+$k1, round($yend)+$k2+1, $color);
        }
    }
}

$permitted_chars = 'ABC';

function generate_string($input, $strength = 10) {
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

$image = imagecreatetruecolor(600, 240);
$destimage = imagecreatetruecolor(300, 120);

imageantialias($image, true);

$colors = [];

$red = rand(100, 125);
$green = rand(100, 120);
$blue = rand(175, 220);

for($i = 0; $i < 5; $i++) {
    $colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
}

imagefill($image, 0, 0, imagecolorallocate($image, 255,255,255));

$white   = imagecolorallocate($image, 255, 255, 255);

for($i = 0; $i < 4; $i++) {
    imagesetthickness($image, rand(1, 4));
    $line_color = $colors[rand(1, 4)];
    /* Dessine une ligne pointillée de 5 pixels rouges, 5 pixels blancs */
    $style = array($line_color, $line_color, $line_color, $line_color, $line_color, $white, $white, $white, $white, $white);
    $style = Array(
        $line_color,
        $line_color,
        $line_color,
        $line_color,
        IMG_COLOR_TRANSPARENT,
        IMG_COLOR_TRANSPARENT,
        IMG_COLOR_TRANSPARENT,
        IMG_COLOR_TRANSPARENT
    );

    imagesetstyle($image, $style);
    $radius = round(rand(10, 180));
    //imagedashedline($image, rand(-10, 600), rand(-10, 240), $radius, $radius, $line_color);
    imagepatternedline($image, rand(-10, 600),  rand(-10, 240), rand(-10, 600), rand(-10, 240), $line_color, rand(1,5), "1111111100000000");
}

$blue1 = imagecolorallocate($image, 0, 162, 255); //rgb(0, 162, 255)
$blue2 = imagecolorallocate($image, 1, 76, 128);//rgb(1, 76, 128)
$blue3 = imagecolorallocate($image, 0, 118, 186);//rgb(0, 118, 186)
$blue4 = imagecolorallocate($image, 86, 194, 255);//rgb(86, 194, 255)
$textcolors = [$blue1, $blue2, $blue3, $blue4];

$fonts = [dirname(__FILE__).'/fonts/phoi-circles.ttf'];
//print reset($fonts);
//die();

$string_length = 1;
$captcha_string = generate_string($permitted_chars, $string_length);

$other_circles = ["G", "H", "I", "J", "K", "L"];
// OTHER CIRCLES
for($i = 0; $i < 8; $i++) {
    $size = rand(30, 160);
    $x = rand(0+$size, 600-$size);
    $y = rand(10+$size, 220-$size);
    imagettftext($image, $size, 0, $x, $y, $textcolors[rand(0, 3)], $fonts[array_rand($fonts)], $other_circles[round(rand(0,5))]);
}

// OPENED CIRCLE
$letter_space = 170/$string_length;
$initial = 15;
$size = rand(40, 120);
$angle = rand(150, 300);
$x = rand(50+$size, 550-$size);
$y = rand(30+$size, 190-$size);
imagettftext($image, $size, $angle, $x, $y, $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[0]);

$_SESSION['captcha_position_click'] = json_encode([$size, $angle, $x, $y]);

imagecopyresized($destimage, $image, 0, 0, 0, 0, 300,120,600,240);

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
