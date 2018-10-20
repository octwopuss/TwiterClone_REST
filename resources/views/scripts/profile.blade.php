@extends('index')	

@section('style')		
<link rel="stylesheet" type="text/css" href="{{asset('css/profile.css')}}">
@endsection

@section('importantPart')	
	<div class="row">			
  		<nav class="col-sm-3 col-md-3 hidden-xs-down bg-faded sidebar profile">
  			<div class="card">
			  <div class="card-body">
			    <h4 class="card-title" style="text-align: center;">Card title</h4>
			    <img src="{{asset('img/pepe.png')}}" class="center rounded-circle">			    
			    <span class="center">@hendroyohanes</span>				    		    	       	
		    	<p class="center hitFollow follow-box badge badge-info"><span class="follow-text">FOLLOW</span></p>
		    	<meta name="csrf-token" content="{{ csrf_token() }}" />
			    <p class="card-text">Code for fun and for food!</p>
			    <span class="card-link">Follows : 102</span>
			    <span class="card-link">Follower : 211</span>
			  </div>
			</div>
  		</nav>
  		<div class="col-md-5">
  			<div class="loading">
		      <center> <img src="{{asset('/img/loading2.gif')}}" style="width: 100px; height: 100px;"></center>                
		    </div>
			<div class="moments"></div>
  		</div>
  	</div>	
@endsection

@section('mainjs')
<script type="text/javascript">			
	const momentsElement = document.querySelector('.moments');     
	const loadingElement = document.querySelector('.loading');  	
	const API_URL = "{{route('friend.showPost', $user->username)}}";
	const API_ADD_FRIEND = "{{route('friend.create')}}";
	const API_CANCEL_FRIENDREQUEST = "{{route('friend.cancel')}}";

	$('.hitFollow').click(function(){
		console.log('clicked');
		var followText = $('.follow-text').text();
		if(followText == 'FOLLOW'){
			$(this).removeClass('badge-info').addClass('badge-light');
			$('.follow-text').text('FOLLOWED');				
			let targetId = "{{$user->id}}";
			let me = "{{Auth::guard('users')->user()->id}}";
			let users = switchId(targetId, me);
			var action = {
				'user_id_one' : users[0],
				'user_id_two' : users[1],
				'status' : 0,
				'action_user_id' : users[0]
			}

			fetch(API_ADD_FRIEND, {
				method : 'POST',
				headers : {
					'Accept' : 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				credentials: "same-origin",
				body : JSON.stringify(action),				
			})
			.then(response => response.json())
			.then(json => console.log(json));
		}else{
			$(this).removeClass('badge-light').addClass('badge-info');
			$('.follow-text').text('FOLLOW');
			let targetId = "{{$user->id}}";
			let me = "{{Auth::guard('users')->user()->id}}";
			let users = switchId(targetId, me);
			var action = {
				'user_id_one' : users[0],
				'user_id_two' : users[1],			
			}

			fetch(API_CANCEL_FRIENDREQUEST, {
				method : 'POST',
				headers : {
					'Accept' : 'application/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				credentials: "same-origin",
				body : JSON.stringify(action),				
			})					
		}
	});
	
	listAllMoments();	


	function switchId(idOne, idTwo){
		var data = [];
		var one, two;
		if(idOne > idTwo){
			var temp = idOne;
			one = idTwo;
			two = temp;						
			data = [one, two];
		}
		else{
			data = [idOne, idTwo];
		}

		return data;
	}

	function listAllMoments(){
	  momentsElement.innerHTML = '';                
	  fetch(API_URL)
	    .then((response)=> response.json())
	    .then((moments)=> { 
	      console.log(moments);
	      moments.reverse();
	      moments.forEach(moment => {                          
	        const card = document.createElement('div');                
	        const cardBody1 = document.createElement('div');
	        const cardBody2 = document.createElement('div');
	        const cardFooter = document.createElement('div');
	        const span = document.createElement('span');
	        const tags = document.createElement('a'); 
	        const desc = document.createElement('p');  
	        const image = document.createElement('img'); 
	        const imagePath = window.location.origin + '/storage/' + moment.image;
	        const deleteButton = document.createElement('a');
	        const user_id = "{{Auth::guard('users')->id()}}";
	        const username = moment.id;                

	        card.setAttribute('class', 'card mb-3');
	        image.setAttribute('class', 'rounded');
	        image.setAttribute('src', imagePath);
	        image.style.height = '100%';
	        image.style.width = '100%';
	        image.style.display = 'block';
	        cardBody1.setAttribute('class', 'card-body');
	        cardBody2.setAttribute('class', 'card-body');
	        tags.setAttribute('class', 'card-link');
	        cardFooter.setAttribute('class', 'card-footer text-muted');                        

	        //DESCRIPTION 
	        desc.textContent = moment.description;                                

	        //TAGS
	        if(moment.tags.length >= 1){
	        span.textContent= "tags: ";                

	        tags.innerHTML = `${moment.tags.map((item, i)=> 
	          `<a href="#" style="text-decoration: none;"> <span class="badge badge-primary">${moment.tags[i]}</span> </a>`)}`;
	        }       
	        
	        //CARD FOOTER
	        // const user = moment.username.replace(/ +/g,'');
	        cardFooter.innerHTML = ` <a href="/user/${moment.username}">${moment.name} </a>, dibuat pada ${moment.created_at}`;        
	        cardBody1.appendChild(desc);
	        cardBody2.appendChild(span);                
	        cardBody2.appendChild(tags);                

	        card.appendChild(image);                
	        card.appendChild(cardBody1);                
	        card.appendChild(cardBody2);
	        card.appendChild(cardFooter);

	        loadingElement.style.display = 'none';
	        
	        momentsElement.appendChild(card);
	      });              
	    });
	  }        
</script>
@endsection