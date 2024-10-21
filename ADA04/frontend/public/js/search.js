import { inputQuery, divResults } from "./selectores.js";

const handleFormQuery = (e) => {
  e.preventDefault();
  const query = inputQuery.value;
  if (query.trim() === "") {
    alert("Ingresa una busqueda...");
    return;
  }

  searchQuery(query);
};

const searchQuery = async (query) => {
  const url = "http://localhost:4000/API/search.php";
  const formData = new FormData();
  formData.append("query", query);
  try {
    const response = await fetch(url, {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    printResult(result);
  } catch (error) {
    console.log(error);
  }
};

const printResult = (result) => {
  console.log(result);
  if (result.message) {
    alert(result.message);
    return;
  }
  result.results.forEach((r) => {
    const { documento, fragmentos, similitud } = r;

    const div = document.createElement("div");
    div.classList.add("p-2", "rounded", "border", "border-gray-500");

    const p = document.createElement("p");
    p.classList.add("block");
    p.textContent = fragmentos;

    const span = document.createElement("span");
    span.classList.add('block')
    span.textContent = similitud;

    const enlace = document.createElement("a");
    enlace.classList.add("block");
    enlace.href = `http://localhost:4000/uploads/${documento}.txt`;
    enlace.download = documento;
    enlace.textContent = "Descargar"; // Agregar texto al enlace

    // Agregar todos los elementos al contenedor
    div.appendChild(p);
    div.appendChild(span);
    div.appendChild(enlace);

    divResults.appendChild(div);
  });
};

export { handleFormQuery };
