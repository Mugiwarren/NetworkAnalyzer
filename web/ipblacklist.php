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
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .header, .footer {
            background-color: #F5A857;
            color: white;
            padding: 10px 0;
            text-align: center;
            width: 100%;
            position: fixed;
            left: 0;
            z-index: 1000;
        }
        .header {
            top: 0;
        }
        .footer {
            bottom: 0;
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
            max-width: 600px;
            margin: 10px 0;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            max-width: 250px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
            overflow-y: auto;
            height: 300px;
            display: block;
        }
        th, td {
            text-align: center;
        }
        /* Ajoutez cette section pour ajouter un padding au contenu de la page pour éviter qu'il ne soit caché derrière le header et le footer */
        .content-wrapper {
            padding-top: 60px; /* La hauteur du header */
            padding-bottom: 60px; /* La hauteur du footer */
            box-sizing: border-box;
        }
        input[type="submit"], input[type="reset"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 5px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            transition-duration: 0.4s;
        }

        input[type="submit"]:hover, input[type="reset"]:hover {
            background-color: #3e8e41;
        }

        input[type="reset"] {
            background-color: #f44336;
        }

        input[type="reset"]:hover {
            background-color: #e53935;
        }
        .header a {
        color: #ffffff;
        text-decoration: none;
        }

        .header a:hover {
            text-decoration: underline;
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
        $file = '../data/blacklistedips.txt';
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
        $file = '../data/blacklistedips.txt';
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
        <form action="" method="post">
            <input type="text" name="delete_ip" placeholder="Delete IP">
            <input type="submit" value="Delete">
        </form>
        <!-- Formulaire pour ajouter une IP -->
        <form action="" method="post">
            <input type="text" name="add_ip" placeholder="Add IP"><br>
            <input type="text" name="add_host" placeholder="Add host name">
            <input type="submit" value="Add">
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