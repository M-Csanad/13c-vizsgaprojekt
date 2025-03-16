import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")
from tests.utility.password_generator import PasswordGenerator

import random
import string, math
import bcrypt
import pyperclip

class UserGenerator:
    
    # Férfi keresztnevek
    male_first_names = [
        "István", "János", "József", "László", "Zoltán", "Sándor", "Ferenc", "Gábor", 
        "Attila", "Péter", "Tamás", "Mihály", "András", "Tibor", "Imre", "Balázs", 
        "Zsolt", "Károly", "Gyula", "Béla",
        "Ákos", "Csaba", "Dániel", "Bence", "Gergő", "Márk", "Miklós", "Norbert",
        "Róbert", "Szilárd", "Szabolcs", "Viktor", "Antal", "Bertalan", "Botond",
        "Géza", "György", "Jácint", "Jenő", "Kristóf", "Levente", "Lőrinc", "Máté",
        "Márió", "Ottó", "Patrik", "Rudolf", "Sebestyén", "Dávid", "Zalán"
    ]

    # Női keresztnevek
    female_first_names = [
        "Mária", "Erzsébet", "Katalin", "Éva", "Ilona", "Anna", "Zsuzsanna", "Margit", 
        "Judit", "Ágnes", "Andrea", "Erika", "Krisztina", "Irén", "Gabriella", "Adrienn", 
        "Viktória", "Klára", "Eszter", "Szilvia",
        "Ágnes", "Anikó", "Barbara", "Beáta", "Bernadett", "Boglárka", "Csilla", "Dorina",
        "Edina", "Emese", "Fanni", "Flóra", "Hajnalka", "Hedvig", "Ildikó", "Jázmin",
        "Kinga", "Lilla", "Melinda", "Nikolett", "Noémi", "Orsolya", "Piroska", "Réka",
        "Rita", "Rozália", "Tünde", "Veronika", "Zita", "Zsófia"
    ]

    # Vezetéknevek
    last_names = [
        "Nagy", "Kovács", "Tóth", "Szabó", "Horváth", "Varga", "Kiss", "Molnár", 
        "Németh", "Farkas", "Balogh", "Papp", "Takács", "Juhász", "Mészáros", "Szűcs", 
        "Simon", "Rácz", "Fekete", "Boros",
        "Bálint", "Barta", "Bodnár", "Bognár", "Borbély", "Deák", "Fazekas", "Fülöp",
        "Gál", "Hegedűs", "Jakab", "Kelemen", "Lukács", "Major", "Mezei", "Nemes",
        "Pál", "Péter", "Rózsa", "Soós", "Székely", "Szőke", "Tóth", "Vass",
        "Veres", "Vincze", "Vörös", "Zsiga", "Zsiga", "Blank"
    ]

    
    # Email domainek (nem létezők)
    email_domains = [
        "bot.example.com", "bot.example.org", "bot.example.net"
    ]
    
    NAMES_COUNT = 2 * len(last_names) * (len(male_first_names) + len(female_first_names))
    
    @staticmethod
    def remove_accents(text):
        """Magyar ékezetes karakterek cseréje ékezet nélkülire"""
        replacements = {
            'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ö': 'o', 'ő': 'o', 'ú': 'u', 'ü': 'u', 'ű': 'u',
            'Á': 'A', 'É': 'E', 'Í': 'I', 'Ó': 'O', 'Ö': 'O', 'Ő': 'O', 'Ú': 'U', 'Ü': 'U', 'Ű': 'U'
        }
        
        for original, new in replacements.items():
            text = text.replace(original, new)
            
        return text
    
    @staticmethod
    def generate_username(last_name, first_name):
        """Felhasználónév készítése névből"""
        # Ékezetek eltávolítása és kisbetűsítés
        last = UserGenerator.remove_accents(last_name.lower())
        first = UserGenerator.remove_accents(first_name.lower())
        
        # Különböző felhasználónév formátumok, némelyik alulvonást vagy kötőjelet tartalmaz
        username_patterns = [
            f"{last}{first[0]}",            # egyszerű: vezetéknév + keresztnév kezdőbetű
            f"{last}_{first[0]}",           # alulvonással elválasztva
            f"{last}-{first[0]}",           # kötőjellel elválasztva
            f"{first[0]}_{last}",           # keresztnév kezdőbetű + alulvonás + vezetéknév
            f"{first[0]}-{last}",           # keresztnév kezdőbetű + kötőjel + vezetéknév
            f"{last[:4]}_{first[:3]}"       # vezetéknév első 4 betűje + alulvonás + keresztnév első 3 betűje
        ]
        
        # Véletlenszerű minta választása
        name = random.choice(username_patterns)
        
        # Nem engedélyezett karakterek eltávolítása
        clean_name = ""
        for c in name:
            if c.isalnum() or c == '_' or c == '-':
                clean_name += c
        
        # Ha túl hosszú, levágjuk
        if len(clean_name) > 15:
            clean_name = clean_name[:15]
            
        # Hozzáadunk egy számot
        number = str(random.randint(1, 999))
        
        # Végső felhasználónév
        username = clean_name + number
        
        # Minimum 3 karakter ellenőrzése
        if len(username) < 3:
            username += "123"[:3-len(username)]
            
        return username
    
    @staticmethod
    def generate_email(last_name, first_name):
        """Email cím készítése névből"""
        # Ékezetek eltávolítása és kisbetűsítés
        last = UserGenerator.remove_accents(last_name.lower())
        first = UserGenerator.remove_accents(first_name.lower())
        
        # Domain kiválasztása
        domain = random.choice(UserGenerator.email_domains)
        
        # Email cím összeállítása
        email = f"{first}.{last}@{domain}"
        
        return email
    
    @staticmethod
    def generate(count=1, male=random.choice([True, False])):
        """Véletlenszerű felhasználó(k) generálása egyedi felhasználónévvel és email címmel"""
        pwd_gen = PasswordGenerator()
        users = []
        used_usernames = set()
        used_emails = set()

        for _ in range(count):
            retry_count = 0
            while True:
                # Név kiválasztása
                if male:
                    first_name = random.choice(UserGenerator.male_first_names)
                else:
                    first_name = random.choice(UserGenerator.female_first_names)
                    
                last_name = random.choice(UserGenerator.last_names)
                
                # Felhasználónév készítése
                username = UserGenerator.generate_username(last_name, first_name)
                    
                # Email készítése
                email = UserGenerator.generate_email(last_name, first_name)
                
                # Ellenőrizzük az egyediséget
                if username in used_usernames or email in used_emails:
                    retry_count += 1
                    if retry_count > 100:  # Védelem végtelen ciklus ellen
                        print("\033[91mHiba: Nem sikerült egyedi felhasználót generálni\033[0m")
                        break
                    continue
                
                used_usernames.add(username)
                used_emails.add(email)
                
                # Jelszó generálása
                password = pwd_gen.generate(random.randint(8, 64))
                
                # Felhasználó objektum
                user = {
                    "first_name": first_name,
                    "last_name": last_name,
                    "username": username,
                    "email": email,
                    "password": password,
                    "gender": "male" if male else "female"
                }
                
                users.append(user)
                break
        
        return users[0] if count == 1 else users
    
    @staticmethod
    def generateAsSQL(count=10):
        """Véletlenszerű felhasználó(k) generálása SQL formátumban"""
        
        chance_of_repetition = 1 - math.exp(-(count * (count - 1)) / UserGenerator.NAMES_COUNT)
        print(f"\n\033[1m{count} felhasználó generálása...\033[0m")
        print("   Statisztika: ")
        percentage = chance_of_repetition * 100
        if percentage > 20:
            print(f"      \033[91mFIGYELEM: Magas ismétlődési esély: {percentage:.4f}% - ez hatással lehet a teljesítményre\033[0m")
        else:
            print(f"      Ismétlődés esélye: {percentage:.4f}%")
        
        users = UserGenerator.generate(count=count)
        
        sql = "INSERT INTO user (email, user_name, last_name, first_name, password_hash) VALUES "
        
        for user in users:
            password_hash = bcrypt.hashpw(user["password"].encode("utf-8"), bcrypt.gensalt()).decode("utf-8")
            sql += f"('{user['email']}', '{user['username']}', '{user['last_name']}', '{user['first_name']}', '{password_hash}'), "
        
        sql = sql[:-2] + ";"
        
        pyperclip.copy(sql)
        print("\033[42m✓ SQL kód vágólapra másolva\033[0m")
        
        return sql
    
if __name__ == "__main__":
    UserGenerator.generateAsSQL(10)