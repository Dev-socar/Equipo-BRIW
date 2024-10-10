<!DOCTYPE html>
<html lang="es">

<head>
	<?php require("views/head.view.php"); ?>
	
	<title>ADA 04</title>
	<meta property="og:title" content="ADA 03" />
	<meta property="og:description" content="" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

</head>

<body>

    <section class="form-section mt-[15vh]" id="test">
        <div class="container">
            <div class="flex">
                <h1 class="font-poppinsBold text-4xl text-cafe"> Cargar documentos </h1>
            </div>

            <form id="uploadForm" action="<?=RUTA?>/helpers/upload.php" method="post" enctype="multipart/form-data">
                <div class="form-group mt-5">
                    <p class=" font-poppins text-cafe mb-2">Selecciona el archivo que deseas indexar. </p>
                    <div class="flex items-center">
                        <label for="file" class="cursor-pointer p-[4px_8px] bg-[#e5db84] text-[#674f23] rounded-md font-poppins font-medium hover:bg-[#d8ce75] transition"> Seleccionar </label>
                        <p class=" font-poppinsBold text-sm ml-3" id="fileName"> </p>
                    </div>
                    <input type="file" accept=".txt" id="file" name="file" class=" hidden ">
                </div>

                <div class="form-group mt-5">
                    <button class="cursor-pointer p-[4px_8px] bg-cafe text-[#ffeeac] rounded-md font-poppins font-medium ">
                        Indexar
                    </button>
                </div>
            </form>
        </div>
    </section>
    <?php require("views/footer.view.php"); ?>
</body>

</html>