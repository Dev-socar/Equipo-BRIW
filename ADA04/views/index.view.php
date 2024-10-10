<!DOCTYPE html>
<html lang="es">

<head>
	<?php require("views/head.view.php"); ?>
	
	<title>ADA 03 Lenguajes de Consulta</title>
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
						<input type="text" value="<?=$TempTextQuery == "" ? "" : $TempTextQuery ?> " name="query" required placeholder="Jhon Doe" class="border border-black w-full rounded-md p-[8px_16px] text-[20px]">
					</div>				

					<div class="col-span-1">
						<button class="p-[15px] bg-black text-white rounded-[20px] hover:bg-white hover:text-black transition ease-linear hover:outline hover:outline-3 hover:outline-black ">
							Consultar
						</button>
					</div>

					<div class="col-span-3">
						<p class="font-poppins">Consulta final:</p>
						<div class="bg-black p-4 text-white">
							<p><?="SELECT * FROM ".$tabla." WHERE $query"?></p>
						</div>

					</div>
				</div>
			</form>
		</div>
	</section>


	<section class="results mt-10">
		<div class="container">
			<div class="row overflow-scroll min-h-[50vh]">

				<?php if ( isset($resultado->num_rows) && $resultado->num_rows > 0): ?>
						<div class="">
							<div class="header  ">
								<div class="flex flex-nowrap  mb-5">
									<?php 
									// Imprimir los encabezados de la tabla
									$firstRow = $resultado->fetch_assoc(); // Obtener la primera fila
									
									foreach ($firstRow as $column => $value): ?>
										<div class="px-[8px] border-r border-r-black min-w-[150px] border-b border-b-black"><?php echo str_replace("_", ' ', htmlspecialchars($column)); ?></div class="">
									<?php endforeach; ?>
								</div>
							</div>
							<div class="body">

								<div class="flex flex-nowrap">
									<?php 								
									foreach ($firstRow as $value): ?>
										<div class="px-[8px] border-r border-r-black min-w-[150px] border-b border-b-black"> <?php echo htmlspecialchars($value) ?> </div>
									<?php endforeach; ?>
								</div>
								
								<?php while ($row = $resultado->fetch_assoc()): ?>
									<div class="flex flex-nowrap">
										<?php foreach ($row as $value): ?>
											<div class="px-[8px] border-r border-r-black min-w-[150px] border-b border-b-black"> <?php echo htmlspecialchars($value) ?> </div>
										<?php endforeach; ?>
									</div>
								<?php endwhile; ?>
								
							</div>
						</div>
				<?php else: ?> 
					0 resultados encontrados.
				<?php endif ?>
			</div>
		</div>
	</section>



	<?php require("views/footer.view.php"); ?>
</body>

</html>