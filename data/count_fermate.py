import json
from collections import defaultdict

# Path to your JSON file
json_file = r"c:\Users\giorgio\Downloads\fermate.json"

# Open and load JSON
with open(json_file, encoding="utf-8") as f:
    data = json.load(f)

line_counts = defaultdict(int)

# Iterate over stops
for entry in data[2]["data"]:  # "data" is inside the third element in your JSON
    lines = entry.get("linee", "")
    if lines:
        # Split by comma, each value counts
        for line in lines.split(","):
            line_counts[line.strip()] += 1

# Print results
for line, count in sorted(line_counts.items(), key=lambda x: int(x[0])):
    print(f"{line}: {count}")
