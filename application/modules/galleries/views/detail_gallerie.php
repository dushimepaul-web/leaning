<?php include VIEWPATH.'includes/Header.php' ;?>
<?php include VIEWPATH.'includes/Sidebar.php' ;?>
<script src="<?= base_url('assets/cdn/ckeditor/ckeditor.js') ?>"></script> 
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Admin</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Gallerie</li>
							</ol>
						</nav>
					</div>
					<div class="ms-auto">
						<a class="btn btn-outline-primary" href="<?=base_url('galleries')?>"> 
                            <i class="uil-list-ul"></i> Listing
                        </a>
					</div>
				</div>
				<!--end breadcrumb-->
				<hr/>
				<div class="card">
					<div class="card-body">
                          <form action="<?=base_url('save_gallerie_detail')?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" value="<?=e($Description['IdGallerie'])?>" name="IdGallerie">
                            <textarea id="editor" class="form-control" name="Description">
                                <?=e($Description['Description'])?>
                            </textarea>
                            <div class="card-footer">
                              <button type="submit" class="btn btn-info">Save</button>  
                            </div>                   
                        </form>
					</div>
				</div>

				<hr/>
			</div>
		</div>
		<!--end page wrapper -->
		


		<?php include VIEWPATH.'includes/Footer.php' ;?>