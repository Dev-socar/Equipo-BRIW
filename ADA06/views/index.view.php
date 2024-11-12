<!DOCTYPE html>
<html lang="es">

<head>
	<?php require("views/head.view.php"); ?>
	
	<title>ADA 06  Búsqueda empresarial</title>
	<meta property="og:title" content="ADA 03 Lenguajes de Consulta" />
	<meta property="og:description" content="" />
</head>

<body>
	<?php require("views/navbar.view.php"); ?>


	<section class="mt-[15vh]">
		<div class="container">
			<form action="" class="flex">
				<div class="grid grid-cols-3 items-center w-3/4 gap-5">
					<div class="col-span-2">
						<input type="text" value="<?=$textQuery?>" name="query" required placeholder="" class="border border-cafe w-full rounded-md p-[8px_16px] text-[20px] outline-none text-cafe font-poppins">
					</div>				

					<div class="col-span-1">
						<button class="p-[15px] bg-cafe text-white rounded-[20px] opacity-85 hover:opacity-100 font-poppins">
							Consultar
						</button>
					</div>
					<div class="col-span-1">
						<a 
							href="<?=RUTA?>/loadFile.php"
							class="p-[10px] bg-cafe text-white text-[10px] rounded-[10px] opacity-85 hover:opacity-100 font-poppins">
							Subir archivos
						</a>
					</div>
				</div>
			</form>
		</div>
	</section>


	<section class="results mt-10">
		<div class="container">
			<div class="grid col-span-1 w-1/2">
				<?php  foreach ($resultados as $key => $result): ?>
					<div class="mt-8 hover:translate-x-2 transition-all" >
						<a href="<?=RUTA.'/helpers/'.$result['ruta_del_documento']?>" target="_blank"  class="">
							<div class="title font-poppinsBold text-cafe"> <?=$result['nombre_real']?> </div>
							<div class="contenido"> <?= substr($result['contenido'], 0, 300)."..." ?>  </div>
						</a>
						<div class="relative mt-6">
							<a href="<?=RUTA.'/helpers/'.$result['ruta_del_documento']?>" 
								download="<?=$result['nombre_real']?>"
								class="p-[10px] bg-cafe text-white rounded-[10px] opacity-85 hover:opacity-100 font-poppins">
								Descargar 
							</a>
						</div>
					</div>
				<?php  endforeach; ?>
			</div>
		</div>
	</section>



	<?php require("views/footer.view.php"); ?>
</body>

</html>