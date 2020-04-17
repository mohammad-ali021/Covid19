<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Covid-19 - Dashboard</title>

  <!-- Custom fonts for this template-->
  <link href="<?=base_url()?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="<?=base_url()?>assets/css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?=base_url()?>assets/style.css">
  <script src="<?=base_url()?>assets/countries.js"></script>
  <script src="<?=base_url()?>assets/miniature.earth.js"></script>


  <script>

    var myearth;
    var localNewsMarker;
    var news = [];
    
    window.addEventListener( "earthjsload", function() {
    
      myearth = new Earth( document.getElementById('element'), {
    
        location : {lat: 18, lng: 50},
        zoom: 1.05,
        light: 'none',
        zoomable:true,
        transparent : true,
        mapSeaColor : 'RGBA(255,255,255,0.76)',
        mapLandColor : 'red',
        mapBorderColor : '#5D5D5D',
        mapBorderWidth : 0.25,
        //mapStyles : ' #CU, #DO, #HT, #JM, #PR { fill: red; stroke: red; } ',
        mapHitTest : true,
    
        autoRotate: true,
        autoRotateSpeed: 0.7,
        autoRotateDelay: 4000,
        
      } );
      
      
      myearth.addEventListener( "ready", function() {
        this.startAutoRotate();
            
        news[0].element.addEventListener( 'click', highlightBreakingNews );
        
        
        // Mongolia
        
        news[1] = myearth.addOverlay( {
          location: {lat: 49, lng: 106},
          offset: 0.3,
          depthScale : 0.25,
          className : 'warning',
          transform: 'translate(-50%, -50%)',
          occlude : "custom",
          newsId : 1
        } );
        
        news[1].element.addEventListener( 'click', highlightBreakingNews );
      
        myearth.addLine({
          polyLine : true,
          locations: [
            {lat: 50, lng: 100},
            {lat: 43, lng: 100},
            {lat: 43, lng: 96},
            {lat: 46, lng: 90},
            {lat: 50, lng: 90},
            {lat: 50, lng: 100}
          ],
          color : "red",
          width: 0.75
        });
    
        
        // Sumatra
        
        news[2] = myearth.addOverlay( {
          location: {lat: 4, lng: 91.5},
          offset: 0.3,
          depthScale : 0.25,
          className : 'warning',
          transform: 'translate(-50%, -50%)',
          occlude : "custom",
          newsId : 2
        } );
        
        news[2].element.addEventListener( 'click', highlightBreakingNews );
        
        myearth.addMarker( {
          location: {lat: 3.52, lng: 97.3},
          mesh : "Pin3",
          color : "red",
          scale: 0.4,
          hotspot: false,
        } );
        
        
      } );
      
      
      
      var startLocation, rotationAngle;
      
      myearth.addEventListener( "dragstart", function() {
        
        startLocation = myearth.location;
        
      } );
      
      myearth.addEventListener( "dragend", function() {
        
        rotationAngle = Earth.getAngle( startLocation, myearth.location );			
        
      } );
      
      var selectedCountry;
      var jsonResult;
      var jsonObj; 
      var options="<option value=''>Select Country </option>";
      var totalCase;
      var totalDeath;
      var totalRecover;
      var totalCritical;
      $.getJSON('https://corona-api.com/countries', function(rows) {
        jsonObj=rows;
        
         totalCase=0;
         totalDeath=0;
         totalRecover=0;
         totalCritical=0;
            for (i in jsonObj){
                for (j in jsonObj[i] ){
                    for (key in jsonObj[i][j]){
                        if (key ==="name" ){
                            options+="<option value="+jsonObj[i][j].code+">"+jsonObj[i][j].name+"</option>";
                        }else if (key==="latest_data"){
                            for (x in jsonObj[i][j][key]){
                                if (x==="confirmed"){
                                    totalCase+=Number(jsonObj[i][j][key].confirmed);
                                    totalDeath+=Number(jsonObj[i][j][key].deaths);
                                    totalRecover+=Number(jsonObj[i][j][key].recovered);
                                    totalCritical+=Number(jsonObj[i][j][key].critical);
                                }
                            }
                        }
                        
                    }
                }
      }
        $('#country_list').html(options);
        $('#total_case').html(totalCase.toLocaleString());
        $('#total_death').html(totalDeath.toLocaleString());
        $('#total_recover').html(totalRecover.toLocaleString());
        $('#total_critical').html(totalCritical.toLocaleString());

      });
      $('.select2').change(function(e){
            printObj(searchJSON(jsonObj, 'data', 'code', this.value), 'result');            
      });
      function searchJSON (json, content, where, is) {
        content = json[content];
        var result = [];
        for (var key in content) {
          if (content[key][where] == is || is == '') {
            result.push(content[key]);
          }
        }
        return result;
      }
    
      function printObj (obj, container) {
        var covidData= [];
        //var covidDataStr= "{";
        for (var i in obj) {
          for (var j in obj[i]) {
            if (j != "coordinates"){
                if(j==="today" || j==="latest_data")
                    {
                    var content = obj[i][j];
                    for(var key in content)
                    {   
                        if (key ==="calculated"){
                            for(var x in content[key]){
                                //covidDataStr+=x +":"+content[key][x]+",";
                                covidData.push(content[key][x]);
                            }
                        }else{
                            //covidDataStr+=key +":"+content[key]+",";
                            covidData.push(content[key]);
                        }
                    }
                }
                else
                {
                    //covidDataStr+=j +":"+obj[i][j]+",";
                    covidData.push(obj[i][j]);
                }
            }
          }
        }

        //document.getElementById(container).innerHTML = covidDataStr;
        $('#total_case').html(covidData[7].toLocaleString());
        $('#total_death').html(covidData[6].toLocaleString());
        $('#total_recover').html(covidData[8].toLocaleString());
        $('#total_critical').html(covidData[9].toLocaleString());
        $('#country_name').html(covidData[0]);
        $('#population').html(covidData[2].toLocaleString());
        $('#today_case').html(covidData[5].toLocaleString());
        $('#today_death').html(covidData[4].toLocaleString());
        $('#recovery_rate').html(covidData[11].toLocaleString()+"%");
        $('#death_rate').html(covidData[10].toLocaleString()+"%");
        $('#updated').html("Updated At: <br/> <br/>"+covidData[3]);

        //var objvalue=JSON.parse(covidData);
      }
    
      myearth.addEventListener( 'click', function( event ) {
        if ( rotationAngle > 5 ) return; // mouseup after drag
        //alert (countries[ event.id ]);
        if ( event.id ) {
        
          if ( selectedCountry != event.id ) {
            selectedCountry = event.id;
            printObj(searchJSON(jsonObj, 'data', 'code', event.id), 'result');
            //get_total();
            //alert(covidData);
          }
          
          // create news marker on first click
          
          if ( ! localNewsMarker ) {
          
            localNewsMarker = this.addMarker( {
              mesh : "Marker",
              color: '#257cff',
              location : event.location,
              scale: 0.01
            } );
            
            localNewsMarker.animate( 'scale', 0.9, { easing: 'out-back' } );
          
          } else {
            
            localNewsMarker.animate( 'location', event.location, { duration: 200, relativeDuration: 50, easing: 'in-out-cubic' } );
          
          }
          
        }
        
      } );
      
    } );
    </script>

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
          </div>

          <div class="row">
            <div class="alert alert-info"><i class="fas fa-2x fa-info-circle "></i> Click on a country or territory to see cases, deaths, and recoveries.</div>
            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
              <div class="card shadow mb-4">
                <!-- World Map -->
                <div id="earth-col">
                  <div id="element" class="little-earth"></div>
                </div>
                <!-- end of World Map -->
              </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
              <div class="card shadow mb-4">
                <!--  -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock fa-2x text-info"></i> <span class ="text-info" id="updated"></span></h5>

                </div>
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary" id="country_name">Country Name</h6>

                </div>
                <select class="form-control select2" id ="country_list" name=""></select>

                <!-- Card Body -->
                <div class="card-body">
                    <h4 class="small font-weight-bold">Population <span class="float-right" id="population">-</span></h4>
                    <h4 class="small font-weight-bold">Today Confirmed Cases <span class="float-right" id="today_case">-</span></h4>
                    <h4 class="small font-weight-bold">Today Death Cases <span class="float-right" id="today_death">-</span></h4>
                    <h4 class="small font-weight-bold">Recovery Rate <span class="float-right" id="recovery_rate">-</span><i class="fas fa-caret-up fa-2x text-success"></i></h4>
                    <h4 class="small font-weight-bold">Death Rate <span class="float-right" id="death_rate">-</span><i class="fas fa-caret-down fa-2x text-danger"></i></h4>
                </div>

                <!-- Confirmed cases-->
                <div class="col-xl-12 col-md-12" style="padding-top:0.5em;padding-bottom: 0.5em;">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Confirmed Cases</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_case">-</div>
                        </div>
                        <div class="col-auto">
                        <i class="fas fa-user fa-2x text-warning"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                
                <!-- Total Deaths -->
                <div class="col-xl-12 col-md-12" style="padding-top:0.5em;padding-bottom: 0.5em;">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Deaths</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"id="total_death">-</div>
                        </div>
                        <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-danger"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>


                <!-- Total Recovery -->
                <div class="col-xl-12 col-md-12" style="padding-top:0.5em;padding-bottom: 0.5em;">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Recover</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"id="total_recover">-</div>
                        </div>
                        <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Total Critical  -->
                <div class="col-xl-12 col-md-12" style="padding-top:0.5em;padding-bottom: 0.5em;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Critical Patient</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_critical">-</div>
                        </div>
                        <div class="col-auto">
                        <i class="fas fa-user-injured fa-2x text-info"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>



              </div>
            </div>
          </div>

          <!-- Content Row -->
         

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; brighttech.us 2020 Developed by <a href="http://behance.net/brighttech" target="_blank">Ali Abassi</a></a></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  

  <!-- Bootstrap core JavaScript-->
  <script src="<?=base_url()?>assets/vendor/jquery/jquery.min.js"></script>
  <script src="<?=base_url()?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?=base_url()?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?=base_url()?>assets/js/sb-admin-2.min.js"></script>
  <script>
      $(document).ready(function() {
        $('.select2').select2();
    });
  </script>
 

  

</body>

</html>
