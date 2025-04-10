import sys
sys.path.append("c:/xampp/htdocs/13c-vizsgaprojekt")
from tests.utility.user_generator import UserGenerator
from tests.utility.password_generator import PasswordGenerator
from tests.utility.database import Database  # Import Database class

import json
import os
import bcrypt
import argparse
import pyperclip
import random
from mysql.connector import Error

class BotUserCreator:
    @staticmethod
    def create_users(count=1, batch_count=1, json_path="tests/utility/bot_accounts.json", execute_sql=False):
        """
        Bot felhasználók létrehozása és mentése az adatbázisba és JSON fájlba

        Paraméterek:
            count: Hány felhasználó jöjjön létre kötegenkénk
            batch_count: Hány köteg felhasználót hozzunk létre
            json_path: A JSON fájl útvonala a bot fiókok tárolásához
            execute_sql: Közvetlen SQL végrehajtás az adatbázisban
        """
        total_count = count * batch_count
        print(f"\n\033[1mGenerálás {total_count} bot felhasználó ({batch_count} batch × {count} felhasználó)...\033[0m")

        # Initialize JSON data if file exists or create empty list
        json_data = []
        json_file = os.path.join(os.path.dirname(__file__), json_path)

        if os.path.exists(json_file):
            try:
                with open(json_file, 'r', encoding='utf-8') as f:
                    json_data = json.load(f)
                print(f"Meglévő JSON fájl betöltve: {len(json_data)} felhasználó található benne.")
            except json.JSONDecodeError:
                print("\033[93mA JSON fájl hibás formátumú vagy üres. Új fájl létrehozása...\033[0m")
                json_data = []
        else:
            print(f"JSON fájl nem létezik. Új fájl létrehozása: {json_file}")

        # Track existing emails and usernames to avoid duplicates
        existing_emails = {user.get('email', '') for user in json_data}
        existing_usernames = {user.get('user_name', '') for user in json_data}

        # Connect to database if execute_sql is True
        db = None
        if execute_sql:
            try:
                db = Database()
                connection_message = db.connect()
                print(f"\033[42m✓ {connection_message}\033[0m")
            except Error as e:
                print(f"\033[91mHiba az adatbázishoz való csatlakozáskor: {e}\033[0m")
                execute_sql = False  # Disable SQL execution if connection failed

        # Generate and process users in batches
        all_created_users = []

        for batch in range(batch_count):
            print(f"\n\033[1mBatch #{batch+1}/{batch_count}\033[0m")

            # Generate users for this batch
            users = UserGenerator.generate(count=count)
            if not isinstance(users, list):
                users = [users]  # Ensure users is a list

            # Create SQL and JSON entries for each user
            sql = "INSERT INTO user (email, user_name, last_name, first_name, password_hash, role, avatar_id) VALUES "
            batch_users = []

            for user in users:
                # Skip if email or username already exists
                if user['email'] in existing_emails or user['username'] in existing_usernames:
                    print(f"\033[93mFigyelmeztetés: Duplikált felhasználó, kihagyva: {user['username']} / {user['email']}\033[0m")
                    continue

                # Generate password hash for database
                password_hash = bcrypt.hashpw(user["password"].encode("utf-8"), bcrypt.gensalt()).decode("utf-8")

                # Assign appropriate avatar based on gender
                avatar_id = 1 if user['gender'] == 'female' else 5  # Female or male avatar

                # Add to SQL - include role and avatar_id
                sql += f"('{user['email']}', '{user['username']}', '{user['last_name']}', '{user['first_name']}', '{password_hash}', 'Bot', {avatar_id}), "

                # Create JSON entry with keys matching database columns
                json_user = {
                    'email': user['email'],
                    'user_name': user['username'],
                    'last_name': user['last_name'],
                    'first_name': user['first_name'],
                    'password': user['password'],  # Store plain password in JSON for bot usage
                    'password_hash': password_hash,
                    'role': 'Bot',
                    'avatar_id': avatar_id,
                    'gender': user['gender'],
                    'is_bot': True  # Mark as bot account
                }

                batch_users.append(json_user)
                all_created_users.append(json_user)

                # Add to tracking sets
                existing_emails.add(user['email'])
                existing_usernames.add(user['username'])

            if batch_users:
                # Complete SQL statement
                final_sql = sql[:-2] + ";"

                # Copy SQL to clipboard
                pyperclip.copy(final_sql)
                print(f"\033[42m✓ SQL kód a vágólapra másolva ({len(batch_users)} felhasználóhoz)\033[0m")

                # Execute SQL if requested and connection is available
                if execute_sql and db:
                    try:
                        rows_affected = db.insert(final_sql)
                        print(f"\033[42m✓ {len(batch_users)} felhasználó sikeresen hozzáadva az adatbázishoz\033[0m")
                    except Error as e:
                        print(f"\033[91mHiba az SQL végrehajtásakor: {e}\033[0m")

                # Add to JSON data
                json_data.extend(batch_users)

                # Write to JSON file
                with open(json_file, 'w', encoding='utf-8') as f:
                    json.dump(json_data, f, indent=2, ensure_ascii=False)
                print(f"\033[42m✓ Bot felhasználók mentve: {json_file}\033[0m")

                # Print batch summary
                print(f"Batch #{batch+1} - {len(batch_users)} új felhasználó generálva")
            else:
                print(f"\033[91mHiba: Nem sikerült új felhasználókat generálni a #{batch+1}. batch-ben\033[0m")

        # Print final summary
        print(f"\n\033[1mÖsszesítés: {len(all_created_users)}/{total_count} bot felhasználó sikeresen létrehozva\033[0m")

        # Print details of created users
        print("\n\033[1mLétrehozott felhasználók részletei:\033[0m")
        for i, user in enumerate(all_created_users):
            print(f"\n--- Bot felhasználó #{i+1} ---")
            print(f"Email: {user['email']}")
            print(f"Felhasználónév: {user['user_name']}")
            print(f"Név: {user['first_name']} {user['last_name']}")
            print(f"Jelszó: {user['password']}")
            print(f"Szerepkör: {user['role']}")
            print(f"Nem: {user['gender']}")
            print(f"Avatar ID: {user['avatar_id']}")

        return all_created_users

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Bot felhasználók létrehozása')
    parser.add_argument('-c', '--count', type=int, default=3,
                        help='Hány felhasználót generáljunk egy batch-ben (alapértelmezett: 3)')
    parser.add_argument('-b', '--batches', type=int, default=1,
                        help='Hány batch-et generáljunk (alapértelmezett: 1)')
    parser.add_argument('-f', '--file', type=str, default='bot_accounts.json',
                        help='A JSON fájl útvonala (alapértelmezett: bot_accounts.json)')
    parser.add_argument('-e', '--execute', action='store_true',
                        help='SQL végrehajtása az adatbázisban (alapértelmezett: kikapcsolva)')

    args = parser.parse_args()

    BotUserCreator.create_users(args.count, args.batches, args.file, args.execute)
