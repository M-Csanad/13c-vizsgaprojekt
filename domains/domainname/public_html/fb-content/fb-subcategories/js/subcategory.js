import FilterWindow from "../../assets/js/filterwindow.js";

const filter = new FilterWindow();

// Egyszerű 5 csillagos értékelés generáló segédfüggvény
function generateStars(element) {
    let rating = element.dataset.rating ?? 0;
    
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const totalStars = 5;
    
    for (let i = 0; i < totalStars; i++) {
        const star = document.createElement("span");
        if (i < fullStars) {
            star.classList.add("filled");
        } else if (i === fullStars && halfStar) {
            star.classList.add("half");
        }
        element.appendChild(star);
    }
}
  
document.querySelectorAll(".review-stars").forEach((el) => generateStars(el));