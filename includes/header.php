<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Class Tournament' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .card-header {
            font-weight: bold;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .badge-gold {
            background-color: #FFD700;
            color: #000;
        }
        .badge-silver {
            background-color: #C0C0C0;
            color: #000;
        }
        .badge-bronze {
            background-color: #CD7F32;
            color: #fff;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-trophy-fill"></i> Class Tournament</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-fill"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="teams.php"><i class="bi bi-people-fill"></i> Teams</a></li>
                <li class="nav-item"><a class="nav-link" href="matches.php"><i class="bi bi-clipboard-check-fill"></i> Matches</a></li>
                <li class="nav-item"><a class="nav-link" href="standings.php"><i class="bi bi-bar-chart-fill"></i> Standings</a></li>
                <li class="nav-item"><a class="nav-link" href="schedule.php"><i class="bi bi-calendar3"></i> Schedule</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
