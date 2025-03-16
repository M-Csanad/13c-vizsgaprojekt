from selenium import webdriver

driver = webdriver.Chrome()

driver.get("http://localhost")
print("✓ Weboldal betöltve")