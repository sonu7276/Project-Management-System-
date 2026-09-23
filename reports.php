<?php
require '../../database/auth.php';
require_admin();
require '../../database/db.php';

function dc($f,$a,$b){
    $p=[];
    if($a) $p[]="$f>='".$GLOBALS['conn']->real_escape_string($a)." 00:00:00'";
    if($b) $p[]="$f<='".$GLOBALS['conn']->real_escape_string($b)." 23:59:59'";
    return $p?'AND '.implode(' AND ',$p):'';
}
function kw($cols,$k){
    if(!$k) return '';
    $ek=$GLOBALS['conn']->real_escape_string($k);
    return 'AND ('.implode(' OR ',array_map(fn($c)=>"$c LIKE '%$ek%'",$cols)).')';
}
function q($sql){ $a=[]; $r=$GLOBALS['conn']->query($sql); if($r) while($row=$r->fetch_assoc()) $a[]=$row; return $a; }
function fmt($d){ return $d?date('d M Y',strtotime($d)):'—'; }
function badge($v,$map){ $c=$map[$v]??'secondary'; return "<span class='badge bg-$c'>".ucfirst($v)."</span>"; }

// Active report tab
$tab = $_GET['tab'] ?? '1';
$kw  = trim($_GET['kw'] ?? '');
$df  = $_GET['df'] ?? '';
$dt  = $_GET['dt'] ?? '';

// Summary counts (always shown)
$cnt_s = q("SELECT COUNT(*) c FROM users WHERE role='student'")[0]['c'];
$cnt_p = q("SELECT COUNT(*) c FROM projects")[0]['c'];
$cnt_t = q("SELECT COUNT(DISTINCT project_id) c FROM project_members")[0]['c'];
$cnt_r = q("SELECT COUNT(*) c FROM join_requests")[0]['c'];
$cnt_m = q("SELECT COUNT(*) c FROM messages")[0]['c'];

// Fetch only active report data
$data = [];
$headers = [];
switch($tab){
    case '1':
        $headers=['Full Name','Username','Email','Roll No','College','Year','Projects','Registered'];
        $data=q("SELECT u.full_name,u.username,u.email,
            COALESCE(sp.roll_no,'—') rn,COALESCE(sp.college,'—') cl,COALESCE(sp.study_year,'—') sy,
            COUNT(DISTINCT pm.project_id) pc,u.created_at
            FROM users u
            LEFT JOIN student_profiles sp ON sp.user_id=u.id
            LEFT JOIN project_members pm ON pm.user_id=u.id
            WHERE u.role='student'
            ".dc('u.created_at',$df,$dt)." ".kw(['u.full_name','u.username','u.email','sp.roll_no'],$kw)."
            GROUP BY u.id ORDER BY u.created_at DESC");
        break;
    case '2':
        $headers=['Title','Owner','Domain','Technology','Status','Max Size','Members','Created'];
        $data=q("SELECT p.title,u.username owner,p.domain,p.technology,p.status,p.max_team_size,
            COUNT(DISTINCT pm.user_id) mc,p.created_at
            FROM projects p
            LEFT JOIN users u ON u.id=p.created_by
            LEFT JOIN project_members pm ON pm.project_id=p.id
            WHERE 1=1
            ".dc('p.created_at',$df,$dt)." ".kw(['p.title','p.domain','p.technology','u.username'],$kw)."
            GROUP BY p.id ORDER BY p.created_at DESC");
        break;
    case '3':
        $headers=['Project','Full Name','Username','Email','Role','Joined'];
        $data=q("SELECT p.title pt,u.full_name,u.username,u.email,pm.role mr,pm.joined_at
            FROM project_members pm
            JOIN projects p ON p.id=pm.project_id
            JOIN users u ON u.id=pm.user_id
            WHERE 1=1 ".kw(['p.title','u.full_name','u.email'],$kw)."
            ORDER BY p.id,pm.joined_at");
        break;
    case '4':
        $headers=['Student','Email','Project','Status','Date'];
        $data=q("SELECT u.full_name,u.email,p.title proj,jr.status,jr.request_date
            FROM join_requests jr
            JOIN users u ON u.id=jr.student_id
            JOIN projects p ON p.id=jr.project_id
            WHERE 1=1
            ".dc('jr.request_date',$df,$dt)." ".kw(['u.full_name','u.email','p.title','jr.status'],$kw)."
            ORDER BY jr.request_date DESC");
        break;
    case '5':
        $headers=['Sender','Receiver','Project','Message Preview','File','Sent At'];
        $data=q("SELECT s.username sender,COALESCE(r.username,'—') recv,p.title proj,
            LEFT(m.message_text,70) msg,IF(m.file_path IS NOT NULL,'Yes','No') hf,m.sent_at
            FROM messages m
            JOIN users s ON s.id=m.sender_id
            LEFT JOIN users r ON r.id=m.receiver_id
            JOIN projects p ON p.id=m.project_id
            WHERE 1=1
            ".dc('m.sent_at',$df,$dt)." ".kw(['s.username','p.title'],$kw)."
            ORDER BY m.sent_at DESC LIMIT 200");
        break;
}

$reportDefs = [
    '1'=>['label'=>'Students',     'icon'=>'bi-people-fill',     'color'=>'primary'],
    '2'=>['label'=>'Projects',     'icon'=>'bi-folder-fill',     'color'=>'success'],
    '3'=>['label'=>'Team Members', 'icon'=>'bi-diagram-3-fill',  'color'=>'warning'],
    '4'=>['label'=>'Join Requests','icon'=>'bi-hourglass-split', 'color'=>'danger'],
    '5'=>['label'=>'Chat Messages','icon'=>'bi-chat-dots-fill',  'color'=>'info'],
];

$now = date('d M Y, h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>System Reports — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:#f4f6f9;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;}
.top-banner{background:linear-gradient(135deg,#1e3c72,#2a5298);color:#fff;padding:25px;text-align:center;font-size:28px;font-weight:600;letter-spacing:1px;}
.custom-nav{background:#1f2d3d;}
.custom-nav .nav-link{color:#cfd8dc!important;font-weight:500;}
.custom-nav .nav-link:hover,.custom-nav .nav-link.active{color:#fff!important;}

/* Summary cards removed */


/* ── REPORTS CARD ── */
.reports-card{background:#fff;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,.08);overflow:hidden;}

/* ── CARD HEADER ── */
.rc-header{background:linear-gradient(135deg,#1e3c72,#2a5298);color:#fff;padding:16px 24px;display:flex;align-items:center;gap:12px;}
.rc-header h5{margin:0;font-size:17px;font-weight:700;flex:1;}
.btn-print-all{background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.5);color:#fff;border-radius:8px;padding:7px 16px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s;}
.btn-print-all:hover{background:rgba(255,255,255,.28);}

/* ── REPORT BUTTONS ── */
.report-btns{background:#f8f9fc;padding:16px 20px;border-bottom:2px solid #eef2f7;display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
.report-btns a{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;border:2px solid transparent;transition:.2s;background:#fff;color:#4a5568;border-color:#e2e8f0;}
.report-btns a:hover{border-color:#1e3c72;color:#1e3c72;}
.report-btns a.active-btn{background:#1e3c72;color:#fff;border-color:#1e3c72;box-shadow:0 4px 12px rgba(30,60,114,.3);}
.report-btns a .btn-cnt{background:rgba(255,255,255,.25);border-radius:12px;padding:1px 8px;font-size:11px;}
.report-btns a:not(.active-btn) .btn-cnt{background:#eef2f7;color:#4a5568;}

/* ── ACTIVE REPORT HEADER ── */
.active-report-head{background:#f1f5fb;padding:13px 22px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e8f0;}
.art-title{font-weight:700;font-size:15px;color:#1f2d3d;flex:1;display:flex;align-items:center;gap:8px;}
.art-cnt{background:#1e3c72;color:#fff;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;}
/* Print button cleaned up */


/* ── FILTER BAR ── */
.filter-bar{background:#fafbff;padding:12px 22px;border-bottom:1px solid #e2e8f0;}
.filter-bar label{font-size:11px;font-weight:600;color:#4a5568;margin-bottom:3px;display:block;}
.filter-bar .form-control{font-size:12px;border-radius:7px;}
.filter-bar .btn{border-radius:7px;}

/* ── TABLE ── */
.rs-table-wrap{overflow-x:auto;}
.rep-table{margin:0;font-size:13px;}
.rep-table thead th{background:#f1f5fb;font-weight:600;color:#4a5568;border-bottom:2px solid #e2e8f0;padding:11px 14px;white-space:nowrap;}
.rep-table tbody tr:hover{background:#f5f8ff;}
.rep-table td{padding:10px 14px;vertical-align:middle;border-color:#f0f4f8;}
.no-data{text-align:center;padding:40px;color:#a0aec0;}
.no-data i{font-size:42px;display:block;margin-bottom:10px;}
.results-bar{font-size:12px;color:#718096;padding:8px 22px;background:#fafbff;border-bottom:1px solid #f0f4f8;}

/* ── PRINT ── */
@media print{
  .no-print,nav,.top-banner,.report-btns,.filter-bar,.results-bar,.btn-print-rep,.btn-print-all{display:none!important;}
  body{background:#fff!important;}
  .reports-card{box-shadow:none!important;border:1px solid #ccc;}
  .active-report-head{background:#f1f5fb!important;print-color-adjust:exact;}
  .rep-table thead th{background:#f1f5fb!important;print-color-adjust:exact;}
  .print-hdr{display:block!important;}
  .container{max-width:100%!important;}
}
.print-hdr{display:none;text-align:center;padding:10px 0;border-bottom:1px solid #ddd;margin-bottom:8px;}
</style>
</head>
<body>

<!-- Banner -->
<div class="top-banner no-print">PROJECT MANAGEMENT SYSTEM</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg custom-nav no-print">
  <div class="container">
    <div class="navbar-nav">
      <a class="nav-link" href="../home.php">Home</a>
      <a class="nav-link" href="../about.php">About Us</a>
      <a class="nav-link active" href="adminDashboard.php">Admin Dashboard</a>
      <a class="nav-link" href="../logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">

  <!-- Back + Title -->
  <div class="d-flex align-items-center gap-2 mb-3 no-print">
   
    <h4 class="mb-0 fw-bold" style="color:#1f2d3d"><i class="bi bi-bar-chart-fill text-primary me-1"></i>System Reports</h4>
     <a href="adminDashboard.php" class="btn btn-secondary ms-auto"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>

  <!-- Print-only header -->
  <div class="print-hdr">
    <strong>PROJECT MANAGEMENT SYSTEM — SYSTEM REPORTS</strong><br>
    <small>Report: <b><?php echo $reportDefs[$tab]['label']; ?></b> &nbsp;|&nbsp; Generated: <?php echo $now; ?>
    <?php if($df||$dt) echo " &nbsp;|&nbsp; $df – $dt"; ?>
    <?php if($kw) echo " &nbsp;|&nbsp; Search: \"".htmlspecialchars($kw)."\""; ?>
    </small>
  </div>

  <!-- Summary counts are now only shown in report selection buttons -->


  <!-- ── MAIN REPORTS CARD ── -->
  <div class="reports-card">

    <!-- Card Header -->
    <div class="rc-header">
      <i class="bi bi-file-earmark-bar-graph fs-5"></i>
      <h5>Detailed Reports</h5>
      <button class="btn-print-all no-print" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Print Report
      </button>
    </div>

    <!-- ── REPORT SELECTION BUTTONS ── -->
    <div class="report-btns no-print">
      <span class="fw-600 text-secondary me-1" style="font-size:13px;">Select Report:</span>
      <?php
      $counts = [$cnt_s,$cnt_p,$cnt_t,$cnt_r,$cnt_m];
      foreach($reportDefs as $tid=>$rd):
        $active = $tab===$tid ? 'active-btn' : '';
        $cnt = $counts[$tid-1];
      ?>
      <a href="?tab=<?php echo $tid;?>" class="<?php echo $active;?>">
        <i class="bi <?php echo $rd['icon'];?>"></i>
        <?php echo $rd['label'];?>
        <span class="btn-cnt"><?php echo $cnt;?></span>
      </a>
      <?php endforeach;?>
    </div>

    <!-- ── ACTIVE REPORT HEADER ── -->
    <?php $ar=$reportDefs[$tab]; ?>
    <div class="active-report-head">
      <div class="art-title">
        <i class="bi <?php echo $ar['icon'];?> text-<?php echo $ar['color'];?> fs-5"></i>
        Report <?php echo $tab;?>: <?php echo $ar['label'];?> Data
      </div>
      <span class="art-cnt"><?php echo count($data);?> records</span>
    </div>

    <!-- ── FILTER BAR ── -->
    <div class="filter-bar no-print">
      <form method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab);?>">
        <div class="col-md-3">
          <label>Date From</label>
          <input type="date" name="df" class="form-control form-control-sm"
                 value="<?php echo htmlspecialchars($df);?>" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
          <label>Date To</label>
          <input type="date" name="dt" class="form-control form-control-sm"
                 value="<?php echo htmlspecialchars($dt);?>" onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
          <label>Search Keyword</label>
          <input type="text" name="kw" class="form-control form-control-sm"
                 placeholder="Search name, email, title…"
                 value="<?php echo htmlspecialchars($kw);?>">
        </div>
        <div class="col-md-2 d-flex gap-1 align-items-end">
          <button type="submit" class="btn btn-primary btn-sm flex-fill">
            <i class="bi bi-search"></i> Filter
          </button>
          <a href="?tab=<?php echo $tab;?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </form>
    </div>

    <!-- Results info bar -->
    <?php if($kw||$df||$dt): ?>
    <div class="results-bar no-print">
      Showing <?php echo count($data);?> result(s)
      <?php if($kw) echo " for <strong>\"".htmlspecialchars($kw)."\"</strong>"; ?>
      <?php if($df||$dt) echo " from <strong>".($df?:'—')."</strong> to <strong>".($dt?:'—')."</strong>"; ?>
      &mdash; <a href="?tab=<?php echo $tab;?>">Clear filters</a>
    </div>
    <?php endif;?>

    <!-- ── DATA TABLE ── -->
    <?php if(empty($data)): ?>
    <div class="no-data">
      <i class="bi bi-inbox"></i>
      No records found<?php if($kw||$df||$dt) echo ' for the selected filters';?>.
    </div>
    <?php else: ?>
    <div class="rs-table-wrap">
      <table class="table rep-table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <?php foreach($headers as $h) echo "<th>".htmlspecialchars($h)."</th>"; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $i=>$r): ?>
          <tr>
            <td><?php echo $i+1;?></td>
            <?php if($tab==='1'): ?>
              <td><?php echo htmlspecialchars($r['full_name']);?></td>
              <td><i class="bi bi-person-circle text-primary me-1"></i><?php echo htmlspecialchars($r['username']);?></td>
              <td><?php echo htmlspecialchars($r['email']);?></td>
              <td><?php echo htmlspecialchars($r['rn']);?></td>
              <td><?php echo htmlspecialchars($r['cl']);?></td>
              <td><?php echo htmlspecialchars($r['sy']);?></td>
              <td><span class="badge bg-primary"><?php echo $r['pc'];?></span></td>
              <td><?php echo fmt($r['created_at']);?></td>
            <?php elseif($tab==='2'): ?>
              <td><strong><?php echo htmlspecialchars($r['title']);?></strong></td>
              <td><?php echo htmlspecialchars($r['owner']??'—');?></td>
              <td><?php echo htmlspecialchars($r['domain']);?></td>
              <td><?php echo htmlspecialchars($r['technology']);?></td>
              <td><?php echo badge($r['status'],['open'=>'success','full'=>'warning text-dark','completed'=>'info']);?></td>
              <td><?php echo $r['max_team_size'];?></td>
              <td><span class="badge bg-success"><?php echo $r['mc'];?></span></td>
              <td><?php echo fmt($r['created_at']);?></td>
            <?php elseif($tab==='3'): ?>
              <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($r['pt']);?></span></td>
              <td><?php echo htmlspecialchars($r['full_name']);?></td>
              <td><?php echo htmlspecialchars($r['username']);?></td>
              <td><?php echo htmlspecialchars($r['email']);?></td>
              <td><?php echo badge($r['mr'],['leader'=>'danger','member'=>'secondary']);?></td>
              <td><?php echo fmt($r['joined_at']);?></td>
            <?php elseif($tab==='4'): ?>
              <td><?php echo htmlspecialchars($r['full_name']);?></td>
              <td><?php echo htmlspecialchars($r['email']);?></td>
              <td><?php echo htmlspecialchars($r['proj']);?></td>
              <td><?php echo badge($r['status'],['pending'=>'warning text-dark','accepted'=>'success','rejected'=>'danger']);?></td>
              <td><?php echo fmt($r['request_date']);?></td>
            <?php elseif($tab==='5'): ?>
              <td><i class="bi bi-person-fill text-primary me-1"></i><?php echo htmlspecialchars($r['sender']);?></td>
              <td><?php echo htmlspecialchars($r['recv']);?></td>
              <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['proj']);?></span></td>
              <td class="text-muted" style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($r['msg']);?></td>
              <td><?php echo $r['hf']==='Yes'?"<span class='badge bg-info'>Yes</span>":"<span class='badge bg-light text-muted border'>No</span>";?></td>
              <td><?php echo $r['sent_at']?date('d M Y H:i',strtotime($r['sent_at'])):'—';?></td>
            <?php endif;?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <?php endif;?>

  </div><!-- /reports-card -->
</div><!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
