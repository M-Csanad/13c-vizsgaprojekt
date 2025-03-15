import mysql.connector
import os
from dotenv import load_dotenv
from pathlib import Path

env_path = Path('c:/xampp/htdocs/13c-vizsgaprojekt/.ext/.env')
load_dotenv(dotenv_path=env_path)

class Database:
    def __init__(self):
        self.db_name = os.getenv("DB_MAIN_NAME", "florens_botanica")
        
        servername = os.getenv("DB_SERVERNAME", "localhost:3307")
        if ":" in servername:
            self.host, self.port = servername.split(":")
            self.port = int(self.port)
        else:
            self.host = servername
            self.port = 3306
            
        self.user = os.getenv("DB_MAIN_USERNAME", "root")
        self.password = os.getenv("DB_MAIN_PASSWORD", "")
        self.db = None

    def connect(self):
        self.db = mysql.connector.connect(
            host=self.host,
            port=self.port,
            user=self.user,
            password=self.password,
            database=self.db_name
        )
        
        return f"Connected to {self.db_name}"

    def disconnect(self):
        return f"Disconnected from {self.db_name}"
    
    def select(self, query):
        cursor = self.db.cursor(dictionary=True)
        cursor.execute(query)
        result = cursor.fetchall()
        cursor.close()
        return result
    
    def update(self, query):
        cursor = self.db.cursor()
        cursor.execute(query)
        self.db.commit()
        cursor.close()
        return cursor.rowcount
    
    def delete(self, query):
        cursor = self.db.cursor()
        cursor.execute(query)
        self.db.commit()
        cursor.close()
        return cursor.rowcount
    
    def insert(self, query):
        cursor = self.db.cursor()
        cursor.execute(query)
        self.db.commit()
        cursor.close()
        return cursor.lastrowid