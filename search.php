<?php
// search.php - Our server-side search engine (YEAR-AWARE VERSION)
    
        // --- CORS & SESSION CONFIGURATION ---
// IMPORTANT: Replace 'your-github-username' with your actual GitHub username.
header("Access-Control-Allow-Origin: https://saadat-notebook.github.io");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// If this is a preflight request, stop here.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'admin/config.php';
header('Content-Type: application/json');

$results = [];

// --- MODIFIED: Check for query AND year ---
if (isset($_GET['q']) && !empty(trim($_GET['q'])) && isset($_GET['year'])) {
    
    // Sanitize the search query to prevent SQL injection
    $query = $conn->real_escape_string(trim($_GET['q']));
    
    // Sanitize the year to ensure it's a safe integer
    $search_year = intval($_GET['year']);

    // Only proceed if the year is valid (1 or 2, etc.)
    if ($search_year > 0) {
        
        // --- UPDATED SQL QUERY ---
        // This query now has a strict AND condition for the academic_year.
        // The parentheses around the OR condition are very important.
        $sql = "SELECT id, title, file_link, preview_link, category, description, video_links_json FROM uploads 
                WHERE 
                    (title LIKE '%$query%' OR subject LIKE '%$query%') 
                AND 
                    academic_year = $search_year
                ORDER BY upload_date DESC
                LIMIT 10";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
        }
    }
}

// At the end, send the JSON-encoded results back to the JavaScript.
echo json_encode($results);

if(isset($conn)) {
    $conn->close();
}
?>