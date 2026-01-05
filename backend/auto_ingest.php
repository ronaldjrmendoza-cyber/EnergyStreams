<?php
include 'db.php';

date_default_timezone_set('Asia/Manila');

// accessing the audio broadcasts folder that contains the recordings
$audioDir = __DIR__ . '/../audio_broadcasts/';
$files = glob($audioDir . '*.mp3');

foreach ($files as $filePath) {

    $fileName = basename($filePath);

    // expected filename: rec_YYYYMMDD-HHMMSS.mp3
    if (!preg_match('/rec_(\d{8})-(\d{6})\.mp3/', $fileName, $m)) {
        continue;
    }

    // parses the date & start time from filename
    $date = DateTime::createFromFormat('Ymd', $m[1])->format('Y-m-d');
    $startTime = DateTime::createFromFormat('His', $m[2])->format('H:i:s');

    // assumes 1-hour duration
    $endTime = date('H:i:s', strtotime($startTime . ' +1 hour'));

    // path is then saved in the DB
    $relativePath = 'audio_broadcasts/' . $fileName;

    // prevents duplicates
    $check = $conn->prepare("
        SELECT ID FROM Audio_Broadcast_Log
        WHERE AUDIO_FILE_PATH = ?
    ");
    $check->bind_param('s', $relativePath);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        continue;
    }
    $check->close();

    $dayNum = date('N', strtotime($date)); // 1=Mon ... 7=Sun

    if ($dayNum >= 1 && $dayNum <= 5) {
        $dayType = 'WEEKDAYS';
    } elseif ($dayNum == 6) {
        $dayType = 'SAT';
    } else {
        $dayType = 'SUN';
    }

    // finds matching program
    $programId = null;

    $stmt = $conn->prepare("
        SELECT p.ID
        FROM Program p
        INNER JOIN Program_Day_Type pdt ON p.ID = pdt.PROGRAM_ID
        INNER JOIN Day_Type dt ON pdt.DAY_TYPE_ID = dt.ID
        WHERE dt.DAY_TYPE = ?
        AND ? BETWEEN p.START_TIME AND p.END_TIME
        LIMIT 1
    ");
    $stmt->bind_param('ss', $dayType, $startTime);
    $stmt->execute();
    $stmt->bind_result($programId);
    $stmt->fetch();
    $stmt->close();

    $insert = $conn->prepare("
        INSERT INTO Audio_Broadcast_Log
        (DATE, START_TIME, END_TIME, AUDIO_FILE_PATH, PROGRAM_ID)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param(
        'ssssi',
        $date,
        $startTime,
        $endTime,
        $relativePath,
        $programId
    );
    $insert->execute();
    $insert->close();
}
