<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>
<script>
$(document).ready(function(){
    function callAjax(route, data, res)
	{
		$.ajax({
			url : route,
			type : "POST",
			data : {
				"_token" : "{{ csrf_token() }}",
				"data" : data
			},
			beforeSend : function()
			{
				$(res).html('<div class="text-center"><h4>Please wait...</h4></div>');
			},
			success : function(data)
			{
				$(res).html(data);
			}
		});
	}

    var media_type = $(".chooseMedia").val();

	callAjax("{{ route('ajax.ietls-courses.preview.media-intro-type') }}", {
        'media_type' : media_type,
    }, '#media_res');

    $(".chooseMedia").on('change', function(e){
		e.preventDefault();

		var media_type = $(this).val();

		callAjax("{{ route('ajax.ietls-courses.preview.media-intro-type') }}", {
            'media_type' : media_type,
        }, '#media_res');
	});
    
    var price_option = $(".choosePriceOption:checked").val();

    callAjax("{{ route('ajax.ietls-courses.preview.price-option') }}", {
        'price_option' : price_option,
        'ietls_course_id' : "{{ $course->id }}"
    }, '#price_option_res');

    $(".choosePriceOption").on('change', function(e){
		e.preventDefault();

		var price_option = $(this).val();

		callAjax("{{ route('ajax.ietls-courses.preview.price-option') }}", {
            'price_option' : price_option,
            'ietls_course_id' : "{{ $course->id }}"
        }, '#price_option_res');
	});

    $(".select2").select2();

    CKEDITOR.replace( 'description' );

    $("#updateCourse").on('submit', function(e){
        e.preventDefault();

        for ( instance in CKEDITOR.instances )
        {
            CKEDITOR.instances[instance].updateElement();
        }

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) 
                {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        //Do something with upload progress here
                        $("#loading").modal({backdrop: 'static', keyboard: false});
                        
                        $("#progressbar").attr('aria-valuenow', percentComplete).css('width', percentComplete + '%').text(percentComplete + '%');
                    }
            }, false);
            return xhr;
            },
            url : "{{ route('ajax.ietls-course.update') }}",
            type : "POST",
            data : new FormData(this),
            contentType : false,
            processData : false,
            cache : false,
            success : function(data)
            {
                $("#loading").modal('hide');
                $("#resModal").modal({backdrop: 'static', keyboard: false});
                $("#res").html(data);
                $("#onCloseModal").click(function(){
                    $("#resModal").modal('hide');
                });
            },
            error: function () {
                $("#loading").modal('hide');
                $("#errorModal").modal('show');
            }
        });
    });

    $('#deleteCourse').on('click', function(e){
        e.preventDefault();
        
        var course_id = $(this).data("course-id");

        $("#deleteCourseModal").modal('show');

        $("#confirmdeleteCourseID").val(course_id);
    });

    $("#confirmdeleteCourse").on('click', function(e){
        e.preventDefault();

        var ietls_course_id = $("#confirmdeleteCourseID").val();

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) 
                {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        //Do something with upload progress here
                        $("#loading").modal({backdrop: 'static', keyboard: false});
                        $("#deleteCourseModal").hide();
                        
                        $("#progressbar").attr('aria-valuenow', percentComplete).css('width', percentComplete + '%').text(percentComplete + '%');
                    }
            }, false);
            return xhr;
            },
            url : "{{ route('ajax.ietls-course.delete') }}",
            type : "POST",
            data : {
				"_token" : "{{ csrf_token() }}",
				"ietls_course_id" : ietls_course_id,
			},
            success : function(data)
            {
                $("#loading").modal('hide');
                $("#resModal").modal({backdrop: 'static', keyboard: false});
                $("#res").html(data);
                $("#onCloseModal").click(function(){
                    $("#resModal").modal('hide');
                    $("#deleteCourseModal").show();
                });
            }
        });
    });
});
</script>