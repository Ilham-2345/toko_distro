<?php include 'views/layouts/header.php'; ?>

<div class="hero" style="position: relative; text-align: center;">
    <img src="assets/images/hero.png"  style="width: 100%; height: 80vh; object-fit: cover;">
    <h1 style="position: absolute; top: 40%; left: 10%; font-family: 'Tilt Warp'; font-size: 4rem; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
        thanksjabbran®
    </h1>
</div>

<div class="container">
    <section class="py-5 text-center" style="width: 75%; margin: 0 auto">
        <h2 class="mb-4">About us</h2>

        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit totam animi minus consectetur perferendis? Recusandae eos similique nostrum repudiandae esse adipisci provident dolorum. Voluptatem quis nobis facilis beatae iste ullam consequuntur blanditiis suscipit culpa, exercitationem omnis iusto aperiam nesciunt molestias veritatis molestiae nostrum. Deleniti aperiam distinctio fuga, velit aut sed debitis officia totam alias corporis et nobis inventore itaque nam perspiciatis explicabo maiores vel culpa qui provident cum.</p>
    </section>

    <section class="mb-3">
        <h2>New Arrivals</h2>
    
        <div class="row mt-4">
            <!-- Card 1 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-2" style="border: 0; border-radius: 26px">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body p-0">
                        <h5 class="card-title">Nama Produk 1</h5>
                        <p class="card-text">Rp 150.000</p>
                        <a href="#" class="btn btn-dark w-100 py-2" style="border-radius: 20px">See details</a>
                    </div>
                </div>
            </div>
    
            <!-- Card 2 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-2" style="border: 0; border-radius: 26px">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body p-0">
                        <h5 class="card-title">Nama Produk 1</h5>
                        <p class="card-text">Rp 150.000</p>
                        <a href="#" class="btn btn-dark w-100 py-2" style="border-radius: 20px">See details</a>
                    </div>
                </div>
            </div>
    
            <!-- Card 3 -->
            <div class="col-md-4 mb-4">
                <div class="card shadow p-2" style="border: 0; border-radius: 26px">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body p-0">
                        <h5 class="card-title">Nama Produk 1</h5>
                        <p class="card-text">Rp 150.000</p>
                        <a href="#" class="btn btn-dark w-100 py-2" style="border-radius: 20px">See details</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-3">Catalouge</h2>
        <div class="row ms-1" style="height: 600px">
            <div class="col-6 d-flex flex-column justify-content-end" style="background-image: url('assets/images/man-katalog.jpg'); background-size: cover">
                <p style="color: white; font-size: 50px; margin: 0" class="ms-4">Man</p>
                <a href="" style="color: white" class="ms-4 mb-5">Explore</a>
            </div>
            <div class="col-6 d-flex flex-column justify-content-end" style="background-image: url('assets/images/women-katalog.jpg'); background-size: cover">
                <p style="color: white; font-size: 50px; margin: 0" class="ms-4">Woman</p>
                <a href="" style="color: white" class="ms-4 mb-5">Explore</a>
            </div>
        </div>
    </section>
</div>


<?php include 'views/layouts/footer.php'; ?>