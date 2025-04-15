<h1 align="center">
  <br>
  <img src="./domains/domainname/public_html/fb-content/assets/media/images/logos/herbalLogoWhite.png" alt="Florens Botanica" width="600">

</h1>

<h4 align="center">Egy gyógynövényekkel foglalkozó online áruház, ami segít az embereknek megismerni a természet gyógyító erejét.</h4>

<p align="center">
  <a href="#felhasznált-technológiák">Felhasznált technológiák</a> •
  <a href="#letöltés">Letöltés</a> •
  <a href="#források">Források</a> •
  <a href="#licenc">Licenc</a>
</p>

## Általános tudnivalók

&emsp;&emsp;A <b>Florens Botanica</b> a 13. évfolyamban sorra kerülő szakmai vizsga (<i>szoftverfejlesztő, -tesztelő</i> szakmán belül) keretein belül készült vizsgaremek, amely több, mint fél év fejlesztés gyümölcseként mutatkozik meg.
<br><br>&emsp;&emsp;Úgy gondoljuk, hogy vizsgamunkánk minőségét a jól átgondolt, alapos tervezési fázisnak, a gondos fejlesztésnek és Tanáraink lelkes segítségének köszönhetjük.

## Felhasznált technológiák

- HTML
  - A weboldalak kialakítására
- JavaScript
  - A frontend interakcióinak megvalósítására
- CSS / SCSS
  - A frontend formázására
- PHP
  - A backend megvalósítására (adatbázis-kezelés, session kezelés)
- SQL
  - Az adatbázis megvalósítására
- XAMPP
  - A lokális szerver futtatására
- Python
  - Az alkalmazás tesztelésére és a dinamikus tartalmak kezelésére

## Letöltés

Az alkalmazásunk legfrissebb verzióját [erről a linkről](https://github.com/M-Csanad/13c-vizsgaprojekt/releases) tudod letölteni.

<h4>Képek</h4>

- [Mountain Rose Herbs](https://mountainroseherbs.com/)
- [Anima Mundi Herbals](https://animamundiherbals.com/)
- [ManuTea](https://www.manutea.hu/)

## Gource

Ha vizuálisan is szeretnéd nyomon követni, hogyan és mennyit fejlesztettünk a projekten, javasoljuk a **[Gource](https://gource.io/)** alkalmazás használatát. Ez egy lenyűgöző vizualizációt nyújt a Git repository történetéről, ahol az avatarok, fájlmozgások és commitok életre kelnek egy animált idővonalon. 🤩

### 🛠 Előkészületek
1. Győződj meg róla, hogy a `.git` mappa elérhető a projekt gyökerében.
2. Futtasd le a `avatar_downloader.py` fájlt ugyanitt – ez letölti a fejlesztők GitHub-profilképeit, így az avatarok is megjelennek: `python avatar_downloader.py`

### 🔎 1 perces videó megtekintése mentés nélkül:
- `gource -1280x720 --seconds-per-day 0.326 --auto-skip-seconds 1 --user-image-dir .git/avatar_cache`
### 🎥 1 perces videó hossz létrehozás:
- `gource -1280x720 --seconds-per-day 0.326 --auto-skip-seconds 1 --user-image-dir .git/avatar_cache --output-ppm-stream gource.ppm`
- `ffmpeg -y -r 60 -f image2pipe -vcodec ppm -i gource.ppm -t 60 -vcodec libx264 -preset veryfast -pix_fmt yuv420p GIT_DevelopmentTime.mp4`

### 💡 Tipp
A --seconds-per-day paraméter úgy lett beállítva, hogy az egész projekt fejlesztési idővonala 1 perc alatt fusson le. Az --auto-skip-seconds opcióval a commit nélküli időszakok automatikusan átugrásra kerülnek.

## Licenc

A projekt egy iskolai projekt céljából készült, a forráskód bármilyen formában történő publikálása, szerkesztése, másolása a készítők engedélye nélkül tilos.

---

> Blank Máté Norman &nbsp;&middot;&nbsp;
> GitHub [@mxte-b](https://github.com/mxte-b) &nbsp;&middot;&nbsp;
> E-mail [13c-blank@ipari.vein.hu](mailto:13c-blank@ipari.vein.hu)

> Milkovics Csanád &nbsp;&middot;&nbsp;
> GitHub [@M-Csanad](https://github.com/M-Csanad) &nbsp;&middot;&nbsp;
> E-mail [13c-milkovics@ipari.vein.hu](mailto:13c-milkovics@ipari.vein.hu)
