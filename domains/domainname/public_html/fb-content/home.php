<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';
?>

<!DOCTYPE html>
<html lang="hu">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home - Florens Botanica</title>

  <style>
    body {
      background: var(--dark_primary);
      color: var(--light_main);
      margin: 0 !important;
      padding: 0 !important;
      font-family: "Karla", sans-serif;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    html,
    main {
      scroll-behavior: smooth;
      scrollbar-width: none;
    }

    body::-webkit-scrollbar,
    main::-webkit-scrollbar,
    .videoContent_wrapper::-webkit-scrollbar,
    .videoContent_scrollable::-webkit-scrollbar {
      display: none;
    }

    p,
    a,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      margin: 0;
      padding: 0;
    }

    /*
 =============================
    parallax effect under video
 =============================
 */

    .Parallax_container {
      display: flex;
      align-items: center;
      justify-content: center;
      position: absolute;
      width: 100vw;
    }

    .Parallax_box {
      width: 100vw;
      height: auto;
      position: absolute;
      will-change: transform;
    }

    .z2 {
      z-index: 2;
    }

    .__BG-designText {
      position: absolute;
      padding: 12px;
      z-index: 2;
    }

    #BGDT01 {
      color: var(--dark_secondary);
      font-size: clamp(12rem, calc(18rem + 0.39vw), 35rem);
      text-transform: uppercase;
      font-family: "Poppins";
      font-weight: 600;
      left: 12rem;
      transform: translateY(120%);
    }

    #BGDT02 {
      color: var(--dark_tertiary);
      font-size: clamp(10rem, calc(18rem + 0.39vw), 20rem);
      font-weight: 300;
      text-transform: capitalize;
      font-family: "Maitree";
      right: 12rem;
      transform: translateY(60%);
    }

    #BGDT03 {
      color: var(--dark_secondary);
      font-size: clamp(12rem, calc(18rem + 0.39vw), 30rem);
      text-transform: capitalize;
      font-family: "Maitree";
      font-weight: 300;
      left: 12rem;
      transform: translateY(-150%);
      mix-blend-mode: difference;
    }

    #BGDT04 {
      color: var(--dark_tertiary);
      font-size: clamp(10rem, calc(14rem + 0.39vw), 20rem);
      font-weight: 600;
      text-transform: uppercase;
      font-family: "Poppins";
      right: 12rem;
      transform: translateY(-220%);
      mix-blend-mode: difference;
    }

    /*
 =============================
    welcome video
 =============================
 */

    .headScroller_container {
      position: relative;
      display: flex;
      flex-direction: row;
      position: relative;
      height: 100vh;
      z-index: 1;
    }

    .videoScene_wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      position: sticky;
      top: 0;
      left: 0;
      overflow: hidden;
      perspective: 1000px;
      height: 100vh;
      background: radial-gradient(circle, rgba(0, 0, 0, 0) 100%, rgb(0, 0, 0) 100%);
    }

    .videoScene_wrapper::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(0, 0, 0, 0) 70%, rgb(0, 0, 0) 100%);
      opacity: 0;
      transition: opacity 2s ease-in-out;
      z-index: 2;
    }

    /* Trigger the vignette */
    .videoScene_wrapper:hover::before {
      opacity: 1;
    }

    #welcomeVideo {
      pointer-events: none;
      filter: brightness(1);
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: translate(0%, 0%) scale(1.3);
      /* Kezdeti állapot */
      transition: transform 1s ease-out;
    }

    .videoContent_wrapper {
      display: flex;
      justify-content: center;
      flex-direction: column;
      position: sticky;
      top: 0;
      right: 0;
      transform: translateY(-100vh);
      width: 100%;
      height: 280vh;
      pointer-events: none;
      margin-left: auto;
      margin-right: 0;
      margin-top: 0;
      padding-top: 0;
    }

    .videoContent_scrollable {
      display: flex;
      position: relative;
      align-items: center;
      text-align: justify;
      flex-direction: column;
      overflow-y: auto;
      padding: 20px;
      height: 280vh;
    }

    .videoContent_scrollable .videoContent_card-content {
      display: flex;
      text-align: center;
      align-items: center;
      justify-content: center;
      width: 25vw;
      margin: 25vh 0;
      text-shadow: 1px 0px 10px var(--dark_primary), 0 0 20px var(--light_main),
        0 0 40px var(--light_main-op5);
    }

    .videoContent_scrollable .videoContent_card-content img {
      width: 50vw;
      height: auto;
      margin-left: auto;
      margin-right: auto;
    }

    .videoContent_scrollable p {
      font-family: "Poppins", sans-serif;
      font-weight: 200;
      font-style: normal;
    }

    /* Alapértelmezett állapot: homályos és láthatatlan */
    .videoContent_HIDE {
      opacity: 0;
      filter: var(--blur8);
      transition: opacity 0.4s ease-out, filter 0.8s ease-out;
    }

    /* Amikor megjelenik: tiszta és látható */
    .videoContent_SHOW {
      opacity: 1;
      filter: blur(0);
      transition: opacity 0.4s ease-out, filter 0.8s ease-out;
    }
  </style>


  <!--lenis stylesheet-->
  <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />

  <!--swiper stylesheet-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.css"
    integrity="sha512-3OuH/9eh0Sx9s/c23ZFG5SJb3GvBluF9cdGgQXhZyMyId4GP87W9QBgkHmocx+8kZaCZmXQUUuLOD4Q4f5PaWQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!--root stylesheet-->
  <link rel="stylesheet" href="/fb-content/assets/css/root.css" />

  <!--casual stylesheets-->
  <link rel="stylesheet" href="/fb-content/assets/css/box.css" />
  <link rel="stylesheet" href="/fb-content/assets/css/font.css" />
  <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">

  <!--main stylesheet-->
  <link rel="stylesheet" href="/fb-content/fb-home/css/main.css" media="all" />
  <link rel="stylesheet" href="/fb-content/fb-home/css/main_media.css" media="all" />

  <!--footer stylesheets-->
  <link rel="stylesheet" href="/fb-content/assets/css/footer.css" media="all" />
  <script src="/fb-content/assets/js/autogenerate__footer.js" defer></script>

  <!--swiper bundle-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.js"
    integrity="sha512-0N/5ZOjfsh3niel+5dRD40HQkFOWaxoVzqMVAHnmAO2DC3nY/TFB7OYTaPRAFJ571IRS/XRsXGb2XyiFLFeu1g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" defer></script>
  <script type="text/javascript" src="/fb-content/fb-home/js/swiper.js" defer></script>
  <script type="text/javascript" src="/fb-content/fb-home/js/swiperThumbs.js" defer></script>

  <!--video restart-->
  <script src="/fb-content/fb-home/js/videoMouseHover.js" defer></script>
  <script src="/fb-content/fb-home/js/videoContentScroll.js" defer></script>

  <!--parallax-->
  <script src="/fb-content/fb-home/js/parallaxEffect.js" defer></script>
  <script src="/fb-content/fb-home/js/imageParallaxGallery.js" defer></script>
  <script src="/fb-content/fb-home/js/movingGradientEffect.js" defer></script>

  <!--page transition-->
  <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script defer src="/fb-content/assets/js/page_transition.js"></script>

</head>

<body>
  <div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div><div class="layer layer-0"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-1"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-2"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-3"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div></div>

  <header>
    <section id="StickyNavbar_container">
      <?php include __DIR__ . '/assets/navbar.php'; ?>
    </section>
  </header>

  <main>
    <section id="__main_head ">
      <div id="headScroller_container flex-block">
        <div class="videoScene_wrapper col-12">

          <video id="welcomeVideo" preload="auto" loading="lazy" autoplay muted loop>
            <!-- WebM formátum (VP9 kodek) -->
            <source src="/fb-content/assets/media/videos/home/1920x1080_30Fps_VP9.webm" type="video/webm"
              media="(min-width: 1920px)">
            <source src="/fb-content/assets/media/videos/home/1280x720_30Fps_VP9.webm" type="video/webm"
              media="(min-width: 1280px)">
            <source src="/fb-content/assets/media/videos/home/640x360_30Fps_VP9.webm" type="video/webm">

            <!-- MP4 formátum (H.264 kodek) -->
            <source src="/fb-content/assets/media/videos/home/1920x1080_30Fps_H264.mp4" type="video/mp4"
              media="(min-width: 1920px)">
            <source src="/fb-content/assets/media/videos/home/1280x720_30Fps_H264.mp4" type="video/mp4"
              media="(min-width: 1280px)">
            <source src="/fb-content/assets/media/videos/home/640x360_30Fps_H264.mp4" type="video/mp4">

            Az Ön böngészője nem támogatja a videó lejátszást.
          </video>


        </div>
        <div class="videoContent_wrapper">
          <div class="videoContent_scrollable">
            <div class="videoContent_card-content">
              <img src="/fb-content/assets/media/images/logos/herbalLogo_white.png" alt="Florens Botanica Logo" />
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

      <!-- <div id="topProducts_container">
        <div class="swiper topProduct_slider">
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <div class="swiper-card_wrapper">
                <div class="card-image">
                  <img src="/fb-content/media/img/testImg/testProduct01.jpg" alt="" loading="lazy"/>
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
                        <img src="/fb-content/media/img/icons/vegan.png" alt="#" loading="lazy" />
                        <img src="/fb-content/media/img/icons/bpa-free.png" alt="#" loading="lazy" />
                        <img src="/fb-content/media/img/icons/serum.png" alt="#" loading="lazy" />
                        <img src="/fb-content/media/img/icons/gluten-free.png" alt="#" loading="lazy" />
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
                  <img src="/fb-content/media/img/testImg/testProduct02.jpg" alt="" />
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
                        <img src="/fb-content/media/img/icons/vegan.png" alt="#" />
                        <img src="/fb-content/media/img/icons/bpa-free.png" alt="#" />
                        <img src="/fb-content/media/img/icons/serum.png" alt="#" />
                        <img src="/fb-content/media/img/icons/pollen.png" alt="#" />
                        <img src="/fb-content/media/img/icons/peanut-free.png" alt="#" />
                        <img src="/fb-content/media/img/icons/honeycomb.png" alt="#" />
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
                  <img src="/fb-content/media/img/testImg/testProduct03.jpg" alt="" />
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
                        <img src="/fb-content/media/img/icons/vegan.png" alt="#" />
                        <img src="/fb-content/media/img/icons/bpa-free.png" alt="#" />
                        <img src="/fb-content/media/img/icons/serum.png" alt="#" />
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
      </div> -->
    </section>
    <section id="__main_body">

      <div id="categorychooser">
        <?php include __DIR__ . '/fb-home/__gen_categorychooser.php'; ?>
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
              <picture>
                <!-- AVIF formátum -->
                <source type="image/avif" srcset="
        /fb-content/assets/media/images/site/home/HerbsOnDesk-3840px.avif 3840w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-2560px.avif 2560w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-1920px.avif 1920w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-1024px.avif 1024w
      " sizes="(max-width: 1024px) 1024px,
             (max-width: 1920px) 1920px,
             (max-width: 2560px) 2560px,
             3840px" />
                <!-- WebP formátum -->
                <source type="image/webp" srcset="
        /fb-content/assets/media/images/site/home/HerbsOnDesk-3840px.webp 3840w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-2560px.webp 2560w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-1920px.webp 1920w,
        /fb-content/assets/media/images/site/home/HerbsOnDesk-1024px.webp 1024w
      " sizes="(max-width: 1024px) 1024px,
             (max-width: 1920px) 1920px,
             (max-width: 2560px) 2560px,
             3840px" />
                <!-- Alapértelmezett JPG -->
                <img src="/fb-content/media/img/HerbsOnDesk-1024px.jpg" alt="Gyógynövények asztalon kis üvegcsékben"
                  loading="lazy" />
              </picture>
            </div>
            <div class="brandImages_imgHorizontal">
              <picture>
                <!-- AVIF formátum -->
                <source type="image/avif" srcset="
        /fb-content/assets/media/images/site/home/HerbsInHands-3840px.avif 3840w,
        /fb-content/assets/media/images/site/home/HerbsInHands-2560px.avif 2560w,
        /fb-content/assets/media/images/site/home/HerbsInHands-1920px.avif 1920w,
        /fb-content/assets/media/images/site/home/HerbsInHands-1024px.avif 1024w
      " sizes="(max-width: 1024px) 1024px,
             (max-width: 1920px) 1920px,
             (max-width: 2560px) 2560px,
             3840px" />
                <!-- WebP formátum -->
                <source type="image/webp" srcset="
        /fb-content/assets/media/images/site/home/HerbsInHands-3840px.webp 3840w,
        /fb-content/assets/media/images/site/home/HerbsInHands-2560px.webp 2560w,
        /fb-content/assets/media/images/site/home/HerbsInHands-1920px.webp 1920w,
        /fb-content/assets/media/images/site/home/HerbsInHands-1024px.webp 1024w
      " sizes="(max-width: 1024px) 1024px,
             (max-width: 1920px) 1920px,
             (max-width: 2560px) 2560px,
             3840px" />
                <!-- Alapértelmezett JPG -->
                <img src="/fb-content/assets/media/images/site/home/HerbsInHands-1024px.jpg"
                  alt="Gyógynövények hölgy kezében" loading="lazy" />
              </picture>
            </div>
          </div>
        </div>
        <div id="notifBlob">Mozgasd</div>
      </div>
      </div>

      <div id="categoryGallery" class="ImgGallery_container">
        <?php include __DIR__ . '/fb-home/__gen_imageGallery.php'; ?>
      </div>

      <div id="break"></div>
    </section>
  </main>
  <footer id="fb-footer"></footer>


  <!--jQuery-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
    integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!--lenis scroll-->
  <script src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"></script>
  <script src="http://localhost/fb-content/assets/js/lenis.js"></script>



  <!--ionicons-->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <!--extras-->


</body>

</html>
