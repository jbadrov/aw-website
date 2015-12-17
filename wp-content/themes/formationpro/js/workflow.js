var get_workflow_interval ;

jQuery(document).ready(function(e) {
	jQuery("#autonomyworks-home").on("click","#get-workflow,#no-tasks",function(){
		jQuery(this).attr("disabled","disabled");
		jQuery.post(workflow.ajax_url,
					{action:'get_workflow'},
					function(data){
						tasks_button(data);
					}
					);
	});
	
	jQuery("#autonomyworks-home").on("click","#start-workflow",function(){
		jQuery(this).attr("disabled","disabled");
		var jobId = jQuery(this).attr("job");
		jQuery.post(workflow.ajax_url,
					{action:'get_job', jobId:jobId},
					function(data){
						console.log(data);
						show_job(data);
					},"json");
	});
	
	jQuery("#autonomyworks-home").on("click","#stop-workflow",function(){
		jQuery(this).attr("disabled","disabled");
		var jobId = jQuery(this).attr("job");
		var output = '<div class="row" style="display: block;margin: 0 auto;width: 760px;">';
		output += '<div class="row">';
		output += '<div class="col-md-3"><strong>Reason : </strong></div>';
		output += '<div class="col-md-9"><div class="col-md-3"><input type="radio" name="reason" value="Completed"> Completed</div>';
		output += '<div class="col-md-3"><input type="radio" name="reason" value=" In Progress"> In Progress</div>';
		output += '<div class="col-md-3"><input type="radio" name="reason" value="Issue"> Issue</div>';
		output += '<div class="col-md-3"><input type="radio" name="reason" value="QA Check"> QA Check</div></div>';
		output += '</div>';
		output += '<div class="row">';
		output += '<div class="col-md-3"><strong>More info : </strong></div>';
		output += '<div class="col-md-9"><input type="text" id="more-info" style="width:100%"></div>';
		output += '</div>';
		output += '<div class="row"><a id="end-stop-workflow"  class="link grey" job="'+jobId+'">OK</a></div>';
		output += '</div>';
		jQuery("#autonomyworks-home").html(output);
	});
	
	jQuery("#autonomyworks-home").on("click","#end-stop-workflow",function(){
		jQuery(this).attr("disabled","disabled");
		var jobId = jQuery(this).attr("job");
		var  reason = jQuery('input[name="reason"]:checked').val();
		if(reason==="undefined") return false;
		var  more_info = jQuery('#more-info').val();
		jQuery.post(workflow.ajax_url,
					{action:'end_job', jobId:jobId,reason:reason,more_info:more_info},
					function(data){
						tasks_button(data);
					});
	});
	
});

function show_job(data){
	if(data.activity_driver===null) data.activity_driver = 'N/A';
	var output = '<div>';
	output += '<div class="row"><div class="col-md-5" style="text-align:left">';
	output += '<div class="row"><div class="col-md-6"><strong>Current Job : </strong></div><div class="col-md-6">'+data.name+'</div></div>';
	output += '<div class="row"><div class="col-md-6"><strong>Started : </strong></div><div class="col-md-6">'+data.estimated_start+'</div></div><div class="row"><div class="col-md-6"><strong>Estimated Finish : </strong></div><div class="col-md-6">'+data.estimated_finish+'</div></div>';
	
	output += '</div>';
	output += '<div class="col-md-7" style="text-align:left">';
	output += '<div class="row"><div class="col-md-4"><strong>Parameter 1 : </strong></div><div id="copy1_text" class="col-md-4">'+data.parameter_1+'</div><div class="col-md-4"><button class="btn" data-clipboard-action="copy" data-clipboard-target="#copy1_text">Copy 1</button></div></div>';
	output += '<div class="row"><div class="col-md-4"><strong>Parameter 2 : </strong></div><div id="copy2_text" class="col-md-4">'+data.parameter_2+'</div><div class="col-md-4"><button class="btn" data-clipboard-action="copy" data-clipboard-target="#copy2_text">Copy 2</button></div></div>';
	output += '<div class="row"><div class="col-md-4"><strong>Activity Driver : </strong></div><div class="col-md-4">'+data.activity_driver+' - <input type="number" min="0" max="9999" style="width:50px" value="'+data.activity_count+'"/></div><div class="col-md-4"><input type="button" id="update_act" value="Update"/></div></div>';
	output += '</div></div>';
	output += '<div class="row"><a id="stop-workflow"  class="link red" job="'+data.job_id+'">STOP</a></div>';
	output += '</div>';
	jQuery("#autonomyworks-home").html(output);
	new Clipboard('.btn');
}

function tasks_button(data){
	console.log(data);
	clearInterval(get_workflow_interval);
	if(data==='0' || data==="null" || data===null) {
		jQuery("#autonomyworks-home").html('<a id="no-tasks"  class="link yellow">ON CALL</a>');
		get_workflow_interval = setTimeout(function(){jQuery("#no-tasks").click()},5000);
	}else{
		jQuery("#autonomyworks-home").html('<a id="start-workflow"  class="link green" job='+data+'>START</a>');
	}
}