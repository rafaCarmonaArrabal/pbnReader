<?php
include(VIEW_PATH . 'templates/headerTemplateView.php');
$urlPost = SERVER_URL . "index.php/post/pbn";
?>
<article class="container mx-auto ">
    <h2 class="text-1xl xl:text-3xl">Subida de archivos pbn.</h2>
    <?php
        if(!empty($message)){
            $bgColor = $message['status'] == 'ok' ? "bg-green-500" : "bg-red-500";
            echo "<div class='w-full px-2 py-2 text-white {$bgColor}'>{$message['message']}</div>";
        }
    ?>
    <section class="pt-2">
        <form method="POST" action="<?php echo $urlPost ?>" enctype="multipart/form-data">
            <div class="w-full mt-2 mb-4">
                <label for="torneo" class="font-bold block">ID del torneo</label>
                <input type="number" min="1" id="torneo" name="torneo" required="required" class="border pl-2 py-1 border-black w-1/5 rounded-none">
            </div>
            <?php
                include(VIEW_PATH . 'templates/tokenComponent.php');
            ?>
            <div>
                <label for="file_pbn" class="font-bold block">Subir archivo</label>
                <input type="file" id="file_pbn" name="file_pbn" alt="Archivo pbn" accept=".pbn">
            </div>
            <div class="w-full mt-2 mb-4">
                <button type="submit" class="border border-black bg-gray-200 hover:bg-gray-400 animation px-4 py-0">Enviar</button>
            </div>
        </form>
    </section>
</article>
<?php
include(VIEW_PATH . 'templates/footerTemplateView.php');
?>
