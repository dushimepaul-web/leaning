
   <!-- ===================== -->
<!--        FOOTER         -->
<!-- ===================== -->

<style>
/* ================= FOOTER BASE ================= */
.footer-abelab {
    position: relative;
    background: url('<?= base_url($this->Model->get_setting('footer_background', 'assets/images/abelab.png')) ?>') no-repeat center center / cover;
    color: #e0e0e0;
    padding: 60px 0;
    overflow: hidden;
}

.footer-abelab::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.9);
    z-index: 0;
}

.footer-abelab .container {
    position: relative;
    z-index: 1;
}

/* ================= TITLES ================= */
.footer-title {
    color: #ff9900;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-title::after {
    content: "";
    width: 50px;
    height: 3px;
    background: #009900;
    position: absolute;
    bottom: -6px;
    left: 0;
    border-radius: 3px;
}

/* ================= LINKS ================= */
.quick-link li {
    margin-bottom: 10px;
}

.quick-link a {
    color: #e0e0e0;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: .3s;
}

.quick-link a:hover {
    color: #ff9900;
    transform: translateX(5px);
}

/* ================= CONTACT ================= */
.contact-info li {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.contact-icon-bg {
    width: 35px;
    height: 35px;
    background: #009900;
    color: #fff;
    border-radius: 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 12px;
}

/* ================= NEWSLETTER ================= */
.newsletter-input {
    height: 48px;
    border: none;
    padding: 0 15px;
}

.btn-subscribe-abelab {
    background: #ff9900;
    color: #000099;
    font-weight: 700;
    height: 48px;
    border: none;
    transition: .3s;
}

.btn-subscribe-abelab:hover {
    background: #e68a00;
    color: #000066;
}

/* ================= WHATSAPP BUTTON ================= */
.back-to-whatsapp {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #25D366;
    color: #fff;
    width: 50px;
    height: 50px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,.3);
    transition: .3s;
    z-index: 999;
    overflow: hidden;
}

.back-to-whatsapp span {
    display: none;
    margin-left: 8px;
    white-space: nowrap;
}

.back-to-whatsapp.show {
    width: 200px;
    justify-content: flex-start;
    padding: 0 15px;
}

.back-to-whatsapp.show span {
    display: inline;
}

/* ================= RESPONSIVE ================= */

/* Tablets */
@media (max-width: 991px) {
    .footer-title {
        font-size: 1.1rem;
    }
}

/* Mobile */
@media (max-width: 767px) {
    .footer-abelab {
        text-align: start;
        padding: 40px 15px;
    }

    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .quick-link a,
    .contact-info li {
        justify-content: flex-start;
    }

    .contact-icon-bg {
        margin-right: 8px;
    }
}

/* Small mobile */
@media (max-width: 576px) {
    .back-to-whatsapp.show {
        width: 50px;
        padding: 0;
    }

    .back-to-whatsapp.show span {
        display: none;
    }
}
.site_logo{
 width: 200px;
 height: auto;
}
.img-fluid{
    height: 100%;
    width: 100%;
    object-fit: cover;
}
</style>

<footer class="footer-abelab">
    <div class="container">
        <div class="row g-4">

            <!-- LOGO + CONTACT -->
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="site_logo">
                <img loading="lazy"
                     class="img-fluid mb-3"
                                          src="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_logo','logo.png')) ?>"
                     alt="<?= e($this->Model->get_setting('site_name','AbeLab'))?>">
                     </div>
                <p class="small">
                    <?= e($this->Model->get_setting(
                        'site_description',
                        'La créativité est au cœur de l’innovation. AbeLab est votre laboratoire de formation technologique.'
                    ))?>
                </p>

                <ul class="list-unstyled mt-4 contact-info">
                    <li>
                        <span class="contact-icon-bg"><i class="fas fa-phone"></i></span>
                        <?= e($this->Model->get_setting('site_phone','+257000000'))?>
                    </li>
                    <li>
                        <span class="contact-icon-bg"><i class="fas fa-map-marker-alt"></i></span>
                        <?= e($this->Model->get_setting('site_address','Bujumbura, Burundi'))?>
                    </li>
                    <li>
                        <span class="contact-icon-bg"><i class="fas fa-envelope"></i></span>
                        <?= e($this->Model->get_setting('site_email','contact@example.com'))?>
                    </li>
                </ul>
            </div>

            <!-- FORMATIONS -->
            <div class="col-lg-2 col-md-4 col-sm-6">
                <h5 class="footer-title">Formations</h5>
                <ul class="list-unstyled quick-link">
                    <?php foreach ($this->Model->read('categories',null,'id_categorie') as $cat): ?>
                        <li>
                            <a href="<?= base_url('Pages/Home/viewcourses/'.$cat['uuid']) ?>">
                                <i class="bi bi-play-fill me-2"></i> <?= e($cat['nom_categories'])?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- A PROPOS -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <h5 class="footer-title">À propos</h5>
                <ul class="list-unstyled quick-link">
                    <li><a href="<?= base_url('Pages/About_us')?>"><i class="bi bi-play-fill me-2"></i> Notre mission</a></li>
                    <li><a href="<?= base_url('Pages/About_us/contact')?>"><i class="bi bi-play-fill me-2"></i>Contact_us</a></li>
                    <li><a href="<?= base_url('Pages/Blog')?>"><i class="bi bi-play-fill me-2"></i> Événements</a></li>
                    <li><a href="#temoignages"><i class="bi bi-play-fill me-2"></i> Témoignages</a></li>
                    <li><a href="<?= base_url('Pages/Galeries')?>"><i class="bi bi-play-fill me-2"></i>Galleries</a></li>
                </ul>
            </div>

            <!-- NEWSLETTER -->
            <div class="col-lg-3 col-md-4">
                <h5 class="footer-title">Newsletter</h5>
                <p class="small">
                    <?= e($this->Model->get_setting('newsletter_text', 'Recevez nos offres et actualités.'))?>
                </p>

                <form action="<?= base_url('Pages/Home/CreateNewsletter') ?>" method="POST">
                    <input type="email" name="email" class="form-control newsletter-input mb-2" placeholder="Votre e-mail" required>
                    <button class="btn btn-subscribe-abelab w-100">
                        S’ABONNER <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>

        </div>

        <div class="text-center small mt-5 pt-3 border-top border-secondary opacity-75">
            © <?= date('Y'); ?> <?= e($this->Model->get_setting('site_name','AbeLab'))?> — Tous droits réservés
        </div>
    </div>

    <!-- WhatsApp -->
    <a class="back-to-whatsapp"
       target="_blank"
       href="https://wa.me/<?= preg_replace('/\D/','',$this->Model->get_setting('site_phone','+2576886345')) ?>">
        <i class="fab fa-whatsapp"></i>
        <span>Chat with us</span>
    </a>
</footer>

<!-- ================= SCRIPTS ================= -->
<script src="<?= base_url('assets/admin/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/js/bootstrap.bundle.min.js') ?>"></script>


                
    
    <!-- Slick Carousel JS -->
    <script type="text/javascript" src="<?= base_url('assets/cdn/slick/slick.min.js') ?>"></script>

    <!-- Waypoints -->
<script src="<?= base_url('assets/cdn/waypoints/jquery.waypoints.min.js') ?>"></script>
<!-- Counter-Up -->
<script src="<?= base_url('assets/cdn/counterup/jquery.counterup.min.js') ?>"></script>




<<script>
$(window).on('scroll', function () {
    $('.back-to-whatsapp').toggleClass('show', $(this).scrollTop() > 200);
});
</script>

<!-- Slick INITIALISATION -->
<script>
  



    $('.partners-slider').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
        arrows: true,
        dots: false,
        responsive: [
            { breakpoint: 992, settings: { slidesToShow: 3 }},
            { breakpoint: 768, settings: { slidesToShow: 2 }},
            { breakpoint: 576, settings: { slidesToShow: 1 }}
        ]
    });

     $(document).ready(function(){
            // Initialisation du slider Slick
            $('#testimonialsSlider').slick({
                dots: true,
                infinite: true,
                speed: 200,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: false,
                appendDots: '.slick-dots-container',
                responsive: [
                    {
                        breakpoint: 1199,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            dots: true,
                            arrows: false
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            dots: true,
                            arrows: false,
                            centerMode: true,
                            centerPadding: '20px'
                        }
                    }
                ]
            });
            
            // Ajustement responsive sur resize
            $(window).on('resize', function(){
                $('#testimonialsSlider').slick('resize');
            });
        });




</script>
</script>

</body>
</html>










            
      