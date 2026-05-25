let logs = [];

const endpoints = [
  "/api/users",
  "/api/login",
  "/api/orders",
  "/api/payment",
  "/api/products",
  "/api/profile"
];

const statusChart = new Chart(document.getElementById("statusChart"), {
  type: "doughnut",
  data: {
    labels: ["200", "404", "500", "502", "504"],
    datasets: [{
      data: [0, 0, 0, 0, 0]
    }]
  }
});

const latencyChart = new Chart(document.getElementById("latencyChart"), {
  type: "bar",
  data: {
    labels: [],
    datasets: [{
      label: "Response Time ms",
      data: []
    }]
  }
});

function generateLog() {
  let statusCodes = [200, 200, 200, 404, 500, 502, 504];
  let status = statusCodes[Math.floor(Math.random() * statusCodes.length)];
  let latency = Math.floor(Math.random() * 3000) + 100;
  let endpoint = endpoints[Math.floor(Math.random() * endpoints.length)];

  let error = "";
  if (status === 404) error = "Resource not found";
  if (status === 500) error = "Database connection failed";
  if (status === 502) error = "Bad gateway from external service";
  if (status === 504) error = "Gateway timeout";

  let log = {
    time: new Date().toLocaleTimeString(),
    endpoint,
    status,
    latency,
    error
  };

  logs.push(log);
  analyzeLogs();
}

function getSeverity(log) {
  if (log.status >= 500 || log.latency > 2000) return "High";
  if (log.status >= 400 || log.latency > 1000) return "Medium";
  return "Low";
}

function getCause(log) {
  if (log.error.includes("Database")) return "Possible database connection or query failure.";
  if (log.error.includes("timeout")) return "API server or external service timeout.";
  if (log.error.includes("gateway")) return "Gateway or third-party service integration issue.";
  if (log.status === 404) return "Invalid endpoint, wrong route, or missing resource.";
  if (log.latency > 2000) return "Latency spike due to slow processing or server overload.";
  return "No major issue detected.";
}

function getRecommendation(log) {
  if (log.error.includes("Database")) return "Check DB credentials, database server status, connection pool, and slow queries.";
  if (log.error.includes("timeout")) return "Check API timeout settings, server load, retry logic, and external API health.";
  if (log.error.includes("gateway")) return "Verify gateway configuration, third-party API availability, and network connectivity.";
  if (log.status === 404) return "Check route mapping, endpoint spelling, HTTP method, and API documentation.";
  if (log.latency > 2000) return "Optimize queries, add caching, reduce payload size, and monitor CPU/RAM.";
  return "No action required.";
}

function updateHealth(failures){
  let health = document.getElementById("systemHealth");

  if(failures.length > 15){
    health.innerHTML = "🔴 Critical System State";
  }
  else if(failures.length > 8){
    health.innerHTML = "🟠 Warning State";
  }
  else{
    health.innerHTML = "🟢 System Stable";
  }
}

function analyzeLogs() {
  let failures = logs.filter(log => log.status >= 400 || log.latency > 1000);
  let high = failures.filter(log => getSeverity(log) === "High");
  let avgLatency = logs.reduce((sum, log) => sum + log.latency, 0) / logs.length;

  updateHealth(failures);

  document.getElementById("total").innerText = logs.length;
  document.getElementById("failures").innerText = failures.length;
  document.getElementById("high").innerText = high.length;
  document.getElementById("latency").innerText = Math.round(avgLatency) + " ms";

  updateCharts();
  updateAlerts(failures.slice(-6).reverse());
}

function updateCharts() {
  let counts = [200, 404, 500, 502, 504].map(code =>
    logs.filter(log => log.status === code).length
  );

  statusChart.data.datasets[0].data = counts;
  statusChart.update();

  let lastLogs = logs.slice(-8);
  latencyChart.data.labels = lastLogs.map(log => log.endpoint);
  latencyChart.data.datasets[0].data = lastLogs.map(log => log.latency);
  latencyChart.update();
}

function updateAlerts(failures) {
  let alertBox = document.getElementById("alerts");
  alertBox.innerHTML = "";

  failures.forEach(log => {
    let severity = getSeverity(log);
    let confidence = Math.floor(Math.random() * 20) + 80;

    alertBox.innerHTML += `
      <div class="alert">
        <h3>${severity} Severity | ${log.endpoint} | Status ${log.status}</h3>
        <p><b>Time:</b> ${log.time}</p>
        <p><b>Error:</b> ${log.error || "Latency spike detected"}</p>
        <p><b>Likely Cause:</b> ${getCause(log)}</p>
        <p><b>AI Confidence:</b> ${confidence}%</p>
        <p><b>AI Recommendation:</b> ${getRecommendation(log)}</p>
      </div>
    `;
  });
}

setInterval(()=>{
  document.getElementById("liveTime").innerText =
  new Date().toLocaleString();
},1000);
async function fetchRealLogs() {
  try {
    let response = await fetch("http://localhost/event_platform/logs.php");
    let data = await response.json();

    logs = data.map(item => ({
      time: item.time,
      endpoint: item.endpoint,
      status: Number(item.status),
      latency: Number(item.latency),
      error: item.error || ""
    }));

    analyzeLogs();
  } catch(error) {
    console.log("Unable to fetch Event Platform logs", error);
  }
}

setInterval(() => {
  document.getElementById("agentStatus").innerText =
  "Agent Monitoring Event Platform APIs → Analyzing → Recommending...";
  fetchRealLogs();
}, 2500);

fetchRealLogs();