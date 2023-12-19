<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location:login.php");
}

include 'database.php';

// Selectarea membrilor din baza de date
$sql = "SELECT * FROM membri";
$result = mysqli_query($conn, $sql);
$membri = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selectarea antrenorilor pentru lista 'antrenori'
$sql = "SELECT id, nume FROM antrenori";
$result = mysqli_query($conn, $sql);
$antrenori = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (!is_numeric($id)) {
        echo "Invalid ID";
        exit;
    }

    $sql = "DELETE FROM membri WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    header("Location: membri.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = mysqli_real_escape_string($conn, $_POST['name']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $varsta = mysqli_real_escape_string($conn, $_POST['age']);
    $data_inregistrarii = mysqli_real_escape_string($conn, $_POST['registration_date']);
    $numar_telefon = mysqli_real_escape_string($conn, $_POST['phone']);
    $antrenor = mysqli_real_escape_string($conn, $_POST['trainer']);
    $abonament = mysqli_real_escape_string($conn, $_POST['membership']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $memberId = mysqli_real_escape_string($conn, $_POST['memberId']);

    if (!empty($memberId)) {
        // Editarea unui membru existent
        $sql = "UPDATE membri SET nume = '$nume', sex = '$sex', varsta = '$varsta', data_inregistrarii = '$data_inregistrarii', numar_telefon = '$numar_telefon', antrenor = '$antrenor', abonament = '$abonament', status = '$status' WHERE id = $memberId";
        mysqli_query($conn, $sql);
    } else {
        // Adăugarea unui membru nou
        $sql = "INSERT INTO membri (nume, sex, varsta, data_inregistrarii, numar_telefon, antrenor, abonament, status) VALUES ('$nume', '$sex', '$varsta', '$data_inregistrarii', '$numar_telefon', '$antrenor', '$abonament', '$status')";
        mysqli_query($conn, $sql);
    }

    header("Location: membri.php");
    exit();
}

// Functie ce preia numele antrenorului in functie de ID
function getMemberNameById($antrenori, $id) {
    foreach ($antrenori as $antrenor) {
        if ($antrenor['id'] == $id) {
            return $antrenor['nume'];
        }
    }
    return 'N/A'; // În cazul în care nu se găsește un membru cu ID-ul respectiv
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="membri.css">
</head>
<body>
<div class="sidebar">
        <a href="membri.php" id="denumireLink">Membri</a>
        <a href="antrenori.php">Antrenori</a>
        <a href="">Membership</a>
        <a href="">Raport</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <h2>Fitness Manager</h2>
            <p>Bine ai venit <span>Nume</span></p>
            <h3>
                Gestionare <span id="titluHeader"></span>
            </h3>
        </div>

        <div class="content">
        <button id="addMemberBtn" class="btn btn-primary">Adaugă Membru</button>

        <!-- formularul de adăugare membru -->
        <div id="addMemberModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form id="member-form" action="membri.php" method="POST">
                <input type="hidden" id="memberId" name="memberId">
                    <label for="name">Nume:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="sex">Sex:</label>
                    <select id="sex" name="sex" required>
                        <option value="F">Feminin</option>
                        <option value="M">Masculin</option>
                    </select>

                    <label for="age">Vârstă:</label>
                    <input type="number" id="age" name="age" required>

                    <label for="registration_date">Data de Înregistrare:</label>
                    <input type="date" id="registration_date" name="registration_date" required>

                    <label for="phone">Număr de Telefon:</label>
                    <input type="tel" id="phone" name="phone" required>

                    <label for="trainer">Antrenor Ales:</label>
                    <select id="trainer" name="trainer">
                        <option value="">Fără Antrenor</option>
                        <?php foreach ($antrenori as $antrenor): ?>
                            <option value="<?= $antrenor['id'] ?>"><?= htmlspecialchars($antrenor['nume']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="membership">Abonament:</label>
                    <select id="membership" name="membership" required>
                        <!-- Opțiunile ar trebui generate dinamic din baza de date -->
                        <option value="membership_id1">Abonament 1</option>
                        <option value="membership_id2">Abonament 2</option>
                    </select>

                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="activ">Activ</option>
                        <option value="inactiv">Inactiv</option>
                    </select>

                    <input type="submit" value="Salveaza">
                </form>
            </div>
        </div>

        <!-- Tabelul cu membri -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nume</th>
                    <th>Sex</th>
                    <th>Vârstă</th>
                    <th>Data Înregistrării</th>
                    <th>Număr Telefon</th>
                    <th>Antrenor</th>
                    <th>Abonament</th>
                    <th>Status</th>
                    <th>Gestiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($membri as $membru): ?>
                    <tr>
                        <!-- Aici afișezi datele fiecărui membru -->
                        <td><?= htmlspecialchars($membru['nume']) ?></td>
                        <td><?= htmlspecialchars($membru['sex']) ?></td>
                        <td><?= htmlspecialchars($membru['varsta']) ?></td>
                        <td><?= htmlspecialchars($membru['data_inregistrarii']) ?></td>
                        <td><?= htmlspecialchars($membru['numar_telefon']) ?></td>
                        <td>
                        <?= htmlspecialchars(getMemberNameById($antrenori, $membru['antrenor']))?>
                        </td>
                        <td><?= htmlspecialchars($membru['abonament']) ?></td>
                        <td><?= htmlspecialchars($membru['status']) ?></td>
                        <td>
                            <button class="btn btn-success editBtn" data-id="<?= $membru['id'] ?>">Editează</button>
                            <button class="btn btn-danger deleteBtn" data-id="<?= $membru['id'] ?>">Șterge</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <script>
        var modal = document.getElementById("addMemberModal");
        var btn = document.getElementById("addMemberBtn");
        var span = document.getElementsByClassName("close")[0];

        document.querySelectorAll('.editBtn').forEach(function(button) {
        button.addEventListener('click', function() {
        var memberId = this.getAttribute('data-id');
        fetch('get_member_data.php?id=' + memberId)
            .then(response => response.json())
            .then(data => {
                // Presupunând că 'data' este un obiect cu datele membrului
                document.getElementById('memberId').value = data.id;
                document.getElementById('name').value = data.nume;
                document.getElementById('sex').value = data.sex;
                document.getElementById('age').value = data.varsta;
                document.getElementById('registration_date').value = data.data_inregistrarii;
                document.getElementById('phone').value = data.numar_telefon;
                document.getElementById('trainer').value = data.antrenor;
                document.getElementById('membership').value = data.abonament;
                document.getElementById('status').value = data.status;

                modal.style.display = "block";
            })
            .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            var memberId = this.getAttribute('data-id');
            if (confirm('Ești sigur că vrei să ștergi acest membru?')) {
                window.location.href = 'membri.php?action=delete&id=' + memberId;
            }
            });
        });

        function resetForm() {
            document.getElementById('memberId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('sex').value = 'F'; 
            document.getElementById('age').value = '';
            document.getElementById('registration_date').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('trainer').value = ''; 
            document.getElementById('membership').value = '';
            document.getElementById('status').value = 'activ'; 
        }

        // Deschiderea formularului prin buton de adaugare
        btn.onclick = function() {
            resetForm();
            modal.style.display = "block";
        }

        // Inchiderea formularului prin <span> (x)
        span.onclick = function() {
            resetForm();
            modal.style.display = "none";
        }

        // Inchiderea formularului apasand oriunde in afara acestuia
        window.onclick = function(event) {
            if (event.target == modal) {
                resetForm();
                modal.style.display = "none";
            }
        }

        window.onload = function() {
            var linkText = document.getElementById('denumireLink').textContent;
            document.getElementById('titluHeader').textContent = linkText;
        };
    </script>
</body>
</html>