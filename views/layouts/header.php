<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ThanksCompany Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --black: #000; --white: #fff; }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; color: var(--black); }
        
        /* Navbar Minimalis */
        nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; border-bottom: 1px solid #eee; }
        .logo { font-family: 'Teko', sans-serif; font-size: 2rem; font-weight: bold; text-transform: lowercase; letter-spacing: 1px; }
        .menu a { text-decoration: none; color: var(--black); margin: 0 15px; text-transform: uppercase; font-size: 0.9rem; font-weight: 600; }
        .icons a { margin-left: 20px; text-decoration: none; font-size: 1.2rem; }
        
        /* Layout Grid */
        .container { padding: 50px; }
        .product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
        .card img { width: 100%; height: auto; object-fit: cover; }
        .card h3 { font-size: 1rem; margin: 10px 0 5px; }
        .btn-black { background: var(--black); color: var(--white); padding: 10px 20px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <nav>
        <div class="burger-menu">☰</div>
        <div class="logo">thanksjabbran®</div>
        <div class="icons">
            <a href="index.php?page=search">🔍</a>
            <a href="index.php?page=cart">🛒</a>
            <a href="index.php?page=login">👤</a>
        </div>
    </nav>