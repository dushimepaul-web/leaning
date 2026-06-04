
		<!--start overlay-->
		 <div class="overlay mobile-toggle-icon"></div>
		<!--end overlay-->
		<!--Start Back To Top Button-->
		  <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<footer class="page-footer">
			<p class="mb-0">Copyright © <?=date('Y')?>. All right reserved.</p>
		</footer>
    
	</div>
	<!--end wrapper-->

<!--start switcher-->
<button class="btn btn-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class='bx bx-cog bx-spin'></i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-60">
      <div class="">
        <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
      </div>
	  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-2 align-items-center justify-content-center p-3" for="LightTheme">
				<span><i class='bx bx-sun fs-2'></i></span>
                <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-2 align-items-center justify-content-center p-3" for="DarkTheme">
				<span><i class='bx bx-moon fs-2'></i></span>
                <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-2 align-items-center justify-content-center p-3" for="SemiDarkTheme">
				<span><i class='bx bx-brightness-half fs-2'></i></span>
                <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-2 align-items-center justify-content-center p-3" for="BoderedTheme">
				<span><i class='bx bx-border-all fs-2'></i></span>
                <span>Bordered</span>
            </label>
          </div>
        </div><!--end row-->

      </div>
    </div>
  </div>
  <!-- Bootstrap + jQuery -->
<script src="<?= base_url() ?>assets/admin/js/jquery.min.js"></script>
<script src="<?= base_url() ?>assets/admin/js/bootstrap.bundle.min.js"></script>

<!-- Plugins -->
<script src="<?= base_url() ?>assets/admin/plugins/simplebar/js/simplebar.min.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/peity/jquery.peity.min.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/admin/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= base_url() ?>assets/admin/cdn.quilljs.com/1.3.6/quill.js"></script>

<!-- App scripts -->
<script src="<?= base_url() ?>assets/admin/js/app.js"></script>

  <script>
    $(document).ready(function() {
      $('#example').DataTable();
      } );
  </script>
  <script>
    $(document).ready(function() {
      var table = $('#example2').DataTable( {
        lengthChange: false,
        buttons: [ 'copy', 'excel', 'pdf', 'print']
      } );
     
      table.buttons().container()
        .appendTo( '#example2_wrapper .col-md-6:eq(0)' );
    } );
  </script>

    <script>
       if ($(".data-attributes span").length) {
           $(".data-attributes span").peity("donut");
       }
    </script>


<!--     <script>
    var quill = new Quill('#editor', {
      theme: 'snow'
    });
    </script> -->

 <script type="text/javascript">
   if (typeof CKEDITOR !== 'undefined') {
   CKEDITOR.replace('editor', {
        // Main toolbar groups configuration
        toolbarGroups: [
            { name: 'clipboard', groups: ['clipboard', 'undo'] },
            { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
            { name: 'links' },
            { name: 'insert' },
            { name: 'forms' },
            { name: 'tools' },
            { name: 'document', groups: ['mode', 'document', 'doctools'] },
            { name: 'others' },
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
            { name: 'styles' },
            { name: 'colors' },
            { name: 'about' }
        ],

        // Additional plugins
        extraPlugins: 'colorbutton,justify,indentblock,indentlist,find,pagebreak,font,iframe,showblocks,preview',

        // Remove unwanted buttons
        removeButtons: 'Source,Save,NewPage,Preview,Print,Templates,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Language,Anchor,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,About',

        // Set the default language
        language: 'en'
    });
    }
 </script>
</body>
</html>