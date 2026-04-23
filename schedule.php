<?php 
$pageTitle = "Match Schedule";
include 'includes/header.php'; 

// Get all matches grouped by date
$matches = $pdo->query("SELECT m.*, t1.class_name as team1_name, t2.class_name as team2_name 
    FROM matches m 
    JOIN teams t1 ON m.team1_id = t1.id 
    JOIN teams t2 ON m.team2_id = t2.id 
    ORDER BY m.match_date DESC")->fetchAll();

// Group matches by date
$matchesByDate = [];
foreach ($matches as $match) {
    $date = date('Y-m-d', strtotime($match['match_date']));
    $matchesByDate[$date][] = $match;
}

// Separate into past and upcoming
$today = date('Y-m-d');
$upcomingMatches = [];
$pastMatches = [];

foreach ($matchesByDate as $date => $dayMatches) {
    if ($date >= $today) {
        $upcomingMatches[$date] = $dayMatches;
    } else {
        $pastMatches[$date] = $dayMatches;
    }
}

// Sort upcoming in ascending order
ksort($upcomingMatches);
// Keep past in descending order
krsort($pastMatches);
?>

<div class="card mb-4 shadow">
    <div class="card-header bg-info text-white">
        <h2 class="mb-0"><i class="bi bi-calendar3"></i> Match Schedule</h2>
    </div>
    <div class="card-body">
        
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                    <i class="bi bi-calendar-event"></i> Upcoming Matches (<?= count($upcomingMatches) > 0 ? array_sum(array_map('count', $upcomingMatches)) : 0 ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button">
                    <i class="bi bi-clock-history"></i> Past Matches (<?= count($pastMatches) > 0 ? array_sum(array_map('count', $pastMatches)) : 0 ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                    <i class="bi bi-list-ul"></i> All Matches (<?= count($matches) ?>)
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Upcoming Matches -->
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                <?php if (count($upcomingMatches) > 0): ?>
                    <?php foreach ($upcomingMatches as $date => $dayMatches): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar-day"></i> 
                                    <?= date('l, F j, Y', strtotime($date)) ?>
                                    <?php if ($date == $today): ?>
                                        <span class="badge bg-warning text-dark">TODAY</span>
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($dayMatches as $match): ?>
                                    <div class="alert alert-light border d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-clock-fill text-primary"></i> 
                                            <strong><?= date('h:i A', strtotime($match['match_date'])) ?></strong>
                                        </div>
                                        <div class="flex-grow-1 text-center">
                                            <span class="badge bg-primary"><?= htmlspecialchars($match['team1_name']) ?></span>
                                            <strong class="mx-3">VS</strong>
                                            <span class="badge bg-primary"><?= htmlspecialchars($match['team2_name']) ?></span>
                                        </div>
                                        <div>
                                            <span class="badge bg-info">Score: <?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 5rem; color: #ccc;"></i>
                        <h4 class="text-muted mt-3">No Upcoming Matches</h4>
                        <p class="text-muted">Add new matches from the Matches page</p>
                        <a href="matches.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Add Match</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Past Matches -->
            <div class="tab-pane fade" id="past" role="tabpanel">
                <?php if (count($pastMatches) > 0): ?>
                    <?php foreach ($pastMatches as $date => $dayMatches): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar-check"></i> 
                                    <?= date('l, F j, Y', strtotime($date)) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($dayMatches as $match): ?>
                                    <div class="alert <?= $match['team1_goals'] == $match['team2_goals'] ? 'alert-warning' : 'alert-light' ?> border d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-clock"></i> 
                                            <small><?= date('h:i A', strtotime($match['match_date'])) ?></small>
                                        </div>
                                        <div class="flex-grow-1 text-center">
                                            <span class="<?= $match['team1_goals'] > $match['team2_goals'] ? 'fw-bold text-success' : '' ?>">
                                                <?= htmlspecialchars($match['team1_name']) ?>
                                                <?= $match['team1_goals'] > $match['team2_goals'] ? ' 🏆' : '' ?>
                                            </span>
                                            <span class="badge bg-dark mx-2"><?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></span>
                                            <span class="<?= $match['team2_goals'] > $match['team1_goals'] ? 'fw-bold text-success' : '' ?>">
                                                <?= $match['team2_goals'] > $match['team1_goals'] ? '🏆 ' : '' ?>
                                                <?= htmlspecialchars($match['team2_name']) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <?php if ($match['team1_goals'] == $match['team2_goals']): ?>
                                                <span class="badge bg-warning text-dark">DRAW</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">FINISHED</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ccc;"></i>
                        <h4 class="text-muted mt-3">No Past Matches</h4>
                        <p class="text-muted">Past matches will appear here</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- All Matches -->
            <div class="tab-pane fade" id="all" role="tabpanel">
                <?php if (count($matches) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th><i class="bi bi-calendar3"></i> Date & Time</th>
                                    <th colspan="3" class="text-center"><i class="bi bi-trophy-fill"></i> Match</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matches as $index => $match): 
                                    $matchDate = strtotime($match['match_date']);
                                    $isUpcoming = $matchDate >= strtotime($today);
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <i class="bi bi-calendar-event"></i> <?= date('d M Y', $matchDate) ?><br>
                                            <small><i class="bi bi-clock"></i> <?= date('h:i A', $matchDate) ?></small>
                                        </td>
                                        <td class="text-end <?= $match['team1_goals'] > $match['team2_goals'] ? 'fw-bold' : '' ?>">
                                            <?= htmlspecialchars($match['team1_name']) ?>
                                            <?= $match['team1_goals'] > $match['team2_goals'] ? ' 🏆' : '' ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $match['team1_goals'] ?> - <?= $match['team2_goals'] ?></span>
                                        </td>
                                        <td class="<?= $match['team2_goals'] > $match['team1_goals'] ? 'fw-bold' : '' ?>">
                                            <?= $match['team2_goals'] > $match['team1_goals'] ? '🏆 ' : '' ?>
                                            <?= htmlspecialchars($match['team2_name']) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($isUpcoming): ?>
                                                <span class="badge bg-success">Upcoming</span>
                                            <?php elseif ($match['team1_goals'] == $match['team2_goals']): ?>
                                                <span class="badge bg-warning text-dark">Draw</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Finished</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ccc;"></i>
                        <h4 class="text-muted mt-3">No Matches Scheduled</h4>
                        <p class="text-muted">Start by adding matches</p>
                        <a href="matches.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Add Match</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View Option -->
        <?php if (count($matches) > 0): ?>
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-calendar-month"></i> Monthly Overview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $monthlyStats = [];
                    foreach ($matches as $match) {
                        $month = date('F Y', strtotime($match['match_date']));
                        if (!isset($monthlyStats[$month])) {
                            $monthlyStats[$month] = 0;
                        }
                        $monthlyStats[$month]++;
                    }
                    foreach ($monthlyStats as $month => $count):
                    ?>
                        <div class="col-md-4 mb-3">
                            <div class="alert alert-info mb-0">
                                <strong><i class="bi bi-calendar3"></i> <?= $month ?></strong><br>
                                <span class="badge bg-info"><?= $count ?> match<?= $count > 1 ? 'es' : '' ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
