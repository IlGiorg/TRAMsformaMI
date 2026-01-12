<?php
$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli("127.0.0.1", "root", "", "tramsformami");
if ($conn->connect_error) die("DB Error");

$stmt = $conn->prepare("
INSERT INTO userdata (linea, tempo, temposemaforo, tempomarcia)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
  "iiii",
  $data['linea'],
  $data['tempo'],
  $data['temposemaforo'],
  $data['tempomarcia']
);

$stmt->execute();
echo "Dati salvati correttamente";
