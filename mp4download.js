var app = angular.module("mp3Convert", ["checklist-model"]);

app.controller("downloadsController", function($scope, $http, $interval) {

    $scope.toDownload = [];
  
    $scope.updateFileList = function() {
      $http.get('downloadable.php')
        .success(function(data) {
          $scope.availableFiles = data;
        });
    };
    
    $interval( function() { $scope.updateFileList(); }, 5000);

    $scope.checkAll = function(e) {
      e.preventDefault();
      $scope.toDownload = $scope.availableFiles.map(function(item) item.filepath);
      $scope.status = "All files selected.";
    };

    $scope.checkNone = function(e) {
      e.preventDefault();
      $scope.toDownload = [];
      $scope.status = "Files to download cleared.";
    };
    
    $scope.download = function() {
      $http({
        method: 'GET',
        url: 'fetch.php',
        params: {
          files: JSON.stringify($scope.toDownload)
        }
      }).success(function() {
        $scope.status = "Files successfully downloaded.";
      }).error(function() {
        $scope.status = "There was a problem.";
      });
    };      
  });
        