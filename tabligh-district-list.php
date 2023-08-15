<?php
require_once "class/initialize.php";
$statement = " divisions t1 ";
$statement .= " ORDER BY t1.name ASC ";
$result_listes = QB::query("SELECT t1.*  FROM {$statement} ")->get();
$dis_count = QB::query("SELECT COUNT(t1.id) AS total_item FROM district t1")->first();
$tha_count = QB::query("SELECT COUNT(t1.id) AS total_item FROM thana t1")->first();
$uni_count = QB::query("SELECT COUNT(t1.id) AS total_item FROM unions t1")->first();
$cont_count = QB::query("SELECT COUNT(t1.id) AS total_item FROM contact t1")->first();
$total_dis = $dis_count->total_item;
$total_tha = $tha_count->total_item;
$total_uni = $uni_count->total_item;
$total_cont = $cont_count->total_item;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>বাংলাদেশের তাবলীগ জামাতের সাথীদের কন্টাক্ট লিস্ট</title>
  <link href="class/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <style>
  .Indigo-400 {
    animation-duration: 0.9s;
    animation-name: fadein;
    animation-timing-function: ease-in;
  }

  @keyframes fadein {
    0% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  .Indigo-400 {
    text-decoration: none;
    color: #5C6BC0;
  }

  .Indigo-400:hover {
    color: #1d8937;
  }

  .purple-400 {
    color: #AB47BC;
  }

  .counter {
    color: #eb3b5a;
    font-family: 'Muli', sans-serif;
    width: 160px;
    height: 160px;
    text-align: center;
    border-radius: 100%;
    padding: 66px 22px 30px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
  }

  .counter:before,
  .counter:after {
    content: "";
    background: #fff;
    width: 80%;
    height: 90%;
    border-radius: 100%;
    box-shadow: -5px 5px 5px rgba(0, 0, 0, 0.3);
    transform: translateX(-50%)translateY(-50%);
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: -1;
  }

  .counter:after {
    background: linear-gradient(45deg, #B81242 49%, #D74A75 50%);
    width: 100%;
    height: 100%;
    box-shadow: none;
    transform: translate(0);
    top: 0;
    left: 0;
    z-index: -2;
    clip-path: polygon(50% 50%, 50% 0, 100% 0, 100% 100%, 0 100%, 0 50%);
  }

  .counter .counter-icon {
    color: #fff;
    background: linear-gradient(45deg, #B81242 49%, #D74A75 50%);
    font-size: 23px;
    line-height: 60px;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1;
    transition: all 0.3s;
  }

  .counter .counter-icon i.material-icons {
    transform: rotateX(0deg);
    transition: all 0.3s ease 0s;
  }

  .counter:hover .counter-icon i.material-icons {
    transform: rotateX(360deg);
  }

  .counter h3 {
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 0 0 3px;
  }

  .counter .counter-value {
    font-size: 20px;
    font-weight: 700;
  }

  .counter.orange {
    color: #F38631;
  }

  .counter.orange:after,
  .counter.orange .counter-icon {
    background: linear-gradient(45deg, #F38631 49%, #F8A059 50%);
  }

  .counter.green {
    color: #88BA1B;
  }

  .counter.green:after,
  .counter.green .counter-icon {
    background: linear-gradient(45deg, #88BA1B 49%, #ACD352 50%);
  }

  .counter.blue {
    color: #5DB3E4;
  }

  .counter.blue:after,
  .counter.blue .counter-icon {
    background: linear-gradient(45deg, #5DB3E4 49%, #7EBEE1 50%);
  }

  @media screen and (max-width:990px) {
    .counter {
      margin-bottom: 40px;
    }
  }

  .parallax {
    /* The image used */
    background-image: url("image/i1.jpg");
    /* Set a specific height */
    min-height: 200px;
    padding: 30px;
    /* border-radius: 10px; */
    /* Create the parallax scrolling effect */
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
  }
  </style>
</head>

<body>
  <div class="container">
    <div class="row">
      <img src="image/header.jpg">
      <div class="col text-center" style="margin-top:30px; margin-bottom: 20px;">
        <h1>বাংলাদেশের তাবলীগ জামাতের সাথীদের কন্টাক্ট লিস্ট</h1>
        <br>
      </div>
    </div>
    <br> <br>
    <div class="parallax">
      <div class="col text-center" style="margin-top:20px; margin-bottom: 20px;">
        <h2>তথ্য ভান্ডার</h2>
        <br>
      </div>
      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="counter">
            <div class="counter-icon">
              <i class="material-icons">place</i>
            </div>
            <h3>জেলা</h3>
            <span class="counter-value"><?php echo $total_dis; ?></span>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="counter orange">
            <div class="counter-icon">
              <i class="material-icons">place</i>
            </div>
            <h3>থানা</h3>
            <span class="counter-value"><?php echo $total_tha; ?></span>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="counter green">
            <div class="counter-icon">
              <i class="material-icons">place</i>
            </div>
            <h3>ইউনিয়ন</h3>
            <span class="counter-value"><?php echo $total_uni; ?></span>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="counter blue">
            <div class="counter-icon">
              <i class="material-icons">contacts</i>
            </div>
            <h3>কন্টাক্ট নম্বর</h3>
            <span class="counter-value"><?php echo $total_cont; ?></span>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="col text-center" style="margin-top:30px; margin-bottom: 20px;">
      <h2>বাংলাদেশের জেলাসমূহ</h2>
      <br>
    </div>
    <p style="color: #5C6BC0;">জেলার তথ্য জানতে জেলার উপর ক্লিক করুন</p>
    <div class="row " data-masonry='{"percentPosition": true }' style="margin-top:15px;">
      <?php
            foreach ($result_listes as $test) {
                $division = $test->id;
                $district_lists = QB::query("SELECT t1.* FROM district t1 WHERE t1.divId = $division ORDER BY t1.name ASC")->get();
            ?>
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <div class="card border border-info shadow-0 mb-3">
          <div class="card-header">
            <b style="color: #0d3d56;"><?php echo $test->bn_name . " বিভাগ" . " (" . $test->name . ")"; ?></b>
          </div>
          <div class="card-body">
            <?php $i = 1; ?>
            <?php foreach ($district_lists as $dis) { ?>
            <p class="card-text"><a class="Indigo-400" href="tabligh-contact-list.php?zila=<?php echo $dis->name; ?>"
                target="_blank"><?php echo $i . "." . $dis->bname . " (" . $dis->name . ")"; ?></a> </p>
            <?php $i++;
                            }
                            ?>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
  </div>
  <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
  <script>
  $(document).ready(function() {
    $('.counter-value').each(function() {
      $(this).prop('Counter', 0).animate({
        Counter: $(this).text()
      }, {
        duration: 3500,
        easing: 'swing',
        step: function(now) {
          $(this).text(Math.ceil(now));
        }
      });
    });
  });
  </script>
  <br>
  <footer>
    <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
      <p style="color:#5C6BC0 ;">Copyright © <?php echo date("Y"); ?> &amp; Developed
        By <a class="text-decoration-none" target="_blank" href="http://esteemsoftbd.com"><span
            style="color: #F38631;">Esteem Soft
            Limited.</span></a></p>
    </div>
  </footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"
  integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async>
</script>

</html>