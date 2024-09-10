<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 

    <title>PHP-Simple To Do List App</title>

    <style type="text/css">
      #taskLists_processing{
        display: none;
      }
    </style>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    
    var SITEURL = '{{URL::to('')}}';
    var base_url = '{{URL::to('')}}';
    $(function(){
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    });
    </script>
  </head>
  <body>
    <!-- Demo header-->
    <section class="header text-center">
        <div class="container">
            <header class="">
                <h1 class="display-4">PHP-Simple To Do List App</h1>
            </header>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        
                        {!! Form::text('task', null, ['class' => 'form-control', 'id' => 'task', 'placeHolder'=>'Enter Task']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <button color="primary" type="button" value="Search" id="addTask" class="btn btn-info pull-left">Add Task</button>
                    
                        <button color="primary" type="button" value="" id="showAll" class="btn btn-info ">Show All</button>  
                     </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12 mx-auto">
                    
                         <table id="taskLists" class="table table-bordered table-hover ">
                                  <thead>
                                      <tr>
                                          <th>#</th>
                                          <th>Task</th>
                                          <th>Status</th>
                                          <th>Created AT</th>
                                          <th>Action</th>
                                      </tr>
                                  </thead>
                                  <tbody> 

                                  </tbody>

                              </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

   
   
    <!--script src="https://cdn.datatables.net/2.1.5/js/dataTables.js" type="text/javascript"></script-->

    <script src="{{ asset ('assets/datatables.net/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset ('assets/datatables.net-bs/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    

    <script src="{{ asset ('assets/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>



    <script type="text/javascript">
      var oTable;
      var token = $('meta[name="csrf-token"]').attr('content');
      $(document).ready(function () {
          oTable = $('#taskLists').dataTable({
              "processing": true,
              "serverSide": true,
              "responsive": true,
              "fnDrawCallback": function (oSettings) {
                  //$('#taskLists').tooltip();
              },
              "ajax": {
                  "url": SITEURL + "/getLists",
                  "type": "POST",
                  "dataType": "json",
                  "data": function (d) {
                      d.myKey = "myValue";
                      d.task = $('#task').val();
                      d.showall = $('#showAll').val();
                      d._token = token;
                  }
              },
              "bFilter": false,
              "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 4]}],
              "order": [[3, "desc"]],
              "aoColumns": [
                  {"data": "sr_no", "sClass": "text-center"},
                  {"data": "task"},
                  {"data": "taskStatus"},
                  {"data": "created_at"},
                  {"data": "action"}
              ]
          });

          $('#showAll').click(function () {
              $('#showAll').val('showall');
              oTable.fnDraw();
          });

          $('#addTask').click(function () {
              oTable.fnDraw();
          });

          $('#task').keydown(function (e) {
              if (e.keyCode == 13) {
                  //oTable.fnDraw();
              }
          });


          jQuery("#taskLists").on("click", ".changeStatus", function () {
              var sID = jQuery(this).attr('rel');
              //alert(sID);
              swal({
                  title: "{{ 'Do you really want to change the status?' }}",
                  text: "{{ 'The status of the task will be completed and will be hide from here.' }}",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "{{ 'Yes, change it!' }}",
                  cancelButtonText: "{{ 'No, cancel plz!' }}",
                  closeOnConfirm: false,
                  closeOnCancel: false
              },
              function (isConfirm) {
                  if (isConfirm) {
                      $.ajax({
                          url: SITEURL + "/change",
                          type: 'POST',
                          dataType: 'json',
                          data: {sID: sID},
                          success: function (data) {
                              swal({title: "{{ 'Completed' }}", text: "{{ 'Record has been updated.' }}", type: "success", confirmButtonText: "{{ 'Ok' }}"});
                              bResetDisplay = false;
                              /* override default behaviour */
                              oTable.fnDraw();
                              bResetDisplay = true;
                              /*restore default behaviour */
                          },
                          error: function () {
                              swal({title: "{{ 'Warning'}}", text: "{{ 'Do not have permission to change the status of the task.' }}", type: "error", confirmButtonText: "{{ 'Ok' }}"});
                          }
                      });
                  } else {
                      swal({title: "{{ 'Cancelled' }}", text: "{{ 'Record is safe now.' }}", type: "error", confirmButtonText: "{{ 'Ok' }}"});
                  }
              });
          });

          jQuery("#taskLists").on("click", ".deleteRecord", function () {
              var sID = jQuery(this).attr('rel');
              //alert(sID);
              swal({
                  title: "{{ 'Are you sure?' }}",
                  text: "{{ 'You will not be able to recover this record in future!' }}",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "{{ 'Yes, delete it!' }}",
                  cancelButtonText: "{{ 'No, cancel plz!' }}",
                  closeOnConfirm: false,
                  closeOnCancel: false
              },
              function (isConfirm) {
                  if (isConfirm) {
                      $.ajax({
                          url: SITEURL + "/remove",
                          type: 'POST',
                          dataType: 'json',
                          data: {sID: sID},
                          success: function (data) {
                              swal({title: "{{ 'Deleted' }}", text: "{{ 'Record has been deleted.' }}", type: "success", confirmButtonText: "{{ 'Ok' }}"});
                              bResetDisplay = false;
                              /* override default behaviour */
                              oTable.fnDraw();
                              bResetDisplay = true;
                              /*restore default behaviour */
                          },
                          error: function () {
                              swal({title: "{{ 'Warning'}}", text: "{{ 'Do not have permission to delete the record.' }}", type: "error", confirmButtonText: "{{ 'Ok' }}"});
                          }
                      });
                  } else {
                      swal({title: "{{ 'Cancelled' }}", text: "{{ 'Record is safe now.' }}", type: "error", confirmButtonText: "{{ 'Ok' }}"});
                  }
              });
          });
      });
  </script>

  </body>
</html>