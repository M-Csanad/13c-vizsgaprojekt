import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException

def Login(driver, username, password, findLink = True):
    print("\nBejeletkezés...")
    
    # Navigálás a bejelentkező oldalra
    if findLink:
        driver.find_element(by=By.CSS_SELECTOR, value="a[href='/login']").click()

    # Űrlap kitöltése
    usernameElement = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.ID, "username"))
    )

    usernameElement.send_keys(username)
    driver.find_element(by=By.ID, value="passwd").send_keys(password)

    # Bejelentkezés gombra kattintás
    driver.find_element(by=By.CSS_SELECTOR, value="input[name='login']").click()
    
    # Ellenőrizzük, hogy sikeres volt-e a bejelentkezés
    try:
        error_message = WebDriverWait(driver, 3).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".message-error"))
        )

        raise Exception(error_message.text)
    except TimeoutException:
        pass

    WebDriverWait(driver, 10).until(lambda d: d.current_url != "http://localhost/login")
    
    print("✓ Bejelentkezés megtörtént")