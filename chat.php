<?php
declare(strict_types=1);
session_start(); require __DIR__.'/config.php';
if(empty($_SESSION['room'])||empty($_SESSION['name'])||!room_exists($_SESSION['room'])) { header('Location:index.php'); exit; }
$room=$_SESSION['room']; $name=$_SESSION['name'];
?>
<!doctype html><html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>غرفة <?=htmlspecialchars($room)?></title><link rel="stylesheet" href="style.css"></head>
<body class="chatbody"><div class="chatapp">
<header class="topbar"><div><div class="brand">غرفة<span>•</span></div><small>دردشة خاصة</small></div>
<div class="roomcode"><span>كود الغرفة</span><b id="roomCode"><?=htmlspecialchars($room)?></b><button onclick="copyCode()">نسخ</button></div>
<a class="leave" href="leave.php">خروج</a></header>
<div id="messages" class="messages"><div class="loading">جاري تحميل المحادثة...</div></div>
<form id="sendForm" class="sendbar"><input id="msg" maxlength="1000" autocomplete="off" placeholder="اكتب رسالتك..." required><button>إرسال</button></form>
</div>
<script>
const room=<?=json_encode($room)?>, me=<?=json_encode($name)?>;
let last=0, loading=false;
const box=document.getElementById('messages');
function esc(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
function render(items){
 for(const m of items){last=Math.max(last,Number(m.id));const mine=m.name===me;
  const el=document.createElement('div');el.className='msg '+(mine?'mine':'');
  el.innerHTML=`<div class="bubble"><div class="meta">${esc(m.name)} <span>${esc(m.time)}</span></div><div>${esc(m.body).replace(/\n/g,'<br>')}</div></div>`;
  box.appendChild(el);
 }
 if(items.length) box.scrollTop=box.scrollHeight;
}
async function poll(){if(loading)return;loading=true;try{const r=await fetch('api.php?action=messages&after='+last,{cache:'no-store'});const d=await r.json();if(d.ok)render(d.messages);}catch(e){}loading=false;}
document.getElementById('sendForm').addEventListener('submit',async e=>{e.preventDefault();const i=document.getElementById('msg');const body=i.value.trim();if(!body)return;
 i.disabled=true;try{const r=await fetch('api.php?action=send',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({body})});const d=await r.json();if(!d.ok)alert(d.error||'تعذر الإرسال');else{render([d.message]);i.value='';}}finally{i.disabled=false;i.focus();}});
function copyCode(){navigator.clipboard?.writeText(room);alert('تم نسخ كود الغرفة: '+room);}
poll();setInterval(poll,1200);
</script></body></html>