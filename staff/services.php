<?php
require('inc/db.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <!-- Include Bootstrap CSS -->
</head>

<body class="bg-light">
    <?php require('inc/sidemenu.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Services</h3>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Services</h5>
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal" data-bs-target="#service-s">
                                <i class="bi bi-plus-square"></i> Add
                            </button>
                        </div>

                        <div class="table-responsive-md" style="height: 350px; overflow-y: scroll;">
                            <table class="table table-hover border">
                                <thead class="sticky-top">
                                    <tr class="bg-dark text-light">
                                        <th scope="col">S.N.</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="services-data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SERVICES MODAL -->
    <div class="modal fade" id="service-s" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1">
        <div class="modal-dialog">
            <form id="services-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Services</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="name" class="form-control shadow-none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Price</label>
                            <input type="text" name="price" class="form-control shadow-none">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-secondary shadow-none">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php @include('inc/scripts.php'); ?>

    <script>
        // Load services on page load
        function loadServices() {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/services.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                document.getElementById("services-data").innerHTML = this.responseText;
            };
            xhr.send("load_services");
        }

        loadServices();

        let servicesForm = document.getElementById('services-form');

        servicesForm.addEventListener('submit', function (e) {
            e.preventDefault();
            addService();
        });

        // Add new service
        function addService() {
            let data = new FormData(servicesForm);
            data.append('add_service', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/services.php", true);

            xhr.onload = function () {
                var myModal = document.getElementById('service-s');
                var modal = bootstrap.Modal.getInstance(myModal);
                modal.hide();

                if (this.responseText == 1) {
                    alert('Success', 'New service added!');
                    servicesForm.reset();
                    loadServices();
                } else {
                    alert('Error', 'Server Down!');
                }
            };
            xhr.send(data);
        }

        // Edit service inline
        function editService(id) {
            // Hide the text and show the input fields
            document.getElementById('name-' + id).classList.add('d-none');
            document.getElementById('price-' + id).classList.add('d-none');
            document.getElementById('name-input-' + id).classList.remove('d-none');
            document.getElementById('price-input-' + id).classList.remove('d-none');

            // Hide the Edit button and show the Save button
            document.getElementById('edit-btn-' + id).classList.add('d-none');
            document.getElementById('save-btn-' + id).classList.remove('d-none');
        }

        // Save edited service
        function saveService(id) {
            let name = document.getElementById('name-input-' + id).value;
            let price = document.getElementById('price-input-' + id).value;

            let data = new FormData();
            data.append('update_service', '');
            data.append('id', id);
            data.append('name', name);
            data.append('price', price);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/services.php", true);

            xhr.onload = function () {
                if (this.responseText == 1) {
                    // Update the displayed name and price
                    document.getElementById('name-' + id).innerText = name;
                    document.getElementById('price-' + id).innerText = price;

                    // Hide the input fields and show the text
                    document.getElementById('name-' + id).classList.remove('d-none');
                    document.getElementById('price-' + id).classList.remove('d-none');
                    document.getElementById('name-input-' + id).classList.add('d-none');
                    document.getElementById('price-input-' + id).classList.add('d-none');

                    // Hide the Save button and show the Edit button
                    document.getElementById('edit-btn-' + id).classList.remove('d-none');
                    document.getElementById('save-btn-' + id).classList.add('d-none');
                } else {
                    alert('Error saving changes!');
                }
            };

            xhr.send(data);
        }

        // Delete service
        function deleteService(id) {
            if (confirm("Are you sure you want to delete this service?")) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/services.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function () {
                    if (this.responseText == 1) {
                        alert('Success', 'Service deleted!');
                        loadServices();
                    } else {
                        alert('Error', 'Server Down!');
                    }
                };
                xhr.send("delete_service=1&id=" + id);
            }
        }
    </script>
</body>

</html>
