<?php if (defined('FOOTER_INCLUDED')) return; define('FOOTER_INCLUDED', true); ?>
  <footer class="d-footer">
  <div class="">
    <p class="mb-0 text-center"> &copy; <span class="current-year"></span> Made with ❤️ by AbeLab</p>
  </div>
</footer></main>

  <!-- jQuery library js -->
  <script src="<?=base_url()?>assets/js/lib/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js -->
  <script src="<?=base_url()?>assets/js/lib/bootstrap.bundle.min.js"></script>
  <!-- Apex Chart js -->
  <script src="<?=base_url()?>assets/js/lib/apexcharts.min.js"></script>
  <!-- Iconify Font js -->
  <script src="<?=base_url()?>assets/js/lib/iconify-icon.min.js"></script>
  <!-- Data Table js -->
  <script src="<?=base_url()?>assets/js/lib/dataTables.min.js"></script>
  
  <!-- jQuery UI js -->
  <script src="<?=base_url()?>assets/js/lib/jquery-ui.min.js"></script>
  
  <!-- main js -->
  <?php $appVer = file_exists(FCPATH.'assets/js/app.js') ? filemtime(FCPATH.'assets/js/app.js') : time(); ?>
  <script src="<?=base_url()?>assets/js/app.js?v=<?= $appVer ?>"></script>
  <!-- API JS -->
  <script>var BASE_URL = '<?= base_url() ?>';</script>
  <?php $apiVer = file_exists(FCPATH.'assets/js/api.js') ? filemtime(FCPATH.'assets/js/api.js') : time(); ?>
  <script src="<?=base_url()?>assets/js/api.js?v=<?= $apiVer ?>"></script>
  <!-- Auto-reload + auto-add activate/deactivate for all modules -->
  <script>
  (function() {
    var mutationMethods = ['create', 'update', 'delete', 'activate', 'deactivate', 'setActive'];
    Object.keys(API).forEach(function(mod) {
      if (typeof API[mod] === 'object' && API[mod] !== null) {
        // Auto-add activate/deactivate if module has delete but not activate
        if (typeof API[mod].delete === 'function') {
          if (typeof API[mod].activate !== 'function') {
            API[mod].activate = function(id) { return API.get('api/' + mod + '/' + id + '/activate'); };
          }
          if (typeof API[mod].deactivate !== 'function') {
            API[mod].deactivate = function(id) { return API.get('api/' + mod + '/' + id + '/deactivate'); };
          }
        }
        mutationMethods.forEach(function(method) {
          if (typeof API[mod][method] === 'function') {
            var original = API[mod][method];
            API[mod][method] = function() {
              var result = original.apply(this, arguments);
              if (result && typeof result.then === 'function') {
                return result.then(function(res) {
                  if (res && res.success) setTimeout(function() { location.reload(); }, 500);
                  return res;
                });
              }
              return result;
            };
          }
        });
      }
    });
  })();
  </script>
<script>
$(function() {
  // ============================ Revenue Statistics Chart ===============================
  var revEl = document.querySelector("#revenueStatistic");
  if (revEl) {
    var options = {
      series: [{ name: 'Total Fee', data: [25, 35, 50, 60, 26, 20, 40, 20, 50, 16, 10, 40] },
               { name: 'Collected Fee', data: [15, 16, 24, 30, 20, 15, 20, 10, 25, 10, 6, 20] }],
      chart: { type: 'bar', height: 250, stacked: true, toolbar: { show: false }, zoom: { enabled: true } },
      colors: ["#25A194", "#FF7A2C"],
      plotOptions: { bar: { horizontal: false, columnWidth: "50%", shape: "pyramid" } },
      xaxis: { categories: ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sep','Oct','Nov','Dec'] },
      yaxis: { labels: { formatter: function(v) { return "$" + v + "k"; }, style: { fontSize: "14px" } } },
      legend: { show: false }, fill: { opacity: 1 }
    };
    new ApexCharts(revEl, options).render();
  }

  // ===================== Income Vs Expense ===============================
  function createChartThree(chartId, color1, color2) {
    var el = document.querySelector("#" + chartId);
    if (!el) return;
    var options = {
      series: [{ name: 'Income', data: [48, 35, 55, 32, 48, 30, 15, 50, 57] },
               { name: 'Expense', data: [12, 20, 15, 26, 22, 60, 40, 32, 25] }],
      legend: { show: false },
      chart: { type: 'area', width: '100%', height: 260, toolbar: { show: false },
               padding: { left: 0, right: 0, top: 0, bottom: 0 } },
      dataLabels: { enabled: false },
      stroke: { curve: 'stepline', width: 2, colors: [color1, color2], lineCap: 'round' },
      grid: { show: true, borderColor: '#D1D5DB', strokeDashArray: 1, position: 'back',
              xaxis: { lines: { show: false } }, yaxis: { lines: { show: true } },
              padding: { top: -20, right: 0, bottom: -10, left: 0 } },
      colors: [color1, color2],
      markers: { colors: [color1, color2], strokeWidth: 1, size: 0, hover: { size: 10 } },
      xaxis: { labels: { show: false },
               categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] },
      yaxis: { labels: { formatter: function(v) { return "$" + v + "k"; }, style: { fontSize: "14px" } } },
      tooltip: { x: { format: 'dd/MM/yy HH:mm' } },
      fill: { type: "gradient", gradient: { shade: "light", type: "vertical", opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } }
    };
    new ApexCharts(el, options).render();
  }
  if (document.querySelector("#incomeExpense")) createChartThree('incomeExpense', '#16a34a', '#FF9F29');

  // ================================ New Admissions Chart ================================
  var naEl = document.querySelector("#newAdmissions");
  if (naEl) {
    var options = {
      series: [40, 87, 87, 30],
      colors: ['#0A51CE', '#25A194', '#FF7A2C', '#009F5E'],
      labels: ['Health', 'Business', 'Lifestyle', 'Entertainment'],
      legend: { show: false },
      chart: { type: 'donut', height: 270, sparkline: { enabled: true },
               margin: { top: 0, right: 0, bottom: 0, left: 0 },
               padding: { top: 0, right: 0, bottom: 0, left: 0 } },
      stroke: { width: 2 }, dataLabels: { enabled: false }
    };
    new ApexCharts(naEl, options).render();
  }

  // ================================ Animated Radial Progress Bar ================================
  if ($('svg.radial-progress').length) {
    $('svg.radial-progress').each(function() {
      $(this).find('circle.complete').removeAttr('style');
    });
    $(window).scroll(function() {
      $('svg.radial-progress').each(function() {
        var $this = $(this);
        if ($(window).scrollTop() >= $this.offset().top - $(window).height() &&
            $(window).scrollTop() <= $this.offset().top + $this.height()) {
          var percent = $this.data('percentage');
          var radius = $this.find('circle.complete').attr('r');
          var circumference = 2 * Math.PI * radius;
          var offset = circumference - ((percent * circumference) / 100);
          $this.find('circle.complete').animate({ 'stroke-dashoffset': offset }, 1250);
        }
      });
    }).trigger('scroll');
  }

  // ============================= Calendar Js =================================
  var display = document.querySelector(".display");
  var days = document.querySelector(".days");
  var previous = document.querySelector(".left");
  var next = document.querySelector(".right");
  if (display && days && previous && next) {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth();
    function displayCalendar() {
      var firstDay = new Date(year, month, 1);
      var lastDay = new Date(year, month + 1, 0);
      var firstDayIndex = firstDay.getDay();
      var numberOfDays = lastDay.getDate();
      var formattedDate = date.toLocaleString("en-US", { month: "long", year: "numeric" });
      display.innerHTML = formattedDate;
      days.innerHTML = "";
      for (var x = 1; x <= firstDayIndex; x++) { days.appendChild(document.createElement("div")); }
      for (var i = 1; i <= numberOfDays; i++) {
        var div = document.createElement("div");
        var currentDate = new Date(year, month, i);
        div.dataset.date = currentDate.toDateString();
        div.innerHTML = i;
        days.appendChild(div);
        if (currentDate.getFullYear() === new Date().getFullYear() &&
            currentDate.getMonth() === new Date().getMonth() &&
            currentDate.getDate() === new Date().getDate()) {
          div.classList.add("current-date");
        }
      }
    }
    displayCalendar();
    previous.addEventListener("click", function() {
      days.innerHTML = "";
      if (month < 0) { month = 11; year--; }
      month--;
      date.setMonth(month);
      displayCalendar();
    });
    next.addEventListener("click", function() {
      days.innerHTML = "";
      if (month > 11) { month = 0; year++; }
      month++;
      date.setMonth(month);
      displayCalendar();
    });
  }
});
</script>

</body>


<!-- Mirrored from edudash-php.theme.picode.in/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 00:41:50 GMT -->
</html>