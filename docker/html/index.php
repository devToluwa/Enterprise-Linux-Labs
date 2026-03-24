<?php
// index.php
require_once('db_config.php');

// Fetch Users from DB
$sql = "SELECT id, name, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Admin | User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 p-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
            <div>
                <h1 class="text-3xl font-bold text-cyan-400">Database Admin v2 Deployed via <span style="color:white">CI/CD</span></h1>
                <p class="text-gray-400">DB Status: <span class="<?php echo $status_color; ?> font-mono"><?php echo $db_status; ?></span></p>
            </div>
            <div class="bg-gray-800 px-4 py-2 rounded-lg border border-gray-700 text-sm">
                Served by: <span class="text-yellow-400"><?php echo $current_app_server; ?></span>
                (<span class="text-gray-400"><?php echo $current_app_ip; ?></span>)
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl">
                <h2 class="text-xl font-semibold mb-6 flex items-center">Manage User</h2>
                <form action="add_user.php" method="POST" class="space-y-4">
                    <input type="text" name="username" required class="w-full bg-gray-900 border border-gray-600 rounded p-2 text-white" placeholder="Full Name">
                    
                    <select name="role" class="w-full bg-gray-900 border border-gray-600 rounded p-2 text-gray-400">
                        <option value="Standard User">Standard User</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Lab Manager">Lab Manager</option>
                    </select>

                    <button type="submit" class="w-full bg-cyan-600 font-bold py-2 rounded">Add User</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Role</th> <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='px-6 py-4 font-mono text-cyan-500'>".$row["id"]."</td>";
                                echo "<td class='px-6 py-4'>".$row["name"]."</td>";
                                echo "<td class='px-6 py-4 text-gray-400'>".$row["role"]."</td>"; // NEW DATA COLUMN
                                echo "<td class='px-6 py-4 text-right'>";
                                echo "  <a href='delete_user.php?id=" . $row["id"] . "'
                                           onclick=\"return confirm('Are you sure you want to delete " . addslashes($row["name"]) . "?');\"
                                           class='text-red-400 hover:text-red-300 font-medium transition cursor-pointer'>
                                           Delete
                                        </a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='p-6 text-center text-gray-500'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
