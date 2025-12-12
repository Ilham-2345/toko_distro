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

        .hover-bg:hover {
            background-color: rgba(0,0,0,0.03);
        }

        button:focus { outline: none; box-shadow: none; }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.2/dist/cdn.min.js"></script>
</head>
<body>
<!-- HTML: Sidebar + Overlay (Tanpa x-show, memakai :style) -->
<div x-data="{ open: false }" x-cloak>

<nav class="position-relative">
  <div class="burger-menu" style="cursor: pointer" @click="open = !open">
    <i class="bi bi-justify"></i>
  </div>

  <!-- Dropdown component -->
  <div x-data="{ open: false }" class="d-inline-block ms-3" style="position: relative">
    <!-- Icon -->
    <button
      @click="open = !open"
      @keydown.escape="open = false"
      aria-haspopup="true"
      :aria-expanded="open"
      class="btn p-0 border-0 bg-transparent"
      style="line-height: 1"
    >
      <i class="bi bi-person-fill fs-4 text-dark"></i>
    </button>

    <!-- Dropdown box -->
    <div
      x-cloak
      x-show="open"
      @click.outside="open = false"
      x-transition:enter="transition ease-out duration-150"
      x-transition:enter-start="opacity-0 translate-y-1 scale-95"
      x-transition:enter-end="opacity-100 translate-y-0 scale-100"
      x-transition:leave="transition ease-in duration-100"
      x-transition:leave-start="opacity-100 translate-y-0 scale-100"
      x-transition:leave-end="opacity-0 translate-y-1 scale-95"
      style="min-width: 180px; position: absolute; right: 0; margin-top: 8px; z-index: 9999;"
      class="shadow rounded bg-white border"
    >
      <div class="py-2">
        <a href="index.php?page=profile" class="d-block px-3 py-2 text-decoration-none text-dark hover-bg">Profile</a>
        <a href="index.php?page=orders" class="d-block px-3 py-2 text-decoration-none text-dark hover-bg">Orders</a>
        <div class="dropdown-divider my-1"></div>
        <!-- jika logout di-handle oleh AuthController -->
        <a href="index.php?page=auth&action=logout" class="d-block px-3 py-2 text-decoration-none text-dark">Logout</a>
      </div>
    </div>
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
      <li class="mb-3"><a href="index.php?page=shop" class="text-dark text-decoration-none">Dashboard</a></li>
      <li class="mb-3"><a href="index.php?page=profile" class="text-dark text-decoration-none">Kelola Produk</a></li>
      <li class="mb-3"><a href="index.php?page=contact" class="text-dark text-decoration-none">Kelola Kategori</a></li>
      <li class="mb-3"><a href="index.php?page=contact" class="text-dark text-decoration-none">Kelola Stok</a></li>
      <li class="mb-3"><a href="index.php?page=contact" class="text-dark text-decoration-none">Kelola Pesanan</a></li>
      <li class="mb-3"><a href="index.php?page=contact" class="text-dark text-decoration-none">Laporan</a></li>
    </ul>

  </aside>
</div>
