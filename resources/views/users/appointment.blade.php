@extends('layouts.app')
@section('script')
<script type="text/javascript">
   $(function () {
   
       /* initialize the external events
        -----------------------------------------------------------------*/
       function init_events(ele) {
         ele.each(function () {
           // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
           // it doesn't need to have a start or end
           var eventObject = {
             title: $.trim($(this).text()) // use the element's text as the event title
           }
   
           // store the Event Object in the DOM element so we can get to it later
           $(this).data('eventObject', eventObject)
   
           // make the event draggable using jQuery UI
           $(this).draggable({
             zIndex        : 1070,
             revert        : true, // will cause the event to go back to its
             revertDuration: 0  //  original position after the drag
           })
   		
         })
       }
   
       init_events($('#external-events div.external-event'))
   
       /* initialize the calendar
        -----------------------------------------------------------------*/
       //Date for the calendar events (dummy data)
       let events = JSON.parse('{!! $appointments !!}');

       var date = new Date()
       var d    = date.getDate(),
           m    = date.getMonth(),
           y    = date.getFullYear()
       $('#calendar').fullCalendar({
         header    : {
           left  : 'prev,next today',
           center: 'title',
           right : 'month,agendaWeek,agendaDay'
         },
         buttonText: {
           today: 'today',
           month: 'month',
           week : 'week',
           day  : 'day'
         },
         //Random default events
         selectable: true,
         events    : events,
         eventClick: function(event) {
           $('#myModal').modal('show');
           $('#title').text(event.title);
           $('#status').val(event.title);

           var start_date = new Date(event.start);
           var start =  $.datepicker.formatDate('mm/dd/yy', start_date);

           $('#start-date').val(start);
           $('#end-date').val(start);
           $('#start-time').val(event.start_time);
           $('#end-time').val(event.end_time);
           $('#calendar-id').val(event.calender_id);
           $('#provider-list').val(event.provider);
           $('#client-list').val(event.client);
         },
         editable  : true,
         droppable : true, // this allows things to be dropped onto the calendar !!!
         drop      : function (date, allDay) { // this function is called when something is dropped
   
           // retrieve the dropped element's stored Event Object
           var originalEventObject = $(this).data('eventObject')
   
           // we need to copy it, so that multiple events don't have a reference to the same object
           var copiedEventObject = $.extend({}, originalEventObject)
   
           // assign it the date that was reported
           copiedEventObject.start           = date
           copiedEventObject.allDay          = allDay
           copiedEventObject.backgroundColor = $(this).css('background-color')
           copiedEventObject.borderColor     = $(this).css('border-color')
   
           // render the event on the calendar
           // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
           $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)
   
           // is the "remove after drop" checkbox checked?
           if ($('#drop-remove').is(':checked')) {
             // if so, remove the element from the "Draggable Events" list
             $(this).remove()
           }
   
         }
       })
   
       /* ADDING EVENTS */
       var currColor = '#3c8dbc' //Red by default
       //Color chooser button
       var colorChooser = $('#color-chooser-btn')
       $('#color-chooser > li > a').click(function (e) {
         e.preventDefault()
         //Save color
         currColor = $(this).css('color')
         //Add color effect to button
         $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
       })
       $('#add-new-event').click(function (e) {
         e.preventDefault()
         //Get value and make sure it is not null
         var val = $('#new-event').val()
         if (val.length == 0) {
           return
         }
   
         //Create events
         var event = $('<div />')
         event.css({
           'background-color': currColor,
           'border-color'    : currColor,
           'color'           : '#fff'
         }).addClass('external-event')
         event.html(val)
         $('#external-events').prepend(event)
   
         //Add draggable funtionality
         init_events(event)
   
         //Remove event from text input
         $('#new-event').val('')
       })
     })
	jQuery(document).ready(function() {
        $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
         });

        $('.timepicker').timepicker({
        	format:"HH:ii",
        });

    });
</script>
@endsection
@section('content')
<section class="content">
   <div class="row">
      <div class="col-md-3">
         <div class="box box-solid">
            <div class="box-header with-border">
               <h4 class="box-title">Appointment Status</h4>
            </div>
            <div class="box-body">
               <!-- the events -->
               <div id="external-events">
                  <?php $cases = Cache::get('status');?>
                  @foreach($cases as $key=>$case)
                  <div class="external-event bg-{{$case}} ">{{ $key }}</div>
                  @endforeach
               </div>
            </div>
            <!-- /.box-body -->
         </div>
         <!-- /. box -->
      </div>
      <!-- /.col -->
      <div class="col-md-9">
         <div class="box box-primary">
            <div class="box-body no-padding">
               <!-- THE CALENDAR -->
               <div id="calendar" class="fc fc-unthemed fc-ltr"></div>
            </div>
            <!-- /.box-body -->
         </div>
         <!-- /. box -->
      </div>
      <!-- /.col -->
   </div>
   <!-- /.row -->
</section>
<!-- Trigger the modal with a button -->
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">update appointment</h4>
         </div>
         <div class="modal-body">
            {{ Form::open(['url' => 'admin/admins/update-calender', 'method' => 'POST','enctype' => 'multipart/form-data']) }}
               <div class="form-group">
                  <label for="status">status:</label>
                  <select name="status" class="form-control" id="status">
                  	<option value="PENDING">PENDING</option>
                  	<option value="REJECTED">REJECTED</option>
                  	<option value="APPROVED">APPROVED</option>
                    <option value="CANCEL">CANCEL</option>
                  </select>
               </div>
               <div class="row">
                <div class="col-xs-3">
               		<label for="start-date">start date:</label>
                  <input type="text" class="datepicker form-control" id="start-date" name="start_date">
                </div>
                <div class="col-xs-3">
                	<label for="start-time">start time:</label>
                  <input type="text" class="timepicker form-control" id="start-time" name="start_time" >
                </div>
                <div class="col-xs-3">
                 <label for="end-date">end date:</label>
                  <input type="text" class="datepicker form-control" id="end-date"
                  name="end_date">
                </div>
                <div class="col-xs-3">
                 <label for="end-time">end time:</label>
                  <input type="text" class="timepicker form-control" id="end-time" name="end_time">
                </div>
                <div class="col-xs-3">
                 <label for="end-time">call type:</label>
                  <select class="form-control" name="call_type">
                    <option value="1">voice call</option>
                    <option value="2">video call</option>
                  </select>
                </div>
              </div>
              <br/>
              <div class="form-group">
                  <label for="client-list">Clients:</label>
                  <select name="clients" class="form-control" id="client-list">
                  	@foreach(UserHelper::getClientNameMapById() as $client)
              			<option value="{{ $client }}">{{ UserHelper::getClientName($client) }}</option>
                  	@endforeach
                  </select>
               </div>
               <div class="form-group">
                  <label for="provider-list">Service Providers:</label>
                  <select name="provider" class="form-control" id="provider-list">
                  	@foreach(UserHelper::getServiceProviderNameById() as $provider)
              			<option value="{{ $provider }}">{{ UserHelper::getServiceProviderName($provider) }}</option>
                  	@endforeach
                  </select>
               </div>
              <input type="hidden" id="calendar-id" name="calendar_id">
         </div>
         <div class="modal-footer">
         	{{ Form::submit('Save',['class' => 'btn btn-primary']) }}
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         </div>
      </div>
      {{ Form::close() }}
   </div>
</div>
@endsection