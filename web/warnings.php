<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Network Analyzer - Warnings</title>
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
    .table-container {
      max-width: 80%; /* Set max width for the table container */
      margin: auto; /* Center the container horizontally */
    }
    .warnings {
      text-align: center;
      color: red;
      font-size: 24px;
      margin-bottom: 20px; /* Add margin to separate from the table */
      padding-top:10px;
    }
  </style>
</head>
<body>
    <div class="header">
        <a href="index.php"><h1>Network Analyzer</h1></a>
    </div>
    <div class="warnings"><h2>Warnings</h2></div> 
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Destination</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
            <?php
              // Chemin du dossier des logs
              $logDir = 'data/logs';

              // Parcourir les fichiers dans le dossier des logs
              $files = scandir($logDir);

              // Tableau pour stocker les détails des fichiers
              $fileDetails = array();

              foreach ($files as $file) {
                  if ($file !== '.' && $file !== '..') {
                      // Vérifier si le fichier est un fichier JSON
                      if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                          // Extraire les informations du nom du fichier
                          $fileNameParts = explode('_', $file);
                          $dateTime = $fileNameParts[0];
                          $date = str_replace('-', '/', substr($dateTime, 0, 10)); // Remplacer "-" par "/"
                          $time = str_replace('-', ':', substr($dateTime, 11, 8)); // Remplacer "-" par ":"

                          // Lire le contenu JSON du fichier
                          $content = file_get_contents($logDir . '/' . $file);
                          $json = json_decode($content);

                          // Extraire les informations du contenu JSON
                          $message = $json->message;
                          $type = $json->type;
                          if(isset($json->destination)){
                            $destination = $json->destination;
                          } else {
                            $destination = "";
                          }

                          // Stocker les détails du fichier dans le tableau
                          $fileDetails[] = array(
                              'date' => $date,
                              'time' => $time,
                              'type' => $type,
                              'destination' => $destination,
                              'message' => $message
                          );
                      } else {
                          // Afficher un avertissement si le fichier n'est pas un fichier JSON
                          echo "<tr><td colspan='5'>Not a JSON file: $file</td></tr>";
                      }
                  }
              }

              // Trier les fichiers par date et heure (les plus récents d'abord)
              array_multisort(array_column($fileDetails, 'date'), SORT_DESC, array_column($fileDetails, 'time'), SORT_DESC, $fileDetails);

              // Afficher les détails des fichiers triés dans le tableau HTML
              foreach ($fileDetails as $fileDetail) {
                  echo "<tr>";
                  echo "<td>{$fileDetail['date']}</td>";
                  echo "<td>{$fileDetail['time']}</td>";
                  echo "<td><b>{$fileDetail['type']}</b></td>";
                  echo "<td>{$fileDetail['destination']}</td>";
                  echo "<td>{$fileDetail['message']}</td>";
                  echo "</tr>";
              }
              ?>


            </tbody>
        </table>
    </div>

  <footer class="footer">
    <p>Reykjavik University - Spring 2024</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
