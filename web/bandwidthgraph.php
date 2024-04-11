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
        width:70%;
        height:80%;
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
    // Arrays to store throughput data and labels
    const throughputDataDown = [];
    const throughputDataUp = [];
    const labels = [];
    const lastHourDataPoints = 240;

    const xhr = new XMLHttpRequest();
    const randomParam = '?t=' + new Date().getTime();
    xhr.open("GET", "bandwidth.txt" + randomParam, true);
    xhr.onload = function () {
    if (this.status === 200) {
        const lines = this.responseText.trim().split("\n");
        console.log("lines:", lines); 
        const startIndex = Math.max(lines.length - lastHourDataPoints, 0);
        const dataPoints = 240;
        // processes data
        for (let i = 0; i < lastHourDataPoints; i++) {
            const line = lines[lines.length - i];
            if (line) { 
                const values = line.split(";").map(Number);
                throughputDataDown.push(values[0]);
                throughputDataUp.push(values[1]);
                labels.push(lastHourDataPoints - (i - startIndex));
            }
        }
            
        
        console.log("throughputDataDown:", throughputDataDown); 
        console.log("throughputDataUp:", throughputDataUp); 
        console.log("labels:", labels);

        createBandwidthChart(throughputDataDown, throughputDataUp, labels);
    }
};
    xhr.send();
    //Creates a chart
    function createBandwidthChart(dataDown, dataUp, labels) {
        const ctx = document.getElementById("bandwidthChart").getContext("2d");
        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Bandwidth Down (last hour)",
                        data: dataDown,
                        borderColor: "#F5A857",
                        fill: false,
                    },
                    {
                        label: "Bandwidth Up (last hour)",
                        data: dataUp,
                        borderColor: "#3498DB",
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
                        callback: function (value, index, values) {
                            if (index === values.length - 1) {
                                var now = new Date();
                                var heure = now.getHours();
                                var minute = now.getMinutes();
                                return heure + ":" + minute.toString().padStart(2, "0");
                            }
                        },
                    },
                    grid: {
                        display: false,
                    },
                },
                y: {
                suggestedMin: 0,
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