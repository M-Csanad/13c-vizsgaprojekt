import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoSuchElementException, StaleElementReferenceException
from selenium.webdriver.support.ui import Select
import time
import random
import json
import os
from datetime import datetime
import re
import lorem
import argparse

from tests.utility.chromedriver import webdriver
from tests.login.autologin import Login
from tests.utility.user_generator import UserGenerator
from tests.productOrder_bot import wait_for_element, wait_for_elements, wait_for_clickable
from tests.productOrder_bot import scroll_to_element, click_with_javascript, navigate_by_url

BOT_ACCOUNTS_PATH = "c:/xampp/htdocs/13c-vizsgaprojekt/tests/utility/bot_accounts.json"
ADMIN_USERNAME = "Kecske"
ADMIN_PASSWORD = "Alma_123"

def create_headless_driver():
    """Chrome böngésző létrehozása fejnélküli módban optimalizált beállításokkal"""
    options = webdriver.ChromeOptions()
    options.add_argument("--headless=new")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    return webdriver.Chrome(options=options)

def get_bot_account():
    """Bot felhasználói fiók kiválasztása a tárolt fiókok közül"""
    if os.path.exists(BOT_ACCOUNTS_PATH):
        try:
            with open(BOT_ACCOUNTS_PATH, 'r', encoding='utf-8') as f:
                accounts = json.load(f)
                if accounts:
                    account = random.choice(accounts)
                    if "user_name" in account and "username" not in account:
                        account["username"] = account["user_name"]
                    return account
        except Exception as e:
            print(f"Hiba a bot fiókok beolvasásakor: {e}")
    return None

def handle_account(driver):
    """Bejelentkezés meglévő bot fiókkal vagy új fiók regisztrálása ha nincs elérhető bot"""
    bot_account = get_bot_account()

    if bot_account:
        print("\n1. Létező bot fiókkal való bejelentkezés...")
        try:
            driver.get("http://localhost/login")
            Login(driver, bot_account["user_name"], bot_account["password"], False)
            return bot_account
        except Exception as e:
            print(f"Bejelentkezés sikertelen: {e}")
            print("Regisztrációra váltás...")

    print("\n1. Új fiók regisztrálása...")
    user = UserGenerator.generate()

    driver.get("http://localhost/register")
    print("✓ Regisztrációs oldal megnyitva")

    driver.find_element(By.ID, "email").send_keys(user["email"])
    driver.find_element(By.ID, "username").send_keys(user["username"])
    driver.find_element(By.ID, "lastname").send_keys(user["last_name"])
    driver.find_element(By.ID, "firstname").send_keys(user["first_name"])
    driver.find_element(By.ID, "password").send_keys(user["password"])
    driver.find_element(By.ID, "passwordConfirm").send_keys(user["password"])

    agree_checkbox = wait_for_clickable(driver, By.ID, "agree")
    agree_checkbox.click()

    print("✓ Űrlap kitöltve")

    register_button = wait_for_clickable(driver, By.CSS_SELECTOR, "input.action-button")
    register_button.click()

    WebDriverWait(driver, 10).until(lambda d: d.current_url == "http://localhost/login")

    Login(driver, user["username"], user["password"], False)

    return user

def order_product(driver):
    """Termék kiválasztása, kosárba helyezése és megrendelése a webshopból"""
    print("\n2. Termék kiválasztása és megrendelése...")

    product_info = None

    driver.get("http://localhost")
    print("  Főoldal betöltve")
    time.sleep(2)

    try:
        category_gallery = wait_for_element(driver, By.ID, "categoryGallery")
        if category_gallery:
            scroll_to_element(driver, category_gallery)

            category_elements = wait_for_elements(driver, By.CSS_SELECTOR, "#categoryGallery .media_box")
            if category_elements:
                selected_category = random.choice(category_elements)
                scroll_to_element(driver, selected_category)

                link = selected_category.find_element(By.TAG_NAME, "a")
                link.click()
                time.sleep(2)
                print("  Kategória kiválasztva")

                product_links = wait_for_elements(driver, By.XPATH,
                    "//a[contains(@href, 'termek') or contains(@href, 'product')]", timeout=5)

                if product_links and len(product_links) > 0:
                    print(f"  {len(product_links)} termék link található")

                    selected_link = random.choice(product_links)
                    product_url = selected_link.get_attribute('href')
                    product_info = {"url": product_url}

                    try:
                        scroll_to_element(driver, selected_link)
                        selected_link.click()
                    except:
                        if not click_with_javascript(driver, selected_link):
                            navigate_by_url(driver, product_url)

                    print("  Termék kiválasztva (link alapján)")
                    time.sleep(2)
                else:
                    print("  Termék linkek nem találhatók, alternatív megoldások...")

                    nav_links = driver.find_elements(By.XPATH,
                        "//a[contains(text(), 'Termék') or contains(text(), 'termék') or contains(text(), 'Minden')]")

                    if nav_links and len(nav_links) > 0:
                        print(f"  Alternatív navigációs link találva: {nav_links[0].text}")
                        try:
                            scroll_to_element(driver, nav_links[0])
                            nav_links[0].click()
                            print("  Navigáció a termékekhez")
                            time.sleep(2)
                        except:
                            if not click_with_javascript(driver, nav_links[0]):
                                navigate_by_url(driver, nav_links[0].get_attribute('href'))

                    for selector in [".cards .card", ".card", ".product-card", "[data-product-id]", ".product"]:
                        product_cards = wait_for_elements(driver, By.CSS_SELECTOR, selector, timeout=3)
                        if product_cards and len(product_cards) > 0:
                            print(f"  {len(product_cards)} termék kártya található '{selector}' szelektorral")

                            selected_card = random.choice(product_cards)
                            card_links = selected_card.find_elements(By.TAG_NAME, "a")

                            if card_links and len(card_links) > 0:
                                product_url = card_links[0].get_attribute('href')
                                product_info = {"url": product_url}

                                try:
                                    scroll_to_element(driver, card_links[0])
                                    card_links[0].click()
                                except:
                                    if not click_with_javascript(driver, card_links[0]):
                                        navigate_by_url(driver, product_url)

                                print("  Termék kiválasztva (kártya alapján)")
                                time.sleep(2)
                                break

                if not product_info:
                    current_url = driver.current_url
                    if current_url.endswith('/'):
                        possible_product_url = current_url + "termek/1"
                    else:
                        possible_product_url = current_url + "/termek/1"

                    print(f"  Kísérlet közvetlen termékoldal betöltésére: {possible_product_url}")
                    navigate_by_url(driver, possible_product_url)
                    product_info = {"url": possible_product_url}
                    time.sleep(2)

                add_to_cart_button = None
                for selector in [".add-to-cart", "button.add-to-cart", ".quick-add", "button.quick-add"]:
                    add_to_cart_button = wait_for_clickable(driver, By.CSS_SELECTOR, selector, timeout=5)
                    if add_to_cart_button:
                        print(f"  'Kosárba' gomb megtalálva: {selector}")
                        break

                if add_to_cart_button:
                    scroll_to_element(driver, add_to_cart_button)
                    try:
                        add_to_cart_button.click()
                    except:
                        click_with_javascript(driver, add_to_cart_button)

                    time.sleep(1)
                    print("  Termék kosárba helyezve")

                    cart_icon = wait_for_clickable(driver, By.CSS_SELECTOR, ".cart-open, #cart-icon, .cart-icon")
                    if cart_icon:
                        try:
                            cart_icon.click()
                        except:
                            click_with_javascript(driver, cart_icon)

                        time.sleep(1)

                        checkout_button = wait_for_clickable(driver, By.CSS_SELECTOR, ".cart-bottom-button a, .checkout-button, a.checkout, button.checkout")
                        if checkout_button:
                            try:
                                checkout_button.click()
                            except:
                                if not click_with_javascript(driver, checkout_button):
                                    current_url = driver.current_url
                                    base_url = current_url.split('/')[0] + '//' + current_url.split('/')[2]
                                    checkout_url = f"{base_url}/checkout"
                                    navigate_by_url(driver, checkout_url)

                            time.sleep(2)

                            print("  Megrendelési űrlap kitöltése...")

                            email_input = wait_for_element(driver, By.ID, "email")
                            if email_input:
                                email_input.clear()
                                email_input.send_keys("teszt.rendeles@example.com")

                            last_name_input = wait_for_element(driver, By.ID, "last-name")
                            if last_name_input:
                                last_name_input.clear()
                                last_name_input.send_keys("Teszt")

                            first_name_input = wait_for_element(driver, By.ID, "first-name")
                            if first_name_input:
                                first_name_input.clear()
                                first_name_input.send_keys("Elek")

                            phone_input = wait_for_element(driver, By.ID, "phone")
                            if phone_input:
                                phone_input.clear()
                                phone_input.send_keys("+36201234567")

                            zip_input = wait_for_element(driver, By.ID, "zip-code")
                            if zip_input:
                                zip_input.clear()
                                zip_input.send_keys("8200")

                            city_input = wait_for_element(driver, By.ID, "city")
                            if city_input:
                                city_input.clear()
                                city_input.send_keys("Veszprém")

                            street_input = wait_for_element(driver, By.ID, "street-house")
                            if street_input:
                                street_input.clear()
                                street_input.send_keys("Teszt utca 123")

                            same_address_checkbox = wait_for_clickable(driver, By.ID, "same-address")
                            if same_address_checkbox and not same_address_checkbox.is_selected():
                                try:
                                    same_address_checkbox.click()
                                except:
                                    driver.execute_script("arguments[0].click();", same_address_checkbox)

                            payment_button = wait_for_clickable(driver, By.CSS_SELECTOR, ".payment-button")
                            if payment_button:
                                try:
                                    payment_button.click()
                                except:
                                    click_with_javascript(driver, payment_button)

                                time.sleep(5)
                                print("  Rendelés leadva")
                                return product_info

    except Exception as e:
        print(f"Hiba a termék rendelése közben: {e}")

    if not product_info:
        raise Exception("Nem sikerült terméket rendelni")

    return product_info

def find_latest_order(driver):
    """Felhasználói felületen a legfrissebb (legnagyobb azonosítójú) rendelés megkeresése"""
    print("\n3. Legfrissebb rendelés keresése...")

    driver.get("http://localhost/settings")
    print("  Beállítások oldal betöltve")
    time.sleep(3)

    orders_tab = wait_for_clickable(driver, By.CSS_SELECTOR, "div.page[data-pageid='2']")
    if orders_tab:
        orders_tab.click()
        time.sleep(2)
        print("  Rendeléseim fül kiválasztva")

        order_elements = None

        order_elements = wait_for_elements(driver, By.CSS_SELECTOR, ".order-card", timeout=3)

        if not order_elements:
            print("  .order-card nem található, próbálkozás más szelektorral...")
            order_elements = wait_for_elements(driver, By.CSS_SELECTOR, ".order-id", timeout=3)

        if not order_elements:
            print("  .order-id nem található, próbálkozás XPath-el...")
            order_elements = wait_for_elements(driver, By.XPATH, "//span[contains(@class, 'id-number')]", timeout=3)

        if not order_elements:
            print("  Keresés bármilyen elem után, amely tartalmaz rendelési számot (#szám)...")
            order_elements = wait_for_elements(driver, By.XPATH, "//*[contains(text(), '#')]", timeout=3)

        if order_elements:
            print(f"  {len(order_elements)} rendelés található")

            highest_order_id = -1
            highest_order_element = None

            for element in order_elements:
                order_id = None

                try:
                    id_number_element = element.find_element(By.CSS_SELECTOR, ".id-number")
                    order_id_text = id_number_element.text.strip().replace("#", "")
                    order_id = int(order_id_text)
                except:
                    try:
                        order_text = element.text
                        match = re.search(r'#(\d+)', order_text)
                        if match:
                            order_id = int(match.group(1))
                    except:
                        pass

                if order_id is not None and order_id > highest_order_id:
                    highest_order_id = order_id
                    highest_order_element = element
                    print(f"  Új legmagasabb rendelés azonosító: #{highest_order_id}")

            if highest_order_id > 0:
                print(f"  Legfrissebb rendelés azonosítója: #{highest_order_id}")
                return str(highest_order_id)
            else:
                print("  Nem sikerült azonosítani a legmagasabb rendelés azonosítót")

        print("  Debug: Screenshot készítése a rendelések oldaláról...")
        try:
            screenshot_path = "tests/errors/orders_screen.png"
            os.makedirs(os.path.dirname(screenshot_path), exist_ok=True)
            driver.save_screenshot(screenshot_path)
            print(f"  Screenshot mentve: {screenshot_path}")

            print("  DEBUG: orders page HTML structure:")
            page_section = driver.find_element(By.CSS_SELECTOR, "div.page[data-pageid='2']")
            if page_section:
                html_snippet = page_section.get_attribute('innerHTML')[:500]
                print(f"  HTML: {html_snippet}...")
        except Exception as e:
            print(f"  Screenshot error: {e}")

    raise Exception("Nem sikerült megtalálni a legfrissebb rendelést")

def admin_process_order(order_id):
    """Admin felületen a megadott azonosítójú rendelés állapotának beállítása 'Teljesítve' státuszra"""
    print(f"\n4. Admin felületen rendelés feldolgozása (#{order_id})...")

    admin_driver = create_headless_driver()

    try:
        admin_driver.get("http://localhost/login")
        Login(admin_driver, ADMIN_USERNAME, ADMIN_PASSWORD, False)

        admin_driver.get("http://localhost/dashboard")
        time.sleep(3)

        try:
            admin_driver.save_screenshot("tests/debug/dashboard_initial.png")

            dashboard_menu = None
            for attempt in range(3):
                print(f"  Dashboard menü keresése ({attempt+1}/3)")
                try:
                    dashboard_menu = admin_driver.find_element(By.CSS_SELECTOR, "div.page[data-pageid='4']")
                    admin_driver.execute_script("arguments[0].scrollIntoView(true);", dashboard_menu)
                    time.sleep(1)
                    admin_driver.execute_script("window.scrollBy(0, -100);")
                    time.sleep(1)

                    admin_driver.execute_script("arguments[0].click();", dashboard_menu)
                    print("  Dashboard menüpontra kattintás sikeres (JS)")
                    break
                except Exception as e:
                    print(f"  Probléma a dashboard menü kezelésekor: {e}")
                    time.sleep(1)

            if not dashboard_menu:
                raise Exception("Nem sikerült elérni a dashboard menüt")

            time.sleep(2)

            admin_driver.save_screenshot("tests/debug/dashboard_after_menu.png")

            order_search = None
            for attempt in range(3):
                print(f"  Rendelés kereső keresése ({attempt+1}/3)")
                try:
                    order_search = admin_driver.find_element(By.ID, "order_search")
                    if order_search:
                        print("  Rendelés kereső megtalálva")
                        order_search.clear()
                        order_search.send_keys(order_id)
                        print(f"  Rendelés azonosító beírva: {order_id}")
                        time.sleep(1)
                        break
                except Exception as e:
                    print(f"  Rendelés kereső probléma: {e}")
                    try:
                        sections = admin_driver.find_elements(By.CSS_SELECTOR, "section")
                        for section in sections:
                            if "Rendelés" in section.text and "állapot" in section.text:
                                section.click()
                                time.sleep(1)
                                inputs = section.find_elements(By.TAG_NAME, "input")
                                for input_elem in inputs:
                                    if input_elem.get_attribute("id") == "order_search":
                                        order_search = input_elem
                                        order_search.clear()
                                        order_search.send_keys(order_id)
                                        print(f"  Rendelés kereső megtalálva szekción belül, azonosító beírva: {order_id}")
                                        break
                                if order_search:
                                    break
                    except Exception as section_error:
                        print(f"  Szekció keresési probléma: {section_error}")
                    time.sleep(1)

            if not order_search:
                raise Exception("Nem sikerült megtalálni a rendelés keresőmezőt")

            time.sleep(2)

            admin_driver.save_screenshot("tests/debug/dashboard_after_search.png")

            dropdown_items = None
            for attempt in range(3):
                print(f"  Találati lista keresése ({attempt+1}/3)")
                try:
                    search_result = admin_driver.find_element(By.XPATH, f"//div[contains(@class, 'search-item')]//div[contains(., '#{order_id}')]")
                    if search_result:
                        print(f"  Találat megtalálva: #{order_id}")
                        admin_driver.execute_script("arguments[0].click();", search_result)
                        print("  Találatra kattintás sikeres (JS)")
                        break
                except Exception as e:
                    print(f"  Találati lista probléma: {e}")
                    try:
                        dropdown_items = admin_driver.find_elements(By.CSS_SELECTOR, ".items .search-item")
                        if dropdown_items and len(dropdown_items) > 0:
                            print(f"  {len(dropdown_items)} találat az items listában")
                            admin_driver.execute_script("arguments[0].click();", dropdown_items[0])
                            print("  Első találatra kattintás sikeres (JS)")
                            break
                    except Exception as dropdown_error:
                        print(f"  Dropdown keresési probléma: {dropdown_error}")
                    time.sleep(1)

            time.sleep(2)

            admin_driver.save_screenshot("tests/debug/dashboard_after_result_click.png")

            status_select = None
            for attempt in range(3):
                print(f"  Státusz select keresése ({attempt+1}/3)")
                try:
                    status_select = admin_driver.find_element(By.ID, "order_status")
                    if status_select:
                        print("  Státusz select megtalálva")
                        if status_select.get_attribute("disabled"):
                            print("  Státusz select le van tiltva, engedélyezés JS-sel")
                            admin_driver.execute_script("arguments[0].disabled = false;", status_select)

                        select = Select(status_select)
                        select.select_by_value("Teljesítve")
                        print("  Státusz beállítva: Teljesítve")
                        break
                except Exception as e:
                    print(f"  Státusz select probléma: {e}")
                    time.sleep(1)

            if not status_select:
                raise Exception("Nem sikerült megtalálni vagy kezelni a státusz select mezőt")

            time.sleep(1)

            submit_button = None
            for attempt in range(3):
                print(f"  Mentés gomb keresése ({attempt+1}/3)")
                try:
                    submit_button = admin_driver.find_element(By.CSS_SELECTOR, "input[type='submit'][name='update_order_status']")
                    if submit_button:
                        print("  Mentés gomb megtalálva")
                        admin_driver.execute_script("arguments[0].scrollIntoView(true);", submit_button)
                        time.sleep(1)
                        admin_driver.execute_script("arguments[0].click();", submit_button)
                        print("  Mentés gombra kattintás sikeres (JS)")
                        break
                except Exception as e:
                    print(f"  Mentés gomb probléma: {e}")
                    time.sleep(1)

            if not submit_button:
                raise Exception("Nem sikerült megtalálni vagy kezelni a mentés gombot")

            time.sleep(3)

            admin_driver.save_screenshot("tests/debug/dashboard_after_save.png")

            return True

        except Exception as process_error:
            print(f"  Hiba a rendelés feldolgozásakor: {process_error}")
            admin_driver.save_screenshot("tests/errors/admin_process_error_details.png")
            return False

    except Exception as e:
        print(f"  Admin hiba: {e}")
        try:
            admin_driver.save_screenshot("tests/errors/admin_process_error.png")
            print("  Hibaképernyő mentve")
        except:
            pass
        return False
    finally:
        admin_driver.quit()

    return False

def submit_product_review(driver, product_info, user=None):
    """Termék értékelés írása és beküldése a termék oldalán"""
    print("\n5. Termékvélemény írása...")

    if product_info and "url" in product_info and product_info["url"]:
        driver.get(product_info["url"])
    else:
        raise Exception("Hiányzik a termék URL")

    time.sleep(2)
    print("  Termék oldal betöltve")

    review_section = wait_for_element(driver, By.CSS_SELECTOR, ".reviews")
    if review_section:
        scroll_to_element(driver, review_section)

        valid_ratings = [3, 4, 5]
        rating = random.choice(valid_ratings)

        star_index = rating - 1

        star_selector = f".review-form-stars .star[data-index='{star_index}']"
        star_element = wait_for_element(driver, By.CSS_SELECTOR, star_selector)

        if star_element:
            scroll_to_element(driver, star_element)

            try:
                star_element.click()
                print(f"  {rating} csillag kiválasztva (index {star_index})")
            except:
                driver.execute_script("arguments[0].click();", star_element)
                print(f"  {rating} csillag kiválasztva JavaScript segítségével (index {star_index})")

            time.sleep(1)

            stars_input = wait_for_element(driver, By.NAME, "stars-input")
            if stars_input:
                driver.execute_script(f"arguments[0].value = '{rating}';", stars_input)
                print(f"  Értékelés értéke beállítva: {rating}")

                title_input = wait_for_element(driver, By.ID, "review-title")
                if title_input:
                    title_text = lorem.sentence().split()
                    title = " ".join(title_text[:random.randint(5, 8)])
                    title_input.send_keys(title)
                    print(f"  Cím megadva: {title}")

                body_textarea = wait_for_element(driver, By.ID, "review-body")
                if body_textarea:
                    body_text = lorem.paragraph()
                    body_textarea.send_keys(body_text)
                    print(f"  Leírás megadva ({len(body_text)} karakter)")

                send_button = wait_for_clickable(driver, By.CSS_SELECTOR, "button.send-button.submit")
                if send_button:
                    send_button.click()
                    print("  Értékelés elküldve")
                    time.sleep(2)
                    return True
        else:
            print("  A csillag értékelés rejtett beviteli mezője nem található")

    raise Exception("Nem sikerült az értékelést beküldeni")

def process_single_order(order_data):
    """Egy megrendeléshez tartozó értékelési folyamat végrehajtása"""
    print(f"\n=== Értékelési folyamat indítása ===")

    if not order_data.get("user") or not order_data.get("product_info") or not order_data.get("product_info").get("url"):
        print("Hiányzó felhasználói vagy termék adatok, kihagyás...")
        return False

    user = order_data["user"]
    product_info = order_data["product_info"]

    print(f"Felhasználó: {user.get('username', 'ismeretlen')}")
    print(f"Termék URL: {product_info.get('url', 'ismeretlen')}")

    driver = create_headless_driver()

    try:
        driver.get("http://localhost/login")
        Login(driver, user["user_name"], user["password"], False)
        print(f"✓ Bejelentkezés: {user['user_name']}")

        try:
            order_id = find_latest_order(driver)
            print(f"✓ Rendelés azonosító: #{order_id}")

            admin_success = admin_process_order(order_id)
            print(f"✓ Admin feldolgozás {'sikeres' if admin_success else 'sikertelen'}")

            if admin_success:
                review_success = submit_product_review(driver, product_info, user)
                print(f"✓ Értékelés beküldése {'sikeres' if review_success else 'sikertelen'}")
                return True

        except Exception as e:
            print(f"Hiba a rendelés azonosító megtalálásakor: {e}")

        return False

    except Exception as e:
        print(f"Általános hiba az értékelési folyamat során: {e}")
        return False

    finally:
        driver.quit()

def process_orders(orders):
    """Több rendeléshez tartozó értékelési folyamat végrehajtása"""
    print(f"\n====== Értékelési folyamat {len(orders)} rendeléshez ======")

    successful = 0
    failed = 0

    for i, order_data in enumerate(orders):
        print(f"\n--- Értékelés #{i+1}/{len(orders)} ---")

        if process_single_order(order_data):
            successful += 1
        else:
            failed += 1

    print(f"\n=== Értékelési folyamat befejezve ===")
    print(f"Sikeres értékelések: {successful}")
    print(f"Sikertelen értékelések: {failed}")
    print(f"Összesen: {len(orders)}")

def process_existing_order(username, password, product_url=None):
    """Meglévő felhasználó legutóbbi rendelésének feldolgozása és értékelése"""
    print(f"\n=== Legutóbbi rendelés feldolgozása felhasználónak: {username} ===")

    driver = create_headless_driver()

    try:
        driver.get("http://localhost/login")
        Login(driver, username, password, False)
        print(f"✓ Bejelentkezés: {username}")

        order_id = find_latest_order(driver)
        print(f"✓ Rendelés azonosító: #{order_id}")

        admin_success = admin_process_order(order_id)
        print(f"✓ Admin feldolgozás {'sikeres' if admin_success else 'sikertelen'}")

        if admin_success and product_url:
            product_info = {"url": product_url}
            review_success = submit_product_review(driver, product_info)
            print(f"✓ Értékelés beküldése {'sikeres' if review_success else 'sikertelen'}")
            return True

        return admin_success

    except Exception as e:
        print(f"Hiba a rendelés feldolgozása során: {e}")
        return False

    finally:
        driver.quit()

def main():
    """Főprogram: feldolgozza a parancssori paramétereket és elindítja a megfelelő folyamatot"""
    print("\n" + "="*50)
    print("TESZT: Termék Értékelés Folyamat")
    print("="*50)

    parser = argparse.ArgumentParser(description='Termék értékelési folyamat')
    parser.add_argument('--orders_file', type=str, help='JSON fájl útvonala a feldolgozandó rendelésekhez')
    parser.add_argument('--username', type=str, help='Felhasználónév a bejelentkezéshez')
    parser.add_argument('--password', type=str, help='Jelszó a bejelentkezéshez')
    parser.add_argument('--product_url', type=str, help='Termék URL az értékeléshez')
    parser.add_argument('--bot_account', action='store_true', help='Bot fiók használata (véletlenszerűen választva)')

    args = parser.parse_args()

    if args.orders_file and os.path.exists(args.orders_file):
        with open(args.orders_file, 'r', encoding='utf-8') as f:
            orders = json.load(f)
            process_orders(orders)
            return

    if args.username and args.password:
        process_existing_order(args.username, args.password, args.product_url)
        return

    if args.bot_account:
        bot_account = get_bot_account()
        if bot_account:
            process_existing_order(bot_account["user_name"], bot_account["password"], args.product_url)
            return
        else:
            print("Nem található bot fiók!")
            return

    driver = create_headless_driver()

    try:
        user = handle_account(driver)
        print(f"✓ Fiók kezelve: {user['username']}")

        try:
            order_id = find_latest_order(driver)
            print(f"✓ Rendelés azonosító megtalálva: #{order_id}")

            admin_success = admin_process_order(order_id)
            print(f"✓ Admin feldolgozás {'sikeres' if admin_success else 'sikertelen'}")

            if admin_success and args.product_url:
                product_info = {"url": args.product_url}
                review_success = submit_product_review(driver, product_info)
                print(f"✓ Értékelés beküldése {'sikeres' if review_success else 'sikertelen'}")
            elif admin_success:
                print("⚠️ Termék URL nincs megadva, értékelés kihagyva")

        except Exception as e:
            print(f"Hiba a rendelés feldolgozása során: {str(e)}")

        time.sleep(random.uniform(1, 2))
        print("\n\033[42;97m ✓ A teszt sikeres! \033[0m")

    except Exception as e:
        print(f"\n\033[41;97m ✗ A teszt sikertelen! \033[0m \n{str(e).splitlines()[0]}")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
