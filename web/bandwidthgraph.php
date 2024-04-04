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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
    <div class="header">
        <h1>Network Analyzer</h1>
    </div>
    <div class="container-fluid">
        <canvas id="bandwidthChart"></canvas>
    </div>
    <script>
        const throughputData = [];
        const labels = [];
        const lastHourDataPoints = 240;

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../data/bandwidth.txt", true);
        xhr.onload = function () {
        if (this.status === 200) {
            const lines = this.responseText.trim().split("\n");
            const startIndex = Math.max(lines.length - lastHourDataPoints, 0);

            for (let i = startIndex; i < lines.length; i++) {
            const value = parseFloat(lines[i]);
            throughputData.push(value);
            labels.push(lastHourDataPoints - (i - startIndex));
            }

            
            createBandwidthChart(throughputData, labels);
            
        }
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
                label: "Bandwidth (last hour)",
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
                text: "Bandwidth (last hour)",
            },
            scales: {
                x: {
                    type: "linear",
                    position: "bottom",
                    suggestedMax: lastHourDataPoints,
                    ticks: {
                        stepSize: 40, // 10 minutes en points (4 points par minute * 10 minutes)
                        callback: function (value, index, values) {
                            if (value === 0 || value % 40 !== 0) {
                            return "";
                            }

                            const now = new Date();
                            const oneHourAgo = new Date(now.getTime() - 3600000); // Il y a une heure
                            const diffMinutes = oneHourAgo.getTime() / 60000 + value / 4;
                            const chartDate = new Date(diffMinutes * 60000);

                            const minutes = chartDate.getMinutes();
                            const hours = chartDate.getHours();

                            return `${hours}:${minutes.toString().padStart(2, "0")}`;
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
        function createBandwidthChart3days(data, labels) {
        const ctx = document.getElementById("bandwidthChart").getContext("2d");
        const chart = new Chart(ctx, {
            type: "line",
            data: {
            labels: labels,
            datasets: [
                {
                label: "Bandwidth (last hour)",
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
                text: "Bandwidth (last hour)",
            },
            scales: {
                x: {
                    type: "linear",
                    position: "bottom",
                    suggestedMax: lastHourDataPoints,
                    ticks: {
                        stepSize: 40, // 10 minutes en points (4 points par minute * 10 minutes)
                        callback: function (value, index, values) {
                            if (value === 0 || value % 40 !== 0) {
                            return "";
                            }

                            const now = new Date();
                            const oneHourAgo = new Date(now.getTime() - 3600000); // Il y a une heure
                            const diffMinutes = oneHourAgo.getTime() / 60000 + value / 4;
                            const chartDate = new Date(diffMinutes * 60000);

                            const minutes = chartDate.getMinutes();
                            const hours = chartDate.getHours();

                            return `${hours}:${minutes.toString().padStart(2, "0")}`;
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