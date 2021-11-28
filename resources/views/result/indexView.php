<?php
include(VIEW_PATH . 'templates/headerTemplateView.php');
$resultUrl = SERVER_URL . "index.php/resultado/";
?>
<article class="container mx-auto ">
    <h2 class="text-1xl xl:text-3xl">Resultados</h2>
    <section class="w-8/12 mx-auto mt-4 relative">
        <div id="table" class="w-full md:overflow-y-auto font-bold bg-white border border-gray-500">
            <div id="column-names" class="flex flex-wrap bg-gray-600 text-white shadow-xl absolute w-full">
                <div class="w-1/6 text-center px-2 py-2 border-r">ID</div>
                <div class="w-5/6 text-center px-2 py-2 border-l">Nombre</div>
            </div>
            <?php
            if (isset($tournaments)) {
                foreach ($tournaments as $row => $tournament) {
                    $border = \end($tournaments) === $tournament ? "" : "border-b";
                    $padding = $row === 0 ? 'pt-16 md:pt-10': '';
                    echo "<div id='column-names' class='flex flex-wrap {$border} {$padding}'>";
                    echo "<div class='w-1/6 text-center px-2 py-2 border-r border-t-0'>{$tournament['id']}</div>";
                    echo "<div class='w-5/6 text-center px-2 py-2 border-l border-t-0'><a target='_blank' href='{$resultUrl}{$tournament['id']}' class='text-main-blue-500 hover:text-main-blue-700 animation'>{$tournament['name']}</a></div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </section>
</article>
<?php
include(VIEW_PATH . 'templates/footerTemplateView.php');

