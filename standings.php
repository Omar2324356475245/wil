<?php 
$pageTitle = "Standings & Rankings";
include 'includes/header.php'; 

// Get standings
$standings = $pdo->query("SELECT *, (goals_scored - goals_conceded) as goal_difference 
    FROM teams 
    ORDER BY points DESC, goal_difference DESC, goals_scored DESC")->fetchAll();

// Calculate matches played for each team
foreach ($standings as &$team) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM matches WHERE team1_id = ? OR team2_id = ?");
    $stmt->execute([$team['id'], $team['id']]);
    $team['matches_played'] = $stmt->fetchColumn();
    
    // Calculate wins, draws, losses
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN (team1_id = ? AND team1_goals > team2_goals) OR (team2_id = ? AND team2_goals > team1_goals) THEN 1 ELSE 0 END) as wins,
        SUM(CASE WHEN (team1_id = ? OR team2_id = ?) AND team1_goals = team2_goals THEN 1 ELSE 0 END) as draws,
        SUM(CASE WHEN (team1_id = ? AND team1_goals < team2_goals) OR (team2_id = ? AND team2_goals < team1_goals) THEN 1 ELSE 0 END) as losses
        FROM matches 
        WHERE team1_id = ? OR team2_id = ?");
    $stmt->execute([$team['id'], $team['id'], $team['id'], $team['id'], $team['id'], $team['id'], $team['id'], $team['id']]);
    $stats = $stmt->fetch();
    $team['wins'] = $stats['wins'] ?? 0;
    $team['draws'] = $stats['draws'] ?? 0;
    $team['losses'] = $stats['losses'] ?? 0;
}
?>

<div class="card mb-4 shadow">
    <div class="card-header bg-warning text-dark">
        <h2 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Tournament Standings & Rankings</h2>
    </div>
    <div class="card-body">
        <?php if (count($standings) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-award-fill"></i> Pos</th>
                        <th><i class="bi bi-mortarboard-fill"></i> Team</th>
                        <th class="text-center"><i class="bi bi-play-circle-fill"></i> P</th>
                        <th class="text-center"><i class="bi bi-check-circle-fill text-success"></i> W</th>
                        <th class="text-center"><i class="bi bi-dash-circle-fill text-warning"></i> D</th>
                        <th class="text-center"><i class="bi bi-x-circle-fill text-danger"></i> L</th>
                        <th class="text-center"><i class="bi bi-arrow-up-circle-fill text-success"></i> GF</th>
                        <th class="text-center"><i class="bi bi-arrow-down-circle-fill text-danger"></i> GA</th>
                        <th class="text-center"><i class="bi bi-calculator-fill"></i> GD</th>
                        <th class="text-center"><i class="bi bi-star-fill text-warning"></i> Pts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $index => $team): 
                        $position = $index + 1;
                        $medal = '';
                        $rowClass = '';
                        if ($position == 1) {
                            $medal = '<span class="fs-4">🥇</span>';
                            $rowClass = 'table-warning';
                        } elseif ($position == 2) {
                            $medal = '<span class="fs-4">🥈</span>';
                            $rowClass = 'table-light';
                        } elseif ($position == 3) {
                            $medal = '<span class="fs-4">🥉</span>';
                            $rowClass = 'table-light';
                        }
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td class="fw-bold"><?= $medal ?> <?= $position ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($team['class_name']) ?></td>
                        <td class="text-center"><?= $team['matches_played'] ?></td>
                        <td class="text-center"><span class="badge bg-success"><?= $team['wins'] ?></span></td>
                        <td class="text-center"><span class="badge bg-warning text-dark"><?= $team['draws'] ?></span></td>
                        <td class="text-center"><span class="badge bg-danger"><?= $team['losses'] ?></span></td>
                        <td class="text-center text-success fw-bold"><?= $team['goals_scored'] ?></td>
                        <td class="text-center text-danger fw-bold"><?= $team['goals_conceded'] ?></td>
                        <td class="text-center">
                            <span class="badge <?= $team['goal_difference'] > 0 ? 'bg-success' : ($team['goal_difference'] < 0 ? 'bg-danger' : 'bg-secondary') ?>">
                                <?= $team['goal_difference'] > 0 ? '+' : '' ?><?= $team['goal_difference'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary fs-6"><?= $team['points'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Legend -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle-fill"></i> Table Legend</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Pos</strong> - Position</li>
                            <li><strong>P</strong> - Matches Played</li>
                            <li><strong>W</strong> - Wins</li>
                            <li><strong>D</strong> - Draws</li>
                            <li><strong>L</strong> - Losses</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li><strong>GF</strong> - Goals For</li>
                            <li><strong>GA</strong> - Goals Against</li>
                            <li><strong>GD</strong> - Goal Difference</li>
                            <li><strong>Pts</strong> - Points (Win: 3, Draw: 1, Loss: 0)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Scorers (Teams) -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-up-circle-fill"></i> Best Attack</h6>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered">
                            <?php 
                            $topScorers = $pdo->query("SELECT * FROM teams ORDER BY goals_scored DESC LIMIT 5")->fetchAll();
                            foreach ($topScorers as $team): 
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?= htmlspecialchars($team['class_name']) ?></div>
                                    </div>
                                    <span class="badge bg-success rounded-pill"><?= $team['goals_scored'] ?> goals</span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-shield-fill-check"></i> Best Defense</h6>
                    </div>
                    <div class="card-body">
                        <ol class="list-group list-group-numbered">
                            <?php 
                            $bestDefense = $pdo->query("SELECT * FROM teams ORDER BY goals_conceded ASC LIMIT 5")->fetchAll();
                            foreach ($bestDefense as $team): 
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?= htmlspecialchars($team['class_name']) ?></div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?= $team['goals_conceded'] ?> conceded</span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 5rem; color: #ccc;"></i>
                <h4 class="text-muted mt-3">No Teams Available</h4>
                <p class="text-muted">Add teams to start tracking standings</p>
                <a href="teams.php" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Add Teams</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
