<!DOCTYPE html>
<html>
<head>
    <title>IP Blacklist</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <style>
         body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa; /* Ajout d'une couleur de fond */
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }

        form {
            width: 100%;
            max-width: 400px; /* Réduit la largeur du formulaire */
            margin-bottom: 20px; /* Ajout d'un espace en bas */
        }

        input[type="text"], input[type="submit"], input[type="reset"] {
            width: 100%;
            padding: 10px; /* Ajuste le rembourrage pour une meilleure expérience utilisateur */
            margin-bottom: 10px; /* Ajout d'un espace entre les éléments du formulaire */
            border: 1px solid #ced4da; /* Ajout d'une bordure */
            border-radius: 4px; /* Ajout d'un peu de bordure */
        }

        input[type="submit"], input[type="reset"] {
            background-color: #007bff; /* Change la couleur de fond des boutons */
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s; /* Ajoute une transition fluide */
        }

        input[type="submit"]:hover, input[type="reset"]:hover {
            background-color: #0056b3; /* Change la couleur de fond au survol */
        }

        .table {
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
            overflow-y: auto;
            border-collapse: collapse; /* Fusionne les bordures de la table */
        }

        th, td {
            padding: 8px; /* Ajuste le rembourrage des cellules */
            border: 1px solid #ddd; /* Ajoute des bordures aux cellules */
            text-align: center;
        }

        .header, .footer {
            background-color: #F5A857;
            color: white;
            padding: 10px 0;
            text-align: center;
            width: 100%;
        }

        .header a {
            color: white;
            text-decoration: none;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: auto; /* Pousse le pied de page en bas */
        }

        </style>
</head>
<body>
    <div class="header">
        <a href="index.php"><h1>Network Analyzer</a>
    </div>
    <div class="content-wrapper">
        <?php
        function redirect($url) {
            if (!headers_sent()) {
                header('Location: ' . $url);
                exit;
            } else {
                echo '<script type="text/javascript">';
                echo 'window.location.href = "' . $url . '";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
                echo '</noscript>';
                exit;
            }
        }
        $file = 'data/blacklistedips.txt';
        $ips = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // Traiter la suppression d'IP
    if (isset($_POST['delete_ip'])) {
        $delete_ip = $_POST['delete_ip'];
        $file = 'data/blacklistedips.txt';
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $new_lines = array();

        foreach ($lines as $line) {
            $line_parts = explode("#", $line);
            if (count($line_parts) >= 1 && trim($line_parts[0]) != $delete_ip) {
                $new_lines[] = $line;
            }
        }

        file_put_contents($file, implode(PHP_EOL, $new_lines));
        redirect($_SERVER['PHP_SELF']);
    }
        // Traiter l'ajout d'IP
    if (isset($_POST['add_ip'])) {
        $add_ip = $_POST['add_ip'];
        $add_host = $_POST['add_host'];
        $file = 'data/blacklistedips.txt';
        $timestamp = date('Y-m-d H:i:s');

        // Vérifier si le fichier se termine par un saut de ligne
        $fileContent = file_get_contents($file);
        if (substr(trim($fileContent), -1) !== PHP_EOL) {
            $fileContent .= PHP_EOL;
            file_put_contents($file, $fileContent);
        }

        $new_line = "{$add_ip}    # {$timestamp}, {$add_host}\n" . PHP_EOL;
        file_put_contents($file, $new_line, FILE_APPEND);
        redirect($_SERVER['PHP_SELF']);
    }

        // Réinitialiser la recherche si le bouton de réinitialisation est cliqué
        if (isset($_POST['reset_search'])) {
            $search = '';
            $filteredIps = $ips;
        }
        // Fonction de recherche d'IP
        function searchIP($ips, $search) {
            if (empty($search)) {
                return $ips;
            }
            return array_filter($ips, function($ip) use ($search) {
                $line_parts = explode("#", $ip);
                if (count($line_parts) >= 2) {
                    $ip_address = trim($line_parts[0]);
                    $host_part = trim($line_parts[1]);
                    $host_parts = explode(",", $host_part);
                    if (count($host_parts) >= 2) {
                        $host = trim($host_parts[1]);
                        return strpos($ip_address, $search) !== false || strpos($host, $search) !== false;
                    }
                }
                return false;
            });
        }
        // Rechercher une IP si le formulaire de recherche est soumis
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $filteredIps = searchIP($ips, $search);
        ?>
        <!-- Barre de recherche -->
        <form action="" method="post">
            <input type="text" id="searchInput" name="search" placeholder="Search IP or Host" value="<?php echo htmlspecialchars($search); ?>">
        </form>
        <!-- Tableau pour afficher les adresses IP -->
        <table id="ipTable" class="table table-striped">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Host</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($filteredIps as $ip) {
                    $line_parts = explode("#", $ip);
                    if (count($line_parts) >= 2) {
                        $ip_address = trim($line_parts[0]);
                        $host_part = trim($line_parts[1]);
                        $host_parts = explode(",", $host_part);
                        if (count($host_parts) >= 2) {
                            $host = trim($host_parts[1]);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ip_address); ?></td>
                                <td><?php echo htmlspecialchars($host); ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <!-- Formulaire pour supprimer une IP -->
        <form action="" method="post" class="form-inline mb-3">
            <div class="input-group mr-sm-2">
                <input type="text" name="delete_ip" class="form-control" placeholder="Delete IP">
            </div>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>

        <!-- Formulaire pour ajouter une IP -->
        <form action="" method="post" class="mb-3">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <input type="text" name="add_ip" class="form-control mb-2" placeholder="Add IP">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Add</button>
                </div>
                <div class="col-auto">
                    <input type="text" name="add_host" class="form-control mb-2" placeholder="Add host name">
                </div>
                
            </div>
        </form>
    </div>
    <footer class="footer">
        <p>Reykjavik University - Spring 2024</p>
    </footer>
    <script>
    document.getElementById('searchInput').addEventListener('input', searchIP);
    document.getElementById('searchButton').addEventListener('click', searchIP);

    function searchIP() {
        const searchTerm = document.getElementById('searchInput').value.trim();
        const tableRows = document.querySelectorAll('#ipTable tbody tr');

        for (const row of tableRows) {
            const ipAddress = row.cells[0].textContent.trim();
            const hostName = row.cells[1].textContent.trim();

            if (ipAddress.includes(searchTerm) || hostName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
    </script>
</body>
</html>