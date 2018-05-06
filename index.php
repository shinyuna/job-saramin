<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="./css/style.css">
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.6/angular.min.js"></script>
	<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=355101c6b5582ff204373a01a427c0eb&libraries=services"></script>
	<!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script> -->

	
	<style>
		/*.text_box{width:400px;}*/
		
	</style>
</head>
<body>
	<div class="data_box first_box" ng-app="myApp" ng-controller="myjob">
	<div class="logo"><a href="index.php"><img src="./images/logo.png" alt="logo"></a></div>
		<div class="search_box">
			<form onsubmit="return false;" class="form">
<!--				<label for="job">채용검색</label>-->
				<input type="text" id="job" placeholder="검색내용을 넣어주세요(ex: 웹/java)">
				<button type="submit" class="job_btn"></button>
				<input type="hidden" class="play_button" ng-click="page(job_val);">
			</form>
			
		</div>
		<div class="text_box dn">
			<!-- 앵귤러로 데이터가져오는부분 -->
			<ul ng-repeat="job in jobs track by $index" ng-if="job.error != 0" >
				<p ng-if="$first" class="total_page_box">총 게시물 : <strong class="total_page">{{ job.total }}</strong></p>
				<li class="job1">{{ job.company }}</li>
				<li class="job2"><a href="{{ job.url }}" target="_blank">{{ job.title }}</a></li>
				<li class="job3">{{ job.experience }} | {{ job.education }} | {{ job.jtype }}</li>
				<li class="job4"><span>키워드</span>{{ job.category }}<span>{{ job.location }}</span></li>
				<li><button class="location_button" data-cnt="{{ job.cnt }}">위치</button></li>
			</ul>
            <div class="page_box">
                <button class="left">◀</button>
                <button class="right">▶</button>
            </div>
		</div>
		<p class="api_source">Powered by <a href="http://www.saramin.co.kr" target="_blank">취업 사람인</a></p>
		
	</div>
	<div id="map_box" class="dn">
	    <div id="map" style="width: 100%; height: 340px"></div>
	</div>
	<script>
		var page = 0;
		var cl_b = false;
 		var job_val = "";
 		var lo_temp = [];
        var lo_temp2 = [];

		  var app =angular.module("myApp",[]);
		    app.controller("myjob",function($scope,$http){
		    	$scope.page = function(){
		    		// $scope.job_val = job_val;

		    		$http.get("data.php?job_val="+job_val+"&page="+page).then(function(json){
		            $scope.jobs = json.data;
		            
		            for (var i = 0; i<10; i++) {
		            	console.log($scope.jobs[i].location);
                    //   var temp = $scope.jobs[i].location.split(',');
		            	//lo_temp[i] = $scope.jobs[i].company;
		            	lo_temp[i] = $scope.jobs[i].company+" "+$scope.jobs[i].location;
                        lo_temp2[i] = $scope.jobs[i].company;
                        console.log(lo_temp2[i]);
		            	console.log(lo_temp[i]);
		            }

		            console.log(page);
		        
		    		});

		        };
		        $scope.TrustDangerousSnippet = function(snippet) {
				  return $scope.trustAsHtml(snippet);
				};  


		    });
		$(".job_btn").on("click",function(){
			
			job_val = job_val_check();
			cl_b = true;
			var se_b = $(".search_box");
			var te_b = $(".text_box");
            var da_b = $(".data_box");
            var fo_b = $(".form")
			if(job_val == ''){
				fo_b.addClass('error');
				return;
			}else{
				se_b.removeClass('error');
                se_b.css({background:'#fafafa'});
                da_b.removeClass('first_box');
                da_b.children('div').css({'padding-left':140});
                te_b.addClass('db');
                
			}
		
			page = 0;
			$(".play_button").click();
		
			//job_play(job_val);
		});
		
			$(".left").on("click",function(){
				job_val = job_val_check();
				if(page<1 || cl_b == false){
					alert('첫 페이지 입니다.');
					return;
				}
				page-=1;
				$(".play_button").click();
			});
			$(".right").on("click",function(){
				job_val = job_val_check();
				console.log($('.total_page').text());
				if($('.total_page').text()<page*10+10|| cl_b == false){
					alert('더는 존재하지 않습니다.');
					return;
				}
				page+=1;
				$(".play_button").click();
			});
		
		

		function job_val_check(){
			
			return $("#job").val();
		};

   $(".text_box").on("click",".location_button",function(){
      $("#map_box").removeClass('dn');
       $("#map").empty(); 
        var data_cnt = $(this).attr("data-cnt");
        initialize(data_cnt);
   });
  
 function initialize(data_cnt) {
        var infowindow = new daum.maps.InfoWindow({zIndex:1});

        var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
            mapOption = {
                center: new daum.maps.LatLng(37.566826, 126.9786567), // 지도의 중심좌표
                level: 5 // 지도의 확대 레벨
            };  

        // 지도를 생성합니다    
        var map = new daum.maps.Map(mapContainer, mapOption); 

        // 장소 검색 객체를 생성합니다
        var ps = new daum.maps.services.Places(); 

        // 키워드로 장소를 검색합니다
        ps.keywordSearch(lo_temp[data_cnt], placesSearchCB); 
      console.log(lo_temp[data_cnt]);
        var error_count = 0;
        // 키워드 검색 완료 시 호출되는 콜백함수 입니다
        function placesSearchCB (data, status, pagination) {
            console.log(status);
           
            if (status === daum.maps.services.Status.OK) {

                // 검색된 장소 위치를 기준으로 지도 범위를 재설정하기위해
                // LatLngBounds 객체에 좌표를 추가합니다
                var bounds = new daum.maps.LatLngBounds();
             
                for (var i=0; i<data.length; i++) {
//                    displayMarker(data[i]);
                    text_marker(data[i]);    
                    bounds.extend(new daum.maps.LatLng(data[i].y, data[i].x));
                }       

                // 검색된 장소 위치를 기준으로 지도 범위를 재설정합니다
                map.setBounds(bounds);
            }else{
             //   console.log(lo_temp[data_cnt]+"|||"+lo_temp2[data_cnt]);
                error_count++;
                if(error_count>=2){
                    $("#map").empty();
                    alert('error / 회사 검색결과x');
                    
                    return false;
                }else{
                  //  console.log(lo_temp2[data_cnt]);
                    console.log(lo_temp2[data_cnt]);
                    ps.keywordSearch(lo_temp2[data_cnt], placesSearchCB);
                }
                
                
            }
        }

       function text_marker(place){
            // 지도에 마커를 표시하는 함수입니다
             var iwContent = '<div style="padding:5px;">'+lo_temp2[data_cnt]+'</div>', // 인포윈도우에 표출될 내용으로 HTML 문자열이나 document element가 가능합니다
            iwPosition = new daum.maps.LatLng(place.y, place.x), //인포윈도우 표시 위치입니다
            iwRemoveable = true; // removeable 속성을 ture 로 설정하면 인포윈도우를 닫을 수 있는 x버튼이 표시됩니다

            // 인포윈도우를 생성하고 지도에 표시합니다
            var infowindow = new daum.maps.InfoWindow({
                map: map, // 인포윈도우가 표시될 지도
                position : iwPosition, 
                content : iwContent,
                removable : iwRemoveable
            });
       }
//        function displayMarker(place) {
//
//            // 마커를 생성하고 지도에 표시합니다
//            var marker = new daum.maps.Marker({
//                map: map,
//                position: new daum.maps.LatLng(place.y, place.x) 
//            });
//
//            // 마커에 클릭이벤트를 등록합니다
//            daum.maps.event.addListener(marker, 'click', function() {
//                // 마커를 클릭하면 장소명이 인포윈도우에 표출됩니다
//                infowindow.setContent('<div style="padding:5px;font-size:12px;">' + place.place_name + '</div>');
//                infowindow.open(map, marker);
//            });
//        }

    }    	
   
   
</script>
 <!--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAqKhvqX8ov6T1TVniC1N7JL_vlNjsM7I&callback=initMap"
        async defer></script> -->
</body>
</html>
