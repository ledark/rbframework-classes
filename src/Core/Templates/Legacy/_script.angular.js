window.angular.module('mainApp')

.controller('{ngController}', function($scope, $http, $timeout){

	$scope.requestData = function() {
		
		$http.get('{httpRequestData}').then(function(response){
			console.log(response);
			$scope.test = response.data[0].exectime;
			
		}, function(response){
			console.log(response);
		});
		
	};

});