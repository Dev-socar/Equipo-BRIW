# _Frontend del Proyecto_

### Requisitos

- Tener node.js instalado

## Tecnologias

Las Tecnologias usadas son:

- [PHP]
- [JavaScript]
- [Tailwind CSS]

### Descripción de Carpetas y Archivos

```
.
├── src
│   ├── css
│   │   ├── config.css
│
├── public
│   ├── index.php
│   ├── app.css
│   ├── js
│       ├── app.js
│       ├── search.js
│       ├── selectores.js
│       └── uploadFile.js
│       
│ 
├── package.json
├── README.md
└── tailwind.config.js
```

## Instalacion

Si deseas modificar los estilos de la aplicacion debes ejecutar primero estos comandos

Verificar que estas en la carpeta [frontend], posteriormente instalar las dependencias, las dependencias de desarrollo e iniciar el script para compilar los estilos.

```sh
cd frontend
npm i
npm run css
```

## Ejecutar Frontend

```sh
cd frontend/public
php -S localhost:4001
```
