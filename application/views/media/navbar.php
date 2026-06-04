<style>
    /* Header principal */
    
    .top-header {
    background-color: ##ff9900;
}

.top-header a:hover {
    opacity: 0.7;
}




/* Onglets */
.tab-btn {
    background: #f2f2f2;
    border: none;
    padding: 14px 0;
    font-weight: 600;
    font-size: 14px;
    position: relative;
}

.tab-btn.active {
    background: #fff;
}

.tab-btn.active::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: #1e2a5a;
}

/* Contenu */
.canvas-tab {
    display: none;
}

.canvas-tab.show {
    display: block;
}

/* Items */
.menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    padding: 0px 25px;
    border-bottom: 1px solid #eee;
}

.menu-item i {
    font-size: 18px;
    color: #444;
}





    /* Barre de recherche */
    .input-group {
        width: 300px;
        overflow: hidden;
    }

    /* Menu principal */
    .menu-bar {
        padding: 10px 80px;
        border-top: 1px solid #e2e1e1;
        border-bottom: 1px solid #b7b6b6;
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .menu-bar .btn,
    .menu-bar .nav-link {
        color: #000099;
        font-weight: 500;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .menu-bar .btn:hover,
    .menu-bar .nav-link:hover {
        color: #000099;
        transform: translateY(-2px);
    }

    /* Dropdown Tous les cours */
    .tous-les-cours {
        position: relative;
    }

    .cours-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 220px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.4rem;
        display: none;
        z-index: 2000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        overflow-y: visible;
        overflow-x: visible;
    }

    .tous-les-cours:hover .cours-dropdown-menu {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    /* Élément cours */
    .cours-item {
        position: relative;
        font-size: 14px;
    }

    /* CARD des courses */
    .courses-card {
        position: absolute;
        top: 0;
        left: 100%;
        width: 280px;
        display: none;
        z-index: 5000;
    }

    .cours-item:hover .courses-card {
        display: block;
        animation: slideInRight 0.3s ease;
    }

    .courses-card .card {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .courses-card .card-header {
        font-size: 15px;
        text-align: center;
    }

    .courses-card .list-group-item {
        padding: 0;
    }

    .courses-card .course-link {
        display: block;
        padding: 10px 15px;
        color: #212529;
        text-decoration: none;
        transition: 0.2s;
    }

    .courses-card .course-link:hover {
        background: #f1f1f1;
        padding-left: 20px;
        color: #000099;
    }

    .cours-link {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        color: #212529;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
        transition: all 0.2s ease;
    }

    .cours-link:hover {
        background-color: #e9ecef;
        padding-left: 1.5rem;
    }

    .course-link {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        color: #212529;
        align-items: center;
        transition: all 0.2s ease;
    }

    .course-link:hover {
        background-color: #f8f9fa;
        color: #000099;
        padding-left: 1.5rem;
    }

    .menu-link{
        display: flex;
        justify-content: space-between;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        color: #212529;
        align-items: center;
        transition: all 0.1s ease;

    }

    .menu-link:hover{
       background-color: #f8f9fa;
        color: #000099;
        padding-left: 1.5rem;
    }

    /* Dropdowns du menu principal (droite) */
    .nav-item.dropdown {
        position: relative;
    }

    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
        animation: fadeIn 0.3s ease;
    }


    .menu-bar .nav-link{
        color: #2e2d2dff;
        font-weight: bold;
        font-size: 13px;
        font-family: sans-serif;
    }
    .nav-item {
        position: relative;
    }

    .nav-item::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: #000099;
        transition: width 0.3s ease;
    }

    .nav-item:hover::after {
        width: 100%;
    }

    .nav-link {
        font-size: 15px;
        font-weight: 600;
        position: relative;
        padding: 0.5rem 1rem;
    }

    .dropdown-menu {
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        min-width: 200px;
    }

    .dropdown-item {
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #e9ecef;
        padding-left: 1.5rem;
    }

    /* Icônes flèche */
    .down {
        color: #ffd700;
    }

    /* Bouton dans header */
    .btn-header {
        width: 180px;
        padding: 10px 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    

    /* Scrollbar styling */
    .cours-dropdown-menu::-webkit-scrollbar,
    .courses-menu::-webkit-scrollbar {
        width: 6px;
    }
    
    .cours-dropdown-menu::-webkit-scrollbar-track,
    .courses-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .cours-dropdown-menu::-webkit-scrollbar-thumb,
    .courses-menu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .cours-dropdown-menu::-webkit-scrollbar-thumb:hover,
    .courses-menu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .site-logo{
       width: 170px; height: 50px; object-fit: contain;
    }

    @media (max-width: 991px) {
    .sticky-mobile {
        position: sticky;
        top: 0;
        z-index: 1030; /* comme fixed-top */
    }
}


</style>


<!-- TOP HEADER RESPONSIVE -->
<div class="top-header py-1">
    <div class="container-fluid">

        <div class="row align-items-center text-white">

            <!-- LANGUE (DESKTOP) -->
            <div class="col-lg-3 col-md-6 d-none d-lg-flex align-items-center">
                <select class="form-select  form-select-sm bg-white border-0 text-dark" style="width: 70px;">
                    <option>FR</option>
                    <option>EN</option>
                </select>
            </div>

            <!-- RÉSEAUX SOCIAUX (TOUS ÉCRANS) -->
            <div class="col-12 col-lg-3">
                <div class="d-flex d-ms-flex d-md-flex d-xs-flex justify-content-center gap-3">
                    <a href="<?= e($this->Model->get_setting('social_facebook', '#')) ?>" class="text-white" target="_blank"><i class="bi bi-facebook"></i></a>
                    <a href="<?= e($this->Model->get_setting('social_twitter', '#')) ?>" class="text-white" target="_blank"><i class="bi bi-twitter-x"></i></a>
                    <a href="<?= e($this->Model->get_setting('social_instagram', '#')) ?>" class="text-white" target="_blank"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <!-- TÉLÉPHONE (TABLETTE + DESKTOP) -->
            <div class="col-lg-6 d-none d-lg-flex justify-content-end align-items-center gap-4">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-telephone"></i>
        <span><?= e($this->Model->get_setting('site_phone', '+25700000000'))?></span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-envelope"></i>
        <span><?=e($this->Model->get_setting('site_email', 'contact@example.com'))?></span>
    </div>
</div>


        </div>

    </div>
</div>


<!-- Top Header 2 -->
<div class="bg-white border-bottom sticky-mobile py-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 py-2">

            <!-- Bouton Menu (mobile uniquement) -->
          <button class="btn  btn-primary d-lg-none align-items-center justify-content-center"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#mobileCanvas"
        aria-controls="mobileCanvas">
         <i class="bi bi-list fs-3"></i> Menu
         </button>
    

            <!-- Logo (à droite sur mobile, à gauche sur desktop) -->
            <div class="ms-auto ms-lg-0">
                <a href="#">
                    <div class="site_logo">
                    <img class="img-fluid site-logo"
                         src="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_logo', 'logo.png')) ?>"
                         alt="<?=e($this->Model->get_setting('site_name', 'AbeLab'))?>">
                         </div>
                         </a>

            </div>

            <!-- Formulaire de recherche et bouton Register (desktop uniquement) -->
            <div class="d-none d-lg-flex align-items-center gap-3 flex-wrap">
                <form action="<?= base_url('Pages/Home/search') ?>" method="GET" class="d-flex">
                    <input style="border: 2px solid #000099;" class="form-control me-2 border rounded-start-4" type="search" name="q" placeholder="Search" aria-label="Search">
                    <button style="border: 2px solid #000099; background-color:#000099;" class="btn rounded-end-4 text-white" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <button class="btn fw-bold px-4 align-items-center text-center rounded-pill" style="width: 110px; background-color: #000099; color: white;">
                    Register
                </button>
            </div>

        </div>
    </div>
</div>


<div class="offcanvas offcanvas-start d-lg-none"
     tabindex="-1"
     id="mobileCanvas"
     style="width: 300px;">

    <!-- HEADER AVEC ONGLET -->
    <div class="border-bottom">
        <div class="d-flex text-center">
            <button class="tab-btn active w-50"
                    onclick="switchTab('menuTab', this)">
                MENU
            </button>
            <button class="tab-btn w-50"
                    onclick="switchTab('coursTab', this)">
                COURSES
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
    </div>

    <!-- BODY -->
    <div class="offcanvas-body p-0">

        <!-- MENU -->
        <div id="menuTab" class="canvas-tab show">
            <ul class="list-group list-group-flush">

        <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('')?>">HOME</a>
        </li>
         <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('Pages/About_us')?>">About</a>
        </li>
        <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('Pages/About_us/contact')?>">Contact us</a>
          </li>  
            <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('Pages/Horaire')?>">
                HORAIRE</a>
        
        </li>
         <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('Pages/Blog')?>">Blog</a>
        </li>
         <li class="list-group-item menu-item menu-link">
            <a class="nav-link" href="<?= base_url('Pages/Galeries')?>">Galerie</a>
        </li>
    </ul>
        </div>

        <!-- COURSES -->
        <div id="coursTab" class="canvas-tab">
            <ul class="list-group list-group-flush">
                <?php 
            $categor = $this->Model->read('categories', null, 'id_categorie');    
          if (!empty($categor)):
           foreach ($categor as $categ):  ?>
                <li class="list-group-item menu-item">
                    <a href="<?= base_url('Pages/Home/viewcourses/' . $categ['uuid']) ?>" 
               class="cours-link d-flex align-items-center justify-content-between">

                <div>
                    <?= e($categ['nom_categories'])?>
                </div>
            </a>
                </li>
                <?php  endforeach; endif; ?>
            </ul>
        </div>

    </div>
</div>

<script>
function switchTab(tabId, btn) {

    // Tabs
    document.querySelectorAll('.canvas-tab').forEach(tab => {
        tab.classList.remove('show');
    });
    document.getElementById(tabId).classList.add('show');

    // Buttons
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });
    btn.classList.add('active');
}
</script>




<!-- Menu Navigation -->
<nav class="menu-bar d-none d-lg-flex">
    <!-- Bouton Tous les Cours -->
    <div class="tous-les-cours">
        <button class="btn" style="background-color: #000099; color: white; width: 220px;">
            <i class="bi bi-book-half"></i>
            Tous les Cours
            <i class="bi bi-chevron-down ms-2"></i>
        </button>

        <div class="cours-dropdown-menu">
    <?php 
    $categories = $this->Model->read('categories', null, 'id_categorie');

    if (!empty($categories)):
        foreach ($categories as $cat):  ?>
        <div class="cours-item">
           
            <a href="<?= base_url('Pages/Home/viewcourses/' . $cat['uuid']) ?>" 
               class="cours-link d-flex align-items-center justify-content-between">

                <div>
                    <i class="bi bi-filetype-c me-2 text-primary"></i>
                    <?= e($cat['nom_categories'])?>
                </div>

                <i class="bi bi-chevron-right text-muted"></i>
            </a>

            <!-- Sous-menu des cours -->
            <div class="courses-card">
                <div class="card">

                    <div class="card-header bg-primary text-white fw-bold">
                        TOUS LES COURS DE <?= strtoupper($cat['nom_categories']); ?>
                    </div>

                    <ul class="list-group list-group-flush">
                        <?php
                        $courses = $this->Model->get_courses_by_categori($cat['id_categorie']);
                        if (!empty($courses)):
                            foreach ($courses as $course): ?>
                            
                                <li class="list-group-item">
                                    <a href="<?= base_url('Pages/Home/coursedetail/' . $course['uuid']) ?>" 
                                       class="course-link d-flex justify-content-between">
                                        <span>
                                            <i class="bi bi-journal-code text-success me-2"></i>
                                            <?= e($course['nom_course'])?>
                                        </span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>

                            <?php endforeach;
                        endif; ?>
                    </ul>

                </div>
            </div>

        </div>

    <?php 
        endforeach;
    endif; 
    ?>
</div>
    </div>

    <!-- Menus principaux à droite -->
    <ul class="nav mb-0">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('')?>">HOME</a>
        </li>
        
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#">
                ABOUT US
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= base_url('Pages/About_us')?>">About Abelab</a></li>
                <li><a class="dropdown-item" href="<?= base_url('Pages/About_us/contact')?>">Contact us</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="<?= base_url('Pages/Horaire')?>">
                HORAIRE
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#">
                RESSOURCES
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= base_url('Pages/Blog')?>">Blog</a></li>
                <li><a class="dropdown-item" href="<?= base_url('Pages/Galeries')?>">Galerie</a></li>
            </ul>
        </li>
    </ul>
</nav>