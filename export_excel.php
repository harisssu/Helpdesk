<?php
require_once __DIR__ . "/db_connect.php";
date_default_timezone_set("Asia/Kuala_Lumpur");

// ambil filter
$from     = isset($_GET["from"]) ? trim($_GET["from"]) : "";
$to       = isset($_GET["to"]) ? trim($_GET["to"]) : "";
$jabatan  = isset($_GET["jabatan"]) ? trim($_GET["jabatan"]) : "Semua";
$kategori = isset($_GET["kategori"]) ? trim($_GET["kategori"]) : "Semua";
$status   = isset($_GET["status"]) ? trim($_GET["status"]) : "Semua";

// build WHERE + param
$where = [];
$types = "";
$params = [];

if ($from !== "") { $where[] = "a.tarikhAduan >= ?"; $types .= "s"; $params[] = $from; }
if ($to !== "")   { $where[] = "a.tarikhAduan <= ?"; $types .= "s"; $params[] = $to; }

if ($jabatan !== "" && $jabatan !== "Semua") {
  $where[] = "j.namaJabatan = ?"; $types .= "s"; $params[] = $jabatan;
}
if ($kategori !== "" && $kategori !== "Semua") {
  $where[] = "a.jenisMasalah = ?"; $types .= "s"; $params[] = $kategori;
}
if ($status !== "" && $status !== "Semua") {
  $where[] = "s.namaStatus = ?"; $types .= "s"; $params[] = $status;
}

$sqlWhere = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";

$sql = "
SELECT
  a.idAduan,
  u.namaUser AS namaPengadu,
  a.jenisMasalah,
  j.namaJabatan AS unit,
  IFNULL(t.namaTechnician, '-') AS namaTechnician,
  a.tarikhAduan,
  s.namaStatus AS status
FROM aduan a
LEFT JOIN user u ON a.noIC = u.noIC
LEFT JOIN jabatan j ON u.idJabatan = j.idJabatan
LEFT JOIN status s ON a.idStatus = s.idStatus
LEFT JOIN technician t ON a.noICTechnician = t.noICTechnician
$sqlWhere
ORDER BY a.tarikhAduan DESC, a.idAduan DESC
";

$stmt = $conn->prepare($sql);
if(!$stmt){ die("SQL error: ".$conn->error); }

if ($types !== "") {
  $bind = [];
  $bind[] = $types;
  for ($i=0; $i<count($params); $i++) $bind[] = &$params[$i];
  call_user_func_array([$stmt, "bind_param"], $bind);
}

$stmt->execute();
$res = $stmt->get_result();

// header download CSV
$filename = "Laporan_Statistik_" . date("Ymd_His") . ".csv";
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

// output CSV
$out = fopen("php://output", "w");

// BOM supaya Excel tak rosak encoding
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($out, ["Bil","Id Aduan","Nama Pengadu","Jenis Masalah","Unit","Nama Technician","Tarikh Aduan","Status"]);

$bil = 1;
while($r = $res->fetch_assoc()){
  fputcsv($out, [
    $bil++,
    $r["idAduan"],
    $r["namaPengadu"],
    $r["jenisMasalah"],
    $r["unit"],
    $r["namaTechnician"],
    $r["tarikhAduan"],
    $r["status"],
  ]);
}

fclose($out);
exit;
