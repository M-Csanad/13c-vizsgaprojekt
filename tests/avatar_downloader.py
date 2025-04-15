import os
import requests

# Felhasználók és hozzárendelt GIT nevek (ahogy commitoltak)
avatars = [
    ("mxte-b", "mxte-b"),
    ("mxte-b", "="),
    ("m-csanad", "M-Csanad"),
    ("m-csanad", "Milkovics Csanád")
]

# Avatar könyvtár létrehozása
output_dir = ".git/avatar_cache"
os.makedirs(output_dir, exist_ok=True)

# Avatarok letöltése
for [github_username, git_name] in avatars:
    url = f"https://github.com/{github_username}.png"
    response = requests.get(url, timeout=10)

    if response.status_code == 200:
        avatar_path = os.path.join(output_dir, f"{git_name}.png")
        with open(avatar_path, "wb") as f:
            f.write(response.content)
        print(f"✔ Letöltve: {git_name} ({github_username})")
    else:
        print(f"✘ Hiba: {github_username} képe nem elérhető")
