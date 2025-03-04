<?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    include_once "../../../../.ext/init.php";

    $isLoggedIn = false;
    $result = getUserData();
    if ($result->isSuccess()) {
      $user = $result->message[0];
      $isLoggedIn = true;

      $curDate = new DateTime();
      $createDate = new DateTime($user["created_at"]);
      $accountAge = $curDate -> diff($createDate) -> days;
    }
    else {
        header("Location: ./login");
        exit();
    }

    // Avatárok lekérdezése
    $avatars = [];
    $result = selectData("SELECT * FROM avatar");
    if ($result->isSuccess()) {
        $avatars = $result->message;
    }

    // Felhasználó rendeléseinek lekérdezése
    $orders = [];
    $result = selectData(
        "SELECT `order`.id, `order`.email, `order`.phone, CONCAT(`order`.last_name, ' ', `order`.first_name) AS full_name, 
        `order`.company_name, `order`.tax_number, `order`.billing_address, `order`.delivery_address, `order`.status, 
        `order`.order_total, `order`.created_at 
        FROM `order` WHERE `order`.user_id=?", $user['id'], 'i'
    );

    if ($result->isSuccess()) {
        foreach ($result->message as $order) {
            $orderId = $order["id"];
            $orders[$orderId] = $order;
        }

        // Rendeléshez tartozó termékek lekérdezése
        $result = selectData(
            "SELECT product.name, order_item.quantity, `order`.id AS order_id FROM order_item 
            INNER JOIN `order` ON order_item.order_id=`order`.id
            INNER JOIN product ON order_item.product_id=product.id 
            WHERE `order`.user_id=?", $user['id'], 'i'
        );

        if ($result->isSuccess()) {
            foreach ($result->message as $orderItems) {
                $orderId = $orderItems["order_id"];
                unset($orderItems["order_id"]);

                if (!isset($orderItems[0])) $orderItems = [$orderItems];
                $orders[$orderId]["items"] = $orderItems;
            }
        }
        else if ($result->isEmpty()) {
            $orders = null;
        }
        else {
            $orders = "Hiba merült fel a rendelésekhez tartozó termékek lekérdezése során. Kérjük próbáld újra később.";
        }
    }
    else if ($result->isEmpty()) {
        $orders = null;
    }
    else {
        $orders = $result->toJSON(true);
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profil beállítások">
    <title>Beállítások</title>
    
    <link rel="stylesheet" href="./fb-auth/assets/css/settings.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/inputs.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/autofill-form.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />

    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer type="module" src="./fb-auth/assets/js/settings.js"></script>
</head>
<body>
    <div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>
    <div class="main">
        <div class="sidebar-wrapper dynamic-border" style="--mouse-x: 50%; --mouse-y: 50%; --radius:0px; --color: rgb(43, 43, 43)">
            <div class="sidebar">
                <div class="profile-general">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left back" id="back-button" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                    <div class="profile-picture">
                        <img src="<?= $user['pfp_uri'] ?>" alt="Profilkép" width="130" height="130">
                    </div>
                    <div class="name">
                        <?= $user['last_name']." ".$user['first_name'] ?>
                    </div>
                    <div class="username">
                        <?= "@".$user['user_name'] ?>
                    </div>
                </div>
                <div class="pages">
                    <div class="page active" data-pageid="0" tabindex="0">
                        Személyes adatok
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                            <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="1" tabindex="0">
                        Automatikus kitöltés
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                            <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="2" tabindex="0">
                        Rendeléseim
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="3" tabindex="0">
                        Jelszó
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                            <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                        </svg>
                    </div>
                </div>
                <div class="sidebar-bottom">
                    <?php 
                        if ($user["role"] === "Administrator") {
                            echo "<a class='dashboard' href='./dashboard' draggable='false'>
                                <div class='text'>Vezérlőpult</div>
                                <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-shield-shaded' viewBox='0 0 16 16'>
                                    <path fill-rule='evenodd' d='M8 14.933a1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56'/>
                                </svg>
                            </a>";
                        }
                    ?>
                    <a class="logout" href="./logout" draggable="false">
                        <div class="text">Kijelentkezés</div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-wrapper dynamic-border"  style="--mouse-x: 50%; --mouse-y: 50%; --radius: 0px; --color:  rgb(43, 43, 43)">
            <div class="content">
                <div class="content-page active"> <!-- Személyes adatok -->
                    <div class="content-main">
                        <div class="page-title">
                            Személyes adatok
                        </div>
                        <div class="divider">Általános adatok</div>
                        <form name="basic-info">
                            <div class="input-group input-group-half" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="last-name">Vezetéknév</label>
                                    <input type="text" name="last-name" id="last-name" required placeholder="" tabindex="1" value="<?= htmlspecialchars($user["last_name"]); ?>" disabled>
                                    <div class="edit-toggler">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy2-fill save" viewBox="0 0 16 16">
                                            <path d="M12 2h-2v3h2z"/>
                                            <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5V2.914a1.5 1.5 0 0 0-.44-1.06L14.147.439A1.5 1.5 0 0 0 13.086 0zM4 6a1 1 0 0 1-1-1V1h10v4a1 1 0 0 1-1 1zM3 9h10a1 1 0 0 1 1 1v5H2v-5a1 1 0 0 1 1-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <div class="input-group input-group-half" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="first-name">Keresztnév</label>
                                    <input type="text" name="first-name" id="first-name" required placeholder="" tabindex="1" value="<?= htmlspecialchars($user["first_name"]); ?>" disabled>
                                    <div class="edit-toggler">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy2-fill save" viewBox="0 0 16 16">
                                            <path d="M12 2h-2v3h2z"/>
                                            <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5V2.914a1.5 1.5 0 0 0-.44-1.06L14.147.439A1.5 1.5 0 0 0 13.086 0zM4 6a1 1 0 0 1-1-1V1h10v4a1 1 0 0 1-1 1zM3 9h10a1 1 0 0 1 1 1v5H2v-5a1 1 0 0 1 1-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <div class="divider">Elérhetőség</div>
                            <div class="input-group input-group-half" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="email">E-mail cím</label>
                                    <input type="text" name="email" id="email" required placeholder="" tabindex="1" value="<?= htmlspecialchars($user["email"]); ?>" disabled>
                                    <div class="edit-toggler">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy2-fill save" viewBox="0 0 16 16">
                                            <path d="M12 2h-2v3h2z"/>
                                            <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5V2.914a1.5 1.5 0 0 0-.44-1.06L14.147.439A1.5 1.5 0 0 0 13.086 0zM4 6a1 1 0 0 1-1-1V1h10v4a1 1 0 0 1-1 1zM3 9h10a1 1 0 0 1 1 1v5H2v-5a1 1 0 0 1 1-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <div class="input-group input-group-half" tabindex="-1">
                                <div class="input-body" tabindex="-1">
                                    <label for="phone">Telefonszám</label>
                                    <input type="text" name="phone" id="phone" required placeholder="" tabindex="1" disabled value="<?= htmlspecialchars($user["phone"]); ?>">
                                    <div class="edit-toggler">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy2-fill save" viewBox="0 0 16 16">
                                            <path d="M12 2h-2v3h2z"/>
                                            <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5V2.914a1.5 1.5 0 0 0-.44-1.06L14.147.439A1.5 1.5 0 0 0 13.086 0zM4 6a1 1 0 0 1-1-1V1h10v4a1 1 0 0 1-1 1zM3 9h10a1 1 0 0 1 1 1v5H2v-5a1 1 0 0 1 1-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="message-wrapper">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <?php if (count($avatars) > 0): ?>
                                <div class="divider">Profilkép</div>
                                <div class="avatar-field">
                                    <?php foreach ($avatars as $a): ?>
                                        <?php if ($a['uri'] == $user['pfp_uri']): ?>
                                            <div id="avatar-<?= htmlspecialchars($a['id']); ?>" class="avatar checked">
                                        <?php else: ?>
                                            <div id="avatar-<?= htmlspecialchars($a['id']); ?>" class="avatar" >
                                        <?php endif; ?>
                                            <img src="<?= htmlspecialchars($a['uri']); ?>" id="avatar-<?= htmlspecialchars($a['id']); ?> draggable="false" alt="A profilképed">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                            </svg>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="didyouknow">
                        <div class="dyk-text">
                            <span>Ön <b><?php echo $accountAge; ?></b> napja a Florens Botanica tagja! Köszönjük, hogy minket választott.</span>
                        </div>
                    </div>
                </div>
                <div class="content-page"> <!-- Automatikus kitöltés -->
                    <div class="content-main">
                        <div class="page-title">
                            Automatikus kitöltés
                        </div>
                        <div class="divider">Szállítási címeim</div>
                        <div class="cards">
                            <div class="saved-cards"></div>
                            <div class="add-field">+</div>
                        </div>
                        <div class="form-wrapper">
                            <form class="autofill-delivery autofill-form" data-lenis-prevent>
                                <div class="form-body-wrapper">
                                    <header>Új szállítási cím</header>
                                    <p class="form-description">Gyakran használt címeit elmentheti, hogy gördülékenyebb legyen a rendelés folyamata.</p>
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="autofill-name">Megnevezés</label>
                                            <input type="text" name="autofill-name" id="autofill-name" required placeholder="A mentett adatok címe" tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="input-group-inline" tabindex="-1">
                                        <div class="input-group" tabindex="-1">
                                            <div class="input-body" tabindex="-1">
                                                <label for="delivery-zip">Irányítószám</label>
                                                <input type="text" name="zip" id="delivery-zip" required placeholder="pl. 8200" tabindex="1">
                                            </div>
                                            <div class="message-wrapper">
                                                <div class="error-message"></div>
                                            </div>
                                        </div>
                                        <div class="input-group" tabindex="-1">
                                            <div class="input-body" tabindex="-1">
                                                <label for="delivery-city">Település</label>
                                                <input type="text" name="city" id="delivery-city" required placeholder="pl. Veszprém" tabindex="1">
                                            </div>
                                            <div class="message-wrapper">
                                                <div class="error-message"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="delivery-street-house">Utca és házszám</label>
                                            <input type="text" name="street-house" id="delivery-street-house" required placeholder="pl. Fő utca 29/B" tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                    <div class="form-close">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button type="button" class="form-save">Mentés</button>
                                    <button type="button" class="form-cancel">Mégse</button>
                                </div>
                            </form>
                        </div>
                        <div class="divider">Számlázási címeim</div>
                        <div class="cards">
                            <div class="saved-cards"></div>
                            <div class="add-field">+</div>
                        </div>
                        <div class="form-wrapper">
                            <form class="autofill-billing autofill-form" data-lenis-prevent>
                                <div class="form-body-wrapper">
                                    <header>Új számlázási cím</header>
                                    <p class="form-description">Gyakran használt címeit elmentheti, hogy gördülékenyebb legyen a rendelés folyamata.</p>
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="autofill-name-billing">Megnevezés</label>
                                            <input type="text" name="autofill-name" id="autofill-name-billing" required placeholder="A mentett adatok címe" tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="input-group-inline" tabindex="-1">
                                        <div class="input-group" tabindex="-1">
                                            <div class="input-body" tabindex="-1">
                                                <label for="billing-zip">Irányítószám</label>
                                                <input type="text" name="zip" id="billing-zip" required placeholder="pl. 8200" tabindex="1">
                                            </div>
                                            <div class="message-wrapper">
                                                <div class="error-message"></div>
                                            </div>
                                        </div>
                                        <div class="input-group" tabindex="-1">
                                            <div class="input-body" tabindex="-1">
                                                <label for="billing-city">Település</label>
                                                <input type="text" name="city" id="billing-city" required placeholder="pl. Veszprém" tabindex="1">
                                            </div>
                                            <div class="message-wrapper">
                                                <div class="error-message"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="billing-street-house">Utca és házszám</label>
                                            <input type="text" name="street-house" id="billing-street-house" required placeholder="pl. Fő utca 29/B" tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                    <div class="form-close">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-lg" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button type="button" class="form-save">Mentés</button>
                                    <button type="button" class="form-cancel">Mégse</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="content-page">  <!-- Rendeléseim -->
                    <div class="content-main">
                        <div class="page-title">Rendeléseim</div>
                        <?php if (is_array($orders)): ?>
                            <div class="order-timeline">
                                <?php foreach ($orders as $order): ?>
                                    <div class="timeline-event">
                                        <div class="event-date"><?= htmlspecialchars($order["created_at"]); ?> - <?= htmlspecialchars($order["status"]); ?></div>
                                        <div class="event-body">
                                            <hr>
                                            <div class="total">
                                                <span class="timeline-section">Összeg:</span> 
                                                <strong><?= htmlspecialchars($order["order_total"]); ?> Ft</strong>
                                            </div>
                                            <div class="order-id">
                                                <span class="timeline-section">Rendelési azonosító:</span>
                                                <span class="id-number">#<?= htmlspecialchars($order["id"]); ?></span>
                                            </div>
                                            <div class="personal-info">
                                                <div><?= htmlspecialchars($order["email"]); ?></div>
                                                <div><?= htmlspecialchars($order["phone"]); ?></div>
                                                <div><?= htmlspecialchars($order["delivery_address"]); ?></div>
                                                <?php if (isset($order["billing_address"])): ?>
                                                    <div><?= htmlspecialchars($order["billing_address"]); ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (isset($order["company_name"]) && isset($order["tax_number"])): ?>
                                                <div class="timeline-section">Céges adatok</div>
                                                <div><?= htmlspecialchars($order["company_name"]); ?></div>
                                                <div><?= htmlspecialchars($order["tax_number"]); ?></div>
                                            <?php endif; ?>
                                            <div class="timeline-section">Tételek</div>
                                            <div class="order-items">
                                                <ul>
                                                    <?php foreach ($order["items"] as $item): ?>
                                                        <li>
                                                            <?= htmlspecialchars($item["name"]); ?>
                                                             - 
                                                            <?= htmlspecialchars($item["quantity"]); ?> db
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="error-message"><?= is_null($orders) ? "Még nincsenek rendeléseid." : htmlspecialchars($orders); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="content-page">
                    <div class="content-main">
                        <div class="page-title">Jelszó</div>
                        <div class="page-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#94d8ff" class="bi bi-info-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                            </svg>
                            <p>Kérjük, hogy fiókja biztonsága érdekében időről időre változtassa meg a jelszavát.</p>
                        </div>
                        <div class="divider">Jelszó módosítása</div>
                        
                        <form class="password-form">
                            <div class="form-body-wrapper">
                                <div class="input-group-inline">
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="old-pass">Régi jelszó</label>
                                            <input type="password" name="old-pass" id="old-pass" autocomplete="current-password"  required tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                    <div class="notice">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-shield-exclamation" viewBox="0 0 16 16">
                                            <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                                            <path d="M7.001 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.553.553 0 0 1-1.1 0z"/>
                                        </svg>
                                        <p>Használjon olyan jelszót, amit máshol még nem használt.</p>
                                    </div>
                                </div>
                                <div class="input-group-inline" tabindex="-1">
                                    <div class="password-input">
                                        <div class="input-group" tabindex="-1">
                                            <div class="input-body" tabindex="-1">
                                                <label for="new-pass">Új jelszó</label>
                                                <input type="password" name="new-pass" id="new-pass" autocomplete="new-password"  required tabindex="1">
                                            </div>
                                            <div class="message-wrapper">
                                                <div class="error-message"></div>
                                            </div>
                                        </div>
                                        <div class="password-state">
                                            <div class="matcher" data-for="charlen">
                                                <div class="state">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-check-circle-fill valid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-circle-fill invalid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                                    </svg>
                                                </div>
                                                <div class="description">8-64 karakter hosszú</div>
                                            </div>
                                            <div class="matcher" data-for="haslower">
                                                <div class="state">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-check-circle-fill valid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-circle-fill invalid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                                    </svg>
                                                </div>
                                                <div class="description">Tartalmaz kisbetűt</div>
                                            </div>
                                            <div class="matcher" data-for="hasupper">
                                                <div class="state">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-check-circle-fill valid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-circle-fill invalid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                                    </svg>
                                                </div>
                                                <div class="description">Tartalmaz nagybetűt</div>
                                            </div>
                                            <div class="matcher" data-for="hasdigit">
                                                <div class="state">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-check-circle-fill valid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-circle-fill invalid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                                    </svg>
                                                </div>
                                                <div class="description">Tartalmaz számot</div>
                                            </div>
                                            <div class="matcher" data-for="hasspecial">
                                                <div class="state">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-check-circle-fill valid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-x-circle-fill invalid" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                                    </svg>
                                                </div>
                                                <div class="description">Tartalmaz speciális karaktert</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group" tabindex="-1">
                                        <div class="input-body" tabindex="-1">
                                            <label for="new-pass-confirm">Új jelszó mégegyszer</label>
                                            <input type="password" name="new-pass-confirm" id="new-pass-confirm" autocomplete="new-password" required tabindex="1">
                                        </div>
                                        <div class="message-wrapper">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="message-container">
                                    
                                </div>
                                <button type="button" class="action-button" tabindex="1">Módosítás</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- Jelszó -->
            </div>
        </div>
    </div>
</body>
</html>