<?php
$host = 'localhost';
$dbname = 'sportiva_db';
$user = 'root';
$pass = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, $options);
    // Auto-migrate legacy hashed image names and placeholder sport SVGs once
    $legacyCheck = $pdo->query("SELECT COUNT(*) FROM venues WHERE main_image LIKE '%_1749%'")->fetchColumn();
    if ($legacyCheck > 0) {
        $venueMap = [
            1 => 'metro_atom.png', 2 => 'gala_sport.png', 3 => 'metro_atom.png', 4 => 'gala_sport.png',
            5 => 'lapangan_futsal.png', 6 => 'centro_sawah_besar.png', 7 => 'lapangan_futsal.png', 8 => 'gala_sport.png',
            9 => 'metro_atom.png', 10 => 'centro_sawah_besar.png', 11 => 'metro_atom.png', 12 => 'gala_sport.png'
        ];
        $stmtV = $pdo->prepare("UPDATE venues SET main_image = ? WHERE id = ?");
        foreach ($venueMap as $vid => $vimg) {
            $stmtV->execute([$vimg, $vid]);
        }
        $pdo->exec("UPDATE mabar_events SET host_logo = 'logo_bmx.png' WHERE id = 1");
        $pdo->exec("UPDATE mabar_events SET host_logo = 'logo_bwf.png' WHERE id = 2");
    }
    $svgCheck = $pdo->query("SELECT COUNT(*) FROM sports WHERE icon_svg LIKE '%...%'")->fetchColumn();
    if ($svgCheck > 0) {
        $sportIcons = [
            1 => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="12 7 16 10 14.5 15 9.5 15 8 10 12 7"/></svg>',
            2 => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 9.5 19 5a2.12 2.12 0 0 0-3-3l-4.5 4.5"/><path d="m9.5 14.5-5 5a2 2 0 0 0 3 3l5-5"/><circle cx="12" cy="12" r="3"/></svg>',
            3 => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93c4.5 4.5 9.64 4.5 14.14 0"/><path d="M4.93 19.07c4.5-4.5 9.64-4.5 14.14 0"/><line x1="2" y1="12" x2="22" y2="12"/></svg>',
            4 => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M6 5.3a9 9 0 0 1 0 13.4"/><path d="M18 5.3a9 9 0 0 0 0 13.4"/></svg>',
            5 => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/><path d="M2 12h20"/></svg>'
        ];
        $stmtS = $pdo->prepare("UPDATE sports SET icon_svg = ? WHERE id = ?");
        foreach ($sportIcons as $sid => $ssvg) {
            $stmtS->execute([$ssvg, $sid]);
        }
    }
} catch (\PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>