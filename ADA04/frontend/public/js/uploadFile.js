import { inputFile } from "./selectores.js";

const handleFormFile = (e) => {
  e.preventDefault();
  const files = inputFile.files;

  // Validar si se seleccionó al menos un archivo
  if (files.length === 0) {
    alert("Por favor selecciona al menos un archivo.");
    return;
  }
  uploadFiles(files);
};

const uploadFiles = async (files) => {
  // Crear una instancia de FormData para enviar los archivos
  const formData = new FormData();

  // Agregar los archivos a formData con un nombre dinámico
  for (let i = 0; i < files.length; i++) {
    formData.append(`archivo_${i}`, files[i]);
  }

  try {
    const url = "http://localhost:4000/API/uploadFile.php";
    const response = await fetch(url, {
      method: "POST",
      body: formData,
    });

    // Verificar si la respuesta fue exitosa
    if (!response.ok) {
      throw new Error("Error en la subida: " + response.statusText);
    }
    const data = await response.json();
    console.log(data);
    inputFile.value = "";
  } catch (error) {
    return { success: false, message: error.message }; // Devolver un objeto JSON con el error
  }
};

export { handleFormFile };
