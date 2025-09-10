<?php
session_start();

// Check if a logout request has been made
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: login.php');
    exit;
}

// Check if the user is authenticated, otherwise redirect to login page
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit;
}

// Check if the user has permission to access this page
// if ($_SESSION['role'] !== 'admin') {
//     header('Location: login.php'); // Or a permission denied page
//     exit;
// }

$tab = $_GET['tab'] ?? 'dashboard';

// Helper function to read a JSON file safely
function get_json_data($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = @file_get_contents($file);
    if ($data === false || trim($data) === '') {
        return [];
    }
    $decoded = json_decode($data, true);
    return $decoded !== null ? $decoded : [];
}

// Helper function to save data to a JSON file
function save_json_data($file, $data) {
    $encoded = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $encoded);
}

// Files
$usersFile    = 'users.json';
$plansFile    = 'data.json';
$projectsFile = 'projects.json';
$requestsFile = 'requests.json';
$clientsFile  = 'clients.json';
$alertsFile   = 'alerts.json';

// Load
$users = get_json_data($usersFile);
$plans = get_json_data($plansFile);
$projects = get_json_data($projectsFile);
$requests = get_json_data($requestsFile);
$clients = get_json_data($clientsFile);
$alerts = get_json_data($alertsFile);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $data = [];

    // All other admin actions require authentication
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {

        // ALERTS: create / edit / delete / reorder
        if ($action === 'create_alert') {
            $alerts = get_json_data($alertsFile);
            $alerts[] = [
                'text'  => $_POST['alert_text'] ?? '',
                'color' => $_POST['alert_color'] ?? '',
                'created_at' => time()
            ];
            save_json_data($alertsFile, $alerts);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=alerts');
            exit;
        }

        if ($action === 'edit_alert') {
            $index = isset($_POST['index']) ? intval($_POST['index']) : null;
            $alerts = get_json_data($alertsFile);
            if ($index !== null && isset($alerts[$index])) {
                $alerts[$index]['text']  = $_POST['alert_text'] ?? $alerts[$index]['text'];
                $alerts[$index]['color'] = $_POST['alert_color'] ?? $alerts[$index]['color'];
                save_json_data($alertsFile, $alerts);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=alerts');
            exit;
        }

        if ($action === 'delete_alert') {
            $index = isset($_POST['index']) ? intval($_POST['index']) : null;
            $alerts = get_json_data($alertsFile);
            if ($index !== null && isset($alerts[$index])) {
                array_splice($alerts, $index, 1);
                save_json_data($alertsFile, $alerts);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=alerts');
            exit;
        }

        if ($action === 'reorder_alert') {
            $index = isset($_POST['index']) ? intval($_POST['index']) : null;
            $direction = $_POST['direction'] ?? '';
            $alerts = get_json_data($alertsFile);
            if ($index !== null && isset($alerts[$index])) {
                if ($direction === 'up' && $index > 0) {
                    $temp = $alerts[$index - 1];
                    $alerts[$index - 1] = $alerts[$index];
                    $alerts[$index] = $temp;
                } elseif ($direction === 'down' && $index < count($alerts) - 1) {
                    $temp = $alerts[$index + 1];
                    $alerts[$index + 1] = $alerts[$index];
                    $alerts[$index] = $temp;
                }
                save_json_data($alertsFile, $alerts);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=alerts');
            exit;
        }

        // REQUESTS: change status
        if ($action === 'change_status') {
            $data = get_json_data($requestsFile);
            $id = $_POST['id'];
            foreach ($data as &$request) {
                if ($request['id'] === $id) {
                    $request['status'] = $_POST['status'];
                    break;
                }
            }
            save_json_data($requestsFile, $data);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=requests');
            exit;
        }

        // PLANS: add/edit/delete/reorder
        if ($action === 'add_plan') {
            $data = get_json_data($plansFile);
            $data[] = [
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'features' => explode(',', $_POST['features']),
                'status' => 'Active' // New plans are active by default
            ];
            save_json_data($plansFile, $data);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=plans');
            exit;
        }
        if ($action === 'edit_plan') {
            $data = get_json_data($plansFile);
            $index = $_POST['index'];
            if (isset($data[$index])) {
                $data[$index]['name'] = $_POST['name'];
                $data[$index]['price'] = $_POST['price'];
                $data[$index]['features'] = explode(',', $_POST['features']);
                save_json_data($plansFile, $data);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=plans');
            exit;
        }
        if ($action === 'delete_plan') {
            $data = get_json_data($plansFile);
            $index = $_POST['index'];
            if (isset($data[$index])) {
                array_splice($data, $index, 1);
                save_json_data($plansFile, $data);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=plans');
            exit;
        }
        if ($action === 'reorder_plan') {
            $index = isset($_POST['index']) ? intval($_POST['index']) : null;
            $direction = $_POST['direction'] ?? '';
            $plans = get_json_data($plansFile);
            if ($index !== null && isset($plans[$index])) {
                if ($direction === 'up' && $index > 0) {
                    $temp = $plans[$index - 1];
                    $plans[$index - 1] = $plans[$index];
                    $plans[$index] = $temp;
                } elseif ($direction === 'down' && $index < count($plans) - 1) {
                    $temp = $plans[$index + 1];
                    $plans[$index + 1] = $plans[$index];
                    $plans[$index] = $temp;
                }
                save_json_data($plansFile, $plans);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=plans');
            exit;
        }
        // New action to toggle plan status
        if ($action === 'change_plan_status') {
            $index = isset($_POST['index']) ? intval($_POST['index']) : null;
            $status = $_POST['status'] ?? '';
            $plans = get_json_data($plansFile);
            if ($index !== null && isset($plans[$index])) {
                $plans[$index]['status'] = $status;
                save_json_data($plansFile, $plans);
            }
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=plans');
            exit;
        }

        // PROJECTS: add/edit/delete
        if ($action === 'add_project') {
            $data = get_json_data($projectsFile);
            $data[] = [
                'id' => uniqid(),
                'client_name' => $_POST['client_name'],
                'project_name' => $_POST['project_name'],
                'status' => $_POST['status']
            ];
            save_json_data($projectsFile, $data);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=projects');
            exit;
        }

        if ($action === 'edit_project') {
            $data = get_json_data($projectsFile);
            $id = $_POST['id'];
            foreach ($data as &$project) {
                if ($project['id'] === $id) {
                    $project['client_name'] = $_POST['client_name'];
                    $project['project_name'] = $_POST['project_name'];
                    $project['status'] = $_POST['status'];
                    break;
                }
            }
            save_json_data($projectsFile, $data);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=projects');
            exit;
        }

        if ($action === 'delete_project') {
            $data = get_json_data($projectsFile);
            $id = $_POST['id'];
            $data = array_filter($data, function($project) use ($id) {
                return $project['id'] !== $id;
            });
            save_json_data($projectsFile, array_values($data)); // re-index array
            header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=projects');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a202c;
            color: #e2e8f0;
        }
    </style>
</head>
<body class="p-6">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="flex items-center space-x-2">
                <a href="?action=logout" class="px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700">Logout</a>
            </div>
        </header>

        <nav class="mb-6">
            <div class="space-x-2">
                <a href="?tab=dashboard" class="px-3 py-2 bg-purple-600 rounded-lg hover:bg-purple-700">Dashboard</a>
                <a href="?tab=requests" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Requests</a>
                <a href="?tab=clients" class="px-3 py-2 bg-green-600 rounded-lg hover:bg-green-700">Clients</a>
                <a href="?tab=alerts" class="px-3 py-2 bg-pink-600 rounded-lg hover:bg-pink-700">Alerts</a>
                <a href="?tab=plans" class="px-3 py-2 bg-indigo-600 rounded-lg hover:bg-indigo-700">Plans</a>
                <a href="?tab=projects" class="px-3 py-2 bg-teal-600 rounded-lg hover:bg-teal-700">Projects</a>
            </div>
        </nav>

        <main class="bg-gray-900 p-6 rounded-lg shadow-lg">

            <!-- DASHBOARD -->
            <?php if ($tab === 'dashboard'): ?>
                <h2 class="text-xl font-semibold mb-4">Dashboard Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-2">Total Clients</h3>
                        <p class="text-3xl font-bold"><?= count($clients) ?></p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-2">Active Projects</h3>
                        <p class="text-3xl font-bold"><?= count(array_filter($projects, function($p) { return $p['status'] === 'Active'; })) ?></p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
                        <p class="text-3xl font-bold"><?= count(array_filter($requests, function($r) { return $r['status'] === 'Pending'; })) ?></p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-2">Total Requests</h3>
                        <p class="text-3xl font-bold"><?= count($requests) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ALERTS -->
            <?php if ($tab === 'alerts'): ?>
                <h2 class="text-xl font-semibold mb-4">Manage Banner Alerts</h2>

                <!-- Add New Alert -->
                <form method="post" class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-6">
                    <input type="hidden" name="action" value="create_alert">
                    <div class="space-y-4">
                        <div>
                            <label for="alert_text" class="block text-sm font-medium text-gray-400">Alert Text</label>
                            <input type="text" name="alert_text" id="alert_text" placeholder="e.g., 'New feature coming soon!'" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                        <div>
                            <label for="alert_color" class="block text-sm font-medium text-gray-400">Background Color (e.g., bg-red-600)</label>
                            <input type="text" name="alert_color" id="alert_color" value="bg-blue-600" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Use Tailwind CSS color classes like `bg-red-600`, `bg-yellow-500`, `bg-green-600`.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-pink-600 rounded-lg hover:bg-pink-700">Add Alert</button>
                    </div>
                </form>

                <!-- List of Existing Alerts -->
                <div class="space-y-4">
                    <?php if (empty($alerts)): ?>
                        <p class="text-gray-400">No alerts found. Add one above.</p>
                    <?php else: ?>
                        <?php foreach ($alerts as $i => $alert): ?>
                            <div class="bg-gray-700 p-4 rounded-lg border border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex-grow">
                                        <div class="p-2 rounded-lg text-sm text-center font-semibold text-white <?= htmlspecialchars($alert['color'] ?? 'bg-blue-600') ?>">
                                            <?= htmlspecialchars($alert['text'] ?? '') ?>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Created: <?= isset($alert['created_at']) ? date('Y-m-d H:i', $alert['created_at']) : 'N/A' ?>
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 space-x-2">
                                        <a href="?tab=alerts&edit=<?= $i ?>" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Edit</a>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="delete_alert">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <button type="submit" class="px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700" onclick="return confirm('Delete this alert?')">Delete</button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="reorder_alert">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="px-2 py-2 bg-gray-600 rounded-lg" title="Move Up">▲</button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="reorder_alert">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="px-2 py-2 bg-gray-600 rounded-lg" title="Move Down">▼</button>
                                        </form>
                                    </div>
                                </div>

                                <?php if (isset($_GET['edit']) && intval($_GET['edit']) === $i): ?>
                                    <form method="post" class="mt-4 bg-gray-800 p-3 rounded-lg border border-gray-600">
                                        <input type="hidden" name="action" value="edit_alert">
                                        <input type="hidden" name="index" value="<?= $i ?>">
                                        <div class="space-y-3">
                                            <input type="text" name="alert_text" value="<?= htmlspecialchars($alert['text'] ?? '') ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                            <input type="text" name="alert_color" value="<?= htmlspecialchars($alert['color'] ?? '') ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Save Changes</button>
                                            <a href="<?= $_SERVER['PHP_SELF'] . '?tab=alerts' ?>" class="px-3 py-2 bg-gray-600 rounded-lg ml-2">Cancel</a>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- PLANS -->
            <?php if ($tab === 'plans'): ?>
                <h2 class="text-xl font-semibold mb-4">Manage Plans</h2>

                <!-- Add New Plan -->
                <form method="post" class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-6">
                    <input type="hidden" name="action" value="add_plan">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-400">Plan Name</label>
                            <input type="text" name="name" id="name" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-400">Price</label>
                            <input type="text" name="price" id="price" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                        <div>
                            <label for="features" class="block text-sm font-medium text-gray-400">Features (comma-separated)</label>
                            <input type="text" name="features" id="features" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-lg hover:bg-indigo-700">Add Plan</button>
                    </div>
                </form>

                <!-- List of Existing Plans -->
                <div class="space-y-4">
                    <?php if (empty($plans)): ?>
                        <p class="text-gray-400">No plans found. Add one above.</p>
                    <?php else: ?>
                        <?php foreach ($plans as $i => $plan): ?>
                            <div class="bg-gray-700 p-4 rounded-lg border border-gray-600">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($plan['name'] ?? 'N/A') ?> - $<?= htmlspecialchars($plan['price'] ?? 'N/A') ?></h3>
                                        <p class="text-sm text-gray-400"><?= htmlspecialchars(implode(', ', $plan['features'] ?? [])) ?></p>
                                        <p class="text-sm font-bold mt-1">Status: <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $plan['status'] === 'Active' ? 'bg-green-600 text-green-100' : 'bg-red-600 text-red-100' ?>"><?= htmlspecialchars($plan['status'] ?? 'N/A') ?></span></p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="?tab=plans&edit=<?= $i ?>" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Edit</a>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="delete_plan">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <button type="submit" class="px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="reorder_plan">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="px-2 py-2 bg-gray-600 rounded-lg" title="Move Up">▲</button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="reorder_plan">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="px-2 py-2 bg-gray-600 rounded-lg" title="Move Down">▼</button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="change_plan_status">
                                            <input type="hidden" name="index" value="<?= $i ?>">
                                            <input type="hidden" name="status" value="<?= $plan['status'] === 'Active' ? 'Inactive' : 'Active' ?>">
                                            <button type="submit" class="px-3 py-2 rounded-lg <?= $plan['status'] === 'Active' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' ?>">
                                                <?= $plan['status'] === 'Active' ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php if (isset($_GET['edit']) && intval($_GET['edit']) === $i): ?>
                                    <form method="post" class="mt-4 bg-gray-800 p-3 rounded-lg border border-gray-600">
                                        <input type="hidden" name="action" value="edit_plan">
                                        <input type="hidden" name="index" value="<?= $i ?>">
                                        <div class="space-y-3">
                                            <input type="text" name="name" value="<?= htmlspecialchars($plan['name'] ?? '') ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                            <input type="text" name="price" value="<?= htmlspecialchars($plan['price'] ?? '') ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                            <input type="text" name="features" value="<?= htmlspecialchars(implode(',', $plan['features'] ?? [])) ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Save Changes</button>
                                            <a href="<?= $_SERVER['PHP_SELF'] . '?tab=plans' ?>" class="px-3 py-2 bg-gray-600 rounded-lg ml-2">Cancel</a>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- PROJECTS -->
            <?php if ($tab === 'projects'): ?>
                <h2 class="text-xl font-semibold mb-4">Manage Projects</h2>

                <!-- Add New Project -->
                <form method="post" class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-6">
                    <input type="hidden" name="action" value="add_project">
                    <div class="space-y-4">
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-gray-400">Client Name</label>
                            <input type="text" name="client_name" id="client_name" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                        <div>
                            <label for="project_name" class="block text-sm font-medium text-gray-400">Project Name</label>
                            <input type="text" name="project_name" id="project_name" required class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-400">Status</label>
                            <select name="status" id="status" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Paused">Paused</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-teal-600 rounded-lg hover:bg-teal-700">Add Project</button>
                    </div>
                </form>

                <!-- List of Existing Projects -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-gray-800 rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Client</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Project</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Status</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr class="border-t border-gray-700">
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($project['client_name']) ?></td>
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($project['project_name']) ?></td>
                                    <td class="py-2 px-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        switch($project['status']) {
                                            case 'Active': echo 'bg-green-600 text-green-100'; break;
                                            case 'Completed': echo 'bg-blue-600 text-blue-100'; break;
                                            case 'Paused': echo 'bg-yellow-600 text-yellow-100'; break;
                                            case 'Cancelled': echo 'bg-red-600 text-red-100'; break;
                                        }
                                        ?>
                                        "><?= htmlspecialchars($project['status']) ?></span>
                                    </td>
                                    <td class="py-2 px-4 text-sm flex space-x-2">
                                        <a href="?tab=projects&edit=<?= htmlspecialchars($project['id']) ?>" class="px-3 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Edit</a>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="action" value="delete_project">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($project['id']) ?>">
                                            <button type="submit" class="px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php if (isset($_GET['edit']) && $_GET['edit'] === $project['id']): ?>
                                    <tr class="bg-gray-800">
                                        <td colspan="4" class="p-4">
                                            <form method="post">
                                                <input type="hidden" name="action" value="edit_project">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($project['id']) ?>">
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-400">Client Name</label>
                                                        <input type="text" name="client_name" value="<?= htmlspecialchars($project['client_name']) ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-400">Project Name</label>
                                                        <input type="text" name="project_name" value="<?= htmlspecialchars($project['project_name']) ?>" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-400">Status</label>
                                                        <select name="status" class="w-full p-2 bg-gray-700 rounded-lg border border-gray-600">
                                                            <option value="Active" <?= $project['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                                            <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                            <option value="Paused" <?= $project['status'] === 'Paused' ? 'selected' : '' ?>>Paused</option>
                                                            <option value="Cancelled" <?= $project['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-700">Save Changes</button>
                                                    <a href="<?= $_SERVER['PHP_SELF'] . '?tab=projects' ?>" class="px-3 py-2 bg-gray-600 rounded-lg ml-2">Cancel</a>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Requests -->
            <?php if ($tab === 'requests'): ?>
                <h2 class="text-xl font-semibold mb-4">Client Requests</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-gray-800 rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">ID</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Client</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Service</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Status</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr class="border-t border-gray-700">
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($request['id']) ?></td>
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($request['client_name']) ?></td>
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($request['service_name']) ?></td>
                                    <td class="py-2 px-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        switch($request['status']) {
                                            case 'Pending': echo 'bg-yellow-600 text-yellow-100'; break;
                                            case 'In Progress': echo 'bg-blue-600 text-blue-100'; break;
                                            case 'Completed': echo 'bg-green-600 text-green-100'; break;
                                            case 'Cancelled': echo 'bg-red-600 text-red-100'; break;
                                        }
                                        ?>
                                        "><?= htmlspecialchars($request['status']) ?></span>
                                    </td>
                                    <td class="py-2 px-4 text-sm">
                                        <form method="post" class="inline-block">
                                            <input type="hidden" name="action" value="change_status">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                                            <select name="status" onchange="this.form.submit()" class="bg-gray-700 p-1 rounded">
                                                <option value="Pending" <?= $request['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="In Progress" <?= $request['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Completed" <?= $request['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="Cancelled" <?= $request['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Clients -->
            <?php if ($tab === 'clients'): ?>
                <h2 class="text-xl font-semibold mb-4">Clients</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-gray-800 rounded-lg">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Name</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Email</th>
                                <th class="py-2 px-4 text-left text-sm font-semibold text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr class="border-t border-gray-700">
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($client['name']) ?></td>
                                    <td class="py-2 px-4 text-sm"><?= htmlspecialchars($client['email']) ?></td>
                                    <td class="py-2 px-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        switch($client['status']) {
                                            case 'Active': echo 'bg-green-600 text-green-100'; break;
                                            case 'Suspended': echo 'bg-yellow-600 text-yellow-100'; break;
                                            case 'Cancelled': echo 'bg-red-600 text-red-100'; break;
                                        }
                                        ?>
                                        "><?= htmlspecialchars($client['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

