<?php session_start(); ?>
<?php include('../includes/header'); ?>



<!--insert  Modal -->
<div class="modal fade" id="insertdata" tabindex="-1" aria-labelledby="insertdataLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="insertdataLabel">INSERT DATA</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="insert" method="POST">
                <div class="modal-body">
                    <label for=>Name</label>
                    <div class="form-group mb-3">
                        <input type="name" class="form-control" name="name" required placeholder="Enter Your Name">
                    </div>
                    <label for=>Email Address</label>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" name="email" required placeholder="Enter Your Email">
                    </div>
                    <label for=>Password</label>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password" required placeholder="Enter Your Password">
                    </div>
                    <label for=>user_type</label>
                    <div class="form-group mb-3">
                        <select name="user_type">
                            <option value="user">user</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="Save_Changes" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php
            if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
                
                ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Hey!</strong> <?php echo $_SESSION['status']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php
                unset($_SESSION['status']);
            }
            ?>
            <div class="card">
                <div class="card-header">
                    <h4>ADD USER</h4>
                    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
                        data-bs-target="#insertdata">
                        ADD USER
                    </button>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer'); ?>