import random

class Review:
    
    # Robot felhasználók azonosítói
    users = [ 1, 2, 3, 4, 5 ]
    
    content = {
        5: [
            ("Tökéletes választás!", "A {product_name} minden várakozásomat felülmúlta! Tökéletes minőség, fantasztikus hatás."),
            ("Elégedettség a csúcson", "Nagyon örülök, hogy a {product_name}-t választottam. Hatékony és gyors eredményt hozott."),
            ("Fantasztikus termék!", "Nem is gondoltam volna, hogy a {product_name} ennyire hatásos lesz. Mindenkinek ajánlom!"),
            ("Hihetetlenül jó!", "A {product_name} valóban segített, sokkal jobban érzem magam, mióta használom."),
            ("Minden pénzt megér!", "Nem volt olcsó, de a {product_name} teljes mértékben megérte az árát!")
        ],
        4: [
            ("Nagyon jó!", "A {product_name} szuperül működik, nagyon elégedett vagyok vele."),
            ("Pozitív élmény", "Nem bántam meg, hogy a {product_name}-t választottam. Jó minőség, korrekt ár."),
            ("Szuper termék", "A {product_name} nagy segítség volt, szívesen rendelek majd újra."),
            ("Megéri az árát", "A {product_name} megérte a befektetést, hatása érezhető."),
            ("Elégedett vagyok", "A {product_name} összességében jól működik, ajánlom mindenkinek.")
        ],
        3: [
            ("Teljesen rendben van", "A {product_name} nem kiemelkedő, de elvégzi a dolgát."),
            ("Átlagos minőség", "A {product_name} használható, de semmi különös."),
            ("Elfogadható termék", "A {product_name} hozta, amit ígért, de nem több."),
            ("Nem rossz, de nem is kiemelkedő", "A {product_name} rendben van, de nem különleges."),
            ("Jó, de lehetne jobb", "A {product_name} alapvetően jó, de némi javítást elbírna.")
        ],
        2: [
            ("Csalódás", "A {product_name} nem váltotta be a hozzá fűzött reményeimet."),
            ("Gyenge minőség", "Sajnos a {product_name} nem hozta az elvárt eredményt."),
            ("Kellemetlen élmény", "A {product_name} használata nem volt túl kellemes."),
            ("Nem ezt vártam", "A {product_name} teljesítménye gyengébb, mint amit ígértek."),
            ("Javításra szorul", "A {product_name} lehetne sokkal jobb is, jelenleg nem ajánlanám.")
        ],
        1: [
            ("Borzasztó", "A {product_name} használhatatlan, teljes pénzkidobás."),
            ("Teljes pénzkidobás", "Ez a {product_name} egyszerűen szörnyű! Soha többé!"),
            ("Szörnyű tapasztalat", "A {product_name} egyáltalán nem működik, borzalmas élmény volt."),
            ("Kerüld el!", "A {product_name} egyáltalán nem éri meg, csak pénzkidobás."),
            ("Nagyon rossz minőség", "A {product_name} egyszerűen használhatatlan. Nagy csalódás.")
        ]
    }

    @staticmethod
    def random(count = 1, product_name = "termék"):
        
        if (count > len(Review.users)):
            return "Nem áll rendelkezésre ennyi felhasználó!"
        
        reviews = []
        available_users = Review.users.copy()
        if count > len(available_users):
            count = len(available_users)
        for _ in range(count):
            user = random.choice(available_users)
            available_users.remove(user)
            
            rating = round(random.random() * 4 + 1, 1)
            rating = round(rating * 2) / 2
            review = random.choice(Review.content[round(rating)])
            review = (review[0], review[1].replace("{product_name}", product_name))
            
            reviews.append({
                "userId": user,
                "review": review,
                "stars": rating
            })
        return reviews