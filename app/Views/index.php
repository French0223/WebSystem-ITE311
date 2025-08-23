<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITE311-LABASA - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h1 class="text-center mb-4 text-primary">Welcome to ITE311-LABASA</h1>
                        
                        <nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded mb-4">
                            <div class="container-fluid">
                                <ul class="navbar-nav mx-auto">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="<?= site_url("/") ?>">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= site_url("about") ?>">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= site_url("contact") ?>">Contact</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                        
                        <div class="content">
                            <h2 class="mb-3">Homepage</h2>
                            <p class="lead">Welcome to our CodeIgniter project! This is the homepage of ITE311-LABASA.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
