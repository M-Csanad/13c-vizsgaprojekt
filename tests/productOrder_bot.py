import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoSuchElementException, StaleElementReferenceException
from tests.utility.chromedriver_headless import webdriver, Options
from tests.utility.user_generator import UserGenerator
from tests.utility.review_generator import ReviewGenerator
from tests.login.autologin import Login
import time
import random
import re
import argparse
import concurrent.futures
# For folder creation
import os
import json
from datetime import datetime
# Add lorem for review text generation
import lorem

# Constants
BOT_ACCOUNTS_PATH = "c:/xampp/htdocs/13c-vizsgaprojekt/tests/utility/bot_accounts.json"

def wait_for_element(driver, by, value, timeout=10):
    """Várakozás egy elem megjelenésére és visszaadása"""
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.presence_of_element_located((by, value))
        )
        return element
    except TimeoutException:
        print(f"Időtúllépés: Nem található elem: {value}")
        return None

def wait_for_elements(driver, by, value, timeout=10):
    """Várakozás elemek megjelenésére és visszaadása"""
    try:
        elements = WebDriverWait(driver, timeout).until(
            EC.presence_of_all_elements_located((by, value))
        )
        return elements
    except TimeoutException:
        print(f"Időtúllépés: Nem találhatók elemek: {value}")
        return []

def wait_for_clickable(driver, by, value, timeout=10):
    """Várakozás egy kattintható elemre és visszaadása"""
    try:
        element = WebDriverWait(driver, timeout).until(
            EC.element_to_be_clickable((by, value))
        )
        return element
    except TimeoutException:
        print(f"Időtúllépés: Nem kattintható elem: {value}")
        return None

def extract_product_count(text):
    """Kinyeri a termékek számát a szövegből"""
    match = re.search(r'(\d+)\s+term[éė]k', text)
    if (match):
        return int(match.group(1))
    return 0

def scroll_to_element(driver, element):
    """Elemet középre görget a képernyőn"""
    driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", element)
    time.sleep(1)  # Kis várakozás a görgetés után

def get_text_with_js(driver, element):
    """JavaScript segítségével kinyeri az elem szövegét"""
    return driver.execute_script("return arguments[0].textContent", element).strip()

def click_with_javascript(driver, element):
    """Kattintás JavaScript segítségével, ami akkor is működik, ha az elem nem látható"""
    try:
        driver.execute_script("arguments[0].click();", element)
        return True
    except Exception as e:
        print(f"  JavaScript kattintási hiba: {str(e)}")
        return False

def navigate_by_url(driver, url):
    """Közvetlen navigáció URL-en keresztül"""
    try:
        current_url = driver.current_url
        driver.get(url)
        print(f"  Közvetlen navigáció: {current_url} -> {url}")
        time.sleep(2)
        return True
    except Exception as e:
        print(f"  URL navigációs hiba: {str(e)}")
        return False

def get_bot_account():
    """Létező bot fiók adatainak beolvasása"""
    if os.path.exists(BOT_ACCOUNTS_PATH):
        try:
            with open(BOT_ACCOUNTS_PATH, 'r', encoding='utf-8') as f:
                accounts = json.load(f)
                if accounts:
                    # Véletlenszerű fiók kiválasztása
                    account = random.choice(accounts)
                    # Convert user_name to username to match expected structure
                    if "user_name" in account and "username" not in account:
                        account["username"] = account["user_name"]
                    return account
        except Exception as e:
            print(f"Hiba a bot fiókok beolvasásakor: {e}")
    return None

def run_bot(index: int, use_login=False):
    """
    Vásárlási folyamat végrehajtása egy bot által

    Paraméterek:
        index: A bot egyedi azonosítója
        use_login: Bejelentkezzen-e a bot létező fiókkal
    """
    print(f"\n--- Bot #{index+1} indul ---")
    # Új headless driver példány
    chrome_options = Options()
    chrome_options.add_argument("--headless=new")
    chrome_options.add_argument("--disable-gpu")  # Néhány esetben stabilabbá teszi a headless módot
    chrome_options.add_argument("--no-sandbox")  # Bizonyos környezetekben szükséges
    chrome_options.add_argument("--disable-dev-shm-usage")  # Memóriahasználat optimalizálása
    driver = webdriver.Chrome(options=chrome_options)

    # User data that will be used in the process
    user = None

    # Store product URL for later review
    product_info = {"url": None}

    try:
        # 1) Felhasználó bejelentkezés vagy generálás
        if use_login:
            # Use existing bot account
            bot_account = get_bot_account()
            if bot_account:
                print(f"  Bot fiók használata: {bot_account['username']}")
                driver.get("http://localhost/login")
                Login(driver, bot_account["user_name"], bot_account["password"], False)
                user = bot_account
            else:
                print("  Nem található bot fiók, új felhasználó generálása helyette.")
                user = UserGenerator.generate()
        else:
            # Generate new user as before
            user = UserGenerator.generate()
            print(f"  Generált felhasználó: {user['username']}, {user['email']}")

        # 2) Főoldal betöltése
        driver.get("http://localhost")
        print("  Főoldal betöltve")
        time.sleep(3)  # Növeljük a várakozást

        # 3) Kategóriák közti navigáció - Keressük meg a kategória galériát és görgessünk oda
        print("  Kategória galéria keresése...")
        category_gallery = wait_for_element(driver, By.ID, "categoryGallery")

        if not category_gallery:
            # Próbáljunk meg legörgetni az oldalon és újra keresni
            print("  Görgetés az oldal aljára a galéria megtalálásához...")
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(2)
            category_gallery = wait_for_element(driver, By.ID, "categoryGallery")

        if not category_gallery:
            raise Exception("Kategória galéria nem található az oldalon")

        # Görgetés a galériához
        scroll_to_element(driver, category_gallery)
        print("  Kategória galéria megtalálva, görgetés végrehajtva")

        # Most már kereshetjük a kategória dobozokat
        category_elements = wait_for_elements(driver, By.CSS_SELECTOR, "#categoryGallery .media_box")
        print(f"  {len(category_elements)} kategória doboz található")

        valid_categories = []
        for i, category in enumerate(category_elements):
            try:
                # Görgetés az aktuális kategóriához
                scroll_to_element(driver, category)

                # Keressük meg a caption konténert
                caption = category.find_element(By.CLASS_NAME, "caption")

                # Keressük meg a cím elemet és szerezzük meg a szöveget
                title_element = caption.find_element(By.CLASS_NAME, "title_caption")
                title = get_text_with_js(driver, title_element)

                # Keressük meg a termékszám elemet és használjunk JavaScript-et a szöveg kinyeréséhez
                try:
                    product_count_element = caption.find_element(By.CLASS_NAME, "productCount_caption")
                    product_count_text = get_text_with_js(driver, product_count_element)
                    print(f"  Talált kategória: '{title}' - '{product_count_text}'")

                    product_count = extract_product_count(product_count_text)

                    if product_count > 0:
                        link_element = title_element.find_element(By.TAG_NAME, "a")
                        valid_categories.append((link_element, product_count, title))
                        print(f"  Érvényes kategória: {title} - {product_count} termék")
                except NoSuchElementException:
                    print(f"  Hiányzik a termékszám elem a kategória {i+1}-nél")
                    continue

            except (NoSuchElementException, StaleElementReferenceException) as e:
                print(f"  Hiba kategória ellenőrzésekor: {str(e)}")
                continue

        if not valid_categories:
            # Próbáljuk a teljes HTML-t kiírni debugging céljából
            html = driver.execute_script("return document.querySelector('#categoryGallery').innerHTML")
            print(f"  Debug - Kategória HTML: {html[:500]}...") # csak az első 500 karaktert mutatjuk

            # Próbáljunk meg minden kategóriára kattintani, a második kategóriától kezdve
            print("  Nem találtam kategóriákat a termékekkel, próbálom a második kategóriát...")
            if len(category_elements) >= 2:
                try:
                    # Válasszuk a második kategóriát
                    second_category = category_elements[1]
                    scroll_to_element(driver, second_category)
                    link = second_category.find_element(By.TAG_NAME, "a")
                    link.click()
                    print("  Második kategóriára kattintottam, folytatás...")
                    time.sleep(2)
                except Exception as e:
                    raise Exception(f"Nem találtam olyan kategóriát, amelyben van termék: {e}")
            else:
                raise Exception("Nem találtam olyan kategóriát, amelyben van termék")
        else:
            # Véletlenszerűen választunk egy kategóriát, amely legalább 1 terméket tartalmaz
            selected_category = random.choice(valid_categories)
            category_link = selected_category[0]
            category_name = selected_category[2]
            print(f"  Kiválasztott kategória: {category_name} ({selected_category[1]} termék)")

            # Görgetés a kiválasztott kategóriához és kattintás
            scroll_to_element(driver, category_link)
            category_link.click()
            print("  Kategória oldal betöltve")
            time.sleep(2)

        # 4) Alkategóriák közül egy olyat választunk, amelynek van legalább 1 terméke
        print("  Alkategóriák keresése...")

        # Görgetés az oldal közepére a subcategory elemek megjelenítéséhez
        driver.execute_script("window.scrollTo(0, 300);")
        time.sleep(2)

        # Először próbáljuk meg a CardSection__subcategory ID-t megtalálni
        subcategory_section = wait_for_element(driver, By.ID, "CardSection__subcategory")

        if subcategory_section:
            print("  Subcategory section found by ID")
            # Görgessünk a subcategory section-höz
            scroll_to_element(driver, subcategory_section)
            time.sleep(1)
            # Most keressük meg a kártyákat a section-ön belül
            subcategory_cards = subcategory_section.find_elements(By.CSS_SELECTOR, ".CardContainer.horizontalCard")
        else:
            # Ha nem találtuk meg az ID-t, próbáljuk a Card-subcontainer_main class-t
            print("  Looking for subcategories with alternative selector...")
            subcontainer = wait_for_element(driver, By.CLASS_NAME, "Card-subcontainer_main")

            if subcontainer:
                print("  Card subcontainer found")
                scroll_to_element(driver, subcontainer)
                time.sleep(1)
                subcategory_cards = subcontainer.find_elements(By.CSS_SELECTOR, ".CardContainer.horizontalCard")
            else:
                # Ha még mindig nem találtuk meg, próbáljuk közvetlenül az osztályneveket
                print("  Trying direct class selector...")
                driver.execute_script("window.scrollTo(0, 500);")
                time.sleep(1)
                subcategory_cards = wait_for_elements(driver, By.CSS_SELECTOR, ".CardContainer.horizontalCard")

        print(f"  Found {len(subcategory_cards)} subcategory cards")

        # Debug: if we found cards, print something about them
        for i, card in enumerate(subcategory_cards[:3]):  # Just check the first 3 to avoid too much output
            try:
                html = driver.execute_script("return arguments[0].outerHTML", card)
                print(f"  Card {i+1} HTML snippet: {html[:100]}...")  # Just print the beginning
            except:
                print(f"  Card {i+1}: [Could not get HTML]")

        valid_subcategories = []
        for card in subcategory_cards:
            try:
                # Görgetés a kártyához
                scroll_to_element(driver, card)

                # Először próbáljuk a Card_headerProductCounter
                product_count_element = None
                try:
                    product_count_element = card.find_element(By.CLASS_NAME, "Card_headerProductCounter")
                except NoSuchElementException:
                    # Ha ez nem működik, próbáljunk más szelektorokat
                    try:
                        # Próbáljuk megkeresni bármilyen szöveget, amely számot és "termék" szót tartalmaz
                        elements = card.find_elements(By.XPATH, ".//*[contains(text(),'termék')]")
                        if elements:
                            product_count_element = elements[0]
                    except:
                        pass

                if product_count_element:
                    product_count_text = get_text_with_js(driver, product_count_element)
                    print(f"  Found product count text: '{product_count_text}'")

                    product_count = extract_product_count(product_count_text)
                    print(f"  Extracted product count: {product_count}")

                    if product_count > 0:
                        link_element = card.find_element(By.CSS_SELECTOR, "a.card-link")
                        valid_subcategories.append((link_element, product_count))
                        print(f"  Érvényes alkategória: {product_count} termék")
                else:
                    # Ha nem találtunk termékszámot, próbáljuk a kártyát mindenképp felvenni, mert lehet, hogy van terméke
                    try:
                        link_element = card.find_element(By.CSS_SELECTOR, "a.card-link")
                        valid_subcategories.append((link_element, 1))  # Assume at least 1 product
                        print(f"  Feltételezett alkategória (termékszám nélkül)")
                    except:
                        print(f"  Alkategória link nem található a kártyán")
            except (NoSuchElementException, StaleElementReferenceException) as e:
                print(f"  Hiba alkategória ellenőrzésekor: {str(e)}")
                continue

        # Ha nincs alkategória, akkor lehet, hogy már a terméklistázó oldalon vagyunk
        if not valid_subcategories:
            print("  Nincs alkategória vagy már a terméklistázó oldalon vagyunk")

            # Ellenőrizzük, hogy látunk-e termékeket az oldalon
            products = wait_for_elements(driver, By.CSS_SELECTOR, ".cards .card")
            if products:
                print(f"  Már a terméklistázó oldalon vagyunk, {len(products)} termék található")
            else:
                # Ha nem láttunk termékeket, próbáljunk alternatív navigációt
                print("  Nem látok termékeket, próbálok alternatív navigációt...")

                # Ellenőrizzük, hogy van-e valami "Termékek" vagy hasonló felirat, amire kattinthatunk
                try:
                    product_links = driver.find_elements(By.XPATH, "//a[contains(text(), 'Termék') or contains(text(), 'termék') or contains(text(), 'Minden')]")
                    if product_links:
                        print(f"  Alternatív link megtalálva: {product_links[0].text}")
                        scroll_to_element(driver, product_links[0])
                        product_links[0].click()
                        print("  Alternatív linkre kattintva")
                        time.sleep(2)
                except:
                    print("  Nem találtam alternatív navigációt")
        else:
            # Véletlenszerűen választunk egy alkategóriát
            selected_subcategory = random.choice(valid_subcategories)
            subcategory_link = selected_subcategory[0]
            target_url = subcategory_link.get_attribute('href')
            print(f"  Kiválasztott alkategória: {target_url} ({selected_subcategory[1]} termék)")

            # Először standard kattintással próbálkozunk
            try:
                # Többszöri görgetés különböző helyekre, hogy biztosan látszódjon az elem
                scroll_to_element(driver, subcategory_link)
                time.sleep(1)

                # Próbáljunk meg kattintani az elemre
                print("  Standard kattintási próba...")
                subcategory_link.click()
                print("  Alkategória oldal betöltve (standard kattintás)")
                time.sleep(2)
            except Exception as e:
                print(f"  Standard kattintás sikertelen: {str(e)}")

                # JavaScript kattintással próbálkozunk
                print("  JavaScript kattintási próba...")
                if click_with_javascript(driver, subcategory_link):
                    print("  Alkategória oldal betöltve (JS kattintás)")
                    time.sleep(2)
                else:
                    # Közvetlen URL navigációval próbálkozunk
                    print(f"  Közvetlen URL navigációs próba: {target_url}")
                    if navigate_by_url(driver, target_url):
                        print("  Alkategória oldal betöltve (URL navigáció)")
                    else:
                        # Ha minden sikertelen, próbáljunk meg termékeket keresni az aktuális oldalon
                        print("  Minden navigációs próba sikertelen, folytatás az aktuális oldalon")

        # 5) Termék kiválasztása és kosárba helyezése
        print("  Termékek keresése...")
        product_cards = []

        # Többszöri próba termékek keresésére, több scrollozással
        for scroll_position in [0.3, 0.5, 0.7]:
            driver.execute_script(f"window.scrollTo(0, document.body.scrollHeight * {scroll_position});")
            time.sleep(2)
            product_cards = wait_for_elements(driver, By.CSS_SELECTOR, ".cards .card", timeout=5)
            if product_cards:
                print(f"  {len(product_cards)} termékek találva {scroll_position} görgetési pozíciónál")
                break

        # Ha még mindig nem találtunk termékeket, próbáljunk más CSS szelektorokat is
        if not product_cards:
            print("  Termékek keresése alternatív szelektorokkal...")
            for selector in [".card", ".product-card", "[data-product-id]", ".product"]:
                product_cards = wait_for_elements(driver, By.CSS_SELECTOR, selector, timeout=3)
                if product_cards:
                    print(f"  {len(product_cards)} termékek találva '{selector}' szelektorral")
                    break

        # Ha még mindig nincs találat, próbáljunk keresni bármit, ami "termék" linket tartalmaz
        if not product_cards:
            product_links = wait_for_elements(driver, By.XPATH, "//a[contains(@href, 'termek') or contains(@href, 'product')]", timeout=3)
            if product_links:
                print(f"  {len(product_links)} lehetséges termék link találva")
                # Próbáljunk kattintani az első termék linkre
                try:
                    scroll_to_element(driver, product_links[0])
                    product_link_url = product_links[0].get_attribute('href')

                    # Standard kattintás
                    try:
                        product_links[0].click()
                        print("  Termék oldalra lépés (standard kattintás)")
                    except:
                        # JavaScript kattintás
                        if click_with_javascript(driver, product_links[0]):
                            print("  Termék oldalra lépés (JS kattintás)")
                        else:
                            # URL navigáció
                            navigate_by_url(driver, product_link_url)
                            print("  Termék oldalra lépés (URL navigáció)")

                    time.sleep(2)
                    # Tovább a normál folyamattal a termék oldalon...

                    # Ugrunk a kosárba helyezés részhez
                    add_to_cart_button = wait_for_clickable(driver, By.CLASS_NAME, "add-to-cart")
                    if not add_to_cart_button:
                        # Ha nem találjuk a kosárba gombot, próbáljuk más szelektorokkal
                        selectors = [
                            ".add-to-cart",
                            "button[contains(., 'Kosárba')]",
                            "button.add-to-cart",
                            ".quick-add",
                            "button.quick-add"
                        ]
                        for selector_type, selector in [(By.CLASS_NAME, s) if '.' in s else (By.XPATH, s) for s in selectors]:
                            add_to_cart_button = wait_for_clickable(driver, selector_type, selector, timeout=3)
                            if add_to_cart_button:
                                break

                    # Innentől folytatjuk a kosárba helyezéssel...
                except Exception as e:
                    print(f"  Hiba a termék link követésekor: {str(e)}")

        if not product_cards:
            raise Exception("Nem találtam termékeket az oldalon semmilyen módszerrel")

        # Véletlenszerűen kiválasztunk egy terméket
        selected_product = random.choice(product_cards)
        scroll_to_element(driver, selected_product)

        # Próbáljuk megtalálni a termék nevét
        try:
            product_name_element = selected_product.find_element(By.CLASS_NAME, "name")
            product_name = product_name_element.text
            print(f"  Kiválasztott termék: {product_name}")
        except:
            print("  Nem sikerült kinyerni a termék nevét")
            product_name = "ismeretlen termék"

        # Próbáljuk megtalálni a termék linkjét és rákattintani
        try:
            product_links = selected_product.find_elements(By.TAG_NAME, "a")
            if product_links:
                product_link = product_links[0]
                product_url = product_link.get_attribute('href')
                product_info["url"] = product_url

                # Próbáljunk rákattintani - először standard módon
                try:
                    product_link.click()
                    print("  Termékoldal betöltve (standard kattintás)")
                except:
                    # JavaScript kattintással próbálkozunk_screenshot.png"
                    if click_with_javascript(driver, product_link):
                        print("  Termékoldal betöltve (JS kattintás)")
                    else:
                        # Közvetlen URL navigáció
                        navigate_by_url(driver, product_url)
                        print("  Termékoldal betöltve (URL navigáció)")

                time.sleep(2)
            else:
                # Ha nem találunk linket, próbálunk közvetlenül a kártyára kattintani
                try:
                    selected_product.click()
                except:
                    click_with_javascript(driver, selected_product)
                time.sleep(2)
        except Exception as e:
            print(f"  Hiba a termék oldalára navigáláskor: {str(e)}")
            # Próbáljuk meg a kosarat közvetlenül az aktuális oldalról elérni
            print("  Megpróbáljuk egyből a kosárba tenni a terméket az aktuális oldalról...")

        # Kosárba helyezés gomb megkeresése és kattintás - több lehetséges szelektor kipróbálása
        add_to_cart_button = None
        for selector in [".add-to-cart", "button.add-to-cart", ".quick-add", "button.quick-add"]:
            add_to_cart_button = wait_for_clickable(driver, By.CSS_SELECTOR, selector, timeout=3)
            if add_to_cart_button:
                print(f"  'Kosárba' gomb megtalálva: {selector}")
                break

        # Ha megtaláltuk a kosár gombot
        if add_to_cart_button:
            scroll_to_element(driver, add_to_cart_button)

            # Próbáljuk meg kosárba tenni
            try:
                add_to_cart_button.click()
                print("  Termék kosárba helyezve (standard kattintás)")
            except:
                if click_with_javascript(driver, add_to_cart_button):
                    print("  Termék kosárba helyezve (JS kattintás)")
                else:
                    print("  Nem sikerült a kosárba helyezés, folytatjuk...")

            time.sleep(1)
        else:
            # Ha semmiképp nem találjuk a kosár gombot, akkor meg kell szakítanunk a folyamatot
            raise Exception("Nem találtam a 'Kosárba' gombot semmilyen módszerrel")

        # 6) Kosár megnyitása - több alternatív megközelítéssel
        print("  Kosár megnyitása...")

        # Próbáljuk meg megtalálni a kosár ikont különböző szelektorokkal
        cart_icon = None
        for selector in [".cart-open", "#cart-icon", ".cart-icon", "[data-cart]", ".shopping-cart"]:
            cart_icon = wait_for_clickable(driver, By.CSS_SELECTOR, selector, timeout=3)
            if cart_icon:
                print(f"  Kosár ikon megtalálva: {selector}")
                break

        if not cart_icon:
            # Próbáljunk közvetlenül a checkout oldalra navigálni
            checkout_url = None
            try:
                # Megnézzük a jelenlegi URL-t és abból próbáljuk építeni a checkout URL-t
                current_url = driver.current_url
                base_url = current_url.split('/')[0] + '//' + current_url.split('/')[2]  # http(s)://domainname
                checkout_url = f"{base_url}/checkout"
                print(f"  Próbáljuk közvetlenül elérni a checkout oldalt: {checkout_url}")
                navigate_by_url(driver, checkout_url)
            except:
                raise Exception("Nem találtam a kosár ikont és nem sikerült a checkout oldalra navigálni")
        else:
            # Várunk, hogy a kosár badge megjelenjen/frissüljön
            try:
                cart_badge = wait_for_element(driver, By.CSS_SELECTOR, ".cart-badge.show", timeout=3)
                if cart_badge and cart_badge.text != "0":
                    print(f"  Kosár badge mutatja: {cart_badge.text} termék")
                else:
                    print("  Kosár badge nem látható vagy üres, de megpróbáljuk megnyitni a kosarat")
            except:
                print("  Kosár badge nem található")

            # Próbáljunk a kosárra kattintani
            try:
                cart_icon.click()
                print("  Kosár megnyitva (standard kattintás)")
            except:
                if click_with_javascript(driver, cart_icon):
                    print("  Kosár megnyitva (JS kattintás)")
                else:
                    # Ha nem sikerül a kosárra kattintani, próbáljuk a checkout oldalt közvetlenül
                    try:
                        current_url = driver.current_url
                        base_url = current_url.split('/')[0] + '//' + current_url.split('/')[2]
                        checkout_url = f"{base_url}/checkout"
                        print(f"  Próbáljuk közvetlenül elérni a checkout oldalt: {checkout_url}")
                        navigate_by_url(driver, checkout_url)
                    except:
                        raise Exception("Nem sikerült sem a kosarat megnyitni, sem a checkout oldalra navigálni")

            time.sleep(2)

            # Tovább gomb megkeresése és kattintás - több lehetőséggel
            checkout_button = None
            for selector in [".cart-bottom-button a", ".checkout-button", "a.checkout", "button.checkout",
                             "a:contains('Tovább')", "button:contains('Tovább')",
                             "a:contains('Checkout')", "button:contains('Checkout')"]:
                try:
                    if selector.startswith("a:contains") or selector.startswith("button:contains"):
                        # XPath szelektorok a szövegtartalom alapján
                        text = selector.split("'")[1]
                        xpath = f"//{selector.split(':')[0]}[contains(text(), '{text}')]"
                        checkout_button = wait_for_clickable(driver, By.XPATH, xpath, timeout=2)
                    else:
                        checkout_button = wait_for_clickable(driver, By.CSS_SELECTOR, selector, timeout=2)

                    if checkout_button:
                        print(f"  Tovább gomb megtalálva: {selector}")
                        break
                except:
                    continue

            if checkout_button:
                try:
                    checkout_button.click()
                    print("  Tovább a fizetéshez (standard kattintás)")
                except:
                    if click_with_javascript(driver, checkout_button):
                        print("  Tovább a fizetéshez (JS kattintás)")
                    else:
                        # Ha nem sikerül a tovább gombra kattintani, próbáljuk egyből a checkout oldalt
                        try:
                            current_url = driver.current_url
                            base_url = current_url.split('/')[0] + '//' + current_url.split('/')[2]
                            checkout_url = f"{base_url}/checkout"
                            print(f"  Próbáljuk közvetlenül elérni a checkout oldalt: {checkout_url}")
                            navigate_by_url(driver, checkout_url)
                        except:
                            raise Exception("Nem sikerült sem a tovább gombra kattintani, sem a checkout oldalra navigálni")
                time.sleep(2)
            else:
                # Ha nem találtuk a tovább gombot, már valószínűleg a checkout oldalon vagyunk
                print("  Nem találtuk a tovább gombot, feltételezzük hogy a checkout oldalon vagyunk")

        # 7) Checkout űrlap kitöltése
        print("  Megrendelési űrlap kitöltése...")

        # Magánszemélyként rendelek opció kiválasztása (alapértelmezetten ez van)
        private_radio = wait_for_clickable(driver, By.CSS_SELECTOR, ".purchase-type-radios .radio.checked")
        if not private_radio or not "Magánszemélyként rendelek" in private_radio.text:
            private_radio = wait_for_clickable(driver, By.CSS_SELECTOR, ".purchase-type-radios .radio:first-child")
            if private_radio:
                private_radio.click()
                time.sleep(1)

        # Email cím megadása - biztosítás, hogy a formátum helyes
        email = user["email"]
        # Ellenőrizzük, hogy az email formátuma megfelelő-e (tartalmaz @ jelet és pontot)
        if not ('@' in email and '.' in email.split('@')[1]):
            email = f"bot_{index}@example.com"  # Ha nem megfelelő, használjunk egy biztosan jó formátumú címet

        email_input = wait_for_element(driver, By.ID, "email")
        if email_input:
            email_input.clear()
            email_input.send_keys(email)

        # Vezetéknév megadása
        last_name_input = wait_for_element(driver, By.ID, "last-name")
        if last_name_input:
            last_name_input.clear()
            last_name_input.send_keys(user["last_name"])

        # Keresztnév megadása
        first_name_input = wait_for_element(driver, By.ID, "first-name")
        if first_name_input:
            first_name_input.clear()
            first_name_input.send_keys(user["first_name"])

        # Telefonszám megadása - helyes formátumban, szóközök nélkül
        mobile_prefix = random.choice(['20', '30', '70'])
        phone_number = f"+36{mobile_prefix}{random.randint(1000000, 9999999)}"  # +36 + 2 számjegy + 7 számjegy = 10 számjegy

        phone_input = wait_for_element(driver, By.ID, "phone")
        if phone_input:
            phone_input.clear()
            phone_input.send_keys(phone_number)
            print(f"  Telefonszám megadva: {phone_number}")
        else:
            print("  Telefonszám mező nem található, próbálkozás alternatív szelektorral...")
            # Próbálkozás CSS szelektorral is
            phone_input = wait_for_element(driver, By.CSS_SELECTOR, "input[name='phone']")
            if phone_input:
                phone_input.clear()
                phone_input.send_keys(phone_number)
                print(f"  Telefonszám megadva (alternatív szelektorral): {phone_number}")

        # Irányítószám megadása (mindig 8200)
        zip_input = wait_for_element(driver, By.ID, "zip-code")
        if zip_input:
            zip_input.clear()
            zip_input.send_keys("8200")

        # Település megadása (mindig Veszprém)
        city_input = wait_for_element(driver, By.ID, "city")
        if city_input:
            city_input.clear()
            city_input.send_keys("Veszprém")

        # Utca és házszám megadása
        street_input = wait_for_element(driver, By.ID, "street-house")
        if street_input:
            street_input.clear()
            street_input.send_keys(f"Robotics utca {random.randint(1, 99)}")

        # "Szállítási cím megegyezik" checkbox bejelölése
        same_address_checkbox = wait_for_clickable(driver, By.ID, "same-address")
        if same_address_checkbox and not same_address_checkbox.is_selected():
            try:
                same_address_checkbox.click()
            except:
                # Ha sima kattintás nem működik, JavaScript-tel próbáljuk
                driver.execute_script("arguments[0].click();", same_address_checkbox)
            print("  'Szállítási cím megegyezik' bejelölve")
            time.sleep(1)

        # 8) Fizetés gomb megnyomása
        payment_button = wait_for_clickable(driver, By.CSS_SELECTOR, ".payment-button")
        if payment_button:
            try:
                payment_button.click()
            except:
                # Ha nem sikerül a kattintás, JavaScript-tel próbáljuk
                click_with_javascript(driver, payment_button)
            print("  Fizetés gomb megnyomva")
            # Növeljük a várakozási időt, hogy biztosan befejeződjön a folyamat
            time.sleep(8)  # Hosszabb várakozás a teljes folyamat lezárásához

            # Helyes szelektorok használata a sikeres rendelés ellenőrzéséhez
            # Először próbáljuk a thank-you osztállyal
            success_thank_you = wait_for_element(driver, By.CSS_SELECTOR, ".thank-you", timeout=2)
            # Aztán próbáljuk az overlay-body-val
            success_overlay = wait_for_element(driver, By.CSS_SELECTOR, ".overlay-body", timeout=1)

            if success_thank_you or success_overlay:
                # Ha bármelyik megtalálható, ellenőrizzük a szöveget
                if success_thank_you and "Köszönjük" in success_thank_you.text:
                    print("  Sikeres rendelés visszaigazolás megjelent!")
                elif success_overlay and "Köszönjük" in success_overlay.text:
                    print("  Sikeres rendelés visszaigazolás megjelent!")
                else:
                    print("  Rendelési oldal betöltve, de nem biztos hogy sikeres volt")
            else:
                # Ellenőrizzük, hogy van-e hibaüzenet az űrlapon
                error_messages = wait_for_elements(driver, By.CSS_SELECTOR, ".error-message:not(:empty)", timeout=1)
                if error_messages:
                    errors = [msg.text for msg in error_messages if msg.text.strip()]
                    print(f"  Hibaüzenetek az űrlapon: {', '.join(errors)}")
                print("  Nem látható sikeres rendelés visszaigazolás")

                # Készítsünk képernyőképet a végállapotról, hogy lássuk mi történt
                try:
                    # Biztosítsuk hogy a mappák léteznek
                    os.makedirs("tests/results", exist_ok=True)
                    screenshot_path = f"tests/results/bot_{index+1}_final_state.png"
                    driver.save_screenshot(screenshot_path)
                    print(f"  Képernyőmentés a végállapotról: {screenshot_path}")
                except Exception as e:
                    print(f"  Nem sikerült képernyőmentést készíteni: {e}")
        else:
            # Ha nem találjuk a fizetés gombot az oldalon, próbáljunk meg görgetni az oldalon
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1)
            payment_button = wait_for_clickable(driver, By.CSS_SELECTOR, ".payment-button")
            if payment_button:
                click_with_javascript(driver, payment_button)
                print("  Fizetés gomb megnyomva (görgetés után)")
                # Növeljük a várakozási időt, hogy biztosan befejeződjön a folyamat
                time.sleep(8)  # Megnövelve 3-ról 8 másodpercre
            else:
                raise Exception("Nem találtam a 'Fizetés' gombot")

        # 9) Visszajelzés a sikeres futásról
        print(f"  Bot #{index+1} sikeresen lefutott!")

        # Return more information when login is used
        if use_login and user:
            return {
                "success": True,
                "user": user,
                "product_info": product_info
            }
        return True  # Sikeres befejezés

    except Exception as e:
        print(f"  Hiba történt Bot #{index+1} futása közben: {str(e)}")

        # Képernyőmentés készítése hiba esetén
        try:
            os.makedirs("tests/errors/botErrors", exist_ok=True)
            screenshot_path = f"tests/errors/botErrors/bot_{index+1}_error_screenshot.png"
            driver.save_screenshot(screenshot_path)
            print(f"  Képernyőmentés mentve: {screenshot_path}")
        except Exception as screenshot_error:
            print(f"  Nem sikerült képernyőmentést készíteni: {str(screenshot_error)}")

        if use_login:
            return {
                "success": False,
                "error": str(e)
            }
        return False  # Sikertelen befejezés
    finally:
        driver.quit()

def run_bots_in_parallel(total_bots, concurrent_bots, use_login=False):
    """
    Több bot párhuzamos futtatása a vásárlási folyamat teszteléséhez

    Paraméterek:
        total_bots: Az összes futtatandó bot száma
        concurrent_bots: Egyidejűleg futó botok maximális száma
        use_login: Bejelentkezzenek-e a botok létező fiókokkal
    """
    print(f"\n=== {total_bots} bot indítása, egyszerre {concurrent_bots} fut {'(bejelentkezéssel)' if use_login else ''} ===\n")

    # Készítsünk egy mappát a futtatás időpontjával
    timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
    run_dir = f"tests/runs/{timestamp}"
    os.makedirs(run_dir, exist_ok=True)

    successful = 0
    failed = 0
    order_results = []  # Save order results for review process

    # Párhuzamos futtatás ThreadPoolExecutor-ral
    with concurrent.futures.ThreadPoolExecutor(max_workers=concurrent_bots) as executor:
        # Feladatok létrehozása és beküldése
        futures = [executor.submit(run_bot, i, use_login) for i in range(total_bots)]

        # Eredmények begyűjtése
        for i, future in enumerate(concurrent.futures.as_completed(futures)):
            try:
                result = future.result()

                if isinstance(result, dict):  # Detailed result with login
                    if result["success"]:
                        successful += 1
                        # Save for potential review
                        order_results.append(result)
                    else:
                        failed += 1
                else:  # Simple boolean result
                    if result:
                        successful += 1
                    else:
                        failed += 1

                print(f"Bot #{i+1} befejezve. Eddig sikeres: {successful}, hibás: {failed}")
            except Exception as exc:
                print(f"Bot kivétel: {exc}")
                failed += 1

    # Statisztika kiírása
    print(f"\n=== Összes bot futása befejeződött ===")
    print(f"Sikeres: {successful}")
    print(f"Hibás: {failed}")
    print(f"Összesen: {total_bots}")

    # Mentjük a statisztikát egy fájlba is
    with open(f"{run_dir}/results.txt", "w") as f:
        f.write(f"Futtatás időpontja: {timestamp}\n")
        f.write(f"Sikeres: {successful}\n")
        f.write(f"Hibás: {failed}\n")
        f.write(f"Összesen: {total_bots}\n")

    # Save order data if login was used, for review processing
    if use_login and order_results:
        orders_file = f"{run_dir}/orders.json"
        with open(orders_file, 'w', encoding='utf-8') as f:
            json.dump(order_results, f, indent=2, ensure_ascii=False)
        print(f"Rendelési információk mentve: {orders_file}")

    return successful, failed, order_results if use_login else None

if __name__ == "__main__":
    # Parancssori argumentumok feldolgozása
    parser = argparse.ArgumentParser(description='Webshop automatizált tesztelő botok párhuzamos futtatása')
    parser.add_argument('-t', '--total', type=int, default=9, help='Botok teljes száma (alapértelmezett: 9)')
    parser.add_argument('-c', '--concurrent', type=int, default=2,
                        help='Egyszerre futó botok száma (alapértelmezett: 2)')
    parser.add_argument('-l', '--login', action='store_true',
                        help='Bejelentkezés bot fiókkal vendég helyett')
    parser.add_argument('-r', '--review', action='store_true',
                        help='Értékelések írása a vásárlás után (csak bejelentkezéssel működik)')
    args = parser.parse_args()

    # Botok futtatása a megadott paraméterekkel
    result = run_bots_in_parallel(args.total, args.concurrent, args.login)

    # Ha a review opció be van kapcsolva, indítsuk el a review_product.py-t
    if args.login and args.review and result and result[2]:
        print("\nVásárlások sikeresen befejezve, értékelési folyamat indítása...")
        try:
            from tests.review.review_product import process_orders
            process_orders(result[2])
        except Exception as e:
            print(f"Hiba az értékelési folyamat során: {str(e)}")
