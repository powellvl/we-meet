document.addEventListener("DOMContentLoaded", () => {
  const collectionHolder = document.getElementById("languageCollection");
  const prototype = collectionHolder.dataset.prototype;
  let index = collectionHolder.children.length;

  function addLanguageField() {
    const newForm = prototype.replace(/__name__/g, index);
    const div = document.createElement("div");
    div.classList.add("language-block", "mb-3");
    div.innerHTML = newForm;
    collectionHolder.appendChild(div);
    index++;

    // Important : on doit réappliquer les écouteurs
    setupSelectEvents();
  }

  function setupSelectEvents() {
    const allSelects = collectionHolder.querySelectorAll(".language-select");

    allSelects.forEach((select) => {
      if (!select.dataset.bound) {
        select.addEventListener("change", function () {
          const isLast =
            select.closest(".language-block") ===
            collectionHolder.lastElementChild;
          const value = select.value;

          if (isLast && value && value !== "") {
            addLanguageField();
          }
        });
        select.dataset.bound = "true"; // on marque cet élément comme déjà lié
      }
    });
  }

  setupSelectEvents(); // Premier appel
});
