<!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Dashboard</title>
 
     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="common.css"> <!-- Link to your CSS file -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

     <!-- Chart.js -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
     <!-- Custom CSS -->
        <style>
            body {
                background-color: #f5f5f5;
                font-family: Arial, sans-serif;
            }
    
            .table thead {
                background-color: #000;
                color: #a06666;
            }
            .table th, .table td {
                text-align: center;
            }
        .settings-card {
          background-color: rgb(255, 255, 255);
          padding: 1.5rem;
          border-radius: 0.5rem;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .toggle-switch {
          display: flex;
          align-items: center;
        }
        .toggle-switch input[type="checkbox"] {
          margin-left: auto;
        }
        logo img {
          width: 10%;
          max-width: 12px;
          margin-bottom: rem;
          
        }
   
  </style>
</head>
<body>
  

</div>
<div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <?php require('inc/sideMenu.php'); ?>
      <!-- Main Content -->
      <div class="col-md-10 p-4">
        <h2>Settings</h2>
        <!-- General Settings -->
        <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="card-title m-0">General Settings</h5>
        <button type="button" class="btn btn-outline-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#general-s">
            <i class="bi bi-pencil-square"></i> Edit
            </button>
         </div>
          <h6 class ="card-subtitle mb-1 fw-bold">Site Title</h6>
              <p class="card-text" id="site_title"></p>
              <h6 class ="card-subtitle mb-1 fw-bold">About US</h6>
              <p class="card-text" id="site_about"></p>
              <h6 class ="card-subtitle mb-1 fw-bold">Our Facilities</h6>
              <p class="card-text" id="site_facilities"></p>
              <h6 class ="card-subtitle mb-1 fw-bold">Choose your perfect hostel room</h6>
              <p class="card-text" id="site_hostelroom"></p>
          
        </div>
        </div>
           <!-- Logo
              <div class="logo">
                <img id="logoPreview" src="https://via.placeholder.com/120x50.png?text=Logo" alt="Logo">
              </div> -->

              <!-- <div class="mb-3">
                <label for="logoUpload" class="form-label"><strong>Change Logo:</strong></label>
                <input type="file" class="form-control" id="logoUpload" accept="image/*">
              </div>
               -->
      
          <!--General Setting Modal -->
        <div class="modal fade" id="general-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="general_s_form">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">General Setting</h5>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label fw-bold">Site Title</label>
                  <input type="text"  name="site_title" id="site_title_inp" class="form-control shadow-none" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">About Us</label>
                  <textarea name="site_about" id="site_about_inp" class="form-control shadow-none" rows="6" required></textarea>
                </div>
               <div class="mb-3">
                  <label class="form-label fw-bold">Our Facilities</label>
                  <textarea name="site_facilities" id="site_facilities_inp" class="form-control shadow-none" rows="6" required></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Choose your perfect Hostel Room</label>
                  <textarea name="site_hostelroom" id="site_hostelroom_inp" class="form-control shadow-none" rows="6" required></textarea>
                </div>
              </div>
              

              <div class="modal-footer">
                <button type="button" onclick="site_title.value=general_data.site_title, site_about.value=general_data.site_about,site_facilities.value=general_data.site_facilities,site_hostelroom.value=general_data.site_hostelroom" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-outline-primary">Submit</button>
                </div>
                
                
            </div>
            </form>
          </div>
        </div>

            <!-- Shutdown setting -->
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="card-title m-0">Shutdown Website</h5>
                    <div class="form-check form-switch">
                      <form>
              <input onchange="upd_shutdown(this.value)"class="form-check-input" type="checkbox" >
              </form>
                    </div>
                  </div>
              <p class="card-text" > No Customer will be able to book hostel when Shutdown Mode is On</p>
            </div>

        <!-- contact detail setting -->
<div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="card-title m-0">Contact Settings</h5>
        <button type="button" class="btn btn-outline-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#contacts-s">
            <i class="bi bi-pencil-square"></i> Edit
            </button>
</div>
         <div class="row">
          <div class="col-lg-6">
            <div class="mb-4">
          <h6 class ="card-subtitle mb-1 fw-bold">Address</h6>
          <p class="card-text" id="address"></p>
            </div>
        <div class="mb-4">
          <h6 class ="card-subtitle mb-1 fw-bold">Phone Number</h6>
          <p class="card-text mb-1" id="phoneno">
            <i class="bi bi-telephone-fill"></i>
          </p>
        </div>
            <div class="mb-4">
          <h6 class ="card-subtitle mb-1 fw-bold">Email</h6>
          <p class="card-text" id="email"></p>
            </div>
          </div>
         </div>
         
              
        </div>
</div>

          <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  
  <?php include('inc/scripts.php'); ?>
    <!-- Script to Preview Logo and Profile Photo Image -->
    <script>

          //general function
          let general_data, contacts_data;
          let shutdownToggle; // Global declaration
          let general_s_form=document.getElementById('general_s_form');
          let site_title_inp=document.getElementById('site_title_inp');
          let site_about_inp=document.getElementById('site_about_inp');
          

          function get_general() 
      {
        let site_title=document.getElementById('site_title');
        let site_about=document.getElementById('site_about');

        let shutdownToggle = document.getElementById('shutdown-toggle');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/settings_crud.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () 
        {
            general_data = JSON.parse(this.responseText);

            document.getElementById("site_title").innerText = general_data.site_title;
            document.getElementById("site_about").innerText = general_data.site_about;

            document.getElementById("site_title_inp").innerText = general_data.site_title_inp;
            document.getElementById("site_about_inp").innerText = general_data.site_about_inp;

            shutdownToggle = document.getElementById('shutdown-toggle');

            // Adjust shutdown toggle
            if(general_data.shutdown==0)
            {
              shutdown_toggle.checked=false;
              shutdown_toggle.value=0;
            }
            else
            {
              shutdown_toggle.checked=true;
              shutdown_toggle.value=1;
            }
            // let shutdownToggle = document.querySelector('.form-check-input');
            // shutdownToggle.checked = general_data.shutdown == 1;
            // shutdownToggle.value = general_data.shutdown;
        }

        xhr.send('get_general');
      }
            
      general_s_form.addEventListener('submit',function(e){
        e.preventDefault();
        upd_general(site_title_inp.value, site_about_inp.value)
      })

      function upd_general(site_title_val,site_about_val)
      {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/settings_crud.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload=function(){

          var myModal=document.getElementById('general-s');
          var modal=bootstrap.Modal.getInstance(myModal);
          modal.hide();

          if(this.responseText.trim()=='1')
        {
          alert('success','Changes Saved!');
          get_general();
        }
         else
         {
          alert('error','No Changes Made!');

         }
         }
        xhr.send('site_title='+site_title_val+'&site_about='+site_about_val+'&upd_general');
      }

      //Shutdown function
      function upd_shutdown(val) 
      {
          // let shutdownToggle = document.querySelector('.form-check-input');
          shutdownToggle.value = shutdownToggle.checked ? 1 : 0;

          let xhr = new XMLHttpRequest();
          xhr.open("POST", "ajax/settings_crud.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

          xhr.onload = function () {
              if (this.responseText.trim() === "1") {
                  alert("success", "Site has been Shutdown!");
                  get_general(); 
              } else {
                  alert("error", "Shutdown mode Off!");
              }
              if (general_data.shutdown == 0) {
            shutdownToggle.checked = false;
            shutdownToggle.value = 0;
        } else {
            shutdownToggle.checked = true;
            shutdownToggle.value = 1;
        }
                };
        xhr.send("upd_shutdown="+val);
      }

      function get_contact() 
      {
        
        let contact_p_id=['address','phoneno','email'];
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/settings_crud.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () 
        {
            contacts_data=JSON.parse(this.responseText);
            console.log(contacts_data);

        }


        xhr.send('get_contact');
      }


    // Preview for Logo Upload
    // document.getElementById('logoUpload').addEventListener('change', function(event) {
    //   const [file] = event.target.files;
    //   if (file) {
    //     const logoPreview = document.getElementById('logoPreview');
    //     logoPreview.src = URL.createObjectURL(file);
    //   }
      
    // });
    window.onload= function(){
      get_general();
      get_contact();
}

    
 </script>

</body>
</html>
