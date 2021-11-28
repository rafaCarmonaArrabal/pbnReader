<?php
include(VIEW_PATH . 'templates/headerTemplateView.php');
?>
    <article class="container mx-auto">
        <h2 class="text-1xl xl:text-3xl capitalize"><?php echo $tournamentName ?? 'Nombre del torneo.' ?></h2>
        <?php
        include(VIEW_PATH . 'templates/resultNavTemplateView.php');
        $absolute =  (count($rankings ?? [])) === 0 ? '' : 'absolute';
        ?>
        <section class="w-11/12 xl:w-full mx-auto mt-4 relative">
            <div id="table" class="overflow-y-auto font-bold bg-white border border-gray-500">
                <div id="column-names" class="flex flex-wrap bg-gray-600 text-gray-100 shadow-xl <?php echo $absolute?> w-full">
                    <div class="w-1/6 text-center px-2 py-2 border-r">Clas.</div>
                    <div class="w-3/6 text-center px-2 py-2 border-l">Pareja</div>
                    <div class="w-1/6 text-center px-2 py-2 border-l">Dirección</div>
                    <div class="w-1/6 text-center px-2 py-2 border-l">% Punt.</div>
                </div>
                <?php
                if (!empty($rankings)) {
                    foreach ($rankings as $row => $ranking) {
                        $border = \end($rankings) === $ranking ? "" : "border-b";
                        $padding = $row === 0 ? 'pt-16 md:pt-10' : '';
                        echo "<div class='flex flex-wrap {$border} {$padding}'>";
                        echo "<div class='w-1/6 text-center px-2 py-2 border-r border-t-0'>{$ranking['rank']}</div>";
                        echo "<div class='w-3/6 text-center px-2 py-2 border-l border-t-0'><a href='{$baseUrl}resultado/{$tournament_id}/scorecard/{$ranking['pair_id']}' class='text-main-blue-500 hover:text-main-blue-700 animation'>{$ranking['names']}</a></div>";
                        echo "<div class='w-1/6 text-center px-2 py-2 border-l border-t-0'>{$ranking['direction']}</div>";
                        echo "<div class='w-1/6 text-center px-2 py-2 border-l border-t-0'>{$ranking['percentage_session']}%</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='w-full text-center px-2 py-2 border-r border-t-0'>No hay resultados.</div>";
                }
                ?>
            </div>
        </section>
    </article>
<?php
include(VIEW_PATH . 'templates/footerTemplateView.php');

