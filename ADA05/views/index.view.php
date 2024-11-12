<!DOCTYPE html>
<html lang="es">

<head>
	<?php require("views/head.view.php"); ?>
	
	<title>ADA 05 Buscador</title>
	<meta property="og:title" content="" />
	<meta property="og:description" content="" />
</head>

<body>
	<?php require("views/navbar.view.php"); ?>


	<section class="mt-[15vh]">
		<div class="container">
			<form action="" class="flex">
				<div class="grid grid-cols-3 items-center w-3/4 gap-5">
					<div class="col-span-2">
						<input type="text" value="" name="search" required placeholder="Search...." class="border border-black w-full rounded-md p-[8px_16px] text-[20px]">
					</div>				

					<div class="col-span-1">
						<button class="p-[15px] bg-black text-white rounded-[20px] hover:bg-white hover:text-black transition ease-linear hover:border hover:border-black ">
							Consultar
						</button>
					</div>
				</div>
			</form>
		</div>
	</section>

	<section class="">
		<div class="container">
			<div class="grid grid-cols-1 w-10/12">
				<?php foreach($allResults as $result): ?>
					<div class="p-2 mt-4 content relative translate-x-0 hover:translate-x-2 transition-transform duration-300 ">
						<a href="<?=$result["link"]?>" target="_blank" class=" ">
							<h1 class="title font-black font-poppins text-xl"> <?= $result["titulo"] ?> </h1>
							<div class="buscador font-poppins text-sm">
								Buscador: <?= $result["buscador"] ?>
							</div>
							<div class="valores font-poppins text-sm">
								<p> Puntuación: <?=$result["score"]?> </p>
								<p> Puntación normalizada: <?=$result["normalizeScore"]?></p>
							</div>
						</a>
					</div>
				<?php endforeach ?>

			</div>
		</div>
	</section>



	<?php require("views/footer.view.php"); ?>
</body>

</html>