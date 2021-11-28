<?php
$resultUrl=  "a";
$scoreCardUrl = "b";
$travellerUrl = "c";
$handUrl = "d";
$current = "a";
$baseUrl = SERVER_URL . "index.php/";
$tournament_id = $tournament_id ?? 2;
?>

<nav id="resultNav" class="w-full bg-gray-500 container mx-auto flex">
    <a href="<?php echo "{$baseUrl}resultado/{$tournament_id}"?>" class="py-1 md:py-2 text-white px-2 hover:bg-gray-700 font-bold animation <?php echo $current === $resultUrl ? 'bg-gray-600' : ''?>">Resultados</a>
    <a href="<?php echo "{$baseUrl}resultado/{$tournament_id}/scorecard"?>" class="py-1 md:py-2 text-white px-2 hover:bg-gray-700 font-bold animation <?php echo $current === $scoreCardUrl ? 'bg-gray-600' : ''?>">Score card</a>
    <a href="" class="py-1 md:py-2 text-white px-2 hover:bg-gray-700 font-bold animation <?php echo $current === $travellerUrl ? 'bg-gray-600' : ''?>">Travellers</a>
    <a href="" class="py-1 md:py-2 text-white px-2 hover:bg-gray-700 font-bold animation <?php echo $current === $handUrl ? 'bg-gray-600' : ''?>">Manos</a>
</nav>
