<?php include 'views/layouts/header.php'; ?>
    <div class="d-flex align-items-center login-page" style="height: 80vh">
        <div class="mx-auto d-flex justify-content-center align-items-center rounded login-box-blurred" style="width: 600px; height: 500px">
            <div class="d-flex flex-column w-100 px-5 gap-4">
                <h2 class="text-center text-ligth login-title">Login</h2>
                <form action="index.php?page=auth&action=login_process" method="POST" class="d-flex flex-column gap-2">
                    <div class="mb-3">
                        <label class="form-label text-light">Email address</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn" style="background: black; color: white">SIGN IN</button>
                </form>
                <p class="text-center"><small>Belum punya akun? <a href="#">Register</a></small></p>
            </div>
        </div>
    </div>
<?php include 'views/layouts/footer.php'; ?>