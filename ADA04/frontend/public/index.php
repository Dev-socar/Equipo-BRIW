<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app.css">
    <title>ADA 04 - Indización y búsqueda</title>
</head>

<body class="grid items-center justify-center bg-slate-50 pt-5">
    <h1 class="text-4xl text-center font-bold">Indización y búsqueda</h1>
    <section class="mt-10">
        <h2 class="text-2xl">Subir Archivos al Servidor</h2>
        <form class="max-w-lg mx-auto mt-5 flex items-start gap-10" id="formFiles" enctype="multipart/form-data">
            <div>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="user_avatar_help" type="file" id="files" name="files[]" multiple />
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-300">Archivos de texto unicamente</div>
            </div>
            <button type="submit"
                class="bg-gray-900 block w-32 p-2 rounded text-white hover:bg-gray-700">Enviar</button>
        </form>
    </section>

    <section class="mt-20">
        <h2 class="text-2xl">Buscar en Archivos</h2>
        <form class="max-w-lg mx-auto mt-5 space-y-3" id="formQuery">
            <div>
                <label for="base-input" class="block mb-2 text-lg font-medium text-gray-500">Que deseas buscar?</label>
                <input type="text" id="inputQuery" placeholder="Ingresa tu busqueda"
                    class="text-base rounded-lg  block w-full p-2.5  border-gray-600 placeholder-gray-400 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit"
                class="bg-gray-900 block w-32 p-2 rounded text-white hover:bg-gray-700">Buscar</button>
        </form>
    </section>

    <script src="./js/app.js" type="module"></script>
</body>

</html>