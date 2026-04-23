<?php 
$pageTitle = "Home - Class Tournament";
include 'includes/header.php'; 

// Get statistics
$totalTeams = $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn();
$totalMatches = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();
$totalGoals = $pdo->query("SELECT SUM(team1_goals + team2_goals) FROM matches")->fetchColumn();

// Get top 3 teams
$topTeams = $pdo->query("SELECT * FROM teams ORDER BY points DESC, (goals_scored - goals_conceded) DESC LIMIT 3")->fetchAll();

// Get recent matches
$recentMatches = $pdo->query("SELECT m.*, t1.class_name as team1_name, t2.class_name as team2_name 
    FROM matches m 
    JOIN teams t1 ON m.team1_id = t1.id 
    JOIN teams t2 ON m.team2_id = t2.id 
    ORDER BY m.match_date DESC LIMIT 5")->fetchAll();
?>

<div class="row">
    <div class="col-md-12">
        <div class="jumbotron bg-primary text-white p-5 rounded mb-4">
            <h1 class="display-4"><i class="bi bi-trophy-fill"></i> Welcome to Class Football Tournament</h1>
            <p class="lead">Track and manage your school football tournament efficiently</p>
            <hr class="my-4 bg-white">
            <p>Manage teams, record matches, view standings, and track schedules all in one place.</p>
            <div class="mt-4">
                <a class="btn btn-light btn-lg me-2" href="teams.php" role="button"><i class="bi bi-people-fill"></i> Manage Teams</a>
                <a class="btn btn-success btn-lg me-2" href="matches.php" role="button"><i class="bi bi-clipboard-check-fill"></i> Record Match</a>
                <a class="btn btn-warning btn-lg" href="standings.php" role="button"><i class="bi bi-bar-chart-fill"></i> View Standings</a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-3"><?= $totalTeams ?></h3>
                <p class="text-muted">Total Teams</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-clipboard-check-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3"><?= $totalMatches ?></h3>
                <p class="text-muted">Matches Played</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-award-fill text-warning" style="font-size: 3rem;"></i>
                <h3 class="mt-3"><?= $totalGoals ?? 0 ?></h3>
                <p class="text-muted">Total Goals</p>
            </div>
        </div>
    </div>
</div>

<!-- Top Teams and Recent Matches -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Top 3 Teams</h5>
            </div>
            <div class="card-body">
                <?php if (count($topTeams) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topTeams as $index => $team): 
                            $badges = ['badge-gold', 'badge-silver', 'badge-bronze'];
                            $icons = ['🥇', '🥈', '🥉'];
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fs-4 me-2"><?= $icons[$index] ?></span>
                                    <strong><?= htmlspecialchars($team['class_name']) ?></strong>
                                </div>
                                <span class="badge <?= $badges[$index] ?> rounded-pill"><?= $team['points'] ?> pts</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No teams yet. <a href="teams.php">Add teams</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Matches</h5>
            </div>
            <div class="card-body">
                <?php if (count($recentMatches) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMatches as $match): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($match['team1_name']) ?></span>
                                    <span class="badge bg-primary rounded-pill"><?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></span>
                                    <span><?= htmlspecialchars($match['team2_name']) ?></span>
                                </div>
                                <small class="text-muted"><i class="bi bi-calendar3"></i> <?= date('d M Y, H:i', strtotime($match['match_date'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No matches yet. <a href="matches.php">Record a match</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <a href="teams.php" class="text-decoration-none">
                            <i class="bi bi-plus-circle-fill text-success" style="font-size: 2rem;"></i>
                            <p class="mt-2">Add Team</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="matches.php" class="text-decoration-none">
                            <i class="bi bi-clipboard-plus-fill text-primary" style="font-size: 2rem;"></i>
                            <p class="mt-2">Record Match</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="standings.php" class="text-decoration-none">
                            <i class="bi bi-list-ol text-warning" style="font-size: 2rem;"></i>
                            <p class="mt-2">View Rankings</p>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="schedule.php" class="text-decoration-none">
                            <i class="bi bi-calendar-event-fill text-info" style="font-size: 2rem;"></i>
                            <p class="mt-2">Match Schedule</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
