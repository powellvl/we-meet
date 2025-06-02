/**
 * Gestion des filtres par URL
 */

document.addEventListener("DOMContentLoaded", () => {
  const searchTitleInput = document.getElementById("search-title");
  const filterLanguageSelect = document.getElementById("filter-language");
  const filterDateInput = document.getElementById("filter-date");
  const roomCards = document.querySelectorAll(".room-card");

  // Fonction pour mettre à jour l'URL avec les paramètres des filtres
  function updateUrlWithFilters() {
    const searchParams = new URLSearchParams(window.location.search);

    // Mettre à jour ou supprimer les paramètres
    if (searchTitleInput.value) {
      searchParams.set("title", searchTitleInput.value);
    } else {
      searchParams.delete("title");
    }

    if (filterLanguageSelect.value) {
      searchParams.set("language", filterLanguageSelect.value);
    } else {
      searchParams.delete("language");
    }

    if (filterDateInput.value) {
      searchParams.set("date", filterDateInput.value);
    } else {
      searchParams.delete("date");
    }

    // Construire l'URL et remplacer l'historique
    const newUrl =
      window.location.pathname +
      (searchParams.toString() ? "?" + searchParams.toString() : "");
    history.replaceState(null, "", newUrl);

    // Filtrer les cartes en fonction des filtres actuels
    filterRooms();
  }

  // Fonction pour filtrer les rooms
  function filterRooms() {
    const titleFilter = searchTitleInput.value.toLowerCase();
    const languageFilter = filterLanguageSelect.value.toLowerCase();
    const dateFilter = filterDateInput.value;

    roomCards.forEach((card) => {
      const title = card.dataset.title;
      const language = card.dataset.lang;
      const date = card.dataset.date;

      const titleMatch = !titleFilter || title.includes(titleFilter);
      const languageMatch = !languageFilter || language === languageFilter;
      const dateMatch = !dateFilter || date === dateFilter;

      // Afficher ou masquer la carte en fonction des filtres
      if (titleMatch && languageMatch && dateMatch) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }

  // Écouter les événements pour les filtres
  searchTitleInput.addEventListener("input", updateUrlWithFilters);
  filterLanguageSelect.addEventListener("change", updateUrlWithFilters);
  filterDateInput.addEventListener("change", updateUrlWithFilters);

  // Filtrer les rooms au chargement initial
  filterRooms();
});
