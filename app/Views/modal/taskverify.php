<?= view('frontend/inc/header') ;?> 
<body>
<?= view('frontend/inc/nav');?>


  <div id="smooth-wrapper">
    <div id="smooth-content">
      <main id="primary" class="site-main">

        <div class="space-for-header"></div>
        <!-- start: Breadcrumb Section -->
        <section class="tj-page-header" data-bg-image="<?=base_url('public/assets/template/assets/images/career-breadcrumb-img.jpg');?>">
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
                <div class="tj-page-header-content text-center">
                  <h1 class="tj-page-title">Careers That Make an Impact – Protect the Digital World with Us</h1>
                  <div class="tj-page-link">
                    <span><i class="tji-home"></i></span>
                    <span>
                      <a href="<?=base_url();?>">Home</a>
                    </span>
                    <span><i class="tji-arrow-right"></i></span>
                    <span>
                      <span>Career</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="page-header-overlay" data-bg-image="<?=base_url('public/assets/template/assets/images/shape/pheader-overlay.jpg');?>"></div>
        </section>
        <!-- end: Breadcrumb Section -->

         <!-- start: Careers Section -->
        <section class="tj-careers-section section-gap">
          <div class="container">
            <div class="row rg-30">
            <?php
            if(!empty($careers)){
                foreach($careers as $career){
                ?>
                    <div class="col-xl-4 col-md-6">
                        <div class="tj-careers wow fadeInUp" data-wow-delay="0.1s">
                        <div class="tj-careers-icon mb-30">
                            <!-- <i class="tji-strategy"></i> -->
                             <img src="<?=($career['image'] ? $career['image'] : '');?>" width="50%" />
                        </div>
                        <div class="tj-careers-tag">
                            <span><?=($career['duration'] ? $career['duration'] : '');?> </span> <span><?=($career['type'] ? $career['type'] : '');?></span>
                        </div>
                        <h4 class="tj-careers-title">
                            <a href="<?=($career['slug'] ? base_url('career/'.$career['slug']) : '');?>"><?=($career['title'] ? $career['title'] :'');?></a>
                        </h4>
                        <div class="tj-careers-salary">
                            <span><?=($career['apply_on'] ? date('Y-M-d',strtotime($career['apply_on'])) : '');?></span> 
                        </div>
                        <div class="tj-careers-bottom">
                            <span class="location"><i class="tji-location"></i><?=($career['location'] ? $career['location'] : '');?></span>
                            <a href="<?=($career['slug'] ? base_url('career/'.$career['slug']) : '');?>" class="tj-careers-btn">
                            <div class="btn-text">
                                <span>Apply Now</span>
                            </div>
                            <span class="btn-icon">
                                <i class="tji-arrow-right"></i>
                                <i class="tji-arrow-right"></i>
                            </span>
                            </a>
                        </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
              

              <div class="col-xl-4 col-md-6">
                <div class="tj-careers wow fadeInUp" data-wow-delay="0.3s">
                  <div class="tj-careers-icon mb-30">
                    <i class="tji-manage"></i>
                  </div>
                  <div class="tj-careers-tag">
                    <span>Full time job/on site</span> <span>Urgent</span>
                  </div>
                  <h4 class="tj-careers-title">
                    <a href="career-details.html">Chief Security Officer (CSO)</a>
                  </h4>
                  <div class="tj-careers-salary">
                    <span>14 to 17 Lakhs</span> / Annum
                  </div>
                  <div class="tj-careers-bottom">
                    <span class="location"><i class="tji-location"></i>Bangalore, Karnataka</span>
                    <a href="career-details.html" class="tj-careers-btn">
                      <div class="btn-text">
                        <span>Apply Now</span>
                      </div>
                      <span class="btn-icon">
                        <i class="tji-arrow-right"></i>
                        <i class="tji-arrow-right"></i>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6">
                <div class="tj-careers wow fadeInUp" data-wow-delay="0.5s">
                  <div class="tj-careers-icon mb-30">
                    <i class="tji-process-1"></i>
                  </div>
                  <div class="tj-careers-tag">
                    <span>Full Time Job/On Site</span> <span>Urgent</span>
                  </div>
                  <h4 class="tj-careers-title">
                    <a href="career-details.html">Security Engineer</a>
                  </h4>
                  <div class="tj-careers-salary">
                    <span>14 to 17 Lakhs</span> / Annum
                  </div>
                  <div class="tj-careers-bottom">
                    <span class="location"><i class="tji-location"></i>Bangalore, Karnataka</span>
                    <a href="career-details.html" class="tj-careers-btn">
                      <div class="btn-text">
                        <span>Apply Now</span>
                      </div>
                      <span class="btn-icon">
                        <i class="tji-arrow-right"></i>
                        <i class="tji-arrow-right"></i>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6">
                <div class="tj-careers wow fadeInUp" data-wow-delay="0.7s">
                  <div class="tj-careers-icon mb-30">
                    <i class="tji-operations"></i>
                  </div>
                  <div class="tj-careers-tag">
                    <span>Full time job/on site</span> <span>Urgent</span>
                  </div>
                  <h4 class="tj-careers-title">
                    <a href="career-details.html">Cloud Security Engineer</a>
                  </h4>
                  <div class="tj-careers-salary">
                    <span>14 to 17 Lakhs</span> / Annum
                  </div>
                  <div class="tj-careers-bottom">
                    <span class="location"><i class="tji-location"></i>Bangalore, Karnataka</span>
                    <a href="career-details.html" class="tj-careers-btn">
                      <div class="btn-text">
                        <span>Apply Now</span>
                      </div>
                      <span class="btn-icon">
                        <i class="tji-arrow-right"></i>
                        <i class="tji-arrow-right"></i>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6">
                <div class="tj-careers wow fadeInUp" data-wow-delay="0.9s">
                  <div class="tj-careers-icon mb-30">
                    <i class="tji-organize"></i>
                  </div>
                  <div class="tj-careers-tag">
                    <span>Full time job/on site</span> <span>Urgent</span>
                  </div>
                  <h4 class="tj-careers-title">
                    <a href="career-details.html">Endpoint Security Engineer</a>
                  </h4>
                  <div class="tj-careers-salary">
                    <span>14 to 17 Lakhs</span> / Annum
                  </div>
                  <div class="tj-careers-bottom">
                    <span class="location"><i class="tji-location"></i>Bangalore, Karnataka</span>
                    <a href="career-details.html" class="tj-careers-btn">
                      <div class="btn-text">
                        <span>Apply Now</span>
                      </div>
                      <span class="btn-icon">
                        <i class="tji-arrow-right"></i>
                        <i class="tji-arrow-right"></i>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6">
                <div class="tj-careers wow fadeInUp" data-wow-delay="1.1s">
                  <div class="tj-careers-icon mb-30">
                    <i class="tji-performance"></i>
                  </div>
                  <div class="tj-careers-tag">
                    <span>Full time job/on site</span> <span>Urgent</span>
                  </div>
                  <h4 class="tj-careers-title">
                    <a href="career-details.html">Ethical Hacker</a>
                  </h4>
                  <div class="tj-careers-salary">
                    <span>14 to 17 Lakhs</span> / Annum
                  </div>
                  <div class="tj-careers-bottom">
                    <span class="location"><i class="tji-location"></i>Bangalore, Karnataka</span>
                    <a href="career-details.html" class="tj-careers-btn">
                      <div class="btn-text">
                        <span>Apply Now</span>
                      </div>
                      <span class="btn-icon">
                        <i class="tji-arrow-right"></i>
                        <i class="tji-arrow-right"></i>
                      </span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <!-- post pagination -->
            <div class="tj-pagination d-flex justify-content-center">
              <ul>
                <li>
                  <span aria-current="page" class="page-numbers current">01</span>
                </li>
                <li>
                  <a class="page-numbers" href="#">02</a>
                </li>
                <li>
                  <a class="next page-numbers" href="#"><i class="tji-arrow-right-long"></i></a>
                </li>
              </ul>
            </div>
          </div>
        </section>
        <!-- end: Careers Section -->



        
        
    </main>
  <?= view('frontend/inc/footer') ?>
    </div>
  </div>
  <?= view('frontend/inc/footerLink') ?>
  <script>

</script>

<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
</script>




</body>

</html>