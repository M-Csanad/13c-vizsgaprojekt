import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from time import sleep

from tests.utility.chromedriver import driver

print("\n" + "="*50)
print("TESZT: Bejelentkezés")
print("="*50)

try:
    print("\nTeszt inicializálása...")
    
    # Weboldal megnyitása
    driver.get("http://localhost")
    print("✓ Weboldal betöltve")

    # Navigálás a bejelentkező oldalra
    print("\nNavigálás a bejelentkezési oldalra...")
    driver.find_element(by=By.CSS_SELECTOR, value="a[href='/login']").click()
    print("✓ Bejelentkezési oldal megnyitva")

    # Űrlap kitöltése
    print("\nŰrlap kitöltése...")
    usernameElement = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.ID, "username"))
    )

    usernameElement.send_keys("admin")
    driver.find_element(by=By.ID, value="passwd").send_keys("Alma_1234")
    print("✓ Felhasználónév és jelszó megadva")

    # Bejelentkezés gombra kattintás
    print("\nBejelentkezés...")
    driver.find_element(by=By.CSS_SELECTOR, value="input[name='login']").click()

    WebDriverWait(driver, 10).until(lambda d: d.current_url != "http://localhost/login")
    print("✓ Átirányítás megtörtént")

    # Ellenőrizzük, hogy sikeres volt-e a bejelentkezés
    print("\nBejelentkezés ellenőrzése...")
    settingsButton = driver.find_element(by=By.CSS_SELECTOR, value="a[href='/settings']")
    assert settingsButton.is_displayed()
    
    print("\n\033[42;97m ✓ A teszt sikeres! \033[0m")

except Exception as e:
    print(f"\n\033[41;97m ✗ A teszt sikertelen! \n{str(e).splitlines()[0]}\033[0m")

finally:
    driver.quit()