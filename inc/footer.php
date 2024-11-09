<div class="container-fluid bg-white mt-5">
<div class="row">
<div class="col-lg-4 p-4">
<h3 class="h-font fw-bold fs-3 mb-2">Her Home Hostel</h3>
<p>
hehehahahhahahaha</p>
</div>
<div class="col-lg-4 p-4">
<h5 class="mb-3">Links</h5>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">About Us</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Contact Us</a> <br>
<a href="" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a>
</div>
<div class="col-lg-4 p-4">
<h5 class="mb-3">Follow us</h5>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-twitter me-1"></i> Twitter
</a><br>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-facebook me-1"></i> Facebook
</a><br>
<a href="#" class="d-inline-block text-dark text-decoration-none mb-2"> <i class="bi bi-instagram ne-1"></i> instagram
</a><br>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    function CheckLoginToBook(status,room_id)
    {
        if(status){
            window.location.href='confirm_booking.php?id='+room_id;
        }
        else{
            alert('error','Please Login for Booking Room!');
        }
    }
</script>

