        <div class="card card-teal">
            <div class="card-header">
                <p class="card-title border-bottom-0">Dokter Poli Kandungan</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <label>Tahun</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="tahun-addon"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" id="yearpicker-poli-obgyn" class="form-control datetimepicker-input" data-toggle="datetimepicker" aria-describedby="tahun-addon" data-target="#yearpicker-poli-obgyn" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped text-sm"  id="table-poli-obgyn" style="width: 100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>dr. Himawan Budityastomo, SpOG</th>
                                        <th>dr. Siti Pattihatun Nasyiroh, SpOG</th>
                                        <th>Jumlah Total</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@push('scripts')
    <script>
    $(document).ready(function(){

            
    $('#yearpicker-poli-obgyn').datetimepicker({
        format: "YYYY",
        useCurrent: false,
        viewMode: "years"
    });

    load_data();
    function load_data(tahun) {
    $('#table-poli-obgyn').DataTable({
        ajax: {
            url:'ralan/obgyn/json',
            dataType:'json',
            data: {
                    tahun:tahun,
                },
            },
        processing: true,
        serverSide: true,
        destroy: false,
        deferRender:true,
        lengthChange: false,
        ordering:false,
        searching : false,
        stateSave: true,
        paging:false,
        dom: 'Blfrtip',
        info: false,
        initComplete: function(settings, json) {
                            toastr.success('Data pasien telah dimuat', 'Berhasil');
                        },
        language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                zeroRecords: "Tidak Ditemukan Data",
                infoEmpty:      "",
                loadingRecords: "Sedang memuat ...",
                infoFiltered:   "(Disaring dari _MAX_ total baris)",
                buttons: {
                            copyTitle: 'Data telah disalin',
                            copySuccess: {
                                            _: '%d baris data telah disalin',
                                        },
                        },
            },
        buttons: [
            {extend: 'copy', text:'<i class="fas fa-copy"></i> Salin',className:'btn btn-info', title: 'laporan-jumlah-pasien-bayi-{{date("dmy")}}'},
            {extend: 'csv',  text:'<i class="fas fa-file-csv"></i> CSV',className:'btn btn-info', title: 'laporan-jumlah-pasien-bayi-{{date("dmy")}}'},
            {extend: 'excel', text:'<i class="fas fa-file-excel"></i> Excel',className:'btn btn-info', title: 'laporan-jumlah-pasien-bayi-{{date("dmy")}}'},
        ],
        columns:[
            {data:'bulan', name:'bulan'},
            {data:'obgyn1', name:'obgyn1'},
            {data:'obgyn2', name:'obgyn2'},
            {data:'total', name:'total'},
            ],
        });
    }

    $('#yearpicker-poli-obgyn').on('change.datetimepicker', function(){
            var tahun = $(this).val();
            $('#table-poli-obgyn').DataTable().destroy();
            load_data(tahun);  
    });

    });    
    </script>
@endpush