<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>applicationsRequest</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      margin: 0;
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
    .form-group{
        width:15%;
        justify-content:center;
        text-align:center;
        margin-left:41%;
        padding-top: 15px;
    }
    .graph-container {
        display: flex;
        justify-content: center;
        margin-top: 50px; /* Ajustez la marge supérieure selon vos besoins */
    }
    canvas {
        margin: 0 auto;
        max-width: 600px;
        margin-top: 10px; /* Ajustez la marge supérieure du canvas selon vos besoins */
    }
    .no-data-message {
        text-align: center;
        font-size: 20px;
        margin-top: 25px;
    }
  </style>
</head>
<body>
    <div class="header">
        <a href="index.php"><h1>Network Analyzer</a>
    </div>
    <div id = "errorMessage"></div>
    <div class="container-fluid">
        <div class="form-group text-center">
        <label for="portsSelect">Select a Port:</label>
        <select class="form-control" id="portsSelect">
        <option value="" disabled>Select one--</option>
        </select>
    </div>

        <div id="portData" class="graph-container">
            <!-- Graphs for the selected port will be displayed here -->
        </div>
    </div>
    <footer class="footer">
        <p>Reykjavik University - Spring 2024</p>
    </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Load ports data and populate select options
    $(document).ready(function() {
        $.get("data/ports.txt", function(data) {
            const ports = data.trim().split("\n");
            const portsSelect = document.getElementById("portsSelect");

            ports.forEach(function(port) {
                const option = document.createElement("option");
                option.text = port;
                portsSelect.add(option);
            });
        });
    });

    // Function to handle port selection
    document.getElementById("portsSelect").addEventListener("change", function() {
        const selectedPort = this.value;

        // Clear the previous graphs
        document.getElementById("portData").innerHTML = "";

        // Load data for the selected port and create graphs
        loadData(selectedPort);
    });

    // Function to load and display data for the selected port
    function loadData(selectedPort) {
    $.ajax({
        url: "data/ports/history_" + selectedPort + ".csv",
        type: 'HEAD',
        error: function () {
            // File does not exist, create graphs with zero values
            //document.getElementById("errorMessage").innerHTML = '<div class="no-data-message">No data available</div>';
            createGraphs(Array(10).fill(0), Array(10).fill(0), 0);
        },
        success: function () {
            // File exists, load and display data for the selected port
            //document.getElementById("errorMessage").innerHTML = '<div class="no-data-message"></div>';
            $.get("data/ports/history_" + selectedPort + ".csv", function(data) {
                // Split the CSV data into lines
                const lines = data.trim().split("\n");

                // Extract the first values from each line to create data for the first graph
                const firstValues = lines.map(function(line) {
                    return parseFloat(line.split(";")[0]);
                });

                // Calculate the average of the second column
                const secondColumnValues = lines.map(function(line) {
                    return parseFloat(line.split(";")[1]);
                });
                const averageSecondColumn = secondColumnValues.reduce((acc, val) => acc + val, 0) / secondColumnValues.length;

                createGraphs(firstValues, secondColumnValues, averageSecondColumn);
            });
        }
    });
}

    // Function to create graphs
    function createGraphs(firstValues, secondColumnValues, averageSecondColumn) {
        // Create a canvas element for the first graph
        const canvas1 = document.createElement("canvas");
        canvas1.width = 800; // Largeur ajustée
        canvas1.height = 600; // Hauteur ajustée
        document.getElementById("portData").appendChild(canvas1);

        // Create the first graph using Chart.js
        new Chart(canvas1.getContext("2d"), {
            type: "line",
            data: {
                labels: Array.from({ length: firstValues.length }, (_, i) => i + 1),
                datasets: [{
                    label: "Number of requests received",
                    data: firstValues,
                    borderColor: "blue",
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: "Number of requests"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Time (minutes ago)"
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                const lastIndex = values.length - 1;
                                const distance = lastIndex - index;
                                if (distance % 4 === 0) {
                                    const minutes = Math.ceil(distance / 4);
                                    return `${minutes}min`;
                                }
                            }
                        }
                    }
                }
            }
        });

        // Create a canvas element for the second graph
        const canvas2 = document.createElement("canvas");
        canvas2.width = 800; // Largeur ajustée
        canvas2.height = 600; // Hauteur ajustée
        document.getElementById("portData").appendChild(canvas2);

        // Create the second graph using Chart.js
        new Chart(canvas2.getContext("2d"), {
            type: "bar",
            data: {
                labels: Array.from({ length: secondColumnValues.length }, (_, i) => i + 1),
                datasets: [{
                    label: "Packet size",
                    data: secondColumnValues,
                    backgroundColor: "green",
                    borderColor: "green",
                    borderWidth: 1
                }, {
                    type: "line",
                    label: "Average Packet Size",
                    data: Array(secondColumnValues.length).fill(averageSecondColumn),
                    borderColor: "red",
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0, // Supprimer les points de la ligne rouge
                    pointHoverRadius: 0 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: "Packet size"
                        }
                    },
                    x: {
                        title: {
                            display: true,
                        }
                    }
                }
            }
        });
    }
  </script>
</body>
</html>
