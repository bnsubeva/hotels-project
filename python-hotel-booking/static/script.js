const searchInput = document.getElementById("searchInput");
const cards = document.querySelectorAll(".hotel-card");

searchInput.addEventListener("input", function () {
    const searchText = searchInput.value.toLowerCase();

    cards.forEach(function (card) {
        const text = card.innerText.toLowerCase();

        if (text.includes(searchText)) {
            card.classList.remove("hidden");
        } else {
            card.classList.add("hidden");
        }
    });
});
