/* updates image & descriptions */
document.addEventListener("DOMContentLoaded", () => {
    const djSections = document.querySelectorAll(".dj-section");
    const mainImage = document.querySelector(".alldjs");
    const mainDescription = document.querySelector("#main-description");
    const descriptions = document.querySelectorAll(".dj-description");

    if (!mainImage || djSections.length === 0) return;

    const totalDJs = djSections.length;
    let currentIndex = -1;

    // hide all DJs & descriptions
    djSections.forEach(sec => sec.style.display = "none");
    descriptions.forEach(d => d.style.display = "none");

    // show main view
    mainImage.style.display = "block";
    mainDescription.style.display = "block";

    function showDJ(index) {
        mainImage.style.display = "none";
        mainDescription.style.display = "none";
        djSections.forEach(sec => sec.style.display = "none");
        descriptions.forEach(d => d.style.display = "none");

        djSections[index].style.display = "flex";
        descriptions[index].style.display = "block";
        currentIndex = index;
    }

    function showMain() {
        mainImage.style.display = "block";
        mainDescription.style.display = "block";
        djSections.forEach(sec => sec.style.display = "none");
        descriptions.forEach(d => d.style.display = "none");
        currentIndex = -1;
    }

    mainImage.addEventListener("click", () => showDJ(0));

    djSections.forEach((sec, i) => {
        const left = sec.querySelector(".left-button");
        const right = sec.querySelector(".right-button");

        if (left) {
            left.addEventListener("click", () => {
                if (i === 0) showMain();
                else showDJ(i - 1);
            });
        }

        if (right) {
            right.addEventListener("click", () => {
                if (i < totalDJs - 1) showDJ(i + 1);
            });
        }
    });
});
