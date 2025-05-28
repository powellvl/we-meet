export function initModalMap(modalId, mapId, latFieldId, lngFieldId) {
  const modalEl = document.getElementById(modalId);

  modalEl.addEventListener("shown.bs.modal", () => {
    const map = new google.maps.Map(document.getElementById(mapId), {
      zoom: 6,
      center: { lat: 46.603354, lng: 1.888334 }, // France par défaut
    });

    let marker = null;

    map.addListener("click", (e) => {
      const lat = e.latLng.lat();
      const lng = e.latLng.lng();

      if (marker) {
        marker.setPosition(e.latLng);
      } else {
        marker = new google.maps.Marker({
          position: e.latLng,
          map: map,
        });
      }

      document.getElementById(latFieldId).value = lat;
      document.getElementById(lngFieldId).value = lng;
    });
  });
}
