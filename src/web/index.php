<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!--lenis stylesheet-->
  <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />

  <!--swiper stylesheet-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.css"
    integrity="sha512-3OuH/9eh0Sx9s/c23ZFG5SJb3GvBluF9cdGgQXhZyMyId4GP87W9QBgkHmocx+8kZaCZmXQUUuLOD4Q4f5PaWQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!--root stylesheet-->
  <link rel="stylesheet" href="./css/root.css" />

  <!--casual stylesheets-->
  <link rel="stylesheet" href="./css/box.css" />
  <link rel="stylesheet" href="./css/font.css" />

  <!--main stylesheet-->
  <link rel="stylesheet" href="./mainPage/css/main.css" />
  <link rel="stylesheet" href="./mainPage/css/main_media.css" />
  <link rel="stylesheet" href="./css/footer.css" />
  <link rel="stylesheet" href="./css/navbar.css" />
  <script src="./js/autogenerate__footer.js" defer></script>
  <script src="./js/autogenerate__navbar.js" defer></script>

  <title>Home - Florens Botanica</title>
</head>

<body>
  <div id="bg"></div>
  <div id="content">
    <header>
      <section id="StickyNavbar_container">
        <?php include './php/navbar.php'; ?>
      </section>
    </header>

    <main>
      <section id="__main_head ">
        <div id="headScroller_container flex-block">
          <div class="videoScene_wrapper col-12">
            <video id="welcomeVideo" autoplay muted loop>
              <source src="./media/video/mainpageWelcome02.webm" type="video/webm" />
              <source src="./media/video/mainpageWelcome02.mp4" type="video/mp4" />
            </video>
          </div>
          <div class="videoContent_wrapper">
            <div class="videoContent_scrollable">
              <div class="videoContent_card-content">
                <img src="./media/img//herbalLogo_white.png" alt="Florens Botanica Logo" />
              </div>
              <div class="videoContent_card-content">
                <p class="__t03-law1">
                  Üdvözlünk a természet kincseinek világában, ahol a
                  gyógynövények ereje és a harmónia találkozik.
                </p>
              </div>
              <div class="videoContent_card-content">
                <p class="__t03-law1">
                  Nálunk minden termék gondosan válogatott, hogy támogassa
                  egészséged és harmóniád.
                </p>
              </div>
              <div class="videoContent_card-content">
                <p class="__t03-law1">
                  Lépj be a Florens Botanica világába, és hagyd, hogy a
                  természet ereje magával ragadjon!
                </p>
              </div>
            </div>
          </div>
        </div>

        <div id="topProducts_container">
          <div class="swiper topProduct_slider">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <div class="swiper-card_wrapper">
                  <div class="card-image">
                    <img src="./media/img/testImg/testProduct01.jpg" alt="" />
                    <button class="book-now">Kosárba</button>
                  </div>
                  <div class="swiper-card">
                    <div class="text-overlay">
                      <h1 class="__t03-law1 title">Termék Név Ide Jön</h1>
                      <div class="rating">
                        <span>⭐️⭐️⭐️⭐️⭐️</span>
                        <span>180k értékelés</span>
                      </div>
                      <div class="text">
                        <p class="__t02-men1 description">
                          Lorem ipsum dolor sit amet, consectetur adipisicing
                          elit. Est ut pariatur totam facilis, id facere
                          dolores eos maiores quaerat iste assumenda, amet
                          recusandae sunt molestias.
                        </p>

                      </div>

                      <div class="card-footer">
                        <div class="cast">
                          <img src="./media/img/icons/vegan.png" alt="#" />
                          <img src="./media/img/icons/bpa-free.png" alt="#" />
                          <img src="./media/img/icons/serum.png" alt="#" />
                          <img src="./media/img/icons/gluten-free.png" alt="#" />
                        </div>
                        <div class="actions">
                          <button class="more-info">Bővebb információ</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="swiper-card_wrapper">
                  <div class="card-image">
                    <img src="./media/img/testImg/testProduct02.jpg" alt="" />
                    <button class="book-now">Kosárba</button>
                  </div>
                  <div class="swiper-card">
                    <div class="text-overlay">
                      <h1 class="__t03-law1 title">Termék Név Ide Jön</h1>
                      <div class="rating">
                        <span>⭐️⭐️⭐️⭐️⭐️</span>
                        <span>97k értékelés</span>
                      </div>
                      <div class="text">
                        <p class="__t02-men1 description">
                          Lorem ipsum dolor sit amet, consectetur adipisicing
                          elit. Est ut pariatur totam facilis, id facere
                          dolores eos maiores quaerat iste assumenda, amet
                          recusandae sunt molestias.
                        </p>
                      </div>

                      <div class="card-footer">
                        <div class="cast">
                          <img src="./media/img/icons/vegan.png" alt="#" />
                          <img src="./media/img/icons/bpa-free.png" alt="#" />
                          <img src="./media/img/icons/serum.png" alt="#" />
                          <img src="./media/img/icons/pollen.png" alt="#" />
                          <img src="./media/img/icons/peanut-free.png" alt="#" />
                          <img src="./media/img/icons/honeycomb.png" alt="#" />
                        </div>
                        <div class="actions">
                          <button class="more-info">Bővebb információ</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="swiper-card_wrapper">
                  <div class="card-image">
                    <img src="./media/img/testImg/testProduct03.jpg" alt="" />
                    <button class="book-now">Kosárba</button>
                  </div>
                  <div class="swiper-card">
                    <div class="text-overlay">
                      <h1 class="__t03-law1 title">Termék Név Ide Jön</h1>
                      <div class="rating">
                        <span>⭐️⭐️⭐️⭐️⭐️</span>
                        <span>120k értékelés</span>
                      </div>
                      <div class="text">
                        <p class="__t02-men1 description">
                          Lorem ipsum dolor sit amet, consectetur adipisicing
                          elit. Est ut pariatur totam facilis, id facere
                          dolores eos maiores quaerat iste assumenda, amet
                          recusandae sunt molestias.
                        </p>
                      </div>

                      <div class="card-footer">
                        <div class="cast">
                          <img src="./media/img/icons/vegan.png" alt="#" />
                          <img src="./media/img/icons/bpa-free.png" alt="#" />
                          <img src="./media/img/icons/serum.png" alt="#" />
                        </div>
                        <div class="actions">
                          <button class="more-info">Bővebb információ</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="__main_body">

        <div id="categorychooser">
          <?php include './php/__gen_categorychooser.php'; ?>
        </div>

        <div id="brand-philosophy_container">
          <div class="Parallax_container">
            <div class="Parallax_box z2" data-speed="0.3">
              <div id="BGDT01" class="__BG-designText">Florens</div>
            </div>
            <div class="Parallax_box z2" data-speed="0.1">
              <div id="BGDT02" class="__BG-designText">Botanica</div>
            </div>
          </div>
          <div id="brand-philosophy">
            <div class="brand-philosophy_wrapper">
              <h1 class="__t01-lan1">Ősi Harmónia</h1>
              <p class="__t03-men1">
                A Florens Botanica nem csupán egy márka – a természet
                tisztelete és a belső béke közötti híd. Minden termékünk
                gondosan válogatott alapanyagokból készül, hogy a föld és a
                növények gyógyító energiáját közvetítse számodra.
              </p>
              <p class="__t03-men1">
                Fedezd fel a ritka növényi kivonatokból és ősi receptekből
                született formulákat, melyek célja az elme, a test és a lélek
                egyensúlyának megteremtése. Termékeinkkel lehetőséged van
                arra, hogy természetes módon tápláld és újítsd meg önmagad.
              </p>
              <p class="__t03-men1">
                Minden, amit kínálunk, a természet iránti tiszteletből fakad,
                és arra hivatott, hogy harmóniát hozzon az életedbe. Engedd,
                hogy termékeinkkel a tiszta, természetes energiák
                mindennapjaid részévé váljanak, megteremtve az egyensúlyt és a
                békét benned és körülötted.
              </p>
            </div>
          </div>
          <div class="Parallax_container">
            <div class="Parallax_box" data-speed="0.1">
              <div id="BGDT03" class="__BG-designText">természet</div>
            </div>
            <div class="Parallax_box" data-speed="0.3">
              <div id="BGDT04" class="__BG-designText">ereje</div>
            </div>
          </div>
          <div id="brand-description" class="flex-block">
            <div class="brandContent col-5">
              <h1 class="__t01-lan1">
                A Gyógyító Múlt és a Fenntartható Jövő
              </h1>
              <div class="brandContentParagraph">
                <p class="__t02-men1">
                  A Florens Botanica termékei ősi gyökerekből merítik
                  erejüket. Az idők során felhalmozott növényi tudás és
                  gyógyító szándék évszázadokon átívelve jutott el hozzánk,
                  hogy mai világunkban újra életre keljen.
                </p>
                <p class="__t02-men1">
                  Az ősi gyógyászat bölcsessége szorosan összefonódik a
                  természet legősibb összetevőivel. Az akkori gyógyítók által
                  használt gyógynövények ereje most korszerű formában, modern
                  felhasználásra készen várja, hogy segítse a
                  mindennapjainkat.
                </p>

                <p class="__t02-men1">
                  Ezt a hagyomány és fejlődés közötti hidat arra építettük,
                  hogy azok is megtapasztalhassák a természet valódi erejét,
                  akik a mai, rohanó világban keresik a természetes
                  megoldásokat.
                </p>
                <p class="__t02-men1">
                  A Florens Botanica küldetése, hogy ezeket a bölcsességeket
                  és energiákat új formában, könnyedén elérhetővé tegye – az
                  egészségesebb test, elme és lélek érdekében.
                </p>
              </div>
            </div>
            <div class="brandImages col-6">
              <div class="brandImages_imgVertical">
                <img src="./media/img/HerbsOnDesk.jpg" alt="Gyógynövények asztalon kis üvekcsékben" />
              </div>
              <div class="brandImages_imgHorizontal">
                <img src="./media/img/HerbsInHands.jpg" alt="Gyógynövények hölgy kezében" />
              </div>
            </div>
          </div>
          <div id="notifBlob">Mozgasd</div>
        </div>

        <div id="categoryGallery" class="ImgGallery_container"><?php include './php/__gen_imageGallery.php'; ?></div>

        <div id="break"></div>
      </section>
    </main>
    <footer id="fb-footer"></footer>
  </div>

  <!--jQuery-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
    integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!--lenis scroll-->
  <script src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"></script>
  <script src="./js/lenis.js"></script>

  <!--swiper bundle-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.js"
    integrity="sha512-0N/5ZOjfsh3niel+5dRD40HQkFOWaxoVzqMVAHnmAO2DC3nY/TFB7OYTaPRAFJ571IRS/XRsXGb2XyiFLFeu1g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script type="text/javascript" src="./mainPage/js/swiper.js"></script>
  <script type="text/javascript" src="./mainPage/js/swiperThumbs.js"></script>

  <!--video restart-->
  <script src="./mainPage/js/videoMouseHover.js"></script>
  <script src="./mainPage/js/videoContentScroll.js"></script>

  <!--parallax-->
  <script src="./mainPage/js/parallaxEffect.js"></script>
  <script src="./mainPage/js/imageParallaxGallery.js"></script>
  <script src="./mainPage/js/movingGradientEffect.js"></script>

  <!--ionicons-->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <!--extras-->
</body>

</html>
