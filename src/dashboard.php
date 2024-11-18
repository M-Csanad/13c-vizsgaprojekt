<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A Florens Botanica vezérlőpultja">
    <title>Vezérlőpult</title>
    <link rel="preload" href="fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/root.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/allergen-checkbox.css">
    <link rel="stylesheet" href="./css/search.css">
    <?php 
        include_once "./auth/init.php"; 
        session_start();
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] != "Administrator") {
                header("Location: ./");
                exit();
            }
        }
        else {
            header("Location: ./");
            exit();
        }
    ?>
    <script defer src="./js/dashboard.js"></script>
    <script defer src="./js/search.js"></script>
    <script defer src="./js/tag-checkbox.js"></script>
</head>
<body>
    <p>
        <a href="./" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            Vissza a főoldalra
        </a>
    </p>
    <header class="page-title">Vezérlőpult</header>

    <nav class="pages">
        <div class="page active" data-pageid="0" tabindex="0">
            Kategóriák
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="1" tabindex="0">
            Termékek
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="2" tabindex="0">
            Oldalak
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="3" tabindex="0">
            Jogosultságok
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
    </nav>

    <!------------------------------ Kategóriák kezelése ----------------------------->
    <div class="section-group active">
        <div class="group-body">
            <!-------------------------- Új kategória létrehozása ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új kategória létrehozása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="">
                        <div class="input-grid">
                            <div class="form-divider">Általános adatok</div>
                            <div class="inline-input">
                                <label for="category_name"><div>Név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="category_name" id="category_name" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="category_subname"><div>Alcím</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="category_subname" id="category_subname" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="type"><div>Típus</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="type" id="type" required>
                                            <option value="main">Főkategória</option>
                                            <option value="sub">Alkategória</option>
                                        </select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="parent_category"><div>Főkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="parent_category" id="parent_category" disabled required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="parent_category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_vertical" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image_vertical" id="thumbnail_image_vertical" required accept="image/png, image/jpeg" data-orientation="vertical" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_horizontal" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image_horizontal" id="thumbnail_image_horizontal" required accept="image/png, image/jpeg"  data-orientation="horizontal" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Videó
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle" id="toggle-button" tabindex="0">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású videó
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_video" class="input-wrapper" tabindex="-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="thumbnail_video" id="thumbnail_video" accept="video/*" data-type="video" data-orientation="horizontal" data-type="video" data-count="singular" tabindex="-1">
                                                Videó kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Videó feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_category'>
                        </div>
                    </form>
                </div>
            </section>

            <!-------------------------- Kategória törlése ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Kategória törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="true" data-confirm-message="A kategória törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="category" data-id-input="category_id" data-type-input="category_type">
                                    <input type="text" name="category_name" id="category_name_delete" placeholder="Kategória keresése" required autocomplete="off">
                                    <label for="category_name_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="category_id" id="category_id_delete" value="null">
                                    <input type="hidden" name="category_type" id="category_type_delete" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Törlés" class="form-submit-danger" name='delete_category'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>

            <!-------------------------- Kategória módosítása ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Kategória módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="true" data-confirm-message="A kategória módosítása nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-autofill-fields="true" data-search-type="category" data-id-input="category_id_modify" data-type-input="category_type_modify">
                                    <input type="text" name="category_name" id="category_name_modify_search" placeholder="Kategória keresése" required autocomplete="off">
                                    <label for="category_name_modify_search" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="category_id" id="category_id_modify" value="null">
                                    <input type="hidden" name="category_type" id="category_type_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Általános adatok</div>
                            <div class="inline-input">
                                <label for="category_name_modify"><div>Név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="name" id="category_name_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="category_subname_modify"><div>Alcím</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="subname" id="category_subname_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description_modify"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="parent_category_modify"><div>Főkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="parent_category" id="parent_category_modify" disabled required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="parent_category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle" id="toggle-button">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_vertical_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="thumbnail_image_vertical" id="thumbnail_image_vertical_modify" required accept="image/png, image/jpeg" data-orientation="vertical" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Függőleges borítókép feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle" id="toggle-button">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_horizontal_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="thumbnail_image_horizontal" id="thumbnail_image_horizontal_modify" required accept="image/png, image/jpeg"  data-orientation="horizontal" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Vízszintes borítókép feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Videó
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle" id="toggle-button-new">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású videó
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_video_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="thumbnail_video" id="thumbnail_video_modify" accept="video/*" data-type="video" data-orientation="horizontal" data-type="video" data-count="singular" tabindex="-1">
                                                Videó kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Videó feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                            <div class="form-submit-wrapper">
                                <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_category'>
                            </div>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Termékek kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <!----------------------------- Új termék felvitele ---------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új termék felvitele</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="">
                        <div class="input-grid">
                            <div class="form-divider">Általános termékadatok</div>
                            <div class="inline-input">
                                <label for="product_name"><div>Termék név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="product_name" id="product_name" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="price"><div>Egységár</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="price" id="price" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="stock"><div>Készlet</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="stock" id="stock" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input tag-wrapper">
                                <label><div>Allergének</div></label>
                                <div class="tag-body">
                                    <div class="tag-items">
                                        <?php
                                            $tags = selectData("SELECT * FROM tag;", null);
                                            if (is_array($tags)) {
                                                $count = 0;
                                                for ($i = 0; $i < count($tags); $i++) {
                                                    $tag = $tags[$i];
                                                    $id = "tag".$i;
                                                    echo "<label for='$id' class='tag-checkbox'><img loading='lazy' src='{$tag['icon_uri']}' draggable='false' title='{$tag['name']}' alt='{$tag['name']}'><input type='checkbox' name='tags[]' id='$id' value='{$tag['id']}'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check2 tag-check' viewBox='0 0 16 16'><path d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0'/></svg></label>";
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Termékoldal adatai</div>
                            <div class="inline-input">
                                <label for="category"><div>Kategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="category" id="product_category_create" required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory"><div>Alkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="subcategory" id="product_subcategory_create" required data-table="subcategory"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="subcategory_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content"><div>Tartalom</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="content" id="content_create" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input">
                                <label><div>Borítókép</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_create" class="input-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image" id="thumbnail_image_create" required accept="image/png, image/jpeg">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label><div>Képek</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="product_images_create" class="input-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="product_images[]" id="product_images_create" multiple required accept="image/png, image/jpeg" data-type="image" data-count="multiple" data-orientation="any">
                                                Képek kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Videó
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle" id="toggle-button-product">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású videó
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="product_video" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="product_video" id="product_video" required accept="video/*" data-type="video" data-orientation="horizontal" data-type="video" data-count="singular" tabindex="-1">
                                                Videó kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Videó feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_product'>
                        </div>
                    </form>
                </div>
            </section>
            <!------------------------------- Termék törlése ------------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Termék törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="true" data-confirm-message="A termék törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id">
                                    <input type="text" name="product_name" id="product_name_delete" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_delete" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-submit-wrapper">
                                <input type="submit" value="Törlés" class="form-submit-danger" name='delete_product'>
                            </div>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
            <!------------------------------ Termék módosítása ----------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title"><div>Termék módosítása</div></div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id" data-autofill-fields="true">
                                    <input type="text" name="product_name" id="product_name_modify" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_modify" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Általános termékadatok</div>
                            <div class="inline-input">
                                <label for="name_modify"><div>Termék név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="name" id="name_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description_modify"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="price_modify"><div>Egységár</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="price" id="price_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="stock_modify"><div>Készlet</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="stock" id="stock_modify" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input tag-wrapper">
                                <label><div>Allergének</div></label>
                                <div class="tag-body">
                                    <div class="tag-items" name="tags">
                                        <?php
                                            $tags = selectData("SELECT * FROM tag;", null);
                                            if (is_array($tags)) {
                                                $count = 0;
                                                for ($i = 0; $i < count($tags); $i++) {
                                                    $tag = $tags[$i];
                                                    $id = "tag".$i;
                                                    echo "<label for='$id-modify' class='tag-checkbox'><img loading='lazy' src='{$tag['icon_uri']}' draggable='false' title='{$tag['name']}' alt='{$tag['name']}'><input type='checkbox' name='tags[]' id='$id-modify' value='{$tag['id']}'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check2 tag-check' viewBox='0 0 16 16'><path d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0'/></svg></label>";
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="thumbnail_image" id="thumbnail_image_modify" required accept="image/png, image/jpeg" data-orientation="vertical" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Borítókép feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Képek
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="product_images_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" multiple disabled name="product_images[]" id="product_images_modify" required accept="image/png, image/jpeg"  data-type="image" data-count="multiple" data-orientation="any" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Termékképek feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input has-toggle">
                                <label>
                                    <div>
                                        Videó
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class="toggle">
                                        <div class="toggle-text off">KI</div>
                                        <div class="toggle-text on">BE</div>
                                        <div class="toggle-circle"></div>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású videó
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="product_video_modify" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" disabled name="product_video" id="product_video_modify" required accept="video/*" data-type="video" data-orientation="horizontal" data-type="video" data-count="singular" tabindex="-1">
                                                Videó kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                    <div class="input-tooltip">
                                        Videó feltöltéséhez kapcsolja be az elemet! 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='modify_product'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Termékek oldalak kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <!----------------------------- Új termék oldal felvitele ---------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új termék oldal felvitele</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id">
                                    <input type="text" name="product_name" id="product_name_page_create" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_page_create" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_page_create" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Termék oldal adatai</div>
                            <div class="inline-input">
                                <label for="category"><div>Kategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="category" id="product_category_page_create" required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory"><div>Alkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="subcategory" id="product_subcategory" required data-table="subcategory"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="subcategory_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content"><div>Tartalom</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="content" id="content" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_product_page'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Jogosultságok kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Jogosultság módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" id="form-role" data-needs-confirm="true" data-confirm-message="Adminisztrátori jogokkal csak megbízható személyeket lásson el!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="user" data-id-input="user_id">
                                    <input type="text" name="user_name" id="user_name_modify" placeholder="Felhasználó keresése" required autocomplete="off">
                                    <label for="user_name_modify" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="user_id" id="user_id_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="inline-input">
                                <label for="role"><div>Jogosultság</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="role" id="role" required>
                                            <option value="Guest">Guest</option>
                                            <option value="Administrator">Administrator</option>
                                        </select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_role'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <?php
        // Kategória létrehozása
        if (isset($_POST['create_category'])) {

            $categoryData = array(
                "name" => $_POST['category_name'],
                "subname" => $_POST['category_subname'],
                "type" => $_POST['type'],
                "description" => $_POST['description']);

            if (isset($_POST['parent_category'])) {
                $categoryData['parent_category'] = $_POST['parent_category'];
                $categoryData['parent_category_id'] = intval($_POST['parent_category_id']);
            }

            $result = createCategory($categoryData);

            if (is_numeric($result)) {
                echo "<div class='success'>Kategória sikeresen létrehozva!</div></div>";
            }
            else {
                echo "<div class='error'>A kategória létrehozása sikertelen! $result</div></div>";
            }
        }

        // Kategória törlése
        if (isset($_POST['delete_category'])) {

            if ($_POST['category_type'] == 'null' || $_POST['category_id'] == 'null') {
                echo "<div class='error'>A kategória törlése sikertelen! Ez a kategória nem létezik!</div></div>"; 
            }
            else {
                $categoryData = array(
                    "name" => $_POST['category_name'],
                    "type" => $_POST['category_type'],
                    "id" => intval($_POST['category_id'])
                );
                $result = removeCategory($categoryData);

                if ($result === true) {
                    echo "<div class='success'>A kategória sikeresen törölve.</div></div>";
                }
                else {
                    echo "<div class='error'>A kategória törlése sikertelen! $result</div></div>";
                }
            }
        }

        // Kategória módosítása
        if (isset($_POST['modify_category'])) {

            $categoryData = array(
                "id" => intval($_POST['category_id']),
                "name" => $_POST['name'],
                "original_name" => $_POST['category_name'],
                "subname" => $_POST['subname'],
                "description" => $_POST['description']);

            if (isset($_POST['parent_category'])) {
                $categoryData['parent_category'] = $_POST['parent_category'];
                $categoryData['parent_category_id'] = intval($_POST['parent_category_id']);
            }

            $result = updateCategory($categoryData);

            if ($result === true) {
                echo "<div class='success'>A kategória sikeresen módosítva.</div></div>";
            }
            else {
                echo "<div class='error'>A kategória módosítása sikertelen! $result</div></div>";
            }
        }

        // Termék létrehozása
        if (isset($_POST['create_product'])) {
            $productData = array(
                "name" => $_POST['product_name'],
                "unit_price" => intval($_POST['price']),
                "stock" => intval($_POST['stock']),
                "description" => $_POST['description'],
                "tags" => $_POST['tags']
            );

            $productPageData = array(
                "product_id" => null, // Termékfeltöltés után lesz beállítva
                "link_slug" => null, // Létrehozáskor meghatározzuk a kategória és alkategória neveiből
                "category_id" => intval($_POST['category_id']),
                "subcategory_id" => intval($_POST['subcategory_id']),
                "page_title" => $_POST['product_name'],
                "page_content" => $_POST['content']
            );

            $productCategoryData = array(
                "category" => $_POST['category'],
                "subcategory" => $_POST['subcategory'],
            );

            $tags = $_POST['tags'];

            $result = createProduct($productData, $productPageData, $productCategoryData);

            if ($result === true) {
                echo "<div class='success'>Termék sikeresen létrehozva!</div></div>";
            }
            else {
                echo "<div class='error'>A termék létrehozása sikertelen! $result</div></div>";
            }
        }

        // Termék módosítása
        if (isset($_POST['modify_product'])) {
            $productData = array(
                "id" => $_POST['product_id'],
                "original_name" => $_POST['product_name'],
                "name" => $_POST['name'],
                "description" => $_POST['description'],
                "price" => $_POST['price'],
                "stock" => $_POST['stock'],
                "tags" => $_POST['tags']
            );

            if (updateProduct($productData) === true) {
                echo "<div class='success'>Termék sikeresen módosítva!</div></div>";
            }
            else {
                echo "<div class='error'>A termék módosítása sikertelen! $result</div></div>";
            }
        }

        // Termék oldal létrehozása
        if (isset($_POST['create_product_page'])) {
            
            $productData = array(
                "name" => $_POST["product_name"],
                "id" => intval($_POST["product_id"])
            );

            $productPageData = array(
                "product_id" => null, // Termékfeltöltés után lesz beállítva
                "link_slug" => null, // Létrehozáskor meghatározzuk a kategória és alkategória neveiből
                "category_id" => intval($_POST['category_id']),
                "subcategory_id" => intval($_POST['subcategory_id']),
                "page_title" => $_POST['product_name'],
                "page_content" => $_POST['content']
            );

            $productCategoryData = array(
                "category" => $_POST['category'],
                "subcategory" => $_POST['subcategory'],
            );

            $result = createProductPage($productData, $productPageData, $productCategoryData);
            if ($result === true) {
                echo "<div class='success'>Termék oldal sikeresen létrehozva!</div>";
            }
            else {
                echo "<div class='error'>A termék oldal létrehozása sikertelen! $result</div>";
            }
        }

        //Termék törlése
        if (isset($_POST['delete_product'])) {
            $productData = array(
                "id" => intval($_POST['product_id']),
                "name" => $_POST['product_name']
            );

            $result = removeProduct($productData);

            if ($result === true) {
                echo "<div class='success'>A termék sikeresen törölve.</div>";
            }
            else {
                echo "<div class='error'>A termék törlése sikertelen! $result</div>";
            }
        }
        
        // Jogosultság változtatása
        if (isset($_POST['modify_role'])) {
            $userId = intval($_POST['user_id']);
            $role = $_POST['role'];
            if (modifyRole($userId, $role) === true) {
                echo "<div class='success'>Sikeres művelet!</div>";
            }
            else {
                echo "<div class='error'>A művelet sikertelen!</div>";
            }
        }
    ?>
</body>
</html>