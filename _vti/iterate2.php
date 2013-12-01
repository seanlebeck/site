<?php

$hello1 = "hello11";
$hello2 = "hello22";
$hello3 = "hello33";
$hello4 = "hello44";
$hello5 = "hello55";
$hello6 = "hello66";
$hello7 = "hello77";
$hello8 = "hello88";
$hello9 = "hello99";
$hello10 = "hello110";

for ( $counter = 1; $counter <= 10; $counter += 1) {
        echo ${'hello' . $counter } , '<br>';
}

?>