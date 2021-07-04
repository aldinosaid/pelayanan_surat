    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gallery
                <a href="<?php echo base_url('/pengaturan/tambah_banner/'); ?>" class="btn btn-primary">
                    Tambah gallery Baru
                </a>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Gallery</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Data Gallery</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="banner" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Album</th>
                                        <th>Album</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1;
                                        foreach ($galleries as $gallery) {
                                    ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $gallery->link_gambar; ?></td>
                                            <td>
                                                <a class="btn btn-danger" href="javascript:void(0)" onClick="deleteField(<?php echo $gallery->id; ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- Default to the left -->
        <strong>Copyright &copy; 2019 <a href="#"><?php echo ambil_pengaturan('nama_website'); ?></a>.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->
<?php $this->load->view('dashboard/_parts/js'); ?>
<!-- REQUIRED JS SCRIPTS -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#banner').DataTable({});
    });

    function ajax_form() {
        $('#form_kk').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            $('.form-loading').show();
            data = $(this).serialize();
            $.ajax({
                url : $(this).attr('action'),
                dataType : 'JSON',
                type : "POST",
                data : data
            }).done(function(r) {
                reinitDataTable();
                $('.form-loading').hide();
                $.notify(r.msg, r.cls);
            }).fail(function() {
                $('.form-loading').hide();
            });
        });
    }

    function reinitDataTable() {
        $.ajax({
            url : baseUrl+'pengaturan/banner_ajax_table'
        }).done(function(r) {
            $('#banner').DataTable().destroy();
            $('#banner tbody').html(r);
            $('#banner').DataTable({});
        }).fail(function() {

        });
    }

    function deleteField(id) {
        var resultConfirm = confirm('Apakah Anda yakin hapus data banner ini?');
        if (resultConfirm) {
            $('#loader').show();
            $.ajax({
                url : baseUrl+'pengaturan/hapus_banner/'+id,
                dataType : 'JSON'
            }).done(function(r) {
                reinitDataTable();
                $('#loader').hide();
                $.notify(r.msg, r.cls);
            }).fail(function() {
                $('#loader').hide();
            });
        }
    }

    function init() {
        ajax_form();
    }

    init();
</script>