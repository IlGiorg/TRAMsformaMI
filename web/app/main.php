<?php
// CONNESSIONE DB
$conn = new mysqli("127.0.0.1", "root", "", "tramsformami");
if ($conn->connect_error) die("DB Error");

// PRENDO LE LINEE
$linee = [];
$res = $conn->query("SELECT id_linea, linea FROM linea ORDER BY linea");
while ($row = $res->fetch_assoc()) {
    $linee[] = $row;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tram Timer</title>

<style>
body { font-family: sans-serif; text-align: center; padding: 20px; }
button { padding: 15px; margin: 5px; font-size: 16px; }
.hidden { display: none; }

#popup {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  display: flex;
  align-items: center;
  justify-content: center;
}
#popup div {
  background: white;
  padding: 20px;
  border-radius: 10px;
}
</style>
</head>
<body>

<!-- POPUP DEVICE -->
<div id="popup">
  <div>
    <p>È questo il tuo device?</p>
    <p id="deviceText"></p>
    <button onclick="confirmDevice(true)">Sì</button>
    <button onclick="confirmDevice(false)">No</button>
  </div>
</div>

<h1>⏱️ Tram Timer</h1>

<select id="linea">
  <option value="">Seleziona linea</option>
  <?php foreach ($linee as $l): ?>
    <option value="<?= $l['id_linea'] ?>">Linea <?= $l['linea'] ?></option>
  <?php endforeach; ?>
</select>

<div>
  <button onclick="startCorsa()">▶ Inizio corsa</button>
  <button id="semaforoBtn" onclick="semaforo()" class="hidden">🚦 Semaforo</button>
  <button id="marciaBtn" onclick="marcia()" class="hidden">🚋 In marcia</button>
  <button onclick="fineCorsa()">⏹ Fine corsa</button>
</div>

<div id="risultato"></div>

<script>
let timer1 = 0, timer2 = 0;
let t1running = false, t2running = false;
let interval;

function tick() {
  if (t1running) timer1++;
  if (t2running) timer2++;
}

interval = setInterval(tick, 1000);

// DEVICE DETECTION
const ua = navigator.userAgent;
document.getElementById("deviceText").innerText = ua.includes("Android")
  ? "Android"
  : ua.includes("iPhone") || ua.includes("Safari")
  ? "Safari / iOS"
  : "Desktop";

function confirmDevice(ok) {
  document.getElementById("popup").style.display = "none";
}

// TIMER CONTROLLI
function startCorsa() {
  t1running = true;
  document.getElementById("semaforoBtn").classList.remove("hidden");
}

function semaforo() {
  t1running = false;
  t2running = true;
  document.getElementById("semaforoBtn").classList.add("hidden");
  document.getElementById("marciaBtn").classList.remove("hidden");
}

function marcia() {
  t2running = false;
  t1running = true;
  document.getElementById("marciaBtn").classList.add("hidden");
  document.getElementById("semaforoBtn").classList.remove("hidden");
}

function fineCorsa() {
  t1running = false;
  t2running = false;

  const totale = timer1 + timer2;

  document.getElementById("risultato").innerHTML = `
    <p><b>Tempo totale corsa:</b> ${totale}s</p>
    <p><b>Tempo in marcia:</b> ${timer1}s</p>
    <p><b>Tempo fermo al semaforo:</b> ${timer2}s</p>
    <button onclick="submit()">Invia al database</button>
  `;
}

function submit() {
  const linea = document.getElementById("linea").value;
  if (!linea) return alert("Seleziona una linea");

  fetch("save.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({
      linea: linea,
      tempo: timer1 + timer2,
      temposemaforo: timer2,
      tempomarcia: timer1
    })
  })
  .then(r => r.text())
  .then(alert);
}
</script>
</body>
</html>
