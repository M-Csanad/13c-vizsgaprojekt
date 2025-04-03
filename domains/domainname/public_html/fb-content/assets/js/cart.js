import { setupNumberField } from "../../fb-products/js/numberfield.js";
import APIFetch from "./apifetch.js";
import Notification from "./notification.js";
import Popup from "./popup.js";
import RateLimiter from "./ratelimit.js";

const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);

// Segédfüggvény API kérésekhez

class Cart {
  isOpen = false;
  url = window.location.pathname;

  data = null;
  cartPrice = 0;
  lastFetchResultType = null;

  debounce = 1000;
  rateLimited = false;

  ease = "power2.inOut";
  ease2 = "power3.inOut";

  constructor() {
    this.init();
  }

  async init() {
    try {
      // Rate limiter beállítása
      // Név - kérés / s
      this.rateLimiter = new RateLimiter(
        {
          "slow"    : 1,
          "faster"  : 2,
          "fast"    : 10
        }
      );

      // Kosár tartalom lekérése
      const fetchPromise = this.fetchCartData();

      // DOM töltését megvárjuk
      await this.waitForLoad();

      // Inicializáljuk az elemeket és az eseménykezelőket
      this.initDOM();
      this.bindEvents();
      this.setupCardEvents();

      // Megvárjuk a tartalom letöltését
      await fetchPromise;

      // Ha szükséges a kosarakat egyesíteni, akkor megkérdezzük a usert.
      if (this.lastFetchResultType == "PROMPT") {
        const title = this.data.title;
        const description = this.data.description;

        // Létrehozzuk a felugró ablakot
        const mergePrompt = new Popup(title, description, async (response) => {
          await this.handleCartMerge(response);
          this.updateUI();
        });

        // Megnyitjuk a felugró ablakot
        mergePrompt.open();
      } else {
        // UI frissítése, ha nincs felugró ablak
        this.updateUI();

        // Kosár ellenőrzése és hibás termékek megjelölése
        await this.validateCart();
      }
    } catch (err) {
      console.error(err);
    }
  }

  // Függvény, amelynek szerepe az oldal betöltésének megvárása
  waitForLoad() {
    return new Promise((resolve) => {
      if (document.readyState === "complete") {
        resolve();
      } else {
        window.addEventListener("load", resolve);
      }
    });
  }

  // DOM elemek megkeresése
  initDOM() {
    // Kosár ablak
    this.domElement = document.querySelector(".cart");
    if (!this.domElement) throw new Error("Nincs cart osztályú elem.");

    // Id és selector
    this.domElement.id = randomId();
    this.selector = `#${this.domElement.id}`;

    this.background = document.querySelector(".modal-background");
    if (!this.background) throw new Error("Nincs háttér.");

    // Kinyitó gomb
    this.openButton = document.querySelector(".cart-open");
    if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

    // Kosár jelvény hozzáadása
    this.cartBadge = document.createElement("div");
    this.cartBadge.className = "cart-badge";
    this.openButton.appendChild(this.cartBadge);

    // Bezáró gomb
    this.closeButton = this.domElement.querySelector(".cart-close");
    if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

    // Kosárba rakó gombok
    this.cartAddButtons = document.querySelectorAll(".add-to-cart");

    this.quantityInput = document.querySelector(".product-quantity");

    // Kosár mérete
    this.cartCount = this.domElement.querySelector(".cart-count");

    // Kosár elemek
    this.cartContainer = this.domElement.querySelector(".cart-items");

    // Üres kosár elem
    this.emptyMessage = this.domElement.querySelector(".cart-empty");

    // Összeg elem
    this.priceContainer = this.domElement.querySelector(".price > .value");

    // Vásárlás gomb
    this.checkoutButton = this.priceContainer.closest("a");

    // GSAP ellenőrzés
    if (!gsap) throw new Error("A GSAP nem található");
    if (!lenis) throw new Error("A Lenis nem található");
  }

  // UI metódusok
  // Eseménykezelők létrehozása
  bindEvents() {
    this.openButton.addEventListener("click", this.open.bind(this));
    this.closeButton.addEventListener("click", this.close.bind(this));

    // Close the cart when clicking outside the cart menu
    this.background.addEventListener("click", () => {
      if (this.isOpen) {
        this.close();
      }
    });

    this.cartAddButtons.forEach((button) =>
      button.addEventListener("click", this.add.bind(this))
    );

    this.cartContainer.addEventListener("click", async (e) => {
      if (e.target.closest(".item-remove")) {
        const index = this.getProductDOMIndex(e);
        await this.remove(index);
        return;
      }

      // Blokkolja az összes interakciót, kivéve a törlést, ha az elem érvénytelen
      const cartItem = e.target.closest(".cart-item");
      if (
        !cartItem ||
        cartItem
          .querySelector(".item-image")
          .classList.contains("invalid-stock")
      ) {
        return;
      }
    });

    // Frissíti a vásárlás gomb validációját az API használatával
    this.checkoutButton.addEventListener("click", async (e) => {
      e.preventDefault();
      e.stopPropagation();

      const result = await APIFetch("/api/cart/check", "GET");

      if (!result.ok) {
        console.error("Sikertelen validáció");
        return;
      }

      const data = await result.json();
      if (data.type === "ERROR") {
        this.markInvalidItems(data.message, this.isOpen);
        return;
      }

      window.location.href = this.checkoutButton.href;
    });
  }

  setupCardEvents() {
    const cardsContainer = document.querySelector(".cards");
    if (!cardsContainer) return;

    // Handle click events on quick-add buttons within card containers
    document.addEventListener("click", (e) => {
      const quickAddButton = e.target.closest(".quick-add");
      if (!quickAddButton) return;

      e.preventDefault();
      this.add(null, this.getUrlFromCard(quickAddButton));
    });
  }

  // Segédfüggvény a termék indexének kinyeréséhez (index a kosár adatok tömbjében)
  getProductDOMIndex(clickEvent) {
    return Array.from(this.cartContainer.children)
      .filter((e) => e.nodeName != "HR")
      .indexOf(clickEvent.target.closest(".cart-item"));
  }

  // Teljesen lefrissíti a kosár felhasználói felületét (Nincsen animálva)
  updateUI(flushContainer = true) {
    const isCartEmpty = this.data.length == 0;

    if (!isCartEmpty) this.priceContainer.innerHTML = this.cartPrice;

    // Számoljuk ki az összes termék mennyiségét
    const totalItemCount = this.data.reduce(
      (sum, product) => sum + product.quantity,
      0
    );

    this.cartCount.innerHTML = `${totalItemCount} elem`;
    this.setElementVisibility(
      this.emptyMessage,
      isCartEmpty ? "visible" : "hidden"
    );
    this.setElementVisibility(
      this.checkoutButton,
      isCartEmpty ? "hidden" : "visible"
    );

    this.cartBadge.textContent = totalItemCount;
    this.cartBadge.classList.toggle("show", totalItemCount > 0);

    if (!flushContainer) return;

    this.cartContainer.innerHTML = "";
    this.data.forEach((product) => {
      const thumbnail_uri = product.thumbnail_uri.split(".")[0];
      const isInvalid = product.quantity > product.stock;

      this.cartContainer.innerHTML += `<div class="cart-item">
                <div class="wrapper">
                <div class="item-image${isInvalid ? " invalid-stock" : ""}">
                    <a href="http://localhost/${product.link_slug}">
                    <picture>
                        <source type="image/avif" srcset="${thumbnail_uri}-768px.avif 1x" media="(min-width: 768px)">
                        <source type="image/webp" srcset="${thumbnail_uri}-768px.webp 1x" media="(min-width: 768px)">
                        <source type="image/jpeg" srcset="${thumbnail_uri}-768px.jpg 1x" media="(min-width: 768px)">
                        <img
                        src="${thumbnail_uri}.jpg"
                        alt="${product.name}"
                        loading="lazy">
                    </picture>
                    ${
                      isInvalid
                        ? `<div class="stock-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                        <span>${
                          product.stock === 0
                            ? "A termék elfogyott"
                            : "Nem megfelelő mennyiség"
                        }</span>
                    </div>`
                        : ""
                    }
                    </a>
                </div>
                <div class="item-body">
                    <div class="item-info">
                        <div class="item-name">${product.name}</div>
                        <div class="item-price">
                            <div class="value">${product.unit_price}</div>
                            <div class="currency">Ft</div>
                        </div>
                        </div>
                    <div class="number-field">
                        <div class="number-field-subtract">-</div>
                        <input type="number" name="product-quantity" class="product-quantity"
                               data-product-id="${product.product_id}"
                               data-invalid="${isInvalid}"
                               placeholder="Darab" max="${
                                 product.stock
                               }" min="1"
                               ${isInvalid ? "disabled" : ""}
                               value="${product.quantity}" pattern="[0-9]*">
                        <div class="number-field-add">+</div>
                    </div>
                    <div class="item-remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg>
                    </div>
                </div>
                </div>
            </div>
            <hr>`;
    });

    // A kosár API frissítéséhez callback definiálása az érvényes elemekhez
    setupNumberField(this.cartContainer, async (delta, _, input) => {
      // Ellenőrizzük, hogy az elem érvényes-e
      if (input.dataset.invalid === "true") {
        return false; // Ne küldjünk API kérést érvénytelen elemekhez
      }

      // Termék azonosító lekérése közvetlenül az input adattulajdonságból
      const productId = input.dataset.productId;
      if (!productId) return false;

      // API kérés küldése
      const result = await APIFetch("/api/cart/update", "PUT", {
        delta: delta,
        product_id: Number(productId),
      });

      if (result.ok) {
        // Kosár adatainak egyszeri frissítése
        await this.fetchCartData();
        this.updateUI(false);
        return true;
      } else {
        console.error("Sikertelen kosár frissítés:", result);
        return false;
      }
    });
  }

  // Kinyitja a kosarat
  open() {
    if (this.isOpen) return;

    lenis.stop();
    this.isOpen = true;

    gsap.set(this.domElement, { visibility: "visible" });
    gsap.set(this.background, { visibility: "visible" });
    gsap.to(this.domElement, {
      x: "0%",
      duration: 0.8,
      ease: this.ease2,
    });
    gsap.to(this.background, {
      opacity: 1,
      duration: 0.8,
      ease: this.ease2,
    });
  }

  // Bezárja a kosarat
  close() {
    if (!this.isOpen) return;

    gsap.to(this.background, {
      opacity: 0,
      duration: 0.6,
      ease: this.ease,
    });
    gsap.to(this.domElement, {
      x: "100%",
      duration: 0.6,
      ease: this.ease,
      onComplete: () => {
        lenis.start();
        this.isOpen = false;
        gsap.set(this.domElement, { visibility: "hidden" });
        gsap.set(this.background, { visibility: "hidden" });
      },
    });
  }

  // A "Kosár üres" üzenet megjelenési állapotát változtatja
  setElementVisibility(element, visibility) {
    // Paraméterek ellenőrzése
    if (!element) throw new Error("Nincs elem megadva!");
    if (visibility != "hidden" && visibility != "visible")
      throw new Error(`Ismeretlen láthatóság (${visibility})`);

    // Ha már az az elem láthatósága, mint amit beadtunk, akkor nem történik semmi
    if (getComputedStyle(element).visibility == visibility) return;

    if (!this.isOpen) {
      if (visibility == "visible") {
        gsap.set(element, { opacity: 1, scale: 1, visibility: "visible" });
      } else {
        gsap.set(element, { opacity: 0, scale: 0, visibility: "hidden" });
      }
    } else {
      if (visibility == "visible") {
        gsap.set(element, { opacity: 0, scale: 0, visibility: "visible" });
        gsap.to(element, {
          opacity: 1,
          scale: 1,
          ease: this.ease,
          duration: 0.4,
        });
      } else {
        gsap.to(element, {
          opacity: 0,
          scale: 0,
          ease: this.ease,
          duration: 0.4,
          onComplete: () => {
            element.style.visibility = "hidden"; // Itt nem GSAP-pal állítom, mert csak egy tulajdonságot állítok
          },
        });
      }
    }
  }

  // Improved method to get URL from card
  getUrlFromCard(card) {
    const url = card.dataset.productUrl;
    if (!url) {
      console.error("No product URL found:", card);
      return null;
    }
    return url;
  }

  /**
   * Hibaüzenet megjelenítése értesítésben
   * @param {string} message - A hibaüzenet
   * @param {string} type - Az értesítés típusa ('error', 'warning', 'success', 'info')
   */
  showNotification(message, type = "info") {
    Notification.show(message, type);
  }

  // Backend metódusok

  async add(e, url = null) {
    const isLimited = this.rateLimiter.useArea("slow");
    if (isLimited) return;

    if (e && e.preventDefault) e.preventDefault();
    
    try {
      if (!url) {
        url = this.url;
      }

      const result = await APIFetch("/api/cart/add", "POST", {
        url: url,
        qty: this.quantityInput ? Number(this.quantityInput.value) : 1,
      });

      if (result.ok) {
        await this.fetchCartData();
        this.updateUI();

        // Always show a notification
        this.showNotification(
          "Termék sikeresen hozzáadva a kosárhoz",
          "success"
        );
      } else {
        // Handle error response
        const errorData = await result.json();
        this.showNotification(
          errorData.message || "Nem sikerült a terméket a kosárhoz adni",
          "error"
        );
      }
    } catch (error) {
      console.error("Kosárba helyezés sikertelen:", error);
      this.showNotification(
        "Nem sikerült kapcsolódni a szerverhez. Kérjük, próbálja újra később.",
        "error"
      );
    }
  }

  // Kitöröl egy terméket a kosárból
  async remove(index) {
    const isLimited = this.rateLimiter.useArea("slow");
    if (isLimited) return;

    const product = this.data[index];

    const result = await APIFetch("/api/cart/remove", "DELETE", {
      id: product.product_id,
    });
    if (result.ok) {
      await this.fetchCartData();
      const removedProduct = Array.from(this.cartContainer.children).filter(
        (e) => e.nodeName != "HR"
      )[index];
      const separator = removedProduct.nextElementSibling;

      gsap.to(separator, {
        marginTop: "-34px",
        opacity: 0,
        duration: 0.6,
        ease: this.ease,
      });
      gsap.to(removedProduct, {
        height: 0,
        opacity: 0,
        duration: 0.6,
        ease: this.ease,
        onComplete: () => {
          separator.remove();
          removedProduct.remove();

          this.updateUI(false); // Nem töröljük ki a kártyákat
        },
      });
    } else {
      this.showNotification("Nem sikerült eltávolítani a terméket", "error");
      throw new Error(
        "Hiba történt a kosár lekérdezése során: " + (await result.json())
      );
    }
  }

  // Lekéri a kosár tartalmát
  async fetchCartData() {
    const result = await APIFetch("/api/cart/get", "GET");

    if (result.ok) {
      const data = await result.json();
      this.data = data.message;
      this.lastFetchResultType = data.type;
      if (data.type == "SUCCESS") {
        this.cartPrice = this.data.reduce(
          (a, b) => a + b.unit_price * b.quantity,
          0
        );
      }
    } else {
      throw new Error(
        "Hiba történt a kosár lekérdezése során: " + (await result.json())
      );
    }
  }

  // Egyesíti a kosarat (vendég -> felhasználó)
  async handleCartMerge(response) {
    const mergeResponse = await APIFetch("/api/cart/merge", "PUT", {
      response: response,
    });

    if (mergeResponse.ok) {
      // Csak akkor kérjük le ismét az atadokat, ha azt meg is változtattuk
      await this.fetchCartData();

      if (response) {
        this.showNotification(
          "Korábbi és új kosár sikeresen összevonva",
          "success"
        );
      }
    } else {
      this.showNotification("Nem sikerült összevonni a kosarakat", "error");
      console.log(mergeResponse);
    }
  }

  // Új metódusok hozzáadása a kosár validálásához
  async validateCart() {
    // Helyi validálás a meglévő adatok használatával
    const invalidItems = this.data
      .filter((item) => item.quantity > item.stock)
      .map((item) => ({
        product_id: item.product_id,
        stock: item.stock,
      }));

    if (invalidItems.length > 0) {
      this.markInvalidItems(invalidItems, this.isOpen);
      return false;
    }

    return true;
  }

  markInvalidItems(invalidItems, animate = false) {
    const cartItems = this.cartContainer.querySelectorAll(".cart-item");
    let notified = false;
    cartItems.forEach((item, index) => {
      const product = this.data[index];
      const invalid = invalidItems.find(
        (i) => i.product_id === product.product_id
      );

      if (invalid) {
        const imageWrapper = item.querySelector(".item-image");
        const stockMsg =
          invalid.stock === 0
            ? "A termék elfogyott"
            : "Nem megfelelő mennyiség";

        // Csak egyszer jelenítünk meg értesítést a problémás termékekről
        if (!notified) {
          this.showNotification(
            "Kosarában olyan termékek vannak, amelyek nem elérhetők a kívánt mennyiségben",
            "warning"
          );
          notified = true;
        }

        // Hibás stílus hozzáadása
        imageWrapper.classList.add("invalid-stock");

        // A mennyiség bevitelének frissítése az aktuális készlet megjelenítéséhez
        const quantityInput = item.querySelector(".product-quantity");
        quantityInput.disabled = true;
        quantityInput.dataset.invalid = "true"; // Érvénytelennek jelöljük a további API kérések elkerülése érdekében

        // Hiba hozzáadása a képhez, ha még nincs jelen
        if (!imageWrapper.querySelector(".stock-error")) {
          const errorElement = document.createElement("div");
          errorElement.className = "stock-error";
          errorElement.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                        <span>${stockMsg}</span>
                    `;
          imageWrapper.appendChild(errorElement);

          if (animate && this.isOpen) {
            gsap.from(errorElement, {
              opacity: 0,
              scale: 0.95,
              duration: 0.4,
              ease: "power2.out",
            });
          }
        }
      }
    });
  }
}

export default Cart;

// Automatikusan inicializáljuk, ha önálló szkriptként töltődik be
if (
  document.currentScript &&
  document.currentScript.getAttribute("init") !== "false"
) {
  document.addEventListener("DOMContentLoaded", () => {
    window.cart = new Cart();
  });
}
