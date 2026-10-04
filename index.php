<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $code = strtoupper(trim($_POST['code'] ?? ''));

    if ($name === '' || mb_strlen($name) > 30) $error = 'اكتب اسمًا صحيحًا (حتى 30 حرف).';
    elseif ($action === 'create') {
        $code = strtoupper(substr(bin2hex(random_bytes(4)),0,6));
        create_room($code);
        $_SESSION['room']=$code; $_SESSION['name']=$name;
        header('Location: chat.php'); exit;
    } elseif ($action === 'join') {
        if (!preg_match('/^[A-Z0-9]{4,12}$/', $code) || !room_exists($code)) $error='كود الغرفة غير صحيح أو الغرفة غير موجودة.';
        else { $_SESSION['room']=$code; $_SESSION['name']=$name; header('Location: chat.php'); exit; }
    }
}
?>
<!doctype html><html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>غرفة — دردشة خاصة</title><link rel="stylesheet" href="style.css"></head>
<body class="home"><main class="card">
<div class="logo">غرفة<span>•</span></div><h1>دردشة خاصة بكود</h1>
<p class="sub">أنشئ غرفة خاصة وشارك الكود مع الشخص الذي تريد الدردشة معه.</p>
<?php if($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post" class="form">
<label>اسمك</label><input name="name" maxlength="30" required placeholder="مثلاً: محمد">
<div class="actions">
<button name="action" value="create" class="primary">إنشاء غرفة جديدة</button>
</div>
<div class="divider"><span>أو ادخل غرفة موجودة</span></div>
<label>كود الغرفة</label><input name="code" maxlength="12" autocomplete="off" placeholder="مثلاً A7K2P9">
<button name="action" value="join" class="secondary">دخول الغرفة</button>
</form>
<div class="features"><span>🔒 خاصة بالكود</span><span>⚡ سريعة</span><span>📱 موبايل</span></div>
</main></body></html>