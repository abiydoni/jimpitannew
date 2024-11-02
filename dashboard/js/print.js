$(document).ready(function() {
	// table initialize
	var table = new DataTable('#example', {
		pageLength: 10, // Set the default number of records per page to 10
        lengthMenu: [10, 25, 50, 100], // Options for the dropdown
		searching: true, // Aktifkan pencarian
		order: [[2, 'asc']], // Urutkan berdasarkan kolom kedua (indeks 1), urutan ascending
		columnDefs: [
			{ 
				"orderable": false, 
				"targets": [2] // Disable ordering for the third column (index 0)
			},
			{ 
				"targets": 1,  // Kolom ke-2 (indeks mulai dari 0)
				"className": "text-left" 
			},
			{ 
				"targets": 2,  // Kolom ke-3 (indeks mulai dari 0)
				"className": "text-left" 
			}
		],
		language: {
            lengthMenu: "_MENU_ Entri per halaman",
            zeroRecords: "No records found",
            info: "Showing page _PAGE_ of _PAGES_",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _MAX_ total records)"
        }
	});
	// Select All functionality
	$('#selectAllCheckbox').change(function() {
		var isChecked = $(this).prop('checked');
		$('.print-checkbox').prop('checked', isChecked);
	});

	// Handle individual checkbox change
	$('.print-checkbox').change(function() {
		var allChecked = $('.print-checkbox:checked').length === $('.print-checkbox').length;
		$('#selectAllCheckbox').prop('checked', allChecked);
	});

	// Event listener untuk "Enter" pada #search-input
	$('#search-input').keypress(function(event) {
		if (event.which === 13) {
			event.preventDefault(); // Mencegah form submit (jika ada)
			var searchText = $(this).val();
			table.search(searchText).draw();
		}
	});

	// Event listener untuk klik pada tombol .search-btn
	$('.search-btn').click(function(event) {
		var searchText = $('#search-input').val();
		table.search(searchText).draw();
	});

	// Event listener untuk klik pada tombol .clear-btn
	$('.clear-btn').click(function(event) {
		$('#search-input').val(''); // Mengosongkan nilai input pencarian
		table.search('').draw(); // Mereset pencarian pada tabel
		});	

	});

	 // Event listener untuk tombol Print Selected
	 document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('printSelectedBtn').addEventListener('click', function() {
			var checkboxes = document.querySelectorAll('.print-checkbox:checked');
			if (checkboxes.length === 0) {
				alert('Select at least one entry to print.');
				return;
			}

			var selectedEntries = [];
			checkboxes.forEach(function(checkbox) {
				var row = checkbox.closest('tr');
				var text = row.cells[0].textContent.trim();
				// Potong teks menjadi array kata-kata
				var words = text.split(' ');

				// Ambil hanya 3 kata pertama
				var firstThreeWords = words.slice(0, 3);

				// Gabungkan 3 kata tersebut kembali dengan spasi di antara mereka
				var namaKK = firstThreeWords.join(' ');

				var codeID = row.cells[1].textContent.trim();
				selectedEntries.push({ namaKK: namaKK, codeID: codeID });
			});

			printSelectedEntries(selectedEntries);
		});
	});

	function printSelectedEntries(entries) {
		// Create an iframe element dynamically
		var iframe = document.createElement('iframe');
		iframe.style.position = 'absolute';
		iframe.style.left = '-9999px'; // Position outside the screen
		iframe.style.width = '210mm'; // Set width to A4
		iframe.style.height = '297mm'; // Set height to A4
		iframe.style.border = 'none'; // Remove border
		document.body.appendChild(iframe);
	
		var iframeDoc = iframe.contentWindow.document;
		iframeDoc.open();
		iframeDoc.write(`
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Print QR Codes</title>
			<style>
				@page { 
					size: A4;
					margin: 0; /* Adjust margins as necessary */
				}
				body {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-around;
					align-items: center;
					padding: 0;
					margin: 0;
					height: 100%;
				}
				.container {
					width: 45%; /* Adjust width to fit 2 per row */
					margin: 2mm; /* Add margin for spacing */
					text-align: center;
					position: relative;
					border: 1px solid #000; /* Add border around each container */
					border-radius: 5px; /* Optional: add border radius */
					padding: 10px; /* Add padding for spacing inside the border */
				}
				.qrCode {
					margin: 0 auto;
				}
				.NamaKK, .CodeText {
					font-family: 'Adumu';
					margin-top: 5px;
				}
				.NamaKK {
					font-size: 24px; /* Adjust size as needed */
				}
				.CodeText {
					font-size: 18px; /* Adjust size as needed */
				}
				/* Page break style */
				.page-break {
					page-break-after: always; /* Add page break after this element */
					display: block; /* Ensure it occupies space */
				}
			</style>
		</head>
		<body>
		`);
	
		entries.forEach(function(entry, index) {
			var containerDiv = document.createElement('div');
			containerDiv.classList.add('container');
	
			var qrCodeDiv = document.createElement('div');
			qrCodeDiv.classList.add('qrCode');
	
			new QRCode(qrCodeDiv, {
				text: entry.codeID,
				width: 120,
				height: 120,
				colorDark: '#000000',
				// colorLight: 'rgba(255, 255, 255, 0)',
				correctLevel: QRCode.CorrectLevel.H
			});
	
			containerDiv.appendChild(qrCodeDiv);
			var NamaKKDiv = document.createElement('div');
			NamaKKDiv.className = 'NamaKK';
			NamaKKDiv.textContent = entry.namaKK;
	
			containerDiv.appendChild(NamaKKDiv);
			iframeDoc.body.appendChild(containerDiv);
	
			// Insert a page break after every 10 entries
			if ((index + 1) % 10 === 0) {
				var pageBreakDiv = document.createElement('div');
				pageBreakDiv.className = 'page-break';
				iframeDoc.body.appendChild(pageBreakDiv);
			}
		});
	
		iframeDoc.write(`
		</body>
		</html>
		`);
	
		iframeDoc.close();
	
		iframe.onload = function() {
			iframe.contentWindow.print();
			setTimeout(function() {
				document.body.removeChild(iframe);
			}, 100);
		};
	}
	
	function printUsers() {
		const printContent = document.getElementById("printableTable").innerHTML;
		const originalContent = document.body.innerHTML;
	
		document.body.innerHTML = printContent;
		window.print();
		document.body.innerHTML = originalContent;
		window.location.reload();
	}
	