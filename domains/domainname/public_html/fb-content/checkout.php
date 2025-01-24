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

    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="stylesheet" href="/fb-content/assets/css/checkout.css">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />

    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="/fb-content/assets/js/checkout.js"></script>
</head>
<body>
    <div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div><div class="layer layer-0"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-1"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-2"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-3"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div></div>

    <main>
        <form class="checkout-form">
            <header>Megrendelés</header>
            <div class="input-group" tabindex="-1">
                <div class="input-body" tabindex="-1">
                    <label for="autofill">Mentett adatok</label>
                    <select name="autofill" id="autofill">
                        <option value="asd">asdasd</option>
                        <option value="asd">asdasd</option>
                        <option value="asd">asdasd</option>
                        <option value="asd">asdasd</option>
                    </select>
                    <div class="status">
                        <div class="error">
                            <div class="message"></div>
                        </div>
                        <div class="success"></div>
                    </div>
                </div>
            </div>
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
                                        <div class="error">
                                            <div class="message"></div>
                                        </div>
                                            <div class="success"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="tax-number">Adószám</label>
                                    <input disabled type="text" name="tax-number" id="tax-number" required placeholder="pl. 12345678-2-10" tabindex="1">
                                    <div class="status">
                                        <div class="error">
                                            <div class="message"></div>
                                        </div>
                                            <div class="success"></div>
                                    </div>
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
                        <input type="text" name="email" id="email" required placeholder="pelda@example.com" tabindex="1">
                        <div class="status">
                            <div class="error">
                                <div class="message"></div>
                            </div>
                                <div class="success"></div>
                        </div>
                    </div>
                </div>
                <div class="input-group-inline" tabindex="-1">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="last-name">Vezetéknév</label>
                            <input type="text" name="last-name" id="last-name" required placeholder="pl. Minta" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="first-name">Keresztnév</label>
                            <input type="text" name="first-name" id="first-name" required placeholder="pl. Károly" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input-group" tabindex="-1">
                    <div class="input-body" tabindex="-1">
                        <label for="phone">Telefonszám</label>
                        <input type="tel" name="phone" id="phone" required placeholder="pl. +36 30 123 1234" tabindex="1">
                        <div class="status">
                            <div class="error">
                                <div class="message"></div>
                            </div>
                                <div class="success"></div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <header>Szállítási cím</header>
                <div class="input-group-inline" tabindex="-1">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="zip-code">Irányítószám</label>
                            <input type="text" name="zip-code" id="zip-code" required placeholder="pl. 8248" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="city">Település</label>
                            <input type="text" name="city" id="city" required placeholder="pl. Nemesvámos" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input-group-inline" tabindex="-1">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="street">Utca</label>
                            <input type="text" name="street" id="street" required placeholder="pl. Fő" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="house-number">Házszám</label>
                            <input type="number" name="house-number" id="house-number" required placeholder="pl. 28" tabindex="1">
                            <div class="status">
                                <div class="error">
                                    <div class="message"></div>
                                </div>
                                <div class="success"></div>
                            </div>
                        </div>
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
                    <div class="input-group-inline" tabindex="-1">
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-zip">Irányítószám</label>
                                <input type="text" name="billing-zip" id="billing-zip" required placeholder="pl. 8200" tabindex="1">
                                <div class="status">
                                    <div class="error">
                                        <div class="message"></div>
                                    </div>
                                    <div class="success"></div>
                                </div>
                            </div>
                        </div>
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-city">Település</label>
                                <input type="text" name="billing-city" id="billing-city" required placeholder="pl. Veszprém" tabindex="1">
                                <div class="status">
                                    <div class="error">
                                        <div class="message"></div>
                                    </div>
                                    <div class="success"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group-inline" tabindex="-1">
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-street">Utca</label>
                                <input type="text" name="billing-street" id="billing-street" required placeholder="pl. Iskola" tabindex="1">
                                <div class="status">
                                    <div class="error">
                                        <div class="message"></div>
                                    </div>
                                    <div class="success"></div>
                                </div>
                            </div>
                        </div>
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="billing-house-number">Házszám</label>
                                <input type="text" name="billing-house-number" id="billing-house-number" required placeholder="pl. 4" tabindex="1">
                                <div class="status">
                                    <div class="error">
                                        <div class="message"></div>
                                    </div>
                                    <div class="success"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
        <div class="order-details">
            <header>Összesítő</header>
            <div class="items">
                <?php foreach ($cart as $item): ?>
                    <?php 
                        $thumbnail_uri = explode('.', $item['thumbnail_uri'])[0];
                    ?>
                    <div class="item">
                        <div class="item-image">
                            <picture>
                                <source type="image/avif" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.avif 1x" media="(min-width: 768px)">
                                <source type="image/webp" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.webp 1x" media="(min-width: 768px)">
                                <source type="image/jpeg" srcset="<?= htmlspecialchars($thumbnail_uri); ?>-768px.jpg 1x" media="(min-width: 768px)">
                                <img 
                                src="<?= htmlspecialchars($thumbnail_uri); ?>.jpg" 
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
            <button class="payment-button">Fizetés</button>
        </div>
    </main>
</body>
</html>