<?php
declare(strict_types=1);
session_start(); require __DIR__.'/config.php';
if(empty($_SESSION['room'])||empty($_SESSION['name'])) json_out(['ok'=>false,'error'=>'انتهت الجلسة.']);
$room=$_SESSION['room']; $name=$_SESSION['name']; $action=$_GET['action']??'';
if(!room_exists($room)) json_out(['ok'=>false,'error'=>'الغرفة غير موجودة.']);
if($action==='send'){
 $body=clean_text($_POST['body']??'',1000);
 if($body==='') json_out(['ok'=>false,'error'=>'الرسالة فارغة.']);
 $s=db()->prepare("INSERT INTO messages(room,name,body,created_at) VALUES(?,?,?,?)");$s->execute([$room,$name,$body,time()]);
 $id=(int)db()->lastInsertId(); json_out(['ok'=>true,'message'=>['id'=>$id,'name'=>$name,'body'=>$body,'time'=>date('H:i')]]);
}
if($action==='messages'){
 $after=max(0,(int)($_GET['after']??0));
 $s=db()->prepare("SELECT id,name,body,created_at FROM messages WHERE room=? AND id>? ORDER BY id ASC LIMIT ".POLL_LIMIT);$s->execute([$room,$after]);
 $rows=$s->fetchAll(); foreach($rows as &$r)$r['time']=date('H:i',$r['created_at']); unset($r);
 json_out(['ok'=>true,'messages'=>$rows]);
}
json_out(['ok'=>false,'error'=>'طلب غير معروف.']);