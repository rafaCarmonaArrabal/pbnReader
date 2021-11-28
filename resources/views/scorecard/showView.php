<?php
include(VIEW_PATH . 'templates/headerTemplateView.php');
$resultUrl = SERVER_URL . "index.php/resultado/";
?>
<article class="container mx-auto">
    <h2 class="text-1xl xl:text-3xl capitalize">Scorecards</h2>
    <?php
    include(VIEW_PATH . 'templates/resultNavTemplateView.php');
    $absolute =  (count($scorecards ?? [])) === 0 ? '' : 'absolute';
    ?>
    <div>
        <select name="players" id="players">
            <option value=""></option>
        </select>
    </div>
    <section class="w-8/12 mx-auto mt-4 relative">
        <div id="table" class="w-full md:overflow-y-auto font-bold bg-white border border-gray-500">
            <div id="column-names" class="flex flex-wrap bg-gray-600 text-white shadow-xl <?php echo $absolute?> w-full">
                <div class="w-1/12 text-center px-2 py-2 border-r truncate">Board</div>
                <div class="w-3/12 text-center px-2 py-2 border-r truncate">Rival</div>
                <div class="w-1/12 text-center px-2 py-2 border-r truncate">Por</div>
                <div class="w-1/12 text-center px-2 py-2 border-r truncate">Salida</div>
                <div class="w-2/12 text-center px-2 py-2 border-r truncate">Resultado</div>
                <div class="w-1/12 text-center px-2 py-2 border-r truncate">P. M.</div>
                <div class="w-1/12 text-center px-2 py-2 border-r truncate">Punt.</div>
                <div class="w-2/12 text-center px-2 py-2 border-r truncate">% punt.</div>
            </div>
            <?php
            if (isset($scorecards)) {
                foreach ($scorecards as $row => $scorecard) {
                    $border = \end($scorecards) === $scorecard ? "" : "border-b";
                    $padding = $row === 0 ? 'pt-16 md:pt-10': '';
                    echo "<div id='column-names' class='flex flex-wrap {$border} {$padding}'>";
                    echo "<div class='w-1/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['board']}</div>";
                    echo "<div class='w-3/12 text-center px-2 py-2 border-r border-t-0'><a target='_blank' href='{$baseUrl}resultado/{$tournament_id}/scorecard/{$scorecard['rival_id']}' class='text-main-blue-500 hover:text-main-blue-700 animation'>{$scorecard['rival']}</a></div>";
                    echo "<div class='w-1/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['declarer']}</div>";
                    echo "<div class='w-1/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['lead']}</div>";
                    echo "<div class='w-2/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['result']}</div>";
                    echo "<div class='w-1/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['mp']}</div>";
                    echo "<div class='w-1/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['score']}</div>";
                    echo "<div class='w-2/12 text-center px-2 py-2 border-r border-t-0'>{$scorecard['percentage']}%</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </section>
</article>
<?php
include(VIEW_PATH . 'templates/footerTemplateView.php');
