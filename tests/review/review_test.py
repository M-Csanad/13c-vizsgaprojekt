import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from time import sleep

from tests.utility.chromedriver import driver
from tests.utility.database import Database
from tests.login.autologin import Login

db = Database()
db.connect()

items = db.select("SELECT name FROM product")
print("\n" + "="*50)
print("TESZT: Értékelés")
print("="*50)

try:
    print("\nTeszt inicializálása...")
    
    Login(driver, "admin", "Alma_123")
    
    print("\n\033[42;97m ✓ A teszt sikeres! \033[0m")

except Exception as e:
    print(f"\n\033[41;97m ✗ A teszt sikertelen! \033[0m \n{str(e).splitlines()[0]}")

finally:
    driver.quit()