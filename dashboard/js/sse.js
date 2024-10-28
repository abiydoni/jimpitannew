document.addEventListener('DOMContentLoaded', function() {
    const eventSource = new EventSource('../api/sse.php');

    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);

        // Update UI for top 5 fastest check-ins
        var medalList = document.getElementById('medal-list');
				// Contoh penggunaan:
				for (var i = 0; i < 6; i++) {
					var position = i + 1;
					var suffix = getRankSuffix(position);
					var listItem = medalList.children[i];
				
					if (data.fastest_checkin[i]) {
						listItem.innerHTML = '<p>' + position + suffix + ' ' + data.fastest_checkin[i].NAMA_GENG + '</p>' +
											 '<i class="bx bx-medal"></i>';
					} else {
						listItem.innerHTML = '<p>' + position + suffix + '</p>' +
											 '<i class="bx bx-medal"></i>';

					}
				}             
                //

		function getRankSuffix(position) {
			// Mendapatkan angka terakhir dari posisi (misalnya 1, 2, 3, dst)
			var lastDigit = position % 10;
			var secondLastDigit = Math.floor(position / 10) % 10;
		
			// Aturan dasar:
			if (secondLastDigit === 1) {
				return "<sup>th</sup>";
			} else {
				switch (lastDigit) {
					case 1:
						return "<sup>st</sup>";
					case 2:
						return "<sup>nd</sup>";
					case 3:
						return "<sup>rd</sup>";
					default:
						return "<sup>th</sup>";
				}
			}
		}

        // Update statistik
        document.getElementById('totalPeserta').innerText = data.total_peserta;
        document.getElementById('totalCheck').innerText = data.total_check;
        document.getElementById('totalUncheck').innerText = data.total_uncheck;
        };
    });

// Function to fetch data from server
function fetchData() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../api/checked_data.php', true); // Adjust URL based on your server endpoint
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            data = JSON.parse(xhr.responseText); // Update global variable 'data' with fetched data
            populateCheckinTable(data); // Populate the table with fetched data
        }
    };
    xhr.send();
}

// Call fetchData function to initiate data fetching and table population
fetchData();

let startIndex = 0; // Index of the first row to display
const rowsToShowCount = 6; // Number of rows to display each time

// Call updateTable function initially
updateTable();

// Call updateTable every 15 seconds
setInterval(updateTable, 30000); // 10000 milliseconds = 10 seconds