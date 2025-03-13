from selenium import webdriver
from selenium.webdriver.common.by import By
from time import sleep
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver import ChromeOptions

options = ChromeOptions()
options.add_argument("--disable-blink-features=AutomationControlled")
options.add_experimental_option("excludeSwitches", ["enable-automation"])
driver = webdriver.Chrome(options=options)
driver.get("http://localhost")

driver.find_element(by=By.CSS_SELECTOR, value="a[href='/login']").click()
element = WebDriverWait(driver, 10).until(
    EC.presence_of_element_located((By.ID, "username"))
)

element.send_keys("admin")
driver.find_element(by=By.ID, value="passwd").send_keys("Alma_123")

driver.find_element(by=By.CSS_SELECTOR, value="input[name='login']").click()
input()