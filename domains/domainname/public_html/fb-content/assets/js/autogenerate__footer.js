document.addEventListener("DOMContentLoaded", function () {
  fetch("http://localhost/fb-content/assets/footer.php")
    .then((response) => response.json())
    .then((data) => {
      const footer = document.getElementById("fb-footer");
      if (footer) {
        footer.innerHTML = `
                    <div class="fb-footer-mask">
                        <div id="fb-ghost-container" class="fb-f-container"></div>
                    </div>
                    <div class="fb-f-wrapper flex-block">
                        <div class="fb-f-container fb-left-container col-2">
                            <div class="fb-f-col">
                                <img id="fb-footerlogo" src="${
                                  data.logo
                                }" alt="blogmarket logo">
                                <p id="fb-company-type_paragraph" class="fb-links fb-paragraph-mini">${
                                  data.description
                                }</p>
                            </div>
                        </div>
                        <div class="fb-f-container fb-right-container col-10">
                            <div class="fb-f-row">
                                ${Object.entries(data.sections)
                                  .map(
                                    ([sectionTitle, links]) => `
                                    <section class="fb-f-col">
                                        <h3>${sectionTitle}</h3>
                                        <ul class="fb-f-content">
                                            ${links
                                              .map((link) =>
                                                link.name
                                                  ? `
                                                <div class="fb-socialinks-container fb-f-content">
                                                    <div class="fb-social-cont-mini">
                                                        <p class="fb-text fb-socialtext">${link.name}</p>
                                                        <a class="fb-socialmedia fb-links" href="${link.url}" target="_blank" rel="noopener">
                                                            ${link.icon}
                                                        </a>
                                                    </div>
                                                </div>
                                            `
                                                  : `
                                                <li><a class="fb-links" href="${link.url}">${link.text}</a></li>
                                            `
                                              )
                                              .join("")}
                                        </ul>
                                    </section>
                                `
                                  )
                                  .join("")}
                            </div>
                        </div>
                    </div>
                    <div class="fb-f-container fb-copyright-container col-12">
                        <div class="fb-f-row">
                            <section class="fb-f-col-big">
                                <ul id="fb-copyright_section" class="fb-f-content">
                                    <li><p class="fb-copyright">${
                                      data.copyright
                                    }</p></li>
                                </ul>
                            </section>
                        </div>
                    </div>
                `;
      }
    })
    .catch((error) => console.error("Error fetching footer data:", error));
});
