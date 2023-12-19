<?php 
    include 'database.php';

    // Selectarea antrenorilor din gym_management
    $sql = "SELECT * FROM antrenori";
    $result = mysqli_query($conn, $sql);
    $antrenori = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Selectarea membrilor pentru lista 'client'
    $sql = "SELECT id, nume FROM membri";
    $result = mysqli_query($conn, $sql);
    $membri = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nume = mysqli_real_escape_string($conn, $_POST['nume']);
        $numar_telefon = mysqli_real_escape_string($conn, $_POST['numar_telefon']);
        $sex = mysqli_real_escape_string($conn, $_POST['sex']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $data_inregistrarii = mysqli_real_escape_string($conn, $_POST['data_inregistrarii']);
        $varsta = mysqli_real_escape_string($conn, $_POST['varsta']);
        $experienta = mysqli_real_escape_string($conn, $_POST['experienta']);
        $client = mysqli_real_escape_string($conn, $_POST['client']);
        $trainerId = mysqli_real_escape_string($conn, $_POST['trainerId']);
    
        if (!empty($trainerId)) {
            // Editarea unui membru existent
            $sql = "UPDATE antrenori SET nume = '$nume', numar_telefon = '$numar_telefon', sex = '$sex', email = '$email', data_inregistrarii = '$data_inregistrarii', varsta = '$varsta', experienta = '$experienta', client = '$client' WHERE id = $trainerId";
            mysqli_query($conn, $sql);
        } else {
            // Adăugarea unui membru nou
            $sql = "INSERT INTO antrenori (nume, numar_telefon, sex, email, data_inregistrarii, varsta, experienta, client) VALUES ('$nume', '$numar_telefon', '$sex', '$email', '$data_inregistrarii', '$varsta', '$experienta', '$client')";
            mysqli_query($conn, $sql);
        }
        
        header("Location: antrenori.php");
        exit();
    }
    function getMemberNameById($membri, $id) {
        foreach ($membri as $membru) {
            if ($membru['id'] == $id) {
                return $membru['nume'];
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
    <title>Antrenori | Gym Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="membri.css">
</head>
<body>
    <div class="sidebar">
        <a href="membri.php">Membri</a>
        <a href="antrenori.php" id="denumireLink">Antrenori</a>
        <a href="">Membership</a>
        <a href="">Raport</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <h2>Fitness Manager</h2>
            <h3>
                Gestionare <span id="titluHeader"></span>
            </h3>
        </div>

        <div class="content">
            <button id="addTrainerBtn" class="btn btn-primary">Adaugă Antrenor</button>

            <!-- formular de adaugare -->
            <div id="addTrainerModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form id="trainer-form" action="antrenori.php" method="POST">
                    <input type="hidden" id="trainerId" name="trainerId">
                        <label for="nume">Nume:</label>
                        <input type="text" id="nume" name="nume" required>

                        <label for="numar_telefon">Număr de Telefon:</label>
                        <input type="text" id="numar_telefon" name="numar_telefon" required>

                        <label for="sex">Sex:</label>
                        <select id="sex" name="sex" required>
                            <option value="F">Feminin</option>
                            <option value="M">Masculin</option>
                        </select>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>

                        <label for="data_inregistrarii">Data de Înregistrare:</label>
                        <input type="date" id="data_inregistrarii" name="data_inregistrarii" required>

                        <label for="varsta">Vârstă:</label>
                        <input type="number" id="varsta" name="varsta" required>

                        <label for="experienta">Experiență (ani):</label>
                        <input type="number" id="experienta" name="experienta" required>

                        <label for="client">Client:</label>
                        <select id="client" name="client">
                            <?php foreach ($membri as $membru): ?>
                                <option value="<?= $membru['id'] ?>"><?= htmlspecialchars($membru['nume']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="submit" value="Salveaza">
                    </form>
                </div>
            </div>

            <table class="table">
            <thead>
                <tr>
                    <th>Nume</th>
                    <th>Număr Telefon</th>
                    <th>Sex</th>
                    <th>Email</th>
                    <th>Data Înregistrării</th>
                    <th>Vârstă</th>
                    <th>Experienta</th>
                    <th>Client</th>
                    <th>Gestiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($antrenori as $antrenor): ?>
                    <tr>
                        <!-- Afisarea datelor fiecărui antrenor -->
                        <td><?= htmlspecialchars($antrenor['nume']) ?></td>
                        <td><?= htmlspecialchars($antrenor['numar_telefon']) ?></td>
                        <td><?= htmlspecialchars($antrenor['sex']) ?></td>
                        <td><?= htmlspecialchars($antrenor['email']) ?></td>
                        <td><?= htmlspecialchars($antrenor['data_inregistrarii']) ?></td>
                        <td><?= htmlspecialchars($antrenor['varsta']) ?></td>
                        <td><?= htmlspecialchars($antrenor['experienta']) ?> ani</td>
                        <td>
                            <?= htmlspecialchars(getMemberNameById($membri, $antrenor['client'])) ?>
                        </td>
                        <td>
                            <button class="btn btn-success editBtn" data-id="<?= $antrenor['id'] ?>">Editează</button>
                            <button class="btn btn-danger deleteBtn" data-id="<?= $antrenor['id'] ?>">Șterge</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </div>
    </div>

    <script>
        // adauga script
        var modal = document.getElementById("addTrainerModal");
        var btn = document.getElementById("addTrainerBtn");
        var span = document.getElementsByClassName("close")[0];

        document.querySelectorAll('.editBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            var trainerId = this.getAttribute('data-id');
            fetch('get_trainer_data.php?id=' + trainerId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('trainerId').value = data.id;
                    document.getElementById('nume').value = data.nume;
                    document.getElementById('numar_telefon').value = data.numar_telefon;
                    document.getElementById('sex').value = data.sex;
                    document.getElementById('email').value = data.email;
                    document.getElementById('data_inregistrarii').value = data.data_inregistrarii;
                    document.getElementById('varsta').value = data.varsta;
                    document.getElementById('experienta').value = data.experienta;
                    document.getElementById('client').value = data.client;

                    modal.style.display = "block";
                })
                .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var trainerId = this.getAttribute('data-id');
                if (confirm('Ești sigur că vrei să ștergi acest antrenor?')) {
                    window.location.href = 'antrenori.php?action=delete&id=' + trainerId;
                }
            });
        });

        function resetForm() {
            document.getElementById('trainerId').value = '';
            document.getElementById('nume').value = '';
            document.getElementById('numar_telefon').value = '';
            document.getElementById('sex').value = 'F';
            document.getElementById('email').value = '';
            document.getElementById('data_inregistrarii').value = '';
            document.getElementById('varsta').value = '';
            document.getElementById('experienta').value = '';
            document.getElementById('client').value = '';
        }

        btn.onclick = function() {
            resetForm();
            modal.style.display = "block";
        }

        span.onclick = function() {
            resetForm();
            modal.style.display = "none";
        }

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