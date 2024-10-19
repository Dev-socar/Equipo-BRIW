import { formFile } from "./selectores.js";
import { handleForm } from "./uploadFile.js";

formFile?.addEventListener("submit", handleForm);
