//Controller for the angularJS app

var app = angular.module('app', []);

app.controller('LogCtrl', function ($scope, $http) {

    $scope.show_response  = 0;
    $scope.loading = 0;
    //function called after hitting the 'get info' button
    $scope.getInfo = function(){
          console.log($scope.repo_link);
          $scope.loading = 1;   
          $scope.show_response  = 0;
          $http.post( "get_issues.php", {"data":$scope.repo_link}).
          success(function(response){
              console.log("From the php file"); 
              console.log(response);
              $scope.count  = response; 
              $scope.show_response  = 1;
              $scope.loading = 0;
          })
          .error(function(data, status) {
                  $scope.data = data || "Request failed";
                  $scope.status = status;			
          });
    };
    
});
