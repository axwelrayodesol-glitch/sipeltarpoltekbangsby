<?php
// wa_gateway.php
// Helper untuk mengirim pesan WhatsApp otomatis

function sendWA($target, $message) {
    // ------------------------------------------------------------------
    // SETTING API WHATSAPP (CONTOH MENGGUNAKAN FONNTE.COM)
    // Silakan daftar di fonnte.com untuk mendapatkan TOKEN API
    // ------------------------------------------------------------------
    $token = 'TOKEN_API_ANDA_DI_SINI'; 
    
    // Jika nomor diawali 0, ubah ke 62
    if (substr($target, 0, 1) == '0') {
        $target = '62' . substr($target, 1);
    }
    
    // Jika token masih default, sistem tidak akan benar-benar hit API agar tidak error
    if ($token == 'TOKEN_API_ANDA_DI_SINI' || empty($token)) {
        // Mode Simulasi (Bisa tambahkan log insert database di sini jika perlu)
        return false;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $message,
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}
?>
