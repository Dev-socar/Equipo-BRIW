# ADA04. Indización y búsqueda

La aplicación Web permitirá al usuario cargar archivos de texto plano desde un formulario. Cada vez que se cargue una colección de archivos, la aplicación generará el índice invertido de todos los archivos que se han ido guardando en una carpeta en el servidor. La aplicación Web utilizará una tabla de diccionario y de posting de una base de datos de MySQL. Se guardará el término índice, número de documentos que lo contienen, el total de frecuencias, el identificador del documento, la frecuencia del término en ese documento, el nombre del archivo de texto que lo contiene y una porción del texto (50 caracteres).

Reto: ¿Cómo implementamos la función CADENA? pista: guardar en los posting las posiciones del término índice dentro del documento.

Al realizar una consulta, se presentarán enlaces a los archivos que podrán ser descargados junto con el fragmento de texto almacenado e indicar el valor de la función de similitud Coseno. Los archivos que aparezcan en el resultado estarán ordenados por la relevancia tf-idf.
