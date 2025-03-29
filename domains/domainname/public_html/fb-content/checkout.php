<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';
    
    // Lekérdezzük a kosár tartalmát
    // Ehhez a cURL-t használjuk, azonban mivel az API használná a SESSION-t és írna is bele
    // le kell zárni a SESSION-t, mert zárolva van ha már aktív (nem lehet egyszerre két helyről beleírni),
    // és miután lekérdeztük, újra el kell indítani, hogy ne vesszenek el az adatok
    $sessionId = session_id();
    session_write_close();

    $curl = curl_init("http://localhost/api/cart/get");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "X-Requested-With: XMLHttpRequest"
    ]);
    curl_setopt($curl, CURLOPT_COOKIE, "PHPSESSID=$sessionId");
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'cURL hiba: ' . curl_error($curl);
        exit();
    }
    else {
        $data = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $cart = $data["message"];
        } else {
            echo "Válasz:\n$response\n";
            exit();
        }
    }
    curl_close($curl);
    session_start();

    // Ha üres a kosár, akkor csak visszairányítjuk a főoldalra
    if (empty($cart)) {
        header("Location: /");
        exit();
    }

    $subtotal = array_reduce($cart, function ($subtotal, $item) {return $subtotal + $item['unit_price'] * $item['quantity'];}, 0);
    $deliveryPrice = 1000;
    $total = $subtotal + $deliveryPrice;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fizetés és rendelés</title>

    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="stylesheet" href="/fb-content/assets/css/checkout.css">
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />
    <link rel="stylesheet" href="/fb-content/assets/css/confetti.css">

    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer type="module" src="/fb-content/assets/js/checkout.js"></script>
</head>
<body>
<div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>

    <div class="checkout-result-overlay">
        <svg class="flower" id="flower-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="none"><path fill="#44911B" d="m8.02 29.6 1.14.39 1.35-3.9 4.02.42a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-3.74-.39a13.2 13.2 0 0 1 5.1-6.46l4.14.44a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-2.9-.31a14.4 14.4 0 0 0 4.02-6.03l.38-1.09a.6.6 0 0 0-.37-.77.6.6 0 0 0-.77.37l-.38 1.09a13.2 13.2 0 0 1-4.05 5.82l-1.7-3.5a.6.6 0 1 0-1.1.53l1.81 3.72a14.4 14.4 0 0 0-5.28 6.59l-1.31-2.69a.6.6 0 1 0-1.1.53l1.84 3.76z"/><path fill="#86D72F" d="M23.98 2a6.74 6.74 0 0 0-3.11 9 6.73 6.73 0 0 0 3.1-9M15.8 9.05l-1.81-3.72a4.23 4.23 0 0 0-1.95 5.65l1.8 3.72a4.23 4.23 0 0 0 1.96-5.65m-5.43 6.39-2.32-4.76a5.4 5.4 0 0 0-2.5 7.22l2.33 4.76a5.41 5.41 0 0 0 2.49-7.22m17.56.03-4.12-.43a4.22 4.22 0 0 0-4.64 3.76l4.12.43a4.22 4.22 0 0 0 4.64-3.76m-8.57 6.13 5.27.55a5.4 5.4 0 0 1-5.94 4.81l-5.27-.55a5.4 5.4 0 0 1 5.94-4.81"/></svg>
        <svg class="flower" id="flower-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none"><path fill="#44911B" d="m8.02 29.6 1.14.39 1.35-3.9 4.02.42a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-3.74-.39a13.2 13.2 0 0 1 5.1-6.46l4.14.44a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-2.9-.31a14.4 14.4 0 0 0 4.02-6.03l.38-1.09a.6.6 0 0 0-.37-.77.6.6 0 0 0-.77.37l-.38 1.09a13.2 13.2 0 0 1-4.05 5.82l-1.7-3.5a.6.6 0 1 0-1.1.53l1.81 3.72a14.4 14.4 0 0 0-5.28 6.59l-1.31-2.69a.6.6 0 1 0-1.1.53l1.84 3.76z"/><path fill="#86D72F" d="M23.98 2a6.74 6.74 0 0 0-3.11 9 6.73 6.73 0 0 0 3.1-9M15.8 9.05l-1.81-3.72a4.23 4.23 0 0 0-1.95 5.65l1.8 3.72a4.23 4.23 0 0 0 1.96-5.65m-5.43 6.39-2.32-4.76a5.4 5.4 0 0 0-2.5 7.22l2.33 4.76a5.41 5.41 0 0 0 2.49-7.22m17.56.03-4.12-.43a4.22 4.22 0 0 0-4.64 3.76l4.12.43a4.22 4.22 0 0 0 4.64-3.76m-8.57 6.13 5.27.55a5.4 5.4 0 0 1-5.94 4.81l-5.27-.55a5.4 5.4 0 0 1 5.94-4.81"/></svg>
        <svg class="flower" id="flower-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="none"><path fill="#44911B" d="m8.02 29.6 1.14.39 1.35-3.9 4.02.42a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-3.74-.39a13.2 13.2 0 0 1 5.1-6.46l4.14.44a.6.6 0 0 0 .66-.54.6.6 0 0 0-.54-.66l-2.9-.31a14.4 14.4 0 0 0 4.02-6.03l.38-1.09a.6.6 0 0 0-.37-.77.6.6 0 0 0-.77.37l-.38 1.09a13.2 13.2 0 0 1-4.05 5.82l-1.7-3.5a.6.6 0 1 0-1.1.53l1.81 3.72a14.4 14.4 0 0 0-5.28 6.59l-1.31-2.69a.6.6 0 1 0-1.1.53l1.84 3.76z"/><path fill="#86D72F" d="M23.98 2a6.74 6.74 0 0 0-3.11 9 6.73 6.73 0 0 0 3.1-9M15.8 9.05l-1.81-3.72a4.23 4.23 0 0 0-1.95 5.65l1.8 3.72a4.23 4.23 0 0 0 1.96-5.65m-5.43 6.39-2.32-4.76a5.4 5.4 0 0 0-2.5 7.22l2.33 4.76a5.41 5.41 0 0 0 2.49-7.22m17.56.03-4.12-.43a4.22 4.22 0 0 0-4.64 3.76l4.12.43a4.22 4.22 0 0 0 4.64-3.76m-8.57 6.13 5.27.55a5.4 5.4 0 0 1-5.94 4.81l-5.27-.55a5.4 5.4 0 0 1 5.94-4.81"/></svg>
        <div class="overlay-body">
            <div class="thank-you">Köszönjük a rendelésedet!</div>
            <p>Nagyon örülünk, hogy a természetes megoldásokat választottad. Ha bármilyen kérdésed van, keress minket bizalommal! 🌿</p>
            <p class="from">- A Florens Botanica csapata</p>
        </div>
        <a href="/" class="back-to-home">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
            </svg>
            <span>Vissza a főoldalra</span>
        </a>
    </div>

    <div class="checkout-error-overlay">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" id="checkout-error-icon" viewBox="0 0 16 16">
        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
        </svg>
        <div class="overlay-body">
            Hiba merült fel a rendelésed leadása során. Kérjük, ellenőrizd a mezők helyességét, a termékek elérhetőségét és próbáld meg újra, vagy keress meg minket. 
        </div>
        <button class="overlay-close">Újra próbálkozok</button>
    </div>

    <main>
        <form class="checkout-form">
            <header>Megrendelés</header>
            <div class="purchase-type-radios" id="purchase-types" data-target=".company-section">
                <div class="radio checked">Magánszemélyként rendelek</div>
                <div class="radio">Cégként rendelek</div>
                <div class="border"></div>
            </div>
            <section class="hideable company-section">
                <div class="section-wrapper">
                    <header>Cég adatai</header>
                    <div class="hideable-inputs">
                        <div class="input-group-inline">
                            <div class="input-group" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="company-name">Cégnév</label>
                                    <input disabled type="text" name="company-name" id="company-name" required placeholder="A megrendelő cég neve" tabindex="1">
                                    <div class="status">
                                        <div class="error"></div>
                                            <div class="success"></div>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <div class="input-group" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="tax-number">Adószám</label>
                                    <input disabled type="text" name="tax-number" id="tax-number" required placeholder="pl. 12345678-2-10" tabindex="1">
                                    <div class="status">
                                        <div class="error"></div>
                                        <div class="success"></div>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <header>Személyes adatok</header>
                <div class="input-group" tabindex="-1">
                    <div class="input-body" tabindex="-1">
                        <label for="email">E-mail</label>
                        <input type="text" name="email" id="email" required placeholder="pelda@example.com" tabindex="1" value="<?php if ($isLoggedIn) echo htmlspecialchars($user["email"]); ?>">
                        <div class="status">
                            <div class="error"></div>
                            <div class="success"></div>
                        </div>
                    </div>
                    <div class="message-wrapper">
                        <div class="error-message"></div>
                    </div>
                </div>
                <div class="input-group-inline" tabindex="-1">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="last-name">Vezetéknév</label>
                            <input type="text" name="last-name" id="last-name" required placeholder="pl. Minta" tabindex="1" value="<?php if ($isLoggedIn) echo htmlspecialchars($user["last_name"]); ?>">
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="first-name">Keresztnév</label>
                            <input type="text" name="first-name" id="first-name" required placeholder="pl. Károly" tabindex="1" value="<?php if ($isLoggedIn) echo htmlspecialchars($user["first_name"]); ?>">
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="input-group" tabindex="-1">
                    <div class="input-body" tabindex="-1">
                        <label for="phone">Telefonszám</label>
                        <input type="tel" name="phone" id="phone" required placeholder="pl. +36 30 123 1234" tabindex="1" value="<?php if ($isLoggedIn && !is_null($user["phone"])) echo htmlspecialchars($user["phone"]); ?>">
                        <div class="status">
                            <div class="error"></div>
                            <div class="success"></div>
                        </div>
                    </div>
                    <div class="message-wrapper">
                        <div class="error-message"></div>
                    </div>
                </div>
            </section>
            <section>
                <header>Szállítási cím</header>
                <div class="notice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                    </svg>
                    <div>
                        Egyelőre csak utánvétellel és házhozszállítással tudjuk feldolgozni a rendeléseket!
                    </div>
                </div>
                <?php if ($isLoggedIn): ?>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="delivery-autofill">Automatikus kitöltés</label>
                            <select name="autofill" id="delivery-autofill">
                                <option value="def"></option>
                            </select>
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="input-group-inline" tabindex="-1">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="zip-code">Irányítószám</label>
                            <input type="text" name="zip-code" id="zip-code" required placeholder="pl. 8248" tabindex="1">
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="city">Település</label>
                            <input type="text" name="city" id="city" required placeholder="pl. Nemesvámos" tabindex="1">
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
                <div class="input-group" tabindex="-1">
                    <div class="input-body" tabindex="-1">
                        <label for="street-house">Utca és házszám</label>
                        <input type="text" name="street-house" id="street-house" required placeholder="pl. Fő utca 29/B" tabindex="1">
                        <div class="status">
                            <div class="error"></div>
                            <div class="success"></div>
                        </div>
                    </div>
                    <div class="message-wrapper">
                        <div class="error-message"></div>
                    </div>
                </div>
            </section>
            <section>
                <header>Számlázási cím</header>
                <div class="input-group-check" data-target=".input-group-hideable">
                    <input type="checkbox" name="same-address" id="same-address" tabindex="1">
                    <label for="same-address">A számlázási adatok megegyeznek a szállítási adatokkal</label>
                </div>
                <div class="input-group-hideable">
                    <?php if ($isLoggedIn): ?>
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-autofill">Automatikus kitöltés</label>
                                <select name="autofill" id="billing-autofill">
                                    <option value="def"></option>
                                </select>
                                <div class="status">
                                    <div class="error"></div>
                                    <div class="success"></div>
                                </div>
                            </div>
                            <div class="message-wrapper">
                                <div class="error-message"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="input-group-inline" tabindex="-1">
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-zip">Irányítószám</label>
                                <input type="text" name="billing-zip" id="billing-zip" required placeholder="pl. 8200" tabindex="1">
                                <div class="status">
                                    <div class="error"></div>
                                    <div class="success"></div>
                                </div>
                            </div>
                            <div class="message-wrapper">
                                <div class="error-message"></div>
                            </div>
                        </div>
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-city">Település</label>
                                <input type="text" name="billing-city" id="billing-city" required placeholder="pl. Veszprém" tabindex="1">
                                <div class="status">
                                    <div class="error"></div>
                                    <div class="success"></div>
                                </div>
                            </div>
                            <div class="message-wrapper">
                                <div class="error-message"></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="billing-street-house">Utca és házszám</label>
                            <input type="text" name="billing-street-house" id="billing-street-house" required placeholder="pl. Fő utca 29/B" tabindex="1">
                            <div class="status">
                                <div class="error"></div>
                                <div class="success"></div>
                            </div>
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
        <div class="order-details">
            <header>Összesítő</header>
            <div class="items" data-lenis-prevent>
                <?php foreach ($cart as $item): ?>
                    <?php 
                        $thumbnail_uri = explode('.', $item['thumbnail_uri'])[0];
                    ?>
                    <div class="item">
                        <div class="item-image">
                            <picture>
                                <source type="image/avif" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.avif 1x">
                                <source type="image/webp" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.webp 1x">
                                <source type="image/jpeg" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.jpg 1x">
                                <img 
                                src="<?= htmlspecialchars($thumbnail_uri); ?>-768px.jpg" 
                                alt="<?= htmlspecialchars($item['name']); ?>" 
                                loading="lazy">
                            </picture>
                        </div>
                        <div class="item-body">
                            <div class="item-info">
                                <div class="item-name"><?= htmlspecialchars($item['name']); ?></div>
                                <div class="quantity">
                                    Mennyiség: <?= htmlspecialchars($item['quantity']); ?>
                                </div>
                                <div class="item-price">
                                    <div class="value"><?= htmlspecialchars($item['unit_price'] * $item['quantity']); ?></div>
                                    <div class="currency">Ft </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
            <div class="price">
                <div class="subtotal inline-group">
                    <div class="label">Részösszeg</div>
                    <div class="item-price">
                        <div class="value"><?= htmlspecialchars($subtotal); ?></div>
                        <div class="currency">Ft </div>
                    </div>
                </div>
                <div class="delivery inline-group">
                    <div class="label">Szállítási díj</div>
                    <div class="item-price">
                        <div class="value"><?= htmlspecialchars($deliveryPrice); ?></div>
                        <div class="currency">Ft </div>
                    </div>
                </div>
                <div class="tax">
                    Az árak tartalmazzák az ÁFA-t.
                </div>
                <div class="total inline-group">
                    <div class="label">Végösszeg</div>
                    <div class="item-price">
                        <div class="value"><?= htmlspecialchars($total); ?></div>
                        <div class="currency">Ft </div>
                    </div>
                </div>
            </div>
            <button class="payment-button">Rendelés</button>
        </div>
    </main>
</body>
</html>