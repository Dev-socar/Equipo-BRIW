import { formFile, formQuery } from "./selectores.js";
import { handleFormFile } from "./uploadFile.js";
import { handleFormQuery } from "./search.js";

formFile?.addEventListener("submit", handleFormFile);
formQuery?.addEventListener("submit", handleFormQuery);
