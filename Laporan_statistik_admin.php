<?php
session_start();
require_once __DIR__ . "/db_connect.php";
date_default_timezone_set("Asia/Kuala_Lumpur");

$namaUser = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Unknown';
$perananNama = "Admin";

/**
 * Helper: build WHERE + params
 * (Letak luar supaya boleh guna untuk semua action)
 */
function buildWhereAndParams($from, $to, $jabatan, $kategori, $status) {
    $where = array();
    $types = "";
    $params = array();

    if ($from !== "") {
        $where[] = "a.tarikhAduan >= ?";
        $types .= "s";
        $params[] = $from;
    }
    if ($to !== "") {
        $where[] = "a.tarikhAduan <= ?";
        $types .= "s";
        $params[] = $to;
    }

    // ✅ Jabatan: support user + ketua unit
    if ($jabatan !== "" && $jabatan !== "Semua") {
        $where[] = "COALESCE(ju.namaJabatan, jk.namaJabatan) = ?";
        $types .= "s";
        $params[] = $jabatan;
    }

    if ($kategori !== "" && $kategori !== "Semua") {
        $where[] = "a.jenisMasalah = ?";
        $types .= "s";
        $params[] = $kategori;
    }
    if ($status !== "" && $status !== "Semua") {
        $where[] = "s.namaStatus = ?";
        $types .= "s";
        $params[] = $status;
    }

    $sqlWhere = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";
    return array($sqlWhere, $types, $params);
}


if (isset($_GET["api"]) && $_GET["api"] == "1") {
    header("Content-Type: application/json; charset=utf-8");

    $action   = isset($_GET["action"]) ? $_GET["action"] : "";
    $from     = isset($_GET["from"]) ? trim($_GET["from"]) : "";
    $to       = isset($_GET["to"]) ? trim($_GET["to"]) : "";
    $jabatan  = isset($_GET["jabatan"]) ? trim($_GET["jabatan"]) : "Semua";
    $kategori = isset($_GET["kategori"]) ? trim($_GET["kategori"]) : "Semua";
    $status   = isset($_GET["status"]) ? trim($_GET["status"]) : "Semua";

    // 1) Dropdown Jabatan
    if ($action === "jabatan") {
        $sql = "SELECT namaJabatan FROM jabatan ORDER BY namaJabatan";
        $res = $conn->query($sql);

        $list = array("Semua");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $row["namaJabatan"];
            }
        }
        echo json_encode(array("success"=>true, "list"=>$list));
        exit;
    }

    // 2) Summary chart
    if ($action === "summary") {

        $deptChosen = ($jabatan !== "" && $jabatan !== "Semua");
        $catChosen  = ($kategori !== "" && $kategori !== "Semua");
        $stChosen   = ($status !== "" && $status !== "Semua");

        $groupBySql = "s.namaStatus";
        $groupKey   = "status";

        if ($deptChosen && !$catChosen && !$stChosen) {
            $groupBySql = "a.jenisMasalah";
            $groupKey   = "kategori";
        } elseif ($stChosen && !$deptChosen && !$catChosen) {
            $groupBySql = "COALESCE(ju.namaJabatan, jk.namaJabatan)";
            $groupKey   = "jabatan";
        } elseif ($catChosen && !$deptChosen && !$stChosen) {
            $groupBySql = "COALESCE(ju.namaJabatan, jk.namaJabatan)";
            $groupKey   = "jabatan";
        } elseif ($deptChosen && $catChosen && !$stChosen) {
            $groupBySql = "s.namaStatus";
            $groupKey   = "status";
        } else {
            if (!$stChosen) {
                $groupBySql = "s.namaStatus";
                $groupKey   = "status";
            } elseif (!$catChosen) {
                $groupBySql = "a.jenisMasalah";
                $groupKey   = "kategori";
            } else {
                $groupBySql = "COALESCE(ju.namaJabatan, jk.namaJabatan)";
                $groupKey   = "jabatan";
            }
        }

        list($sqlWhere, $types, $params) = buildWhereAndParams($from, $to, $jabatan, $kategori, $status);

        $sql = "
    SELECT $groupBySql AS label, COUNT(*) AS total
    FROM aduan a
    LEFT JOIN user u ON a.noIC = u.noIC
    LEFT JOIN jabatan ju ON u.idJabatan = ju.idJabatan

    LEFT JOIN ketuaunit k ON a.noICKetua = k.noICKetua
    LEFT JOIN jabatan jk ON k.idJabatan = jk.idJabatan

    LEFT JOIN status s ON a.idStatus = s.idStatus
    $sqlWhere
    GROUP BY label
    ORDER BY total DESC
";


        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(array("success"=>false, "error"=>"SQL prepare failed: ".$conn->error));
            exit;
        }

        if ($types !== "") {
            $bind = array();
            $bind[] = $types;
            for ($i=0; $i<count($params); $i++) $bind[] = &$params[$i];
            call_user_func_array(array($stmt, "bind_param"), $bind);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $labels = array();
        $values = array();

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $lab = $row["label"];
                if ($lab === null || $lab === "") $lab = "(Tiada)";
                $labels[] = $lab;
                $values[] = (int)$row["total"];
            }
        }

        echo json_encode(array(
            "success" => true,
            "group"   => $groupKey,
            "labels"  => $labels,
            "values"  => $values
        ));
        exit;
    }

    // 3) Click bar -> details
    if ($action === "details") {
        $group = isset($_GET["group"]) ? $_GET["group"] : "status";
        $label = isset($_GET["label"]) ? trim($_GET["label"]) : "";

        list($sqlWhere, $types, $params) = buildWhereAndParams($from, $to, $jabatan, $kategori, $status);

        if ($label !== "" && $label !== "(Tiada)") {
            if ($group === "kategori") {
                $sqlWhere .= ($sqlWhere ? " AND " : "WHERE ") . "a.jenisMasalah = ?";
            } elseif ($group === "jabatan") {
                 $sqlWhere .= ($sqlWhere ? " AND " : "WHERE ") . "COALESCE(ju.namaJabatan, jk.namaJabatan) = ?";
            } else {
                $sqlWhere .= ($sqlWhere ? " AND " : "WHERE ") . "s.namaStatus = ?";
            }
            $types .= "s";
            $params[] = $label;
        }

        $sql = "
    SELECT
        a.idAduan,
        COALESCE(u.namaUser, k.namaKetua, '-') AS namaPengadu,
        a.jenisMasalah,
        COALESCE(ju.namaJabatan, jk.namaJabatan, '-') AS unit,
        IFNULL(t.namaTechnician, '-') AS namaTechnician,
        a.tarikhAduan,
        s.namaStatus AS status
    FROM aduan a

    LEFT JOIN user u ON a.noIC = u.noIC
    LEFT JOIN jabatan ju ON u.idJabatan = ju.idJabatan

    LEFT JOIN ketuaunit k ON a.noICKetua = k.noICKetua
    LEFT JOIN jabatan jk ON k.idJabatan = jk.idJabatan

    LEFT JOIN status s ON a.idStatus = s.idStatus
    LEFT JOIN technician t ON a.noICTechnician = t.noICTechnician

    $sqlWhere
    ORDER BY a.tarikhAduan DESC, a.idAduan DESC
    LIMIT 500
";


        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(array("success"=>false, "error"=>"SQL prepare failed: ".$conn->error));
            exit;
        }

        if ($types !== "") {
            $bind = array();
            $bind[] = $types;
            for ($i=0; $i<count($params); $i++) $bind[] = &$params[$i];
            call_user_func_array(array($stmt, "bind_param"), $bind);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $rows = array();
        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
        }

        echo json_encode(array("success"=>true, "rows"=>$rows));
        exit;
    }

    echo json_encode(array("success"=>false, "error"=>"Action tidak sah"));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>e-ICT Aduan</title>
  <meta charset="utf-8" />

  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url("img/bg.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      position: relative;
      overflow: hidden;
    }
    .app { width:100%; height:100vh; display:flex; flex-direction:column; }
    .topbar { height:50px; background:#0306a0ff; display:flex; align-items:center; padding:0 20px; flex-shrink:0; }
    .topbar .system { font-weight:bold; color:#fff; margin-right:35px; font-size:30px; }
    .topbar .page-title { font-weight:bold; color:#fff; margin-left:30px; font-size:25px; }
    .layout { display:flex; flex:1; overflow:hidden; }

    .sidebar { width:240px; background:#e6e6e6; display:flex; flex-direction:column; flex-shrink:0; }
    .user-info { display:flex; align-items:center; gap:20px; padding:20px; border-bottom:1px solid #000; }
    .user-info img { width:40px; height:40px; border-radius:50%; }

    .menu { flex:1; }
    .menu a { display:block; padding:15px 18px; text-decoration:none; color:#000; border-bottom:1px solid #000; }
    .menu a:hover { background:#d9d9d9; }
    .menu a.active { color: #1e40ff; font-weight:bold; }

    .sidebar-logout { padding:15px; margin-top: auto; }
    .logout-btn { width:100%; padding:8px; border:none; border-radius:8px; background:#0306a0ff; color:#fff; font-weight:bold; cursor:pointer; }

    .content { flex:1; padding:30px; overflow:auto; }

    .card { background: rgba(230,230,230,0.98); border: 2px solid #000; padding: 16px; }
    .filter-row { display:flex; align-items:end; gap:14px; flex-wrap:wrap; }
    .field { display:flex; flex-direction:column; gap:6px; }
    .field label { font-weight:bold; font-size:14px; }
    .field input, .field select { padding:6px; width:170px; }
    .btn { padding:7px 18px; border:none; background:#0306a0ff; color:#fff; font-weight:bold; border-radius:6px; cursor:pointer; }

    .export-row{
      margin-top: 10px;
      display:flex;
      gap: 12px;
      justify-content: flex-end; /* tukar flex-start kalau nak kiri */
    }

    .chart-wrap { background:#fff; border:2px solid #000; margin-top: 14px; padding: 14px; }
    .hint { margin-top:8px; font-size:13px; color:#222; }

    .modal {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.45);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 18px;
      z-index: 9999;
    }
    .modal.show { display:flex; }
    .modal-card {
      width: min(980px, 96vw);
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 16px 40px rgba(0,0,0,0.35);
    }
    .modal-head {
      background:#0306a0ff;
      color:#fff;
      padding: 12px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      font-weight: bold;
    }
    .modal-body { padding: 14px; max-height: 70vh; overflow:auto; }
    .close-btn {
      background: transparent;
      border: 1px solid rgba(255,255,255,0.7);
      color:#fff;
      padding: 6px 10px;
      border-radius: 8px;
      cursor:pointer;
      font-weight: bold;
    }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:8px; text-align:left; font-size: 13px; }
    th { background:#f2f2f2; }
    .empty { padding: 10px; color:#333; }
  </style>
</head>

<body>
<div class="app">

  <div class="topbar">
    <div class="system">e-ICT Aduan</div>
    <div class="page-title">Laporan Statistik</div>
  </div>

  <div class="layout">
    <div class="sidebar">
      <div class="user-info">
        <img src="img/profile.jpg" alt="User">
        <div>
          <strong><?= htmlspecialchars($namaUser); ?></strong><br>
          <small><?= htmlspecialchars($perananNama); ?></small>
        </div>
      </div>

      <div class="menu">
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="Senarai_aduan_admin.php">Senarai Aduan</a>
        <a href="Senarai_Pengguna_admin.php">Senarai Pengguna</a>
        <a href="Laporan_statistik_admin.php" class="active">Laporan Statistik</a>
      </div>

      <div class="sidebar-logout">
        <form action="logout.php" method="post">
          <button type="submit" onclick="return confirmLogout()" class="logout-btn">Log Keluar</button>
        </form>
      </div>
    </div>

    <div class="content">
      <div class="card">

        <!-- ROW 1 -->
        <div class="filter-row">
          <div class="field">
            <label>From:</label>
            <input type="date" id="from">
          </div>

          <div class="field">
            <label>To:</label>
            <input type="date" id="to">
          </div>

          <div class="field">
            <label>Jabatan:</label>
            <select id="jabatan">
              <option>Semua</option>
            </select>
          </div>

          <div class="field">
            <label>Kategori:</label>
            <select id="kategori">
              <option>Semua</option>
              <option>Komputer</option>
              <option>Printer</option>
              <option>Software</option>
              <option>Internet</option>
              <option>Monitor</option>
              <option>Lain-lain</option>
            </select>
          </div>

          <div class="field">
            <label>Status:</label>
            <select id="status">
              <option>Semua</option>
              <option>Baru</option>
              <option>Dalam Tindakan</option>
              <option>Selesai</option>
              <option>Hantar Kedai</option>
            </select>
          </div>

          <button class="btn" id="btnCari" type="button">Cari</button>
        </div>

        <!-- ROW 2 -->
        <div class="export-row">
          <button class="btn" id="btnPDFChart" type="button">Export PDF</button>
          <button class="btn" id="btnExcel" type="button">Export Excel</button>
        </div>

        <!-- CHART -->
        <div class="chart-wrap">
          <canvas id="statChart" height="90"></canvas>
          <div class="hint" id="hintText">Klik bar untuk lihat senarai aduan ikut filter.</div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal details -->
<div class="modal" id="modal">
  <div class="modal-card">
    <div class="modal-head">
      <div id="modalTitle">Butiran</div>
      <button class="close-btn" id="closeModal" type="button">Tutup</button>
    </div>
    <div class="modal-body">
      <div id="modalContent"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

<script>
  function qs(id){ return document.getElementById(id); }

  function getFilters() {
    return {
      from: qs('from').value,
      to: qs('to').value,
      jabatan: qs('jabatan').value,
      kategori: qs('kategori').value,
      status: qs('status').value
    };
  }

  async function fetchJSON(url) {
    const res = await fetch(url);
    const text = await res.text();
    try { return JSON.parse(text); }
    catch(e){ console.log("NOT JSON:", text); throw e; }
  }

  async function loadJabatanOptions() {
    const data = await fetchJSON('Laporan_statistik_admin.php?api=1&action=jabatan');
    if (!data.success) return;
    const sel = qs('jabatan');
    sel.innerHTML = '';
    data.list.forEach(j => {
      const opt = document.createElement('option');
      opt.value = j;
      opt.textContent = j;
      sel.appendChild(opt);
    });
  }

  let chart = null;
  let lastSummary = { group: 'status', labels: [], values: [] };

  async function loadSummary() {
    const f = getFilters();
    const params = new URLSearchParams({
      api: '1',
      action: 'summary',
      from: f.from,
      to: f.to,
      jabatan: f.jabatan,
      kategori: f.kategori,
      status: f.status
    });

    const data = await fetchJSON('Laporan_statistik_admin.php?' + params.toString());
    if (!data.success) {
      alert(data.error || 'Gagal load statistik');
      return;
    }

    lastSummary = data;
    renderChart(data.labels, data.values);
  }

  function renderChart(labels, values) {
    const ctx = qs('statChart').getContext('2d');
    if (chart) chart.destroy();

    chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Aduan',
          data: values
        }]
      },
      options: {
        responsive: true,
        onClick: async (evt, elements) => {
          if (!elements || !elements.length) return;
          const idx = elements[0].index;
          const label = lastSummary.labels[idx];
          await loadDetails(label);
        },
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
      }
    });

    qs('hintText').textContent =
      'Chart dipaparkan mengikut ' + lastSummary.group + '. Klik bar untuk lihat senarai aduan.';
  }

  async function loadDetails(barLabel) {
    const f = getFilters();
    const params = new URLSearchParams({
      api: '1',
      action: 'details',
      group: lastSummary.group,
      label: barLabel,
      from: f.from,
      to: f.to,
      jabatan: f.jabatan,
      kategori: f.kategori,
      status: f.status
    });

    const data = await fetchJSON('Laporan_statistik_admin.php?' + params.toString());
    if (!data.success) {
      alert(data.error || 'Gagal load butiran');
      return;
    }

    const title = (lastSummary.group === 'kategori' ? 'Kategori: ' : 'Status: ') + barLabel;
    qs('modalTitle').textContent = 'Butiran Aduan (' + title + ')';

    const rows = data.rows || [];
    if (!rows.length) {
      qs('modalContent').innerHTML = '<div class="empty">Tiada data untuk filter ini.</div>';
    } else {
      let html = '<table><thead><tr>' +
        '<th>Bil</th><th>Id Aduan</th><th>Nama Pengadu</th><th>Jenis Masalah</th>' +
        '<th>Unit</th><th>Nama Technician</th><th>Tarikh Aduan</th><th>Status</th>' +
        '</tr></thead><tbody>';

      rows.forEach((r, i) => {
        html += '<tr>' +
          '<td>' + (i+1) + '</td>' +
          '<td>' + esc(r.idAduan) + '</td>' +
          '<td>' + esc(r.namaPengadu) + '</td>' +
          '<td>' + esc(r.jenisMasalah) + '</td>' +
          '<td>' + esc(r.unit) + '</td>' +
          '<td>' + esc(r.namaTechnician) + '</td>' +
          '<td>' + esc(r.tarikhAduan) + '</td>' +
          '<td>' + esc(r.status) + '</td>' +
          '</tr>';
      });

      html += '</tbody></table>';
      qs('modalContent').innerHTML = html;
    }

    qs('modal').classList.add('show');
  }

  function esc(s){
    if (s === null || s === undefined) return '';
    return String(s)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  qs('btnCari').addEventListener('click', loadSummary);
  qs('closeModal').addEventListener('click', () => qs('modal').classList.remove('show'));
  qs('modal').addEventListener('click', (e) => {
    if (e.target.id === 'modal') qs('modal').classList.remove('show');
  });

  // Export Excel (gunakan fail export_excel.php)
  qs('btnExcel').addEventListener('click', () => {
    const f = getFilters();
    const params = new URLSearchParams(f);
    window.location.href = 'export_excel.php?' + params.toString();
  });

  // Export PDF (chart sahaja)
  qs('btnPDFChart').addEventListener('click', () => {
    if (!chart) {
      alert("Chart belum ada. Tekan 'Cari' dulu.");
      return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

    const canvas = document.getElementById('statChart');
    const imgData = canvas.toDataURL('image/png', 1.0);

    doc.addImage(imgData, 'PNG', 10, 10, 277, 190);
    doc.save('chart.pdf');
  });

  (async function(){
    await loadJabatanOptions();
    await loadSummary();
  })();

  function confirmLogout() {
    return confirm("Anda Pasti Untuk Log Keluar?");
  }
</script>

</body>
</html>
