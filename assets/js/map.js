export function initMap(rooms) {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 12,
    center: { lat: 44.8378, lng: -0.5792 }, // Bordeaux par défaut
  });

  // Fonction pour afficher les détails d'une room dans la modale
  function showRoomDetails(room) {
    console.log("Showing room details:", room);

    // Configurer le titre, description et date
    document.getElementById("modalRoomTitle").textContent = room.title;
    document.getElementById("modalRoomDescription").textContent =
      room.description;
    document.getElementById("modalRoomCreator").textContent =
      room.creator.firstname + " " + room.creator.lastname;

    // Configurer l'avatar placeholder avec les initiales du créateur
    document.getElementById("modalRoomCreatorInitials").textContent =
      room.creator.firstname.charAt(0) + room.creator.lastname.charAt(0);

    document.getElementById("modalRoomDate").textContent = new Date(
      room.date
    ).toLocaleString("fr-FR");
    document.getElementById("modalRoomLanguage").textContent =
      room.language?.name ?? "Inconnue";

    // Emoji par langue
    const emojiMap = {
      français: "🇫🇷",
      anglais: "🇬🇧",
      espagnol: "🇪🇸",
      allemand: "🇩🇪",
      italien: "🇮🇹",
      japonais: "🇯🇵",
      chinois: "🇨🇳",
      russe: "🇷🇺",
      arabe: "🇸🇦",
      portugais: "🇵🇹",
    };
    const emoji = emojiMap[room.language?.name?.toLowerCase()] ?? "🌐";
    document.getElementById("modalRoomEmoji").textContent = emoji;

    // Places restantes
    const maxParticipants = 4;
    const currentCount = room.participants.length;
    const remaining = maxParticipants - currentCount;
    document.getElementById("modalRoomSlots").textContent =
      remaining > 0 ? `${remaining} place(s)` : "Complet";

    // Participants
    const list = document.getElementById("modalRoomParticipants");
    list.innerHTML = "";
    if (room.participants.length === 0) {
      list.innerHTML = `<p class="text-muted fst-italic">Aucun participant pour le moment.</p>`;
    } else {
      room.participants.forEach((p) => {
        const div = document.createElement("div");
        div.className =
          "d-flex justify-content-between align-items-center p-2 bg-light shadow-sm participant";

        div.innerHTML = `
          <div class="d-flex align-items-center gap-2">
            <div class="avatar-placeholder participant-avatar">
              <span>${p.firstname.charAt(0)}${p.lastname.charAt(0)}</span>
            </div>
            <div>${p.firstname} ${p.lastname}</div>
          </div>
          <span class="badge-membre">
            ${p.isProfessor ? "Professeur" : "Élève"}
          </span>
        `;
        list.appendChild(div);
      });
    }

    // Si user déjà inscrit ou créateur → désactiver bouton
    const joinBtn = document.getElementById("joinRoomLink");
    const currentUserId = window.currentUserId;
    const isCreator = room.creatorId === currentUserId;
    const alreadyJoined = room.participants.some((p) => p.id === currentUserId);

    // Vérification de debug
    console.log("Current user ID:", currentUserId);
    console.log("Room creator ID:", room.creatorId);
    console.log("Is creator:", isCreator);

    if (isCreator) {
      joinBtn.textContent = "Vous êtes l'organisateur";
      joinBtn.className = "btn btn-outline-secondary w-100";
      joinBtn.disabled = true;
    } else if (alreadyJoined) {
      joinBtn.textContent = "Déjà inscrit";
      joinBtn.className = "btn btn-outline-success w-100";
      joinBtn.disabled = true;
    } else if (remaining === 0) {
      joinBtn.textContent = "Complet";
      joinBtn.className = "btn btn-outline-danger w-100";
      joinBtn.disabled = true;
    } else {
      joinBtn.textContent = "Rejoindre l'activité";
      joinBtn.className = "btn btn-primary w-100";

      // Utiliser l'URL générée par Symfony
      joinBtn.href = window.joinRoomUrlTemplate.replace("ROOM_ID", room.id);

      joinBtn.disabled = false;
    }

    // Afficher la modale
    new bootstrap.Modal(document.getElementById("roomInfoModal")).show();
  }

  rooms.forEach((room) => {
    console.log("Room id:", room.id);
    const roomInfoCard = document.querySelector(`[data-id="${room.id}"]`);
    console.log(roomInfoCard);

    // Ajouter le gestionnaire d'événement pour les clics sur les cartes dans la liste
    if (roomInfoCard) {
      roomInfoCard.addEventListener("click", () => {
        console.log("Room card clicked:", room);
        showRoomDetails(room);
      });
    }

    // Créer un marqueur sur la carte
    const marker = new google.maps.Marker({
      position: { lat: room.latitude, lng: room.longitude },
      map: map,
      title: room.title,
    });

    // Ajouter le gestionnaire d'événement pour les clics sur les marqueurs
    marker.addListener("click", () => {
      console.log("Marker clicked:", room);
      showRoomDetails(room);
    });
  });
}
