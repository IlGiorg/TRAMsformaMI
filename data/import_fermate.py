import csv
import mysql.connector

# CONFIG
db_config = {
    "host": "127.0.0.1",
    "port": 3307,
    "user": "root",
    "password": "",
    "database": "tramsformami"
}

csv_file = r"c:\Users\giorgio\Downloads\tpl_fermate.csv"

# Connect to DB
print("Connecting to the database...")
conn = mysql.connector.connect(**db_config)
cursor = conn.cursor()
print("Connected!")

# Create table if not exists (without lat/lng)
create_table_query = """
CREATE TABLE IF NOT EXISTS fermate (
    id_amat INT PRIMARY KEY,
    ubicazione TEXT,
    linee TEXT
)
"""
cursor.execute(create_table_query)
print("Table ready.")

# Open CSV and import
with open(csv_file, newline='', encoding='utf-8') as f:
    reader = csv.DictReader(f, delimiter=';')  # semicolon-separated
    for i, row in enumerate(reader, 1):
        try:
            # Insert into DB (only the columns we need)
            cursor.execute(
                "INSERT INTO fermate (id_amat, ubicazione, linee) VALUES (%s, %s, %s)",
                (row.get("id_amat"), row.get("ubicazione"), row.get("linee"))
            )

            if i % 50 == 0:
                print(f"{i} rows inserted...")

        except Exception as e:
            print(f"Error inserting row {row.get('id_amat')}: {e}")

conn.commit()
print("All rows imported!")
cursor.close()
conn.close()
print("Connection closed.")
