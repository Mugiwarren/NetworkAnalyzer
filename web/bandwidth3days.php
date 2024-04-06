<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandwidth</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin: 0;
        }
        .container-fluid {
        height: calc(100% - 56px); /* 56px is the height of the header */
        display: flex;
        flex-direction: column;
        justify-content: center;
        width:80%;
        }
        .header, .footer {
        background-color: #F5A857;
        color: white;
        padding: 10px 0;
        text-align: center;
        }
        .footer {
        margin-top: auto; /* Pushes the footer to the bottom */
        }
        .header a {
        color: #ffffff;
        text-decoration: none;
        }

        .header a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
    <div class="header">
        <a href="index.php"><h1>Network Analyzer</a>
    </div>
    <div class="container-fluid">
        <canvas id="bandwidthChart"></canvas>
    </div>
    <script>
        const throughputData = [];
const labels = [];
const avgPreviousLinesList = [];

const xhr = new XMLHttpRequest();
xhr.open("GET", "bandwidth.txt", true);
xhr.onload = function () {
    if (this.status === 200) {
        const lines = this.responseText.trim().split("\n");
        const dataPoints = 480;
        const averagePoints = 36;
        for (let i = 0; i < lines.length; i += averagePoints) {
            let sum = 0;
            for (let j = i; j < i + averagePoints; j++) {
                sum += parseFloat(lines[j]);
            }
            const avg = sum / averagePoints;
            avgPreviousLinesList.push(avg);
            // Pushing meaningful labels for the x-axis
        }
        // Filling the beginning with zeros if needed
        for (let i = avgPreviousLinesList.length; i < dataPoints; i++) {
            avgPreviousLinesList.unshift(0);
        }
        for (let k = 0; k < dataPoints; k++){
            labels.push(k);
        }

    }
    createBandwidthChart(avgPreviousLinesList, labels);
};
xhr.send();

function createBandwidthChart(data, labels) {
    const ctx = document.getElementById("bandwidthChart").getContext("2d");
    const chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Bandwidth (last 3 days)",
                    data: data,
                    borderColor: "#F5A857",
                    fill: false,
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            title: {
                display: true,
                text: "Bandwidth (last 3 days)",
            },
            scales: {
                x: {
                    type: "linear",
                    position: "bottom",
                    suggestedMax: 480,
                    ticks: {
                        callback: function (value, index, values) {
                            const now = new Date();
                            const oneDayAgo = new Date(now.getTime() - 24 * 60 * 60 * 1000); // 1 day ago
                            const twoDaysAgo = new Date(now.getTime() - 2 * 24 * 60 * 60 * 1000); // 2 days ago
                            const threeDaysAgo = new Date(now.getTime() - 3 * 24 * 60 * 60 * 1000); // 3 days ago
                            
                            const ticksCount = data.length;
                            const tickIndex = Math.round(index * (ticksCount - 1) / (values.length - 1));
                            if (tickIndex === 0) {
                                return threeDaysAgo.toLocaleDateString();
                            } else if (tickIndex <= 190 && tickIndex >= 130) { // Tier du graphe il y a 2 jours
                                return twoDaysAgo.toLocaleDateString();
                            } else if (tickIndex <= 350 && tickIndex >= 300) { // Deuxième tier hier
                                return oneDayAgo.toLocaleDateString();
                            } else if (tickIndex === ticksCount - 1) { // Dernier point aujourd'hui
                                return now.toLocaleDateString();
                            } else {
                                return "";
                            }

                        },
                    },
                    grid: {
                        display: false,
                    },
                },
                y: {
                    suggestedMin: 0,
                    suggestedMax: 20000,
                    title: {
                        display: true,
                        text: "Throughput (Kbps)",
                    },
                },
            },
        },
    });
}
    </script>
    <footer class="footer">
        <p>Reykjavik University - Spring 2024</p>
    </footer>
    </body>
    </html>