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
            font-weight: bold; 
            text-transform: lowercase; 
            letter-spacing: 1px; 
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
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <nav>
        <div class="burger-menu" style="cursor: pointer"><i class="bi bi-justify"></i></div>
        <div class="logo">thanksjabbran®</div>
        <div class="icons">
            <a href="index.php?page=search" class="text-dark"><i class="bi bi-search"></i></a>
            <a href="index.php?page=cart" class="text-dark"><i class="bi bi-cart2"></i></a>
            <a href="index.php?page=login" class="text-dark"><i class="bi bi-person-fill"></i></a>
        </div>
    </nav>