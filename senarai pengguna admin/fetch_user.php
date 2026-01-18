<?php
include "db_connect.php";

$noIC = isset($_POST['noIC']) ? $_POST['noIC'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

if(!$noIC || !$type){
    echo json_encode([]);
    exit;
}

$tables = [
    'user' => [
        'table'=>'user',
        'noIC'=>'noIC',
        'name'=>'namaUser',
        'email'=>'emel',
        'idJabatan'=>'idJabatan',
        'idPeranan'=>'idPeranan',
        'password'=>'kataLaluan',
        'jawatan'=>'jawatan',
        'noOffice'=>'noOffice'
    ],
    'admin' => [
        'table'=>'admin',
        'noIC'=>'noICAdmin',
        'name'=>'namaAdmin',
        'email'=>'emelAdmin',
        'idJabatan'=>'idJabatan',
        'idPeranan'=>'idPeranan',
        'password'=>'kataLaluanAdmin',
        'jawatan'=>'jawatanAdmin',
        'noOffice'=>'noOfficeAdmin'
    ],
    'technician' => [
        'table'=>'technician',
        'noIC'=>'noICTechnician',
        'name'=>'namaTechnician',
        'email'=>'emelTechnician',
        'idJabatan'=>'idJabatan',
        'idPeranan'=>'idPeranan',
        'password'=>'kataLaluanTechnician',
        'jawatan'=>'jawatanTechnician',
        'noOffice'=>'noOfficeTechnician'
    ],
    'ketuaunit' => [
        'table'=>'ketuaunit',
        'noIC'=>'noICKetua',
        'name'=>'namaKetua',
        'email'=>'emelKetua',
        'idJabatan'=>'idJabatan',
        'idPeranan'=>'idPeranan',
        'password'=>'kataLaluanKetua',
        'jawatan'=>'jawatanKetua',
        'noOffice'=>'noOfficeKetua'
    ]
];

if(!isset($tables[$type])){
    echo json_encode([]);
    exit;
}

$cols = $tables[$type];

$sql = "SELECT * FROM ".$cols['table']." WHERE ".$cols['noIC']." = ?";
$stmt = $conn->prepare($sql);
if(!$stmt){
    echo json_encode([]);
    exit;
}
$stmt->bind_param("s", $noIC);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if($data){
    $response = [
        'noIC' => $data[$cols['noIC']],
        'namaUser' => $data[$cols['name']],
        'emel' => $data[$cols['email']],
        'idJabatan' => $data[$cols['idJabatan']],
        'idPeranan' => $data[$cols['idPeranan']],
        'kataLaluan' => $data[$cols['password']],
        'jawatan' => $data[$cols['jawatan']],
        'noOffice' => $data[$cols['noOffice']]
    ];
    echo json_encode($response);
} else {
    echo json_encode([]);
}
?>
