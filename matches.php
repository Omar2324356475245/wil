<?php 
$pageTitle = "Matches Management";
include 'includes/header.php'; 

// Handle Add Match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_match'])) {
    $team1_id = $_POST['team1_id'];
    $team2_id = $_POST['team2_id'];
    $team1_goals = $_POST['team1_goals'];
    $team2_goals = $_POST['team2_goals'];
    $match_date = $_POST['match_date'];
    
    if ($team1_id == $team2_id) {
        echo '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle-fill"></i> Error: A team cannot play against itself!</div>';
    } else {
        try {
            // Insert match
            $stmt = $pdo->prepare("INSERT INTO matches (team1_id, team2_id, team1_goals, team2_goals, match_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$team1_id, $team2_id, $team1_goals, $team2_goals, $match_date]);
            
            // Update team1 stats
            if ($team1_goals > $team2_goals) {
                $team1_points = 3; // Win
            } elseif ($team1_goals == $team2_goals) {
                $team1_points = 1; // Draw
            } else {
                $team1_points = 0; // Loss
            }
            
            $stmt = $pdo->prepare("UPDATE teams SET points = points + ?, goals_scored = goals_scored + ?, goals_conceded = goals_conceded + ? WHERE id = ?");
            $stmt->execute([$team1_points, $team1_goals, $team2_goals, $team1_id]);
            
            // Update team2 stats
            if ($team2_goals > $team1_goals) {
                $team2_points = 3; // Win
            } elseif ($team2_goals == $team1_goals) {
                $team2_points = 1; // Draw
            } else {
                $team2_points = 0; // Loss
            }
            
            $stmt = $pdo->prepare("UPDATE teams SET points = points + ?, goals_scored = goals_scored + ?, goals_conceded = goals_conceded + ? WHERE id = ?");
            $stmt->execute([$team2_points, $team2_goals, $team1_goals, $team2_id]);
            
            echo '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Match recorded successfully!</div>';
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle-fill"></i> Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Handle Delete Match
if (isset($_GET['delete_id'])) {
    try {
        // Get match details first
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        $match = $stmt->fetch();
        
        if ($match) {
            // Reverse team1 stats
            if ($match['team1_goals'] > $match['team2_goals']) {
                $team1_points = 3;
            } elseif ($match['team1_goals'] == $match['team2_goals']) {
                $team1_points = 1;
            } else {
                $team1_points = 0;
            }
            
            $stmt = $pdo->prepare("UPDATE teams SET points = points - ?, goals_scored = goals_scored - ?, goals_conceded = goals_conceded - ? WHERE id = ?");
            $stmt->execute([$team1_points, $match['team1_goals'], $match['team2_goals'], $match['team1_id']]);
            
            // Reverse team2 stats
            if ($match['team2_goals'] > $match['team1_goals']) {
                $team2_points = 3;
            } elseif ($match['team2_goals'] == $match['team1_goals']) {
                $team2_points = 1;
            } else {
                $team2_points = 0;
            }
            
            $stmt = $pdo->prepare("UPDATE teams SET points = points - ?, goals_scored = goals_scored - ?, goals_conceded = goals_conceded - ? WHERE id = ?");
            $stmt->execute([$team2_points, $match['team2_goals'], $match['team1_goals'], $match['team2_id']]);
            
            // Delete match
            $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
            $stmt->execute([$_GET['delete_id']]);
            
            echo '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Match deleted successfully!</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle-fill"></i> Error: ' . $e->getMessage() . '</div>';
    }
}

// Get all teams for dropdown
$teams = $pdo->query("SELECT * FROM teams ORDER BY class_name")->fetchAll();
?>

<div class="card mb-4 shadow">
    <div class="card-header bg-success text-white">
        <h2 class="mb-0"><i class="bi bi-clipboard-check-fill"></i> Matches Management</h2>
    </div>
    <div class="card-body">
        <!-- Add Match Form -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-plus-circle-fill"></i> Record New Match</h5>
            </div>
            <div class="card-body">
                <?php if (count($teams) >= 2): ?>
                <form method="post" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label"><i class="bi bi-1-circle-fill"></i> Team 1</label>
                        <select class="form-select" name="team1_id" required>
                            <option value="">Select Team 1</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Goals</label>
                        <input type="number" class="form-control text-center" name="team1_goals" min="0" value="0" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label"><i class="bi bi-2-circle-fill"></i> Team 2</label>
                        <select class="form-select" name="team2_id" required>
                            <option value="">Select Team 2</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label"><i class="bi bi-calendar3"></i> Match Date & Time</label>
                        <input type="datetime-local" class="form-control" name="match_date" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Goals</label>
                        <input type="number" class="form-control text-center" name="team2_goals" min="0" value="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" name="add_match" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle-fill"></i> Record Match
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i> You need at least 2 teams to record a match. 
                        <a href="teams.php" class="alert-link">Add teams first</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Matches Table -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Matches (<?= $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn() ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th><i class="bi bi-calendar3"></i> Date & Time</th>
                                <th colspan="3" class="text-center"><i class="bi bi-trophy-fill"></i> Match Result</th>
                                <th class="text-center"><i class="bi bi-gear-fill"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $matches = $pdo->query("SELECT m.*, t1.class_name as team1_name, t2.class_name as team2_name 
                                FROM matches m 
                                JOIN teams t1 ON m.team1_id = t1.id 
                                JOIN teams t2 ON m.team2_id = t2.id 
                                ORDER BY m.match_date DESC")->fetchAll();
                            
                            if (count($matches) > 0):
                                foreach ($matches as $index => $match): 
                                    $result_class = '';
                                    $result_icon = '';
                                    if ($match['team1_goals'] > $match['team2_goals']) {
                                        $result_class = 'table-success';
                                        $result_icon = '<i class="bi bi-trophy-fill text-warning"></i>';
                                    } elseif ($match['team1_goals'] < $match['team2_goals']) {
                                        $result_class = 'table-danger';
                                        $result_icon = '<i class="bi bi-trophy-fill text-warning"></i>';
                                    } else {
                                        $result_class = 'table-warning';
                                        $result_icon = '<i class="bi bi-dash-circle-fill"></i>';
                                    }
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <small><i class="bi bi-calendar-event"></i> <?= date('d M Y', strtotime($match['match_date'])) ?><br>
                                        <i class="bi bi-clock"></i> <?= date('H:i', strtotime($match['match_date'])) ?></small>
                                    </td>
                                    <td class="text-end <?= $match['team1_goals'] > $match['team2_goals'] ? 'fw-bold' : '' ?>">
                                        <?= htmlspecialchars($match['team1_name']) ?>
                                        <?= $match['team1_goals'] > $match['team2_goals'] ? $result_icon : '' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6"><?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></span>
                                    </td>
                                    <td class="<?= $match['team2_goals'] > $match['team1_goals'] ? 'fw-bold' : '' ?>">
                                        <?= $match['team2_goals'] > $match['team1_goals'] ? $result_icon : '' ?>
                                        <?= htmlspecialchars($match['team2_name']) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete_id=<?= $match['id'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure? This will update team statistics.')" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No matches recorded yet. Add your first match above!</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
