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
      margin: 0;
    }
    .container-fluid {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .header, .footer {
      background-color: #F5A857;
      color: white;
      padding: 20px 0;
      text-align: center;
    }
    .footer {
      margin-top: auto; /* Pushes the footer to the bottom */
    }
    .card {
      width: 100%;
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card-title {
      font-size: 1.5rem;
      margin-bottom: 15px;
    }
    .btn-primary {
      width: 100%;
      background-color: #6c757d; /* Gris sobre */
      color: white; /* Texte blanc */
      border: none;
      font-weight: bold; /* Texte en gras */
    }
    .btn-primary:hover {
      background-color: #5a6268; /* Gris légèrement plus foncé au survol */
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Network Analyzer</h1>
  </div>

  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-6 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Bandwidth</h5>
            <form action="bandwidthgraph.php" method="get">
                <button type="submit" class="btn btn-primary">Last hour</button>
            </form>
            <form action="bandwidth3days.php" method="get">
                <button type="submit" class="btn btn-primary mt-3">Last 3 days</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Applications Requests</h5>
            <br>
            <form action="applicationsRequest.php" method="get">
                <button type="submit" class="btn btn-primary">View Requests</button><br><br>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Warnings</h5>
            <form action="warnings.php" method="get">
                <button type="submit" class="btn btn-primary">View Warnings</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Blacklisted IPs</h5>
            <form action="ipblacklist.php" method="get">
                <button type="submit" class="btn btn-primary">View Blacklisted IPs</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <p><b>Reykjavik University - Spring 2024</b></p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
