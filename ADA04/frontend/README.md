# _Frontend del Proyecto_

### Requisitos
- Tener node.js instalado

## Tecnologias

Las Tecnologias usadas son:

- [HTML] 
- [JavaScript] 
- [Tailwind CSS] 

### Descripción de Carpetas y Archivos

- **/src**: Contiene el código fuente del proyecto.
  - **/css**: Archivo de configuracion para Tailwind CSS.
    - **/config.css**: Archivo con directivas de Tailwind CSS.
  - **/js**: Archivos de Javascript (usando modulos).
    - **/app.js**: Archivo principal de JS.
    - **/selectores.js**: Archivo donde se obtenienen los elementos a usar (formulario, inputs, etc).
    - **/uploadFile.js**: Archivo que contiene funciones para validar y enviar la peticion a la API para subir archivos.

- **/public**: Archivos públicos que se sirven directamente.
  - **index.html**: Archivo HTML principal.
  - **app.css**: Archivo CSS compilado.

- **package.json**: Archivo de configuración del proyecto, que incluye dependencias y scripts.

- **README.md**: Documentación principal del proyecto.

- **tailwind.config.js**: Archivo de configuracion de Tailwind CSS.

## Instalacion

Si deseas modificar los estilos de la aplicacion debes ejecutar primero estos comandos

Verificar que estas en la carpeta [frontend], posteriormente instalar las dependencias, las dependencias de desarrollo e iniciar el script para compilar los estilos.

```sh
cd frontend
npm i
npm run css
```

