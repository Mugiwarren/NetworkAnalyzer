<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Network Analyzer</title>
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
  </style>
</head>
<body>
    <div class="header">
        <a href="index.php"><h1>Network Analyzer</a>
    </div>

  <footer class="footer">
    <p>Reykjavik University - Spring 2024</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
