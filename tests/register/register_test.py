import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from time import sleep

from tests.utility.chromedriver_headless import driver
from tests.login.autologin import Login
from tests.utility.user_generator import UserGenerator

print("\n" + "="*50)
print("TESZT: Regisztrálás")
print("="*50)


try:
    print("\nTeszt inicializálása...")
    
    print("\nNavigálás a regisztrációs oldalra...")
    driver.get("http://localhost/register")
    print("✓ Regisztrációs oldal megnyitva")
    
    print("\nFelhasználó generálása...")
    user = UserGenerator.generate()
    print(f"✓ Felhasználó generálva: {user['username']} {user['email']}")
    
    print("\nŰrlap kitöltése...")
    
    driver.find_element(by=By.ID, value="email").send_keys(user["email"])
    driver.find_element(by=By.ID, value="username").send_keys(user["username"])
    driver.find_element(by=By.ID, value="lastname").send_keys(user["last_name"])
    driver.find_element(by=By.ID, value="firstname").send_keys(user["first_name"])
    driver.find_element(by=By.ID, value="password").send_keys(user["password"])
    driver.find_element(by=By.ID, value="passwordConfirm").send_keys(user["password"])
    
    driver.find_element(by=By.ID, value="agree").click()
    
    print("✓ Űrlap kitöltve")
    
    print("\nRegisztráció...")
    driver.find_element(by=By.CSS_SELECTOR, value="input.action-button").click()
    
    WebDriverWait(driver, 10).until(lambda d: d.current_url == "http://localhost/login")
    print("✓ Átirányítás megtörtént")
    
    Login(driver, user["username"], user["password"], False)
    
    print("\n\033[42;97m ✓ A teszt sikeres! \033[0m")

except Exception as e:
    print(f"\n\033[41;97m ✗ A teszt sikertelen! \033[0m \n{str(e).splitlines()[0]}")

finally:
    driver.quit()