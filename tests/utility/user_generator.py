import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")
from tests.utility.password_generator import PasswordGenerator

import random
import string

class UserGenerator:
    
    # Férfi keresztnevek
    male_first_names = [
        "István", "János", "József", "László", "Zoltán", "Sándor", "Ferenc", "Gábor", 
        "Attila", "Péter", "Tamás", "Mihály", "András", "Tibor", "Imre", "Balázs", 
        "Zsolt", "Károly", "Gyula", "Béla"
    ]
    
    # Női keresztnevek
    female_first_names = [
        "Mária", "Erzsébet", "Katalin", "Éva", "Ilona", "Anna", "Zsuzsanna", "Margit", 
        "Judit", "Ágnes", "Andrea", "Erika", "Krisztina", "Irén", "Gabriella", "Adrienn", 
        "Viktória", "Klára", "Eszter", "Szilvia"
    ]
    
    # Vezetéknevek
    last_names = [
        "Nagy", "Kovács", "Tóth", "Szabó", "Horváth", "Varga", "Kiss", "Molnár", 
        "Németh", "Farkas", "Balogh", "Papp", "Takács", "Juhász", "Mészáros", "Szűcs", 
        "Simon", "Rácz", "Fekete", "Boros"
    ]
    
    # Email domainek (nem létezők)
    email_domains = [
        "bot.example.com", "bot.example.org", "bot.example.net"
    ]
    
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
        """Véletlenszerű felhasználó(k) generálása"""
        pwd_gen = PasswordGenerator()
        users = []
        
        for _ in range(count):
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
        
        return users[0] if count == 1 else users