<!-- ============================================================== -->
<!-- Header -->
<!-- ============================================================== -->
<?php $this->load->view('includes/header'); ?>
    <link href="<?=base_url()?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/libs/fullcalendar/dist/fullcalendar.min.css">
<!-- ============================================================== -->
<!-- End Header  -->
<!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
           
                <!-- ============================================================== -->
                <!-- Sales Summery -->
                <!-- ============================================================== -->
                <div class="row">
					<div class="col-lg-12">
					   <div class="dashboard-header">
					       <div class="dashboard-header-overlay"></div>
					       <div class="dashboard-header-data">
					           <div class="row">
					               <div class="col-lg-2">
					                   <img src="<?=base_url()?>assets/images/icon.png" alt="homepage" class="dark-logo" />
					               </div>
					               <div class="col-lg-10">
					                    <h1> <?=(!empty(SITENAME))?SITENAME:""?></h1>
					               </div>
					           </div>
					       </div>
					   </div>
					</div>
				</div>
                <div class="container-fluid">
                <?php //echo $this->permission->getDashboardShortcut(); ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
        					<div class="col-lg-12">
        					    <div class="card earning-widget">
                                    <div class="card-body">
                                        <h4 class="m-b-0">Accounts</h4>
                                    </div>
                                    <div class="border-top p-20">
                                        <ul class="dashboard-btn">
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Purchase Order</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Goods Receipt</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Purchase Invoice</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Delivery Challan</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Sales Invoice</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Bank Reconciliation</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Receivables</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Payables</span></a></li>
                					   </ul>
                                    </div>
                                </div>
        					</div>
        				</div>
        				
        				<div class="row">
        					<div class="col-lg-12">
        					    <div class="card earning-widget">
                                    <div class="card-body">
                                        <h4 class="m-b-0">Production</h4>
                                    </div>
                                    <div class="border-top p-20">
                                        <ul class="dashboard-btn">
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Material planning</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Employee Report</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Purchase inward</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Finished goods inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">WIP Inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">RM Inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Customer order monitorring</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">GSTR 1</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">GSTR 2A</span></a></li>
                					   </ul>
                                    </div>
                                </div>
        					</div>
        				</div>
        				
        				<div class="row">
        					<div class="col-lg-12">
        					    <div class="card earning-widget">
                                    <div class="card-body">
                                        <h4 class="m-b-0">Reports</h4>
                                    </div>
                                    <div class="border-top p-20">
                                        <ul class="dashboard-btn">
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Material planning</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Employee Report</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Purchase inward</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Finished goods inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">WIP Inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">RM Inventory</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">Customer order monitorring</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">GSTR 1</span></a></li>
                					       <li><a href="#" class=""><i class="icon-Gear"></i><span class="hide-menu">GSTR 2A</span></a></li>
                					   </ul>
                                    </div>
                                </div>
        					</div>
        				</div>
        			</div>
        			
        			<div class="col-md-4">
        			    <div class="row">
        					<div class="col-lg-12">
        					    <div class="card earning-widget">
                                    <div class="card-body">
                                        <h4 class="m-b-0">TO DO LIST</h4>
                                    </div>
                                    <div class="border-top p-20">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
    					    </div>
        			    </div>
        			</div>
    			</div>
            </div>
            <!-- ============================================================== -->
            <!-- Trade history / Exchange -->
            <!-- ============================================================== -->
        </div>
        

        <!-- ============================================================== -->
            <?php $this->load->view('includes/footer'); ?>

            <!--<script src="<?=base_url()?>assets/libs/chartist/dist/chartist.min.js"></script>
            <script src="<?=base_url()?>assets/libs/chartist/dist/chartist-plugin-legend.js"></script>
            <script src="<?=base_url()?>assets/js/pages/chartist/chartist-plugin-tooltip.js"></script>
            <script src="<?=base_url()?>assets/js/pages/chartist/chartist-init.js"></script>-->
            <script src="<?=base_url()?>assets/js/pages/c3-chart/bar-pie/c3-stacked-column.js"></script>
            <script src="<?=base_url()?>assets/js/pages/dashboards/dashboard3.js"></script>
            
            <script src="<?=base_url()?>assets/libs/fullcalendar/dist/fullcalendar.min.js"></script> 
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
        
        
<script>
<?php if(empty($today_rate)) { ?>
setTimeout(function(){
	$(".rateTrg").trigger("click");
}, 2000);

<?php } ?>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 650,
    events: base_url + controller + '/getTodoList',
    
    selectable: true,
    select: async function (start, end, allDay) {
      const { value: formValues } = await Swal.fire({
        title: 'Add Event',
        html:
          '<input id="title" class="swal2-input" placeholder="Enter title">' +
          '<textarea id="description" class="swal2-input" placeholder="Enter description"></textarea>',
        focusConfirm: false,
        preConfirm: () => {
          return [
            document.getElementById('title').value,
            document.getElementById('description').value
          ]
        }
      });

      if (formValues) {
        // Add event
        fetch(base_url + controller + '/saveTodo', {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({id:'',start:start.startStr, end:start.endStr, title:formValues[0] ,description:formValues[1]}),
        })
        .then(response => response.json())
        .then(data => {
          if (data.status == 1) {
            Swal.fire('Event added successfully!', '', 'success');
            //Swal.fire('Event Add feature is disabled for this demo!', '', 'warning');
          } else {
            Swal.fire(data.message, '', 'error');
          }

          // Refetch events from all sources and rerender
          calendar.refetchEvents();
        })
        .catch(console.error);
      }
    },

    eventClick: function(info) {
      info.jsEvent.preventDefault();
      
      // change the border color
      info.el.style.borderColor = 'red';
      
      Swal.fire({
        title: info.event.title,
        //icon: 'info',
        imageUrl: base_url + 'assets/images/favicon.svg',
        html:'<p>'+info.event.extendedProps.description+'</p>',
        showCloseButton: true,
        showCancelButton: true,
        showDenyButton: true,
        cancelButtonText: 'Close',
        confirmButtonText: 'Delete',
        denyButtonText: 'Edit',
      }).then((result) => {
        if (result.isConfirmed) {
          // Delete event
          fetch(base_url + controller + '/deleteTodo', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: info.event.id}),
          })
          .then(response => response.json())
          .then(data => {
            if (data.status == 1) {
              Swal.fire('Event deleted successfully!', '', 'success');
              //Swal.fire('Event Delete feature is disabled for this demo!', '', 'warning');
            } else {
              Swal.fire(data.message, '', 'error');
            }

            // Refetch events from all sources and rerender
            calendar.refetchEvents();
          })
          .catch(console.error);
        } else if (result.isDenied) {
          // Edit and update event
          Swal.fire({
            title: 'Edit Event',
            html:
            '<input id="swalEvtTitle_edit" class="swal2-input" placeholder="Enter title" value="'+info.event.title+'">'+
            '<textarea id="swalEvtDesc_edit" class="swal2-input" placeholder="Enter description">'+info.event.extendedProps.description+'</textarea>',
            focusConfirm: false,
            confirmButtonText: 'Submit',
            preConfirm: () => {
              return [
                document.getElementById('swalEvtTitle_edit').value,
                document.getElementById('swalEvtDesc_edit').value
              ];
            }
          }).then((result) => {
            if (result.value) {
              // Event update request
              fetch(base_url + controller + '/saveTodo', {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: info.event.id,title:result.value[0] ,description:result.value[1]}),
              })
              .then(response => response.json())
              .then(data => {
                if (data.status == 1) {
                  Swal.fire('Event updated successfully!', '', 'success');
                  //Swal.fire('Event Update feature is disabled for this demo!', '', 'warning');
                } else {
                  Swal.fire(data.error, '', 'error');
                }

                // Refetch events from all sources and rerender
                calendar.refetchEvents();
              })
              .catch(console.error);
            }
          });
        } else {
          Swal.close();
        }
      });
    }
  });

  calendar.render();
});
</script>