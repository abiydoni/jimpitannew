document
  .getElementById("reportBtn")
  .addEventListener("click", async function () {
    const monthPicker = document.getElementById("monthPicker").value; // Format "Oct 2024"
    if (!monthPicker) {
      Swal.fire({
        icon: "warning",
        title: "Ooops!",
        text: "Silakan pilih tanggal & tahun laporan yg akan di unduh",
        timer: 10000,
        timerProgressBar: true,
        customClass: {
          popup: "rounded",
          timerProgressBar: "custom-timer-progress-bar",
          confirmButton: "roundedBtn",
        },
      });
      return;
    }

    const [month, year] = monthPicker.split(" ");
    const monthNumber = new Date(Date.parse(month + " 1, 2024")).getMonth() + 1;
    if (isNaN(monthNumber) || monthNumber < 1 || monthNumber > 12) {
      alert("Invalid month selected");
      return;
    }

    // **Mengambil tarif dari API secara async**
    let nominal = 0; // Default nominal jika API gagal memberikan data

    // Fungsi untuk mendapatkan tarif dari server (menggunakan async/await)
    async function fetchTarif() {
      try {
        const response = await fetch("../api/fetch_tarif.php");
        const data = await response.json();
        if (data.success) {
          nominal = data.tarif;
          console.log("Tarif berhasil diperoleh:", nominal);
        } else {
          console.warn("Gagal mengambil tarif:", data.message);
        }
      } catch (error) {
        console.error("Kesalahan mengambil tarif:", error);
      }
    }

    // **Menunggu fetchTarif selesai sebelum lanjut**
    await fetchTarif(); // Pastikan tarif diambil sebelum melanjutkan

    // Mengambil laporan berdasarkan bulan dan tahun yang dipilih
    const reportResponse = await fetch(
      `../api/fetch_reports.php?month=${monthNumber}&year=${year}`
    );
    const dataReports = await reportResponse.json();

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Reports");

    worksheet.getCell("A1").value = "Jimpitan - RT07 Salatiga";
    worksheet.getCell("A1").alignment = {
      horizontal: "left",
      vertical: "middle",
    };
    worksheet.getCell("A1").font = { bold: true, size: 14 };

    const monthNames = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    const monthYear = `${monthNames[monthNumber - 1]} ${year}`;
    worksheet.getCell("A2").value = monthYear;
    worksheet.getCell("A2").alignment = {
      horizontal: "left",
      vertical: "middle",
    };
    worksheet.getCell("A2").font = { bold: true, size: 12 };

    worksheet.getCell("A3").value = "";

    // Menentukan jumlah hari dalam bulan yang dipilih
    const daysInMonth = new Date(year, monthNumber, 0).getDate();

    // Membuat baris header
    const headerRow = worksheet.addRow([
      "",
      ...Array.from({ length: daysInMonth }, (_, i) => i + 1),
      "Total",
      "Estimasi",
      "Piutang",
    ]);

    // Fungsi untuk mengonversi indeks kolom menjadi huruf kolom
    function getColumnLetter(columnIndex) {
      let temp;
      let letter = "";
      while (columnIndex > 0) {
        temp = (columnIndex - 1) % 26;
        letter = String.fromCharCode(temp + 65) + letter;
        columnIndex = Math.floor((columnIndex - temp) / 26);
      }
      return letter;
    }

    headerRow.eachCell((cell) => {
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "001F3F" },
      };
      cell.alignment = { horizontal: "center", vertical: "middle" };
      cell.font = { bold: true, color: { argb: "ffffff" } };
      cell.border = {
        top: { style: "thin", color: { argb: "ffffff" } },
        left: { style: "thin", color: { argb: "ffffff" } },
        bottom: { style: "thin", color: { argb: "ffffff" } },
        right: { style: "thin", color: { argb: "ffffff" } },
      };
    });

    worksheet.getColumn(1).width = 25;
    for (let i = 2; i <= daysInMonth + 3; i++) {
      worksheet.getColumn(i).width = 6; // Set width for days + Total + Estimasi + Piutang
    }

    let totalEstimation = 0; // Variabel untuk menghitung total estimasi

    dataReports.forEach((row, index) => {
      const rowData = [row.kk_name];
      let total = 0;

      for (let i = 1; i <= daysInMonth; i++) {
        const value = row[i] !== null ? Number(row[i]) : "";
        rowData.push(value);
        if (value) {
          total += value;
        }
      }

      rowData.push(total > 0 ? total : "");

      // **Hitung Estimasi menggunakan nominal**
      const estimation = nominal * daysInMonth; // **Menggunakan tarif yang diambil**
      rowData.push(estimation);

      totalEstimation += estimation; // Tambahkan estimasi ke total estimasi

      // Formula untuk Piutang
      const totalColumnIndex = daysInMonth + 2; // Kolom 'Total'
      const estimationColumnIndex = totalColumnIndex + 1; // Kolom 'Estimasi'
      const piutangColumnIndex = estimationColumnIndex + 1; // Kolom 'Piutang'

      // Jika Total kosong atau 0, Piutang = Estimasi
      const piutangFormula = `IF(OR(${getColumnLetter(totalColumnIndex)}${
        index + 5
      }="", ${getColumnLetter(totalColumnIndex)}${
        index + 5
      }=0), ${getColumnLetter(estimationColumnIndex)}${
        index + 5
      }, ${getColumnLetter(estimationColumnIndex)}${
        index + 5
      } - ${getColumnLetter(totalColumnIndex)}${index + 5})`;
      rowData.push({ formula: piutangFormula });

      const newRow = worksheet.addRow(rowData);

      let fillColor = index % 2 === 0 ? "F5F5F7" : "D2E0FB";

      if (index === dataReports.length - 1) {
        fillColor = "EAD8B1";
      }

      newRow.eachCell((cell, colNumber) => {
        cell.alignment = { horizontal: "middle", vertical: "middle" };
        cell.border = {
          top: { style: "thin", color: { argb: "ffffff" } },
          left: { style: "thin", color: { argb: "ffffff" } },
          bottom: { style: "thin", color: { argb: "ffffff" } },
          right: { style: "thin", color: { argb: "ffffff" } },
        };
        cell.fill = {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: fillColor },
        };

        if (colNumber === rowData.length) {
          cell.font = { bold: true };
          cell.width = 8;
          cell.fill = {
            type: "pattern",
            pattern: "solid",
            fgColor: { argb: fillColor },
          };
        }
      });
    });

    // Menambahkan baris total estimasi
    const totalRow = worksheet.addRow([
      "",
      ...Array(daysInMonth).fill(""), // Kosongkan untuk kolom hari
      "",
      totalEstimation,
      "",
    ]);

    totalRow.eachCell((cell, colNumber) => {
      cell.alignment = { horizontal: "center", vertical: "middle" };
      cell.font = { bold: true };
      if (colNumber === totalRow.cellCount - 1) {
        cell.fill = {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFFF00" },
        };
      }
    });

    const now = new Date();
    const timestamp = now.toTimeString().split(" ")[0].replace(/:/g, ""); // Format HHMMSS
    const monthName = monthNames[monthNumber - 1];
    const fileName = `Report_${monthName}_${year}_${timestamp}.xlsx`;

    // **Mengekspor file Excel**
    const buffer = await workbook.xlsx.writeBuffer();
    const file = new Blob([buffer], { type: "application/octet-stream" });

    const link = document.createElement("a");
    link.href = URL.createObjectURL(file);
    link.download = fileName;
    link.click();
  });
