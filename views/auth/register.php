<?php include 'views/layouts/header.php'; ?>
    <div class="d-flex align-items-center justify-content-center" style="height: 80vh">
        <form action="index.php?page=auth&action=register" method="POST" style="width: 500px">
            <h2 class="fw-bold">Register</h2>
            <div class="my-3">
                <label for="exampleFormControlInput1" class="form-label">Username</label>
                <input type="text" class="form-control" name="name" id="exampleFormControlInput1" placeholder="Your username">
            </div>
            <div class="my-3">
                <label for="exampleFormControlInput1" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="my-3">
                <label for="addressFormControlInput1" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="addressFormControlInput1">
            </div>
            <div class="my-3">
                <label for="phoneFormControlInput1" class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" id="phoneFormControlInput1">
            </div>
            <div class="mb-3">
                <label for="inputPassword5" class="form-label">Password</label>
                <input type="password" id="inputPassword5" name="password" class="form-control" aria-describedby="passwordHelpBlock">
                <div id="passwordHelpBlock" class="form-text">
                Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                </div>
            </div>
            <button type="submit" class="btn btn-dark w-100">Register</button>
        </form>
    </div>
<?php include 'views/layouts/footer.php'; ?>