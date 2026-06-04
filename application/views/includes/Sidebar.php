<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
<div class="sidebar-header">
<div>
<img src="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_logo', 'logo.png')) ?>" class="logo-icon" alt="logo icon">
</div>
<div>
<h4 class="logo-text"><?= e($this->Model->get_setting('site_name', 'AbeLab')) ?></h4>
</div>
<div class="mobile-toggle-icon ms-auto"><i class='bx bx-x'></i>
</div>
</div>
<!--navigation-->
<ul class="metismenu" id="menu">


<li>
<a href="javascript:;" class="has-arrow">
	<div class="parent-icon"><i class='bx bx-home-alt'></i>
	</div>
	<div class="menu-title">Tableau de bord</div>
</a>
<ul>
	<li> <a href="<?=base_url('Dashboard')?>"><i class='bx bx-radio-circle'></i>Tableau de bord</a>
	</li>
</ul>
</li>




<!-- À propos -->
<li class="menu-label">À propos</li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-info-circle"></i></div>
                <div class="menu-title">À propos</div>
            </a>
            <ul>
                <li><a href="<?=base_url('About_us')?>"><i class='bx bx-radio-circle'></i>About Us</a></li>
                <li><a href="<?=base_url('Mission')?>"><i class='bx bx-radio-circle'></i>Mission</a></li>
                <li><a href="<?=base_url('Vision')?>"><i class='bx bx-radio-circle'></i>Vision</a></li>
            </ul>
        </li>   
		
		
		<!-- Cours / Classes -->
        <li class="menu-label">Cours & Classes</li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-book'></i></div>
                <div class="menu-title">Cours</div>
            </a>
            <ul>
            	<li><a href="<?=base_url('Attendace_course_mode')?>"><i class='bx  bx-radio-circle'></i>Mode de présence</a></li>
            	<li><a href="<?=base_url('Categories')?>"><i class='bx bx-radio-circle'></i>Categorie</a></li>
                <li><a href="<?=base_url('Courses')?>"><i class='bx bx-radio-circle'></i>Courses</a></li>
                <li><a href="<?=base_url('Timetable')?>"><i class='bx bx-radio-circle'></i>Timetable</a></li>
                <li><a href="<?=base_url('Timetable_courses')?>"><i class='bx bx-radio-circle'></i>Timetable Courses</a></li>
            </ul>
        </li>

        <!-- Étudiants -->
        <li class="menu-label">Étudiants</li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">Gestion Étudiants</div>
            </a>
            <ul>
                <li><a href="<?=base_url('Students')?>"><i class='bx bx-radio-circle'></i>Students</a></li>
                <li><a href="<?=base_url('Inscriptions')?>"><i class='bx bx-radio-circle'></i>Inscriptions</a></li>
            </ul>
        </li>

        <!-- Enseignants -->
        <li class="menu-label">Enseignants</li>
        <li>
            <a href="<?=base_url('Teachers')?>">
                <div class="parent-icon"><i class='bx bx-chalkboard'></i></div>
                <div class="menu-title">Teachers</div>
            </a>
        </li>

        <!-- Paiement / Modes -->
        <li class="menu-label">Paiement</li>
        <li>
            <a href="<?=base_url('Mode_payement')?>">
                <div class="parent-icon"><i class='bx bx-credit-card'></i></div>
                <div class="menu-title">Modes de Paiement</div>
            </a>
        </li>

        <!-- Médias & News -->
        <li class="menu-label">Médias & News</li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-image'></i></div>
                <div class="menu-title">Médias</div>
            </a>
            <ul>
                <li><a href="<?=base_url('News_media')?>"><i class='bx bx-radio-circle'></i>News Media</a></li>
                
                <li><a href="<?=base_url('galleries/Galleries')?>"><i class='bx bx-radio-circle'></i>Gallery</a></li>
                <li><a href="<?=base_url('Carousel')?>"><i class='bx bx-radio-circle'></i>Carousels</a></li>
                <li><a href="<?=base_url('Parteners')?>"><i class='bx bx-radio-circle'></i>Parteners</a></li>
                <li><a href="<?=base_url('testimony')?>"><i class='bx bx-radio-circle'></i>Temoignages</a></li><li><a href="<?=base_url('Newsletter')?>"><i class='bx bx-radio-circle'></i>Newsletters</a></li>
                <li><a href="<?=base_url('Events')?>"><i class='bx bx-radio-circle'></i>Evenements</a></li>
                <li><a href="<?=base_url('Join_us')?>"><i class='bx bx-radio-circle'></i>Why jois us</a></li>
            </ul>
        </li>

        <!-- Contact -->
        <li class="menu-label">Contact</li>
        <li>
            <a href="<?=base_url('Contact_us')?>">
                <div class="parent-icon"><i class='bx bx-envelope'></i></div>
                <div class="menu-title">Messages</div>
            </a>
        </li>

        <!-- Paramètres -->
        <li class="menu-label">Paramètres</li>
        <li>
            <a href="<?=base_url('Settings')?>">
                <div class="parent-icon"><i class='bx bx-cog'></i></div>
                <div class="menu-title">Settings</div>
            </a>
        </li>

        <!-- Utilisateurs & Rôles -->
        <li class="menu-label">Administration</li>
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-shield'></i></div>
                <div class="menu-title">Utilisateurs</div>
            </a>
            <ul>
                <li><a href="<?=base_url('Users')?>"><i class='bx bx-radio-circle'></i>Users</a></li>
                <li><a href="<?=base_url('groups')?>"><i class='bx bx-radio-circle'></i>Groupes</a></li>
            </ul>
        </li>

        <!-- Profil -->
        <li class="menu-label">Compte</li>
        <li>
            <a href="<?=base_url('Profile')?>">
                <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
                <div class="menu-title">Mon Profil</div>
            </a>
        </li>

</ul>
<!--end navigation-->
</div>
<!--end sidebar wrapper -->




<!--start header -->
<header>
<div class="topbar">
<nav class="navbar navbar-expand gap-2 align-items-center">
<div class="mobile-toggle-menu d-flex"><i class='bx bx-menu'></i>
</div>

  <div class="top-menu ms-auto">
	<ul class="navbar-nav align-items-center gap-1">
		<li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
			<a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
			</a>
		</li>

		<li class="nav-item dark-mode d-none d-sm-flex">
			<a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
			</a>
		</li>

		

         <!-- don't remove -->
		<li class="nav-item dropdown dropdown-app">
			<div class="dropdown-menu dropdown-menu-end p-0">
				<div class="app-container p-2 my-2">
				  <div class="row gx-0 gy-2 row-cols-3 justify-content-center p-2">

				  </div>

				</div>
			</div>
		</li>






		<li class="nav-item dropdown dropdown-large">
			<a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown"><span class="alert-count">0</span>
				<i class='bx bx-bell'></i>
			</a>
			<div class="dropdown-menu dropdown-menu-end">
				<a href="javascript:;">
					<div class="msg-header">
						<p class="msg-header-title">Notification</p>
						<p class="msg-header-badge">0 New</p>
					</div>
				</a>
				<div class="header-notifications-list">
					
				</div>
				<a href="javascript:;">
					<div class="text-center msg-footer">
						<button class="btn btn-primary w-100">Voir tous les Notifications</button>
					</div>
				</a>
			</div>
		</li>



        <!-- don't remove -->
		<li class="nav-item dropdown dropdown-large">
			<div class="dropdown-menu dropdown-menu-end">
				<div class="header-message-list">

				</div>
				<a href="javascript:;">
					<div class="text-center msg-footer">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<h5 class="mb-0">Total</h5>
							<h5 class="mb-0 ms-auto">$489.00</h5>
						</div>
						<button class="btn btn-primary w-100">Voir</button>
					</div>
				</a>
			</div>
		</li>
	</ul>
</div>

<?php 
$group=$this->Model->readOne('groups',['uuid'=>$this->session->userdata('uuidGroup')]); 
$group_name = $group ? $group['group_name'] : '';
 ?>
<div class="user-box dropdown px-3">
	<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		<?php $avatar = $this->Model->readOne('users', ['uuid' => $this->session->userdata('uuid')])['image'] ?? ''; ?>
<img src="<?= base_url($avatar ? 'attachments/Users/' . $avatar : $this->Model->get_setting('default_avatar', 'assets/admin/images/user.png')) ?>" class="user-img" alt="user avatar" style="object-fit:cover;width:40px;height:40px;border-radius:50%;">
		<div class="user-info">
			<p class="user-name mb-0"><?= e($this->session->userdata('user')) ?></p>
			<p class="designattion mb-0"><?= e($group_name) ?></p>
		</div>
	</a>
	<ul class="dropdown-menu dropdown-menu-end">
		<li><a class="dropdown-item d-flex align-items-center" href="<?=base_url('Profile')?>"><i class="bx bx-user fs-5"></i><span>Profil</span></a>
		</li>
		<li><a class="dropdown-item d-flex align-items-center" href="<?=base_url('Settings')?>"><i class="bx bx-cog fs-5"></i><span>Paramètre</span></a>
		</li>
		<li><a class="dropdown-item d-flex align-items-center" href="<?=base_url('Dashboard')?>"><i class="bx bx-home-circle fs-5"></i><span>Tableau de bord</span></a>
		</li>
		<li>
			<div class="dropdown-divider mb-0"></div>
		</li>
		<li><a class="dropdown-item d-flex align-items-center" href="<?=base_url('Logout')?>"><i class="bx bx-log-out-circle"></i><span>Déconnexion</span></a>
		</li>
	</ul>
</div>
</nav>
</div>
</header>
<!--end header -->







   

        
