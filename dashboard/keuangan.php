<?php
session_start();
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}

// Include the database connection
include 'api/db.php';

// Prepare and execute the SQL statement
$stmt = $pdo->prepare("SELECT * FROM kas_umum ORDER BY date_trx DESC");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['tanggal'])) {
    $tanggal = htmlspecialchars($_POST['tanggal']); // Sanitize input
    echo "<div class='alert alert-success mt-3'>Tanggal yang dipilih: <strong>$tanggal</strong></div>";
}

// Mengatur timezone agar waktu sesuai dengan Indonesia
date_default_timezone_set('Asia/Jakarta');

// Mendapatkan tanggal saat ini dengan format: Tahun-Bulan-Hari (format untuk input date)
$tanggalSekarang = date("Y-m-d");
?>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>ARUS KAS</h3>
                        <button type="button" id="openModal" class="btn-download" data-bs-toggle="modal" data-bs-target="#inputModal">
                            <i class='bx bxs-add-to-queue'></i> Transaksi
                        </button>
                        <input type="text" id="datePicker" class="custom-select" placeholder="Pilih Tanggal">
                        <button type="button" id="resetFilterBtn">
                            <i class="bx bx-x-circle" style="font-size: 24px; color: red;"></i>
                        </button>
                        <button type="button" id="printButton" class="btn-print">
                            <i class='bx bxs-print'></i> Cetak
                        </button>
                        <input type="text" id="monthPicker" class="custom-select" placeholder="Pilih Bulan" />

                    </div>
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                        <thead class="bg-gray-200">
                            <tr>
                                <th style="text-align: left;">Kode</th>
                                <th style="text-align: center;">Tanggal</th>
                                <th style="text-align: center;">Reff</th>
                                <th style="text-align: left;">Keterangan</th>
                                <th style="text-align: right;">Debet</th>
                                <th style="text-align: right;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($data) {
                                    foreach ($data as $row): ?>
                                        <tr class="border-b hover:bg-gray-100">
                                            <td><?php echo htmlspecialchars($row["coa_code"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["date_trx"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["reff"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["desc_trx"]); ?></td>
                                            <td><?php echo "Rp " . number_format(htmlspecialchars($row["debet"]), 0, ',', '.'); ?></td> 
                                            <td><?php echo "Rp " . number_format(htmlspecialchars($row["kredit"]), 0, ',', '.'); ?></td> <!-- asem variable billingual hahahahah :) -->
                                        </tr>   
                                    <?php endforeach; 
                                } else {    
                                    echo '<tr><td colspan="6" style="text-align:center;">No data available</td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>  
            </div>
    <!-- Modal -->
    <div id="inputModal" class="fixed inset-0 flex items-center justify-center hidden"> 
        <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
        <div class="modal-container bg-white w-11/12 md:w-1/4 mx-auto shadow-lg" style="border-radius: 15px;"> <!-- Perkecil lebar modal -->
            <div class="modal-header flex justify-between items-center p-4">
                <h5 class="text-lg font-semibold" id="modalLabel">Tambah Data Keuangan</h5>
                <button type="button" class="close-modal text-gray-500 hover:text-gray-800" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body p-4">
                <form id="dataForm">
                    <div class="mb-4">
                        <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                        <input value="<?php echo $tanggalSekarang; ?>" type="text" id="modalDatePicker" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; height: 40px; padding: 0 10px;" placeholder="Pilih Tanggal" name="tanggal" required> <!-- Perkecil tinggi input -->
                    </div>
                    <div class="mb-4">
                        <label for="dropdown" class="block text-sm font-medium">Reff</label>
                        <select id="dropdown" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; height: 40px; padding: 0 10px;" required> <!-- Perkecil tinggi select -->
                            <option value="" disabled selected>-- Pilih Opsi --</option>
                            <option value="IN">Debet</option>
                            <option value="OUT">Kredit</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="kode" class="block text-sm font-medium">Kode</label>
                        <input type="text" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; height: 40px; padding: 0 10px;" id="kode" name="kode" readonly> <!-- Perkecil tinggi input -->
                    </div>
                    <div class="mb-4" id="debitBox" style="display: none;">
                        <label for="debitTextbox" class="block text-sm font-medium">Debit</label>
                        <input type="text" id="debitTextbox" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; height: 40px; padding: 0 10px;" placeholder="Rp."> <!-- Perkecil tinggi input -->
                    </div>
                    <div class="mb-4" id="kreditBox" style="display: none;">
                        <label for="kreditTextbox" class="block text-sm font-medium">Kredit</label>
                        <input type="text" id="kreditTextbox" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; height: 40px; padding: 0 10px;" placeholder="Rp."> <!-- Perkecil tinggi input -->
                    </div>
                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                        <textarea id="keterangan" class="mt-1 block w-full border border-gray-300" style="border-radius: 15px; padding: 10px;" placeholder="Isi keterangan" rows="3" required></textarea> <!-- Perkecil tinggi textarea -->
                    </div>
                    <button type="submit" class="w-full text-white py-2" style="background-color: #3c91e6; border-radius: 15px;">Simpan</button>
                </form>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = new DataTable('#example', {
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            order: [[1, 'desc']],
            columnDefs: [
                { targets: 0, className: "text-left" },
                { targets: 1, className: "text-center" },
                { targets: 2, className: "text-center" },
                { targets: 3, className: "text-left" },
                { targets: 4, className: "text-right" },
                { targets: 5, className: "text-right" }
            ],
            language: {
                lengthMenu: "_MENU_ Entri per halaman",
                zeroRecords: "No records found",
                info: "Showing page _PAGE_ of _PAGES_",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)"
            }
        });

        // Search functionality
        $('#search-input').keypress(function(event) {
            if (event.which === 13) {
                event.preventDefault();
                var searchText = $(this).val();
                table.search(searchText).draw();
            }
        });

        $('.search-btn').click(function() {
            var searchText = $('#search-input').val();
            table.search(searchText).draw();
        });

        $('.clear-btn').click(function() {
            $('#search-input').val('');
            table.search('').draw();
        });

        // Date picker initialization
        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                filterTableByDate(dateStr);
            }
        });

        // Filter table by date
        function filterTableByDate(selectedDate) {
            const rows = document.querySelectorAll("#example tbody tr");
            rows.forEach(row => {
                const dateCell = row.cells[1].textContent; // Assuming date is in the second column
                row.style.display = (dateCell === selectedDate) ? "" : "none";
            });
        }

        // Reset filters
        document.getElementById("resetFilterBtn").addEventListener("click", function() {
            document.getElementById("datePicker")._flatpickr.clear();
            const rows = document.querySelectorAll("#example tbody tr");
            rows.forEach(row => row.style.display = "");
        });

        // Open modal
        $('#openModal').click(function() {
            $('#inputModal').removeClass('hidden').addClass('modal-show'); // Show modal with animation
            $('#generatedCode').val(''); // Clear the generated code when opening the modal
            $('#kode').val(''); // Clear the kode input as well
        });

        // Close modal
        $('#inputModal').on('click', '.close-modal, .modal-overlay', function() {
            console.log('Modal closed'); // Debug log
            $('#inputModal').removeClass('modal-show'); // Remove class for animation

            // Wait for animation to finish before hiding modal
            setTimeout(function() {
                $('#inputModal').addClass('hidden'); // Hide modal
            }, 300); // Duration should match CSS transition
        });

        // Date picker initialization
        flatpickr("#modalDatePicker", {
            dateFormat: "Y-m-d"
        });

        // Handle form submission
        $('#dataForm').on('submit', function(e) {
            e.preventDefault(); // Prevent page refresh

            const dataToSend = {
                tanggal: $('#modalDatePicker').val(),
                kode: $('#kode').val(), // Use the generated code from the kode input
                reff: $('#dropdown').val(),
                keterangan: $('#keterangan').val(),
                debit: $('#debitTextbox').val() || '0', // Default to 0 if not filled
                kredit: $('#kreditTextbox').val() || '0' // Default to 0 if not filled
            };

            console.log(dataToSend); // Log the data being sent

            $.ajax({
                url: 'api/add_trx.php', // Your endpoint
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(dataToSend),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Data saved!', '', 'success').then(() => {
                            window.location.href = 'keuangan.php'; // Arahkan ke halaman keuangan setelah menyimpan
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                    // Reset form and close modal if necessary
                    $('#dataForm')[0].reset();
                    $('#inputModal').addClass('hidden');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire('Error saving data', '', 'error');
                }
            });
        });

        // Dropdown logic for debit/kredit
        $('#dropdown').change(function() {
            const selectedValue = $(this).val();
            $('#debitBox').toggle(selectedValue === "IN"); // Show debit input for "IN"
            $('#kreditBox').toggle(selectedValue === "OUT"); // Show kredit input for "OUT"
            
            // Generate the code when the dropdown value changes
            const generatedCode = generateCode(selectedValue);
            $('#kode').val(generatedCode); // Populate the generated code into the kode input
        });

        // Function to generate the code based on the selected option
        function generateCode(selectedValue) {
            let baseCode;

            if (selectedValue === "IN") { // For Debit IN
                baseCode = "100-000-"; // Base code for debit
            } else if (selectedValue === "OUT") { // For Kredit OUT
                baseCode = "100-000-"; // Base code for kredit
            } else {
                return ''; // Return empty if no valid option is selected
            }

            // Replace this with a dynamic count from your database
            let count = 2; // Placeholder; replace with actual count logic
            const generatedCode = `${baseCode}${String(count).padStart(3, '0')}`;

            return generatedCode; // Return the generated code
        }
        });
    </script>
    <script>
        // Inisialisasi date picker untuk memilih bulan
        flatpickr("#monthPicker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // Tampilkan bulan dalam format singkat
                    dateFormat: "Y-m", // Format tanggal yang diinginkan
                    altFormat: "F Y", // Format alternatif untuk tampilan
                    theme: "light" // Tema date picker
                })
            ]
        });

        document.getElementById('printButton').addEventListener('click', function() {
            const selectedMonth = document.getElementById('monthPicker').value; // Ambil nilai dari date picker
            if (selectedMonth) {
                // Tampilkan form cetak tanpa membuka halaman baru
                Swal.fire({
                    title: 'Form Cetak',
                    html: `<p>Bulan yang dipilih: ${selectedMonth}</p>`,
                    showCancelButton: true,
                    confirmButtonText: 'Cetak',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(`api/print_kas.php?month=${selectedMonth}`, '_blank'); // Tampilkan halaman print
                    }
                });
            } else {
                Swal.fire('Peringatan', 'Silakan pilih bulan terlebih dahulu!', 'warning'); // Tambahkan pesan peringatan
            }
        });
    </script>
