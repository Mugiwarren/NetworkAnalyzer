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
      background-color: orange;
      color: white;
      padding: 10px 0;
      text-align: center;
    }
    .footer {
      margin-top: auto; /* Pushes the footer to the bottom */
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Network Analyzer</h1>
  </div>

  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Link 1</h5>
            <p class="card-text">This is a link or graph</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center">
          <div class="card-body">
        
