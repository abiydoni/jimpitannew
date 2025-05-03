function sendMessageWhatsApp() {
  const url = "https://wa.appsbee.my.id/send-message";
  const payload = {
    phoneNumber: "6289510101008",
    message: "Halo, ini pesan dari WhatsApp Gateway!",
  };

  const options = {
    method: "post",
    contentType: "application/json",
    payload: JSON.stringify(payload),
  };

  const response = UrlFetchApp.fetch(url, options);
  Logger.log(response.getContentText());
}
