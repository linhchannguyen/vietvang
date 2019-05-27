
        </div>
        
        <!-- jQuery -->
        <script src="{{url('assets/admin/js/jquery.min.js')}}"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="{{url('assets/admin/js/bootstrap.min.js')}}"></script>

        <!-- Metis Menu Plugin JavaScript -->

        <script src="{{url('assets/admin/js/metisMenu.min.js')}}"></script>
        <!-- DataTables JavaScript -->
        <script src="{{url('assets/admin/js/dataTables/jquery.dataTables.min.js')}}"></script>
        <script src="{{url('assets/admin/js/dataTables/dataTables.bootstrap.min.js')}}"></script>
        <script src="{{asset('')}}assets/js/sweetalert.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="{{url('assets/js/pages/my_ajax.js')}}"></script>
        <script src="{{url('assets/js/pages/crud.js')}}"></script>
        <script>
            $(document).ready(function() {
                $('#dataTables-example').DataTable({
                        responsive: true
                });
            });
        </script>
        @yield('script')
    </body>
</html>
