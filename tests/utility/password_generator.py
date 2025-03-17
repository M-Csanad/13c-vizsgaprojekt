import random
import string

class PasswordGenerator:
    @staticmethod
    def generate(length = 12):
        length = min(max(length, 8), 64)
        
        # Karakter készletek definiálása
        lowercase = string.ascii_lowercase
        uppercase = string.ascii_uppercase
        digits = string.digits
        special = string.punctuation
        
        # Minden típusból választunk egy karaktert
        must_have = [
            random.choice(lowercase),
            random.choice(uppercase),
            random.choice(digits),
            random.choice(special)
        ]
        
        # Az összes lehetséges karakter
        all_chars = lowercase + uppercase + digits + special
        
        # Feltöltjük a jelszót a megfelelő hosszúságra
        password = must_have + random.choices(all_chars, k=max(0, length - 4))
        
        # Összekeverjük a karaktereket
        random.shuffle(password)
        
        # Visszaadjuk a jelszót string formátumban
        return ''.join(password)