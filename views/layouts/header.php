<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ThanksCompany Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tilt+Warp&display=swap" rel="stylesheet">
    <style>
        * { 
            --black: #000; 
            --white: #fff;
            font-family: 'Geist', sans-serif;
        }
        body { 
            margin: 0; 
            padding: 0; 
            color: var(--black); 
        }
        
        /* Navbar Minimalis */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 20px 50px; 
            border-bottom: 1px solid #eee; 
        }
        .logo { 
            font-family: 'Tilt Warp', sans-serif; 
            font-size: 2rem; 
            text-transform: lowercase; 
            letter-spacing: 1px; 
            text-decoration: none;
            color: black;
        }
        .menu a { 
            text-decoration: none; 
            color: var(--black); 
            margin: 0 15px; 
            text-transform: uppercase; 
            font-size: 0.9rem; 
            font-weight: 600; 
        }
        .icons a { 
            margin-left: 20px; 
            text-decoration: none; 
        }
        
        /* Layout Grid */
        .container { 
            padding: 50px; 
        }
        .product-grid { 
            display: grid; 
            grid-template-columns: 
            repeat(4, 1fr); gap: 30px; 
        }
        .card img { 
            width: 100%; 
            height: auto; 
            object-fit: cover; 
        }
        .card h3 { 
            font-size: 1rem; 
            margin: 10px 0 5px; 
        }
        .btn-black { 
            background: var(--black); 
            color: var(--white); 
            padding: 10px 20px; 
            text-decoration: none; 
            display: inline-block; 
        }

        /* sembunyikan saat awal (x-cloak) */
        [x-cloak] { display: none !important; }

        /* NAV agar tetap di atas konten kalau perlu */
        nav {
            position: relative;
            z-index: 60; /* optional */
            background: transparent;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Overlay full-screen (fade) */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 45;
            /* transisi smooth untuk opacity & visibility */
            transition: opacity 300ms ease, visibility 300ms ease;
            /* start hidden by default (visibility controlled inline via :style) */
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Sidebar kiri */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 400px;
            max-width: 85%;
            height: 100vh;
            background: #fff;
            z-index: 70; /* NAIKKAN agar lebih tinggi dari navbar & overlay */
            padding: 20px;
            box-shadow: 2px 0 12px rgba(0,0,0,0.12);

            transform: translateX(-100%);
            transition: transform 320ms cubic-bezier(.2,.9,.2,1), opacity 220ms ease;
            will-change: transform;
            opacity: 1;
            overflow-y: auto;
        }

        /* Login */
        .login-page {
            background: url('./assets/images/hero.png');
            background-size: cover;
        }

        .login-title {
            font-family: 'Tilt Warp', sans-serif;
            color: white;
            text-shadow: 2px 2px 5px #303030ff;
        }

        .login-box-blurred {
            background: rgba(255, 255, 255, 0.1); /* sedikit transparan */
            backdrop-filter: blur(10px);          /* efek blur */
            -webkit-backdrop-filter: blur(10px);  /* untuk Safari */
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.2/dist/cdn.min.js"></script>
</head>
<body>
<!-- HTML: Sidebar + Overlay (Tanpa x-show, memakai :style) -->
<div x-data="{ open: false }" x-cloak>

  <nav>
    <div class="burger-menu" style="cursor: pointer" @click="open = !open">
      <i class="bi bi-justify"></i>
    </div>

    <a href="index.php?page=home" class="logo">thanksjabbran®</a>

    <div class="icons">
      <a href="index.php?page=shop" class="text-dark"><i class="bi bi-search"></i></a>
      <a href="index.php?page=cart" class="text-dark"><i class="bi bi-cart2"></i></a>
      <a href="index.php?page=login" class="text-dark"><i class="bi bi-person-fill"></i></a>
    </div>
  </nav>

  <!-- Overlay: visibility & opacity di-handle oleh :style -->
  <div
    @click="open = false"
    :style="open 
      ? 'opacity: 0.5; visibility: visible; pointer-events: auto;' 
      : 'opacity: 0; visibility: hidden; pointer-events: none;'"
    class="overlay">
  </div>

  <!-- Sidebar: transform di-handle oleh :style -->
  <aside
    :style="open ? 'transform: translateX(0);' : 'transform: translateX(-100%);'"
    class="sidebar">
    <div @click="open = false" class="me-2 mt-2" style="float: right; cursor: pointer">
        <i class="bi bi-x-lg"></i>
    </div>
    
    <ul class="list-unstyled d-flex flex-column mt-5 ms-3 gap-3">
      <li class="mb-3"><a href="index.php?page=shop" class="text-dark text-decoration-none">Shop</a></li>
      <li class="mb-3"><a href="index.php?page=profile" class="text-dark text-decoration-none">About</a></li>
      <li class="mb-3"><a href="index.php?page=contact" class="text-dark text-decoration-none">Contact</a></li>
    </ul>

  </aside>
</div>
