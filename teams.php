<?php 
$pageTitle = "Teams Management";
include 'includes/header.php'; 

// Handle Delete Team
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        echo '<div class="alert alert-success alert-dismissible fade show">Team deleted successfully!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger alert-dismissible fade show">Error: Cannot delete team with existing matches!</div>';
    }
}

// Handle Add Team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO teams (class_name) VALUES (?)");
        $stmt->execute([$_POST['class_name']]);
        echo '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Team added successfully!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle-fill"></i> Error: Team name already exists!</div>';
    }
}

// Handle Edit Team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_team'])) {
    try {
        $stmt = $pdo->prepare("UPDATE teams SET class_name = ? WHERE id = ?");
        $stmt->execute([$_POST['class_name'], $_POST['team_id']]);
        echo '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill"></i> Team updated successfully!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle-fill"></i> Error: Team name already exists!</div>';
    }
}

// Get team for editing
$editTeam = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editTeam = $stmt->fetch();
}
?>

<div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0"><i class="bi bi-people-fill"></i> Teams Management</h2>
    </div>
    <div class="card-body">
        <!-- Add/Edit Team Form -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?= $editTeam ? '<i class="bi bi-pencil-fill"></i> Edit Team' : '<i class="bi bi-plus-circle-fill"></i> Add New Team' ?></h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <?php if ($editTeam): ?>
                        <input type="hidden" name="team_id" value="<?= $editTeam['id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-8">
                        <label class="form-label">Class Name</label>
                        <input type="text" class="form-control" name="class_name" 
                               placeholder="e.g., Class A1, Grade 10-A" 
                               value="<?= $editTeam ? htmlspecialchars($editTeam['class_name']) : '' ?>" 
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <?php if ($editTeam): ?>
                            <button type="submit" name="edit_team" class="btn btn-warning w-100">
                                <i class="bi bi-pencil-fill"></i> Update Team
                            </button>
                        <?php else: ?>
                            <button type="submit" name="add_team" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle-fill"></i> Add Team
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($editTeam): ?>
                        <div class="col-md-12">
                            <a href="teams.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel Edit
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Teams Table -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Teams (<?= $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn() ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th><i class="bi bi-mortarboard-fill"></i> Class Name</th>
                                <th><i class="bi bi-star-fill"></i> Points</th>
                                <th><i class="bi bi-arrow-up-circle-fill text-success"></i> Goals For</th>
                                <th><i class="bi bi-arrow-down-circle-fill text-danger"></i> Goals Against</th>
                                <th><i class="bi bi-calculator-fill"></i> Goal Difference</th>
                                <th class="text-center"><i class="bi bi-gear-fill"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $teams = $pdo->query("SELECT * FROM teams ORDER BY points DESC, (goals_scored - goals_conceded) DESC")->fetchAll();
                            if (count($teams) > 0):
                                foreach ($teams as $index => $team): 
                                    $goalDiff = $team['goals_scored'] - $team['goals_conceded'];
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($team['class_name']) ?></strong></td>
                                    <td><span class="badge bg-primary"><?= $team['points'] ?></span></td>
                                    <td><span class="text-success fw-bold"><?= $team['goals_scored'] ?></span></td>
                                    <td><span class="text-danger fw-bold"><?= $team['goals_conceded'] ?></span></td>
                                    <td>
                                        <span class="badge <?= $goalDiff > 0 ? 'bg-success' : ($goalDiff < 0 ? 'bg-danger' : 'bg-secondary') ?>">
                                            <?= $goalDiff > 0 ? '+' : '' ?><?= $goalDiff ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?edit_id=<?= $team['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="?delete_id=<?= $team['id'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this team?')" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No teams added yet. Add your first team above!</p>
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
