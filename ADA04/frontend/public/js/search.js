import { inputQuery } from "./selectores.js";

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
    console.log(result);
  } catch (error) {
    console.log(error);
  }
};

export { handleFormQuery };
