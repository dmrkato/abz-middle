<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ABZ middle test</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css')}}">
    <!-- jsGrid -->
    <link rel="stylesheet" href="{{ asset('plugins/jsgrid/jsgrid.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/jsgrid/jsgrid-theme.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css')}}">
</head>
<body class="dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="margin-left: 0;margin-top:0;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Users</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users</h3>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <div id="jsGrid-user"></div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create-user-modal">
                        Create User
                    </button>
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
</div>
<!-- ./wrapper -->

<!-- Modal -->
<div class="modal fade" id="create-user-modal" tabindex="-1" role="dialog" aria-labelledby="create-user-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-user-modal-label">Create User Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create-user-form">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="create-user-modal-name">Name</label>
                            <input type="text" class="form-control" id="create-user-modal-name" name="name" placeholder="Name">
                            <span id="create-user-modal-name-error" class="error invalid-feedback" style="display: none"></span>
                        </div>
                        <div class="form-group">
                            <label for="create-user-modal-email">Email address</label>
                            <input type="email" name="email" class="form-control" id="create-user-modal-email" placeholder="Email address">
                            <span id="create-user-modal-email-error" class="error invalid-feedback" style="display: none"></span>
                        </div>
                        <div class="form-group">
                            <label for="create-user-modal-phone">Phone</label>
                            <input name="phone" type="text" class="form-control" id="create-user-modal-phone" placeholder="+380XXXXXXXXX">
                            <span id="create-user-modal-phone-error" class="error invalid-feedback" style="display: none"></span>
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <select class="form-control" name="position_id" id="create-user-modal-position_id">
                                <option>--Please select position--</option>
                            </select>
                            <span id="create-user-modal-position_id-error" class="error invalid-feedback" style="display: none"></span>
                        </div>
                        <div class="form-group">
                            <label for="create-user-modal-photo">Photo</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="create-user-modal-photo" name="photo">
                                    <label class="custom-file-label" for="create-user-modal-photo">Choose photo</label>
                                </div>
                            </div>
                            <span id="create-user-modal-photo-error" class="error invalid-feedback" style="display: none"></span>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<!-- jsGrid -->
<script src="{{ asset('plugins/jsgrid/demos/db.js') }}"></script>
<script src="{{ asset('plugins/jsgrid/jsgrid.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('plugins/toastr/toastr.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<!-- Page specific script -->
<script>
    $(function () {
        bsCustomFileInput.init();


        const gridData = {
            positions: [],
            users: []
        }

        let grid = $("#jsGrid-user");
        let createUserModal = $('#create-user-modal');
        let createUserForm = $('#create-user-form');
        let positionSelect = $('#create-user-modal-position_id');
        let toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        //remove "is-invalid" class when update input data
        createUserForm.find('input, select').each(function(index, element) {
            $(element).on('change', function(event) {
                let element = $(this);
                element.removeClass('is-invalid');
                let errorSpan = $('#' + element.attr('id') + '-error');
                errorSpan.hide();
            })
        })

        grid.jsGrid({
            height: "auto",
            width: "100%",
            sorting: false,
            paging: true,
            pageLoading: true,
            pageSize: 6,
            fields: [
                { name: "id", title:"Id", type: "text", width: 20 },
                { name: "name", title: "Name", type: "text", width: 100 },
                { name: "email", title: "Email", type: "text", width: 150},
                { name: "phone", title: "Phone", type: "text", width: 100 },
                { name: "position_id", title: "Position", type: "select", items:gridData.positions, valueField: "id", textField: "name", width: 50 },
                {
                    name: "photo",
                    type: "text",
                    itemTemplate: function(value, item) {
                        return '<img src="' + value +'" "width="70" height="70">';
                    }
                },
            ],
            controller: {
                loadData: async function(filter) {
                    let response = await $.ajax('/api/v1/users', {
                        method: 'GET',
                        data: {
                            page: filter.pageIndex,
                            count: filter.pageSize
                        },
                    });
                    if (response.success === true) {
                        return {
                            data: response.users,
                            itemsCount: response.total_users
                        }
                    }
                }
            }
        });
        function updatePosition() {
            $.ajax('api/v1/positions', {
                method: 'GET',
                success: function (data, textStatus) {
                    if (data.success === true) {
                        gridData.positions = data.positions;
                        grid.jsGrid('fieldOption', 'position_id', 'items', gridData.positions);

                        //clear edition form select options
                        positionSelect.find('option').remove();

                        //refill edition form select options
                        positionSelect.append($('<option>', {text: '--Please select position--'}));
                        for (const position of gridData.positions) {
                            positionSelect.append($('<option>', {value: position.id, text:position.name}));
                        }
                    }
                }
            });
        }
        updatePosition();
        grid.jsGrid('loadData');

        createUserForm.on('submit', function(e){
            e.preventDefault();
            let createUserData = new FormData(this);

            $.ajax('/api/v1/token', {
                method: 'GET',
                success: function (data, textStatus) {
                    if (data.success === true) {
                        let token = data.token;
                        $.ajax('api/v1/users', {
                            method: 'POST',
                            contentType: false,
                            processData: false,
                            headers: {
                                Token: token
                            },
                            data: createUserData,
                            success: function(data) {
                                if (data.success === true) {
                                    toast.fire({
                                        icon: 'success',
                                        title: 'User was created'
                                    })
                                    createUserModal.modal('hide');
                                    grid.jsGrid('loadData');
                                }
                            },
                            error: function (jqXHR) {
                                let data = jqXHR.responseJSON;
                                let statusCode = jqXHR.status;
                                if (data.success === false) {
                                    if (statusCode === 422 && data.fails) {
                                        for (const [key, value] of Object.entries(data.fails)) {
                                            $('#create-user-modal-' + key).addClass('is-invalid');
                                            $('#create-user-modal-' + key + '-error')
                                                .text(value)
                                                .show();
                                        }
                                    }
                                    if (statusCode === 409 && data.message) {
                                        let fields = $('#create-user-modal-email,#create-user-modal-phone');
                                        fields.addClass('is-invalid');
                                        fields.each(function(index,item) {
                                            let element = $(item);
                                            let errorSpan = $('#' + element.attr('id') + '-error');
                                            errorSpan.text(data.message).show();
                                        })
                                    }
                                }
                            }
                        })
                    }
                }
            })
        });
    });
</script>
</body>
</html>
