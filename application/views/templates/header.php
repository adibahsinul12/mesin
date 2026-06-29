<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Lab Mesin Poltesa</title>
    <style>
        /* KUNCI PERBAIKAN: Reset margin bawaan browser agar layout nempel mentok */
        html, body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: #f4f6f9; 
            min-height: 100vh;
        }

        .wrapper { display: flex; flex: 1; min-height: 100vh; }
        
        /* Kunci posisi sidebar di sebelah kiri tanpa celah */
        .sidebar { 
            width: 250px; 
            background: #222d32; 
            color: #fff; 
            padding: 20px 10px; 
            box-sizing: border-box; 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 100vh; 
            overflow-y: auto; 
            z-index: 1000; 
        }
        
        .sidebar h3 { text-align: center; margin-bottom: 25px; color: #3c8dbc; font-size: 18px; }
        .sidebar a { display: block; color: #b8c7ce; padding: 12px 15px; text-decoration: none; border-radius: 4px; margin-bottom: 5px; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #1e282c; color: #fff; border-left: 3px solid #3c8dbc; }
        
        /* Geser panel utama pas di samping batas sidebar */
        .main-panel { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            background: #f4f6f9; 
            margin-left: 250px; 
            min-height: 100vh; 
            box-sizing: border-box;
        }
        
        /* KUNCI PERBAIKAN: Navbar mentok ke atas layar (top: 0) & presisi di samping sidebar */
        .navbar { 
            background: #fff; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 1px 4px rgba(0,0,0,0.1); 
            position: fixed;
            top: 0;
            right: 0;
            left: 250px; 
            height: 60px; 
            box-sizing: border-box;
            z-index: 999; 
        }
        
        /* Jarak atas konten disesuaikan dengan tinggi navbar fixed */
        .content { 
            padding: 25px; 
            flex: 1; 
            margin-top: 60px; 
            box-sizing: border-box;
        }
        
        .profile-card { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .btn-logout { background: #d9534f; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-logout:hover { background: #c9302c; }
        footer { background: #fff; padding: 15px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #d2d6de; }
    </style>
</head>
<body>
<div class="wrapper">